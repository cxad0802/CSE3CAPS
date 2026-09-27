<?php

namespace App\Http\Controllers;

use App\Services\FirestoreService;
use App\Support\BorrowRequest;
use App\Support\Equipment;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * The student side of borrowing: raise a request and track its progress.
 *
 * Requests are only ever created with the "pending" status; approving one and
 * adjusting stock is an administrator action, see Admin\BorrowRequestController.
 */
class BorrowRequestController extends Controller
{
    /**
     * Purposes offered by the form. Stored as free text, validated here so a
     * hand-crafted request cannot put arbitrary content in the document.
     */
    private const PURPOSES = [
        'assignment' => 'Class assignment or project',
        'research' => 'Academic research',
        'event' => 'University event',
        'other' => 'Other',
    ];

    public function create(Request $request, FirestoreService $firestore): View|RedirectResponse
    {
        $equipmentId = (string) $request->query('equipment', '');
        $snapshot = $equipmentId === ''
            ? null
            : $firestore->equipments()->document($equipmentId)->snapshot();

        if (! $snapshot || ! $snapshot->exists()) {
            return redirect()->route('catalog')->withErrors([
                'equipment' => 'Choose an item from the catalogue to request.',
            ]);
        }

        return view('borrow.create', [
            'equipment' => Equipment::fromSnapshot($snapshot),
            'purposes' => self::PURPOSES,
        ]);
    }

    public function store(Request $request, FirestoreService $firestore): RedirectResponse
    {
        $validated = $request->validate([
            'equipment_id' => ['required', 'string'],
            'borrow_from' => ['required', 'date', 'after_or_equal:today'],
            'borrow_to' => ['required', 'date', 'after:borrow_from'],
            'purpose' => ['required', 'string', 'in:'.implode(',', array_keys(self::PURPOSES))],
            'details' => ['nullable', 'string', 'max:500'],
            'terms' => ['accepted'],
        ]);

        $snapshot = $firestore->equipments()->document($validated['equipment_id'])->snapshot();

        if (! $snapshot->exists()) {
            return back()->withInput()->withErrors([
                'equipment_id' => 'That item is no longer in the catalogue.',
            ]);
        }

        $equipment = Equipment::fromSnapshot($snapshot);

        if (! $equipment->isBorrowable()) {
            return back()->withInput()->withErrors([
                'equipment_id' => 'That item is currently unavailable.',
            ]);
        }

        $purpose = self::PURPOSES[$validated['purpose']];

        if (! empty($validated['details'])) {
            $purpose .= ' - '.$validated['details'];
        }

        $firestore->borrowRequests()->add([
            'userId' => $request->session()->get('firebase.uid'),
            'userEmail' => $request->session()->get('firebase.email'),
            'equipmentId' => $equipment->id,
            // Denormalised so the admin queue renders without reading equipments.
            'equipmentName' => $equipment->name,
            // The form borrows a single unit; bulk loans are raised one at a time.
            'quantity' => 1,
            'borrowFrom' => new \DateTimeImmutable($validated['borrow_from']),
            'borrowTo' => new \DateTimeImmutable($validated['borrow_to']),
            'purpose' => $purpose,
            'status' => 'pending',
            'reviewedBy' => null,
            'reviewedAt' => null,
            'reviewNote' => null,
            'returnedAt' => null,
            'createdAt' => $firestore->now(),
            'updatedAt' => $firestore->now(),
        ]);

        return redirect()->route('requests.mine')
            ->with('status', 'Your request for '.$equipment->name.' has been submitted for approval.');
    }

    public function index(Request $request, FirestoreService $firestore): View
    {
        $filter = (string) $request->query('filter', 'all');

        $query = $firestore->borrowRequests()
            ->where('userId', '=', $request->session()->get('firebase.uid'));

        $requests = array_map(
            BorrowRequest::fromSnapshot(...),
            $firestore->documentsOf($query),
        );

        usort($requests, BorrowRequest::newestFirst(...));

        // Grouped rather than filtered by a single status, because the screen
        // offers "active" as pending plus approved.
        $statuses = match ($filter) {
            'active' => ['pending', 'approved'],
            'returned' => ['returned'],
            default => null,
        };

        if ($statuses !== null) {
            $requests = array_values(array_filter(
                $requests,
                fn (BorrowRequest $borrowRequest) => in_array($borrowRequest->status, $statuses, true),
            ));
        } else {
            $filter = 'all';
        }

        return view('customer.requests', [
            'requests' => $requests,
            'filter' => $filter,
        ]);
    }

    public function cancel(Request $request, FirestoreService $firestore, string $id): RedirectResponse
    {
        $document = $firestore->borrowRequests()->document($id);
        $snapshot = $document->snapshot();

        // Ownership is checked here rather than in a rule: Firestore queries
        // cannot enforce that the document belongs to the signed-in student.
        if (! $snapshot->exists() || $snapshot->get('userId') !== $request->session()->get('firebase.uid')) {
            abort(404);
        }

        if (! BorrowRequest::fromSnapshot($snapshot)->isCancellable()) {
            return back()->withErrors([
                'request' => 'This request has already been reviewed and can no longer be cancelled.',
            ]);
        }

        $document->set([
            'status' => 'cancelled',
            'updatedAt' => $firestore->now(),
        ], ['merge' => true]);

        return back()->with('status', 'Request cancelled.');
    }
}
