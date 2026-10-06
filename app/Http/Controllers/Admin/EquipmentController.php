<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\FirestoreService;
use App\Support\BorrowRequest;
use App\Support\Equipment;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

/**
 * Inventory management: the administrator's view of the equipments collection.
 *
 * The asset tag the university already uses is the Firestore document id, so a
 * borrow request can be traced back to a physical label.
 */
class EquipmentController extends Controller
{
    public function index(Request $request, FirestoreService $firestore): View
    {
        $search = trim((string) $request->query('search', ''));

        $equipments = array_map(
            Equipment::fromSnapshot(...),
            $firestore->documentsOf($firestore->equipments()->orderBy('name')),
        );

        return view('admin.inventory', [
            'inventory' => array_map(
                fn (Equipment $equipment) => $equipment->toArray(),
                array_values(array_filter(
                    $equipments,
                    fn (Equipment $equipment) => $equipment->matches($search),
                )),
            ),
            'search' => $search,
        ]);
    }

    public function create(): View
    {
        return view('admin.equipment_create');
    }

    public function store(Request $request, FirestoreService $firestore): RedirectResponse
    {
        $validated = $request->validate([
            // Becomes the document id, so it may not contain a slash or collide
            // with the placeholder document that firestore:init writes.
            'asset_id' => [
                'required', 'string', 'max:64',
                'regex:/^[A-Za-z0-9_-]+$/',
                Rule::notIn([FirestoreService::SCHEMA_DOCUMENT_ID]),
            ],
            'equipment_name' => ['required', 'string', 'max:120'],
            'category' => ['required', Rule::in(Equipment::CATEGORIES)],
            'serial_number' => ['nullable', 'string', 'max:120'],
            'initial_status' => ['required', Rule::in(Equipment::STATUSES)],
            'warranty_expiration' => ['nullable', 'date'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ], [
            'asset_id.regex' => 'The asset ID may only contain letters, numbers, hyphens and underscores.',
        ]);

        $document = $firestore->equipments()->document($validated['asset_id']);

        // set() would overwrite an existing asset without warning.
        if ($document->snapshot()->exists()) {
            return back()->withInput()->withErrors([
                'asset_id' => 'An asset with the ID '.$validated['asset_id'].' already exists.',
            ]);
        }

        $available = $validated['initial_status'] === 'available' ? 1 : 0;

        $document->set([
            'name' => $validated['equipment_name'],
            'category' => $validated['category'],
            'status' => $validated['initial_status'],
            'description' => $validated['notes'] ?? '',
            'serialNumber' => $validated['serial_number'] ?? null,
            'warrantyExpiresAt' => $validated['warranty_expiration'] ?? null,
            // The form registers one physical asset at a time.
            'totalQuantity' => 1,
            'availableQuantity' => $available,
            'createdAt' => $firestore->now(),
            'updatedAt' => $firestore->now(),
        ]);

        return redirect()->route('admin.inventory')
            ->with('success', $validated['equipment_name'].' has been added to the inventory.');
    }

    public function edit(Request $request, FirestoreService $firestore): View|RedirectResponse
    {
        $id = (string) $request->query('id', '');

        if ($id === '') {
            return redirect()->route('admin.inventory')
                ->withErrors(['error' => 'No equipment ID provided.']);
        }

        $snapshot = $firestore->equipments()->document($id)->snapshot();

        if (! $snapshot->exists()) {
            return redirect()->route('admin.inventory')
                ->withErrors(['error' => 'Equipment not found.']);
        }

        return view('admin.inventory-edit', [
            'equipment' => Equipment::fromSnapshot($snapshot)->toArray(),
        ]);
    }

    public function update(Request $request, FirestoreService $firestore, string $id): RedirectResponse
    {
        $validated = $request->validate([
            'equipment_name' => ['required', 'string', 'max:120'],
            'category' => ['required', Rule::in(Equipment::CATEGORIES)],
            'status' => ['required', Rule::in(Equipment::STATUSES)],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        $document = $firestore->equipments()->document($id);

        if (! $document->snapshot()->exists()) {
            return redirect()->route('admin.inventory')
                ->withErrors(['error' => 'Equipment not found.']);
        }

        // Merged rather than replaced, so the quantities the borrowing
        // transactions maintain are left alone.
        $document->set([
            'name' => $validated['equipment_name'],
            'category' => $validated['category'],
            'status' => $validated['status'],
            'description' => $validated['notes'] ?? '',
            'updatedAt' => $firestore->now(),
        ], ['merge' => true]);

        return redirect()->route('admin.inventory')
            ->with('success', $validated['equipment_name'].' has been updated.');
    }

    public function destroy(FirestoreService $firestore, string $id): RedirectResponse
    {
        $snapshot = $firestore->equipments()->document($id)->snapshot();

        if (! $snapshot->exists()) {
            return back()->withErrors(['error' => 'Equipment not found.']);
        }

        // Deleting an asset that is out on loan would leave the request
        // pointing at nothing, and the loan could then never be returned.
        $openRequests = array_filter(
            array_map(
                BorrowRequest::fromSnapshot(...),
                $firestore->documentsOf(
                    $firestore->borrowRequests()->where('equipmentId', '=', $id)
                ),
            ),
            fn (BorrowRequest $borrowRequest) => in_array($borrowRequest->status, ['pending', 'approved'], true),
        );

        if ($openRequests !== []) {
            return back()->withErrors([
                'error' => 'This asset has '.count($openRequests).' open request(s) and cannot be deleted. '
                    .'Set its status to Retired instead.',
            ]);
        }

        $firestore->equipments()->document($id)->delete();

        return back()->with('success', 'Equipment deleted successfully.');
    }
}
