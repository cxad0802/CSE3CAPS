<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\FirestoreService;
use App\Support\BorrowRequest;
use App\Support\Equipment;
use DateTimeImmutable;
use Illuminate\View\View;

/**
 * Landing page of the manager portal: current workload and stock at a glance.
 *
 * The figures are counted per asset document, which is what the inventory
 * screen lists, so the two screens agree with each other.
 */
class DashboardController extends Controller
{
    public function __invoke(FirestoreService $firestore): View
    {
        $equipments = array_map(
            Equipment::fromSnapshot(...),
            $firestore->documentsOf($firestore->equipments()),
        );

        $requests = array_map(
            BorrowRequest::fromSnapshot(...),
            $firestore->documentsOf($firestore->borrowRequests()),
        );

        $now = new DateTimeImmutable('today');
        $horizon = $now->modify('+30 days');

        $criticalAlerts = [];
        $expiringWarranties = [];
        $categoryCounts = [];

        foreach ($equipments as $equipment) {
            if ($equipment->isInMaintenance()) {
                $criticalAlerts[] = [
                    'title' => $equipment->name.' ('.$equipment->id.')',
                    'message' => $equipment->description ?: 'Status: maintenance. Requires attention.',
                    'time' => 'Action required',
                ];
            }

            $expires = $equipment->warrantyExpiresAt;

            if ($expires !== null && $expires <= $horizon) {
                $expired = $expires < $now;
                $expiringWarranties[] = [
                    'id' => $equipment->id,
                    'name' => $equipment->name,
                    'expiration' => $expires->format('M j, Y'),
                    'action' => $expired ? 'Expired' : 'Expiring',
                    'expired' => $expired,
                ];
            }
        }

        foreach ($requests as $borrowRequest) {
            if ($borrowRequest->status === 'approved'
                && $borrowRequest->borrowTo
                && $borrowRequest->borrowTo < $now) {
                $days = $now->diff($borrowRequest->borrowTo)->days;
                $criticalAlerts[] = [
                    'title' => $borrowRequest->equipmentName.' ('.$borrowRequest->equipmentId.')',
                    'message' => 'Overdue by '.$days.' '.str()->plural('day', $days).'. Borrower: '.$borrowRequest->userEmail,
                    'time' => 'Overdue',
                ];
            }

            if (! in_array($borrowRequest->status, ['cancelled', 'rejected'], true)) {
                $category = 'Other';
                foreach ($equipments as $equipment) {
                    if ($equipment->id === $borrowRequest->equipmentId) {
                        $category = ucfirst($equipment->category);
                        break;
                    }
                }
                $categoryCounts[$category] = ($categoryCounts[$category] ?? 0) + 1;
            }
        }

        arsort($categoryCounts);
        $topCategories = array_slice($categoryCounts, 0, 4, true);
        $maxCount = $topCategories === [] ? 1 : max($topCategories);
        $colors = ['bg-red-700', 'bg-red-600 opacity-80', 'bg-red-900 opacity-60', 'bg-red-200'];

        $frequentlyBorrowed = [];
        $i = 0;
        foreach ($topCategories as $name => $count) {
            $frequentlyBorrowed[] = [
                'name' => $name,
                'count' => $count,
                'percentage' => (int) round(($count / $maxCount) * 100),
                'colorClass' => $colors[$i % count($colors)],
            ];
            $i++;
        }

        // Counted in PHP rather than with one query per status, which keeps the
        // dashboard to a single read of each collection.
        return view('admin.dashboard', [
            'totalAssets' => count($equipments),
            'availableAssets' => count(array_filter(
                $equipments,
                fn (Equipment $equipment) => $equipment->isBorrowable(),
            )),
            'inMaintenance' => count(array_filter(
                $equipments,
                fn (Equipment $equipment) => $equipment->isInMaintenance(),
            )),
            'checkedOut' => count(array_filter(
                $requests,
                fn (BorrowRequest $borrowRequest) => $borrowRequest->status === 'approved',
            )),
            'criticalAlerts' => array_slice($criticalAlerts, 0, 5),
            'expiringWarranties' => array_slice($expiringWarranties, 0, 5),
            'allWarranties' => $expiringWarranties,
            'frequentlyBorrowed' => $frequentlyBorrowed,
        ]);
    }
}
