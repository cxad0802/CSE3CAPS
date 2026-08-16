<?php

namespace App\Http\Controllers;

use App\Services\FirestoreService;
use Illuminate\Http\JsonResponse;
use Kreait\Firebase\Contract\Auth as FirebaseAuth;
use Throwable;

/**
 * Diagnostic endpoint used during development to confirm that the Firebase
 * Admin SDK and Firestore are configured correctly. Registered only in the
 * local environment (see routes/web.php).
 */
class FirebaseHealthController extends Controller
{
    public function __invoke(): JsonResponse
    {
        $checks = [
            'grpc_extension' => $this->check(function () {
                if (! extension_loaded('grpc')) {
                    throw new \RuntimeException('The grpc extension is not loaded. See docs/php-grpc-windows.md.');
                }

                return 'version '.phpversion('grpc');
            }),
            'credentials_file' => $this->check(function () {
                $path = base_path(config('firebase.credentials'));

                if (! is_file($path)) {
                    throw new \RuntimeException("Service account key not found at [{$path}].");
                }

                return 'found';
            }),
            'firebase_auth' => $this->check(function () {
                $auth = app(FirebaseAuth::class);
                // Listing zero users is the cheapest call that still proves the
                // credentials are accepted by the Identity Toolkit API.
                iterator_to_array($auth->listUsers(1));

                return 'reachable';
            }),
            'firestore' => $this->check(function () {
                $firestore = app(FirestoreService::class);
                $snapshot = $firestore->equipments()
                    ->document(FirestoreService::SCHEMA_DOCUMENT_ID)
                    ->snapshot();

                return $snapshot->exists()
                    ? 'reachable, collections initialised'
                    : 'reachable, but collections are missing - run: php artisan firestore:init';
            }),
        ];

        $healthy = collect($checks)->every(fn (array $check) => $check['ok']);

        return response()->json([
            'healthy' => $healthy,
            'project_id' => config('firebase.project_id'),
            'checks' => $checks,
        ], $healthy ? 200 : 503, [], JSON_PRETTY_PRINT);
    }

    /**
     * @return array{ok: bool, detail: string}
     */
    protected function check(callable $probe): array
    {
        try {
            return ['ok' => true, 'detail' => (string) $probe()];
        } catch (Throwable $e) {
            return ['ok' => false, 'detail' => $e->getMessage()];
        }
    }
}
