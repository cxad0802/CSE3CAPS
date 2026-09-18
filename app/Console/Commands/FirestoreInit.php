<?php

namespace App\Console\Commands;

use App\Services\FirestoreService;
use Google\Cloud\Core\Timestamp;
use Google\Cloud\Firestore\CollectionReference;
use Illuminate\Console\Command;
use Throwable;

/**
 * Creates the Firestore collections used by the borrowing system.
 *
 * Firestore has no schema and no "create collection" operation: a collection
 * exists as soon as it holds a document. This command writes one placeholder
 * document per collection that also serves as living documentation of the
 * expected fields, and optionally a small set of demo equipment for the UI.
 *
 * Safe to run repeatedly.
 */
class FirestoreInit extends Command
{
    protected $signature = 'firestore:init
                            {--demo : Also create sample equipment documents for the catalog}
                            {--fresh : Delete the placeholder schema documents before writing them again}';

    protected $description = 'Create the users, equipments and borrowRequests collections in Firestore';

    public function handle(): int
    {
        $this->components->info('Connecting to Firestore project ['.(config('firebase.project_id') ?: 'from service account').']');

        try {
            // Resolved here rather than injected so that a missing service
            // account key is reported as a readable error instead of a stack trace.
            $firestore = app(FirestoreService::class);

            $collections = [
                'users' => $firestore->users(),
                'equipments' => $firestore->equipments(),
                'borrowRequests' => $firestore->borrowRequests(),
            ];

            foreach ($collections as $label => $collection) {
                $this->writeSchemaDocument($label, $collection, $firestore);
            }

            if ($this->option('demo')) {
                $this->seedDemoEquipment($firestore);
            }

            $this->verify($collections);
        } catch (Throwable $e) {
            $this->components->error($e->getMessage());
            $this->line('  See docs/firebase-setup.md for the required configuration.');

            return self::FAILURE;
        }

        $this->newLine();
        $this->components->info('Firestore is ready. Collection shapes are documented in docs/firestore-schema.md.');

        return self::SUCCESS;
    }

    protected function writeSchemaDocument(string $label, CollectionReference $collection, FirestoreService $firestore): void
    {
        $document = $collection->document(FirestoreService::SCHEMA_DOCUMENT_ID);

        if ($this->option('fresh') && $document->snapshot()->exists()) {
            $document->delete();
        }

        $document->set([
            'isSchema' => true,
            'description' => "Placeholder document describing the shape of the {$collection->id()} collection. Application queries must skip documents where isSchema is true.",
            'fields' => $this->schemaFor($label),
            'updatedAt' => $firestore->now(),
        ], ['merge' => true]);

        $this->components->twoColumnDetail(
            "collection <fg=cyan>{$collection->id()}</>",
            '<fg=green>ready</>'
        );
    }

    /**
     * Field name to type description, mirroring docs/firestore-schema.md.
     *
     * @return array<string, string>
     */
    protected function schemaFor(string $label): array
    {
        return match ($label) {
            'users' => [
                'uid' => 'string, required, equals the document id (Firebase Auth uid)',
                'email' => 'string, required',
                'displayName' => 'string, required',
                'role' => 'string, required, one of: admin, user',
                'studentId' => 'string, optional',
                'photoUrl' => 'string, optional',
                'disabled' => 'boolean, required',
                'createdAt' => 'timestamp, required',
                'updatedAt' => 'timestamp, required',
            ],
            'equipments' => [
                'name' => 'string, required',
                'category' => 'string, required, one of: camera, laptop, audio, cable, other',
                'description' => 'string, optional',
                'imageUrl' => 'string, optional',
                'location' => 'string, optional',
                'totalQuantity' => 'integer, required, >= 0',
                'availableQuantity' => 'integer, required, 0 <= value <= totalQuantity',
                'status' => 'string, required, one of: available, unavailable',
                'createdAt' => 'timestamp, required',
                'updatedAt' => 'timestamp, required',
            ],
            'borrowRequests' => [
                'userId' => 'string, required, users document id',
                'userEmail' => 'string, required, denormalised for admin lists',
                'equipmentId' => 'string, required, equipments document id',
                'equipmentName' => 'string, required, denormalised for admin lists',
                'quantity' => 'integer, required, >= 1',
                'borrowFrom' => 'timestamp, required',
                'borrowTo' => 'timestamp, required, after borrowFrom',
                'purpose' => 'string, required',
                'status' => 'string, required, one of: pending, approved, rejected, returned, cancelled',
                'reviewedBy' => 'string, optional, admin uid',
                'reviewedAt' => 'timestamp, optional',
                'reviewNote' => 'string, optional',
                'returnedAt' => 'timestamp, optional',
                'createdAt' => 'timestamp, required',
                'updatedAt' => 'timestamp, required',
            ],
            default => [],
        };
    }

    protected function seedDemoEquipment(FirestoreService $firestore): void
    {
        $items = [
            [
                'name' => 'Canon EOS R50 Camera Kit',
                'category' => 'camera',
                'description' => '24MP mirrorless body with 18-45mm lens, battery and charger.',
                'location' => 'Beth Gleeson, Room 210',
                'totalQuantity' => 4,
            ],
            [
                'name' => 'Dell Latitude 5450 Laptop',
                'category' => 'laptop',
                'description' => 'Core i7 laptop with charger, preloaded with the lab software image.',
                'location' => 'Library Loans Desk',
                'totalQuantity' => 10,
            ],
            [
                'name' => 'Rode Wireless GO II Microphone',
                'category' => 'audio',
                'description' => 'Dual channel wireless microphone set with windshields.',
                'location' => 'Beth Gleeson, Room 210',
                'totalQuantity' => 6,
            ],
            [
                'name' => 'HDMI to USB-C Cable (2m)',
                'category' => 'cable',
                'description' => 'For connecting laptops to projectors in the workshop rooms.',
                'location' => 'Media Cupboard',
                'totalQuantity' => 15,
            ],
        ];

        $this->newLine();
        $this->components->info('Seeding demo equipment');

        foreach ($items as $item) {
            // Keyed by name so re-running the command updates instead of duplicating.
            $existing = $firestore->equipments()
                ->where('name', '=', $item['name'])
                ->limit(1)
                ->documents();

            $payload = array_merge($item, [
                'imageUrl' => null,
                'availableQuantity' => $item['totalQuantity'],
                'status' => 'available',
                'updatedAt' => $firestore->now(),
            ]);

            if ($existing->isEmpty()) {
                $payload['createdAt'] = $firestore->now();
                $firestore->equipments()->add($payload);
                $state = '<fg=green>created</>';
            } else {
                unset($payload['availableQuantity']);
                $existing->rows()[0]->reference()->set($payload, ['merge' => true]);
                $state = '<fg=yellow>updated</>';
            }

            $this->components->twoColumnDetail("  {$item['name']}", $state);
        }
    }

    /**
     * Read the placeholder documents back, which proves credentials, gRPC and network all work.
     *
     * @param  array<string, CollectionReference>  $collections
     */
    protected function verify(array $collections): void
    {
        $this->newLine();
        $this->components->info('Verifying read access');

        foreach ($collections as $collection) {
            $snapshot = $collection->document(FirestoreService::SCHEMA_DOCUMENT_ID)->snapshot();

            if (! $snapshot->exists()) {
                throw new \RuntimeException("Wrote the schema document to [{$collection->id()}] but could not read it back.");
            }

            $updatedAt = $snapshot->get('updatedAt');
            $when = $updatedAt instanceof Timestamp
                ? $updatedAt->get()->format('Y-m-d H:i:s')
                : 'unknown';
            $count = iterator_count($collection->limit(50)->documents());

            $this->components->twoColumnDetail(
                "  {$collection->id()}",
                "{$count} document(s), last write {$when}"
            );
        }
    }
}
