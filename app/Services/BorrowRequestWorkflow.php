<?php

namespace App\Services;

use App\Support\BorrowRequest;
use Google\Cloud\Firestore\Transaction;
use RuntimeException;
use Throwable;

/**
 * The one place a borrow request changes status.
 *
 * Moving a request and moving the stock it reserves has to be atomic, so every
 * transition runs inside a Firestore transaction: two administrators approving
 * at the same moment cannot lend the same unit twice, and a request can never
 * end up approved without a unit having been taken out of the pool.
 *
 * The allowed transitions mirror the state diagram in docs/firestore-schema.md.
 */
class BorrowRequestWorkflow
{
    /**
     * Statuses a request may move to, keyed by the status it is leaving.
     *
     * @var array<string, list<string>>
     */
    private const TRANSITIONS = [
        'pending' => ['approved', 'rejected', 'cancelled'],
        'approved' => ['returned'],
    ];

    /**
     * How many units the pool gains when a request reaches a given status.
     *
     * Approving takes a unit out; a return or a cancellation of an approved
     * loan puts it back. Rejecting a pending request never held one.
     *
     * @var array<string, int>
     */
    private const STOCK_DELTA = [
        'approved' => -1,
        'returned' => +1,
    ];

    public function __construct(private FirestoreService $firestore) {}

    /**
     * Move a request to $to and adjust the stock of its item, both or neither.
     *
     * @param  string  $actorUid  uid recorded as having made the decision
     * @param  string|null  $mustBelongTo  when set, the request is only touched
     *                                     if it belongs to this uid; used for
     *                                     the actions students perform on their
     *                                     own requests
     * @return string the equipment name, for the confirmation message
     *
     * @throws RuntimeException when the request is missing, owned by somebody
     *                          else, or already past the requested status
     */
    public function transition(
        string $id,
        string $to,
        string $actorUid,
        ?string $note = null,
        ?string $mustBelongTo = null,
    ): string {
        $requestRef = $this->firestore->borrowRequests()->document($id);
        $equipments = $this->firestore->equipments();
        $firestore = $this->firestore;

        try {
            return $firestore->client()->runTransaction(
                function (Transaction $transaction) use ($requestRef, $equipments, $to, $actorUid, $note, $mustBelongTo, $firestore): string {
                    $snapshot = $transaction->snapshot($requestRef);

                    if (! $snapshot->exists()) {
                        throw new RuntimeException('That request no longer exists.');
                    }

                    $borrowRequest = BorrowRequest::fromSnapshot($snapshot);

                    if ($mustBelongTo !== null && $borrowRequest->userId !== $mustBelongTo) {
                        throw new RuntimeException('That request belongs to somebody else.');
                    }

                    $allowed = self::TRANSITIONS[$borrowRequest->status] ?? [];

                    if (! in_array($to, $allowed, true)) {
                        throw new RuntimeException(
                            'A request that is already '.$borrowRequest->status.' cannot be marked '.$to.'.'
                        );
                    }

                    $delta = self::STOCK_DELTA[$to] ?? 0;

                    if ($delta !== 0) {
                        $equipmentRef = $equipments->document($borrowRequest->equipmentId);
                        $equipment = $transaction->snapshot($equipmentRef);

                        if (! $equipment->exists()) {
                            throw new RuntimeException('The item for this request is no longer in the catalogue.');
                        }

                        $data = $equipment->data() ?? [];
                        $available = (int) ($data['availableQuantity'] ?? 0);
                        $total = (int) ($data['totalQuantity'] ?? 0);
                        $updated = $available + ($delta * $borrowRequest->quantity);

                        if ($updated < 0) {
                            throw new RuntimeException(
                                'There are no units of '.$borrowRequest->equipmentName.' left to lend.'
                            );
                        }

                        $transaction->set($equipmentRef, [
                            // Clamped so a double return cannot push stock above what is owned.
                            'availableQuantity' => min($updated, $total),
                            'updatedAt' => $firestore->now(),
                        ], ['merge' => true]);
                    }

                    $payload = [
                        'status' => $to,
                        'updatedAt' => $firestore->now(),
                    ];

                    // A student cancelling or returning their own request is not
                    // a review, so the reviewer fields are left untouched.
                    if ($mustBelongTo === null) {
                        $payload['reviewedBy'] = $actorUid;
                        $payload['reviewedAt'] = $firestore->now();
                    }

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
}
