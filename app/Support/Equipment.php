<?php

namespace App\Support;

use Google\Cloud\Firestore\DocumentSnapshot;

/**
 * One equipments document, shaped for the Blade views.
 *
 * Firestore is schemaless, so every field is read defensively: a document
 * written by hand in the console must not be able to break a listing page.
 * The authoritative field list is in docs/firestore-schema.md.
 */
readonly class Equipment
{
    public function __construct(
        public string $id,
        public string $name,
        public string $category,
        public ?string $description,
        public ?string $imageUrl,
        public ?string $location,
        public int $totalQuantity,
        public int $availableQuantity,
        public string $status,
    ) {}

    public static function fromSnapshot(DocumentSnapshot $snapshot): self
    {
        $data = $snapshot->data() ?? [];

        return new self(
            id: $snapshot->id(),
            name: (string) ($data['name'] ?? 'Unnamed item'),
            category: (string) ($data['category'] ?? 'other'),
            description: $data['description'] ?? null,
            imageUrl: $data['imageUrl'] ?? null,
            location: $data['location'] ?? null,
            totalQuantity: (int) ($data['totalQuantity'] ?? 0),
            availableQuantity: (int) ($data['availableQuantity'] ?? 0),
            status: (string) ($data['status'] ?? 'unavailable'),
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

    public function categoryLabel(): string
    {
        return match ($this->category) {
            'camera' => 'Cameras',
            'laptop' => 'Laptops',
            'audio' => 'Audio',
            'cable' => 'Cables and adapters',
            default => 'Other',
        };
    }

    public function matches(string $term): bool
    {
        if ($term === '') {
            return true;
        }

        // Firestore has no substring search, so the catalogue is filtered here.
        $haystack = mb_strtolower($this->name.' '.$this->category.' '.$this->description);

        return str_contains($haystack, mb_strtolower($term));
    }
}
