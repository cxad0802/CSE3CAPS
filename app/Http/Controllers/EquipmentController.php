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

        // Ordered by name alone: adding category would need the composite index
        // from firestore.indexes.json, which is not deployed to the project yet.
        $query = $firestore->equipments()->orderBy('name');

        $equipments = array_values(array_filter(
            array_map(
                Equipment::fromSnapshot(...),
                $firestore->documentsOf($query),
            ),
            fn (Equipment $equipment) => $equipment->matches($search),
        ));

        return view('customer.catalog', [
            'equipments' => $equipments,
            'search' => $search,
        ]);
    }
}
