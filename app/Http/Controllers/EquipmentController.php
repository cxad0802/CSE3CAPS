<?php

namespace App\Http\Controllers;

use App\Services\FirestoreService;
use App\Support\Equipment;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Public, read-only catalogue of borrowable equipment.
 */
class EquipmentController extends Controller
{
    public function index(Request $request, FirestoreService $firestore): View
    {
        $search = trim((string) $request->query('search', ''));

        // Ordered by name alone, and the status filter is applied below rather
        // than with a where(): combining the two would need the composite index
        // from firestore.indexes.json, which is not deployed to the project yet.
        $equipments = array_map(
            Equipment::fromSnapshot(...),
            $firestore->documentsOf($firestore->equipments()->orderBy('name')),
        );

        $catalog = array_map(
            fn (Equipment $equipment) => $equipment->toArray(),
            array_values(array_filter(
                $equipments,
                fn (Equipment $equipment) => $equipment->isBorrowable() && $equipment->matches($search),
            )),
        );

        return view('customer.catalog', [
            'catalog' => $catalog,
            'search' => $search,
        ]);
    }
}
