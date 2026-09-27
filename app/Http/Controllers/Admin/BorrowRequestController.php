<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\FirestoreService;
use App\Support\BorrowRequest;
use Google\Cloud\Firestore\Transaction;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use RuntimeException;
use Throwable;

/**
 * The administrator queue: review requests and record returns.
 *
 * Every decision that moves stock runs inside a Firestore transaction, so two
 * administrators approving at the same moment cannot lend the same unit twice.
 */
class BorrowRequestController extends Controller
{
    public function index(Request $request, FirestoreService $firestore): View
    {
        $status = (string) $request->query('status', 'all');

        $query = $firestore->borrowRequests();

        if (in_array($status, BorrowRequest::STATUSES, true)) {
            $query = $query->where('status', '=', $status);
        } else {
            $status = 'all';
        }

        $requests = array_map(
            BorrowRequest::fromSnapshot(...),
            $firestore->documentsOf($query),
        );

        usort($requests, BorrowRequest::newestFirst(...));

        return view('admin.requests', [
            'requests' => $requests,
            'status' => $status,
            'pendingCount' => $this->countByStatus($firestore, 'pending'),
        ]);
    }

    public function approve(Request $request, FirestoreService $firestore, string $id): RedirectResponse
    {
        try {
            $name = $this->decide($firestore, $id, 'approved', -1, $request->session()->get('firebase.uid'));
        } catch (RuntimeException $e) {
            return back()->withErrors(['request' => $e->getMessage()]);
        }

        return back()->with('status', 'Approved the loan of '.$name.'.');
    }

    public function reject(Request $request, FirestoreService $firestore, string $id): RedirectResponse
    {
        $validated = $request->validate([
            'review_note' => ['nullable', 'string', 'max:500'],
        ]);

        try {
            $name = $this->decide(
                $firestore,
                $id,
                'rejected',
                0,
                $request->session()->get('firebase.uid'),
                $validated['review_note'] ?? null,
            );
        } catch (RuntimeException $e) {
            return back()->withErrors(['request' => $e->getMessage()]);
        }

        return back()->with('status', 'Rejected the request for '.$name.'.');
    }

    public function markReturned(Request $request, FirestoreService $firestore, string $id): RedirectResponse
    {
        try {
            $name = $this->decide($firestore, $id, 'returned', +1, $request->session()->get('firebase.uid'));
        } catch (RuntimeException $e) {
            return back()->withErrors(['request' => $e->getMessage()]);
        }

        return back()->with('status', 'Recorded the return of '.$name.'.');
    }

    /**
     * Move a request to a new status and adjust the stock of the item by
     * $stockDelta units, both or neither.
     *
     * @param  int  $stockDelta  -1 when approving, +1 when a loan comes back, 0 otherwise
     *
     * @throws RuntimeException when the transition is not allowed
     */
    private function decide(
        FirestoreService $firestore,
        string $id,
        string $to,
        int $stockDelta,
        string $adminUid,
        ?string $note = null,
    ): string {
        $requestRef = $firestore->borrowRequests()->document($id);
        $equipments = $firestore->equipments();

        try {
            return $firestore->client()->runTransaction(
                function (Transaction $transaction) use ($requestRef, $equipments, $to, $stockDelta, $adminUid, $note, $firestore): string {
                    $snapshot = $transaction->snapshot($requestRef);

                    if (! $snapshot->exists()) {
                        throw new RuntimeException('That request no longer exists.');
                    }

                    $borrowRequest = BorrowRequest::fromSnapshot($snapshot);
                    $this->assertTransitionAllowed($borrowRequest->status, $to);

                    if ($stockDelta !== 0) {
                        $equipmentRef = $equipments->document($borrowRequest->equipmentId);
                        $equipment = $transaction->snapshot($equipmentRef);

                        if (! $equipment->exists()) {
                            throw new RuntimeException('The item for this request is no longer in the catalogue.');
                        }

                        $available = (int) ($equipment->data()['availableQuantity'] ?? 0);
                        $total = (int) ($equipment->data()['totalQuantity'] ?? 0);
                        $updated = $available + ($stockDelta * $borrowRequest->quantity);

                        if ($updated < 0) {
                            throw new RuntimeException('There are no units of '.$borrowRequest->equipmentName.' left to lend.');
                        }

                        $transaction->set($equipmentRef, [
                            // Clamped so a double return cannot push stock above what is owned.
                            'availableQuantity' => min($updated, $total),
                            'updatedAt' => $firestore->now(),
                        ], ['merge' => true]);
                    }

                    $payload = [
                        'status' => $to,
                        'reviewedBy' => $adminUid,
                        'reviewedAt' => $firestore->now(),
                        'updatedAt' => $firestore->now(),
                    ];

                    if ($note !== null) {
                        $payload['reviewNote'] = $note;
                    }

                    if ($to === 'returned') {
                        $payload['returnedAt'] = $firestore->now();
                    }

                    $transaction->set($requestRef, $payload, ['merge' => true]);

                    return $borrowRequest->equipmentName;
                }
            );
        } catch (RuntimeException $e) {
            throw $e;
        } catch (Throwable $e) {
            throw new RuntimeException('Could not update the request: '.$e->getMessage(), 0, $e);
        }
    }

    /**
     * @throws RuntimeException
     */
    private function assertTransitionAllowed(string $from, string $to): void
    {
        // Mirrors the state diagram in docs/firestore-schema.md.
        $allowed = match ($from) {
            'pending' => ['approved', 'rejected', 'cancelled'],
            'approved' => ['returned'],
            default => [],
        };

        if (! in_array($to, $allowed, true)) {
            throw new RuntimeException('A request that is already '.$from.' cannot be marked '.$to.'.');
        }
    }

    private function countByStatus(FirestoreService $firestore, string $status): int
    {
        return count($firestore->documentsOf(
            $firestore->borrowRequests()->where('status', '=', $status)
        ));
    }
}
