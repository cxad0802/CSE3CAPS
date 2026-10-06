<?php

namespace App\Http\Controllers;

use App\Services\BorrowRequestWorkflow;
use App\Services\FirestoreService;
use App\Support\BorrowRequest;
use App\Support\Equipment;
use DateTimeImmutable;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use RuntimeException;

/**
 * The student side of borrowing: raise a request and track its progress.
 *
 * Requests are only ever created with the "pending" status. Everything that
 * moves a request afterwards goes through BorrowRequestWorkflow so the stock
 * of the item stays in step with it.
 */
class BorrowRequestController extends Controller
{
    /**
     * Purposes the form offers, validated here so a hand-crafted post cannot
     * put arbitrary content in the document.
     */
    private const PURPOSES = [
        'assignment' => 'Course assignment or project',
        'research' => 'Academic research',
        'presentation' => 'Presentation or event',
    ];

    public function create(Request $request, FirestoreService $firestore): View|RedirectResponse
    {
        $equipmentId = (string) $request->query('equipmentId', '');
        $snapshot = $equipmentId === ''
            ? null
            : $firestore->equipments()->document($equipmentId)->snapshot();

        if (! $snapshot || ! $snapshot->exists()) {
            return redirect()->route('catalog')->withErrors([
                'equipment' => 'Choose an item from the catalogue to request.',
            ]);
        }

        $equipment = Equipment::fromSnapshot($snapshot);

        if (! $equipment->isBorrowable()) {
            return redirect()->route('catalog')->withErrors([
                'equipment' => $equipment->name.' is not available to borrow at the moment.',
            ]);
        }

        // The name is read back from Firestore rather than taken from the query
        // string, so a doctored link cannot mislabel the item being requested.
        return view('borrow.create', [
            'equipmentId' => $equipment->id,
            'equipmentName' => $equipment->name,
        ]);
    }

    public function store(Request $request, FirestoreService $firestore): RedirectResponse
    {
        $validated = $request->validate([
            'equipmentId' => ['required', 'string'],
            'borrowFrom' => ['required', 'date', 'after_or_equal:today'],
            'borrowTo' => ['required', 'date', 'after:borrowFrom'],
            'purpose' => ['required', 'string', 'in:'.implode(',', array_keys(self::PURPOSES))],
            'details' => ['nullable', 'string', 'max:500'],
        ]);

        $snapshot = $firestore->equipments()->document($validated['equipmentId'])->snapshot();

        if (! $snapshot->exists()) {
            return back()->withInput()->withErrors([
                'equipmentId' => 'That item is no longer in the catalogue.',
            ]);
        }

        $equipment = Equipment::fromSnapshot($snapshot);

        if (! $equipment->isBorrowable()) {
            return back()->withInput()->withErrors([
                'equipmentId' => 'That item is currently unavailable.',
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
            'borrowFrom' => new DateTimeImmutable($validated['borrowFrom']),
            'borrowTo' => new DateTimeImmutable($validated['borrowTo']),
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
            ->with('success', 'Your request for '.$equipment->name.' has been submitted and is pending approval.');
    }

    public function index(Request $request, FirestoreService $firestore): View
    {
        $filter = (string) $request->query('filter', 'all');

        // Scoped to the signed-in student. Without this where() the screen would
        // list every request in the system.
        $query = $firestore->borrowRequests()
            ->where('userId', '=', $request->session()->get('firebase.uid'));

        $requests = array_map(
            BorrowRequest::fromSnapshot(...),
            $firestore->documentsOf($query),
        );

        usort($requests, BorrowRequest::newestFirst(...));

        if (in_array($filter, ['pending', 'approved'], true)) {
            $requests = array_values(array_filter(
                $requests,
                fn (BorrowRequest $borrowRequest) => $borrowRequest->status === $filter,
            ));
        } else {
            $filter = 'all';
        }

        return view('customer.requests', [
            'myRequests' => array_map(
                fn (BorrowRequest $borrowRequest) => $borrowRequest->forStudentList(),
                $requests,
            ),
            'currentFilter' => $filter,
        ]);
    }

    public function cancel(Request $request, BorrowRequestWorkflow $workflow, string $id): RedirectResponse
    {
        return $this->act($request, $workflow, $id, 'cancelled', 'Request for %s cancelled.');
    }

    public function returnItem(Request $request, BorrowRequestWorkflow $workflow, string $id): RedirectResponse
    {
        return $this->act($request, $workflow, $id, 'returned', '%s has been marked as returned.');
    }

    /**
     * Apply a transition a student is allowed to make to their own request.
     *
     * Ownership is enforced inside the transaction rather than by a Firestore
     * rule, because a rule cannot see the Laravel session.
     */
    private function act(
        Request $request,
        BorrowRequestWorkflow $workflow,
        string $id,
        string $to,
        string $message,
    ): RedirectResponse {
        $uid = (string) $request->session()->get('firebase.uid');

        try {
            $name = $workflow->transition($id, $to, $uid, mustBelongTo: $uid);
        } catch (RuntimeException $e) {
            return back()->withErrors(['request' => $e->getMessage()]);
        }

        return back()->with('success', sprintf($message, $name));
    }
}
