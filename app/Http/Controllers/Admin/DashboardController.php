<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\FirestoreService;
use App\Support\BorrowRequest;
use App\Support\Equipment;
use Illuminate\View\View;

/**
 * Landing page of the manager portal: current workload and stock at a glance.
 */
class DashboardController extends Controller
{
    public function index(FirestoreService $firestore): View
    {
        $requests = array_map(
            BorrowRequest::fromSnapshot(...),
            $firestore->documentsOf($firestore->borrowRequests()),
        );

        $equipments = array_map(
            Equipment::fromSnapshot(...),
            $firestore->documentsOf($firestore->equipments()),
        );

        // Counted in PHP rather than with one query per status: the collection is
        // small, and this keeps the dashboard to a single read of each collection.
        $counts = array_count_values(array_map(fn (BorrowRequest $r) => $r->status, $requests));

        $recent = $requests;
        usort($recent, BorrowRequest::newestFirst(...));

        return view('admin.dashboard', [
            'counts' => [
                'pending' => $counts['pending'] ?? 0,
                'approved' => $counts['approved'] ?? 0,
                'returned' => $counts['returned'] ?? 0,
                'rejected' => $counts['rejected'] ?? 0,
            ],
            'totalRequests' => count($requests),
            'equipmentCount' => count($equipments),
            'unitsAvailable' => array_sum(array_map(fn (Equipment $e) => $e->availableQuantity, $equipments)),
            'unitsOwned' => array_sum(array_map(fn (Equipment $e) => $e->totalQuantity, $equipments)),
            'recent' => array_slice($recent, 0, 5),
        ]);
    }
}
