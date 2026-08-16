<?php

namespace App\Providers;

use Google\Cloud\Firestore\FirestoreClient;
use Illuminate\Contracts\Cache\Factory as CacheFactory;
use Illuminate\Support\ServiceProvider;
use Kreait\Firebase\Contract\Auth as FirebaseAuth;
use Kreait\Firebase\Factory;
use RuntimeException;

class FirebaseServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(Factory::class, function ($app): Factory {
            $config = $app['config']->get('firebase');

            $factory = (new Factory)
                ->withServiceAccount($this->credentialsPath($config['credentials']))
                ->withFirestoreDatabase($config['firestore']['database']);

            if (! empty($config['project_id'])) {
                $factory = $factory->withProjectId($config['project_id']);
            }

            // Google's public signing keys rotate; caching them keeps token
            // verification from making an outbound request per HTTP request.
            $cache = $app->make(CacheFactory::class)->store($config['auth']['cache_store']);

            return $factory
                ->withVerifierCache($cache)
                ->withAuthTokenCache($cache);
        });

        $this->app->singleton(FirebaseAuth::class, fn ($app) => $app->make(Factory::class)->createAuth());

        $this->app->singleton(
            FirestoreClient::class,
            fn ($app) => $app->make(Factory::class)->createFirestore()->database()
        );
    }

    /**
     * Resolve the service account path, allowing it to be relative to the project root.
     */
    protected function credentialsPath(string $path): string
    {
        $resolved = str_starts_with($path, '/') || preg_match('/^[A-Za-z]:[\\\\\/]/', $path) === 1
            ? $path
            : base_path($path);

        if (! is_file($resolved)) {
            throw new RuntimeException(
                "Firebase service account key not found at [{$resolved}]. ".
                'Download it from the Firebase console and set FIREBASE_CREDENTIALS in .env. '.
                'See docs/firebase-setup.md.'
            );
        }

        return $resolved;
    }
}
