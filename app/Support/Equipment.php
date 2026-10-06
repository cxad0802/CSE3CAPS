<?php

namespace App\Support;

use DateTimeImmutable;
use DateTimeInterface;
use Google\Cloud\Core\Timestamp;
use Google\Cloud\Firestore\DocumentSnapshot;
use Throwable;

/**
 * One equipments document, shaped for the Blade views.
 *
 * Firestore is schemaless, so every field is read defensively: a document
 * written by hand in the console must not be able to break a listing page.
 * The authoritative field list is in docs/firestore-schema.md.
 */
readonly class Equipment
{
    /**
     * Categories the inventory forms offer.
     */
    public const CATEGORIES = ['laptops', 'cameras', 'audio', 'projectors', 'accessories', 'other'];

    /**
     * Lifecycle states an asset can be in. Only "available" can be borrowed;
     * the others describe why an asset is being withheld.
     */
    public const STATUSES = ['available', 'in-use', 'maintenance', 'retired'];

    /**
     * Category names used by the demo seed and the original schema document,
     * mapped onto the names the forms use.
     *
     * Normalising on read means the edit form pre-selects the right option for
     * an older document instead of silently rewriting its category on save.
     *
     * @var array<string, string>
     */
    private const CATEGORY_ALIASES = [
        'laptop' => 'laptops',
        'camera' => 'cameras',
        'projector' => 'projectors',
        'cable' => 'accessories',
        'cables' => 'accessories',
        'accessory' => 'accessories',
    ];

    public function __construct(
        public string $id,
        public string $name,
        public string $category,
        public ?string $description,
        public ?string $location,
        public int $totalQuantity,
        public int $availableQuantity,
        public string $status,
        public ?DateTimeImmutable $warrantyExpiresAt,
    ) {}

    public static function fromSnapshot(DocumentSnapshot $snapshot): self
    {
        $data = $snapshot->data() ?? [];

        return new self(
            id: $snapshot->id(),
            name: (string) ($data['name'] ?? 'Unnamed item'),
            category: self::normaliseCategory($data['category'] ?? null),
            description: $data['description'] ?? null,
            location: $data['location'] ?? null,
            totalQuantity: (int) ($data['totalQuantity'] ?? 0),
            availableQuantity: (int) ($data['availableQuantity'] ?? 0),
            status: (string) ($data['status'] ?? 'retired'),
            // Older inventory screens stored this as warranty_expiration.
            warrantyExpiresAt: self::toDate($data['warrantyExpiresAt'] ?? $data['warranty_expiration'] ?? null),
        );
    }

    /**
     * Whether a student may raise a request for this item right now. An item can
     * be in stock but withheld, for example while it is being repaired.
     */
    public function isBorrowable(): bool
    {
        return $this->status === 'available' && $this->availableQuantity > 0;
    }

    public function isInMaintenance(): bool
    {
        return $this->status === 'maintenance';
    }

    public function matches(string $term): bool
    {
        if ($term === '') {
            return true;
        }

        // Firestore has no substring search, so the catalogue is filtered here.
        $haystack = mb_strtolower($this->id.' '.$this->name.' '.$this->category.' '.$this->description);

        return str_contains($haystack, mb_strtolower($term));
    }

    /**
     * Row for the student catalogue and the admin inventory table, both of
     * which read plain array keys.
     *
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'category' => $this->category,
            'description' => $this->description,
            'status' => $this->status,
            'location' => $this->location,
            'totalQuantity' => $this->totalQuantity,
            'availableQuantity' => $this->availableQuantity,
            'warrantyExpiresAt' => $this->warrantyExpiresAt?->format('Y-m-d'),
        ];
    }

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

    private static function normaliseCategory(mixed $value): string
    {
        $category = mb_strtolower(trim((string) $value));
        $category = self::CATEGORY_ALIASES[$category] ?? $category;

        return in_array($category, self::CATEGORIES, true) ? $category : 'other';
    }
}
