<?php

namespace App\Support;

use DateTimeImmutable;
use Google\Cloud\Core\Timestamp;
use Google\Cloud\Firestore\DocumentSnapshot;

/**
 * One borrowRequests document, shaped for the Blade views.
 *
 * The equipment name and the borrower's email are denormalised into the
 * document when the request is created, so neither the student's list nor the
 * admin queue needs a second read per row. See docs/firestore-schema.md.
 */
readonly class BorrowRequest
{
    /**
     * Statuses a request can hold, in the order the status transitions allow.
     */
    public const STATUSES = ['pending', 'approved', 'rejected', 'returned', 'cancelled'];

    public function __construct(
        public string $id,
        public string $userId,
        public string $userEmail,
        public string $equipmentId,
        public string $equipmentName,
        public int $quantity,
        public ?DateTimeImmutable $borrowFrom,
        public ?DateTimeImmutable $borrowTo,
        public string $purpose,
        public string $status,
        public ?string $reviewNote,
        public ?DateTimeImmutable $createdAt,
    ) {}

    public static function fromSnapshot(DocumentSnapshot $snapshot): self
    {
        $data = $snapshot->data() ?? [];

        return new self(
            id: $snapshot->id(),
            userId: (string) ($data['userId'] ?? ''),
            userEmail: (string) ($data['userEmail'] ?? ''),
            equipmentId: (string) ($data['equipmentId'] ?? ''),
            equipmentName: (string) ($data['equipmentName'] ?? 'Unknown item'),
            quantity: (int) ($data['quantity'] ?? 1),
            borrowFrom: self::toDate($data['borrowFrom'] ?? null),
            borrowTo: self::toDate($data['borrowTo'] ?? null),
            purpose: (string) ($data['purpose'] ?? ''),
            status: in_array($data['status'] ?? null, self::STATUSES, true) ? $data['status'] : 'pending',
            reviewNote: $data['reviewNote'] ?? null,
            createdAt: self::toDate($data['createdAt'] ?? null),
        );
    }

    /**
     * Comparator putting the newest request first.
     *
     * Sorting happens here rather than with orderBy('createdAt') because
     * combining that with a where() on another field needs one of the composite
     * indexes in firestore.indexes.json, and those are only live once someone
     * runs "firebase deploy --only firestore:indexes". At the scale of this
     * system the collection is read in full anyway.
     */
    public static function newestFirst(self $a, self $b): int
    {
        return ($b->createdAt?->getTimestamp() ?? 0) <=> ($a->createdAt?->getTimestamp() ?? 0);
    }

    /**
     * Short human-facing reference. Firestore ids are 20 random characters,
     * which is unusable in conversation, so only the leading run is shown.
     */
    public function reference(): string
    {
        return '#'.strtoupper(substr($this->id, 0, 6));
    }

    public function statusLabel(): string
    {
        return match ($this->status) {
            'pending' => 'Pending approval',
            'approved' => 'Active loan',
            'rejected' => 'Rejected',
            'returned' => 'Returned',
            'cancelled' => 'Cancelled',
        };
    }

    /**
     * Tailwind classes for the status pill, kept beside the label so the two
     * cannot drift apart.
     */
    public function statusClasses(): string
    {
        return match ($this->status) {
            'pending' => 'bg-yellow-100 text-yellow-800 border-yellow-200',
            'approved' => 'bg-green-100 text-green-800 border-green-200',
            'rejected' => 'bg-red-100 text-red-800 border-red-200',
            'returned' => 'bg-gray-100 text-gray-800 border-gray-200',
            'cancelled' => 'bg-gray-100 text-gray-500 border-gray-200',
        };
    }

    public function accentClasses(): string
    {
        return match ($this->status) {
            'pending' => 'bg-yellow-400',
            'approved' => 'bg-green-500',
            'rejected' => 'bg-red-500',
            default => 'bg-gray-400',
        };
    }

    public function period(): string
    {
        if (! $this->borrowFrom || ! $this->borrowTo) {
            return 'Dates not recorded';
        }

        return $this->borrowFrom->format('M j').' - '.$this->borrowTo->format('M j');
    }

    public function days(): ?int
    {
        if (! $this->borrowFrom || ! $this->borrowTo) {
            return null;
        }

        return (int) $this->borrowFrom->diff($this->borrowTo)->days;
    }

    /**
     * A student may withdraw a request only while nobody has acted on it.
     */
    public function isCancellable(): bool
    {
        return $this->status === 'pending';
    }

    private static function toDate(mixed $value): ?DateTimeImmutable
    {
        if ($value instanceof Timestamp) {
            return DateTimeImmutable::createFromInterface($value->get());
        }

        return $value instanceof DateTimeImmutable ? $value : null;
    }
}
