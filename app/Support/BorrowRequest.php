<?php

namespace App\Support;

use DateTimeImmutable;
use DateTimeInterface;
use Google\Cloud\Core\Timestamp;
use Google\Cloud\Firestore\DocumentSnapshot;
use Throwable;

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
     * A student may withdraw a request only while nobody has acted on it.
     */
    public function isCancellable(): bool
    {
        return $this->status === 'pending';
    }

    /**
     * A loan can be handed back once, after it was approved.
     */
    public function isReturnable(): bool
    {
        return $this->status === 'approved';
    }

    public function period(): string
    {
        if (! $this->borrowFrom || ! $this->borrowTo) {
            return 'Dates not recorded';
        }

        return $this->borrowFrom->format('M j').' to '.$this->borrowTo->format('M j, Y');
    }

    /**
     * Free-text match used by the admin search box. Firestore has no substring
     * search, so the queue is filtered here after it has been read.
     */
    public function matches(string $term): bool
    {
        if ($term === '') {
            return true;
        }

        $haystack = mb_strtolower($this->id.' '.$this->userEmail.' '.$this->equipmentName);

        return str_contains($haystack, mb_strtolower($term));
    }

    /**
     * Row for resources/views/customer/requests.blade.php.
     *
     * @return array<string, mixed>
     */
    public function forStudentList(): array
    {
        return [
            'id' => $this->id,
            'equipmentName' => $this->equipmentName,
            'createdAt' => $this->createdAt?->format('M j, Y') ?? 'N/A',
            'status' => $this->status,
        ];
    }

    /**
     * Row for resources/views/admin/requests.blade.php.
     *
     * @return array<string, mixed>
     */
    public function forAdminQueue(): array
    {
        return [
            'id' => $this->id,
            'created_at' => $this->createdAt?->format('M j, Y H:i') ?? 'N/A',
            'borrower_name' => $this->userEmail !== '' ? $this->userEmail : 'Unknown borrower',
            'equipment_name' => $this->equipmentName,
            'duration' => $this->period(),
            'status' => $this->status,
        ];
    }

    /**
     * Firestore returns a Timestamp for documents this application wrote, but
     * dates entered through the earlier closure-based screens were stored as
     * plain strings, so both have to be understood.
     */
    private static function toDate(mixed $value): ?DateTimeImmutable
    {
        if ($value instanceof Timestamp) {
            return DateTimeImmutable::createFromInterface($value->get());
        }

        if ($value instanceof DateTimeInterface) {
            return DateTimeImmutable::createFromInterface($value);
        }

        if (is_string($value) && $value !== '') {
            try {
                return new DateTimeImmutable($value);
            } catch (Throwable) {
                return null;
            }
        }

        return null;
    }
}
