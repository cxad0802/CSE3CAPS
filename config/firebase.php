<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Service account credentials
    |--------------------------------------------------------------------------
    |
    | Path to the service account JSON key downloaded from the Firebase console
    | (Project settings -> Service accounts). The path may be absolute or
    | relative to the project root. See docs/firebase-setup.md.
    |
    */

    'credentials' => env('FIREBASE_CREDENTIALS', 'storage/app/firebase/service-account.json'),

    'project_id' => env('FIREBASE_PROJECT_ID'),

    /*
    |--------------------------------------------------------------------------
    | Firestore
    |--------------------------------------------------------------------------
    |
    | "database" targets a named Firestore database; "(default)" is the one
    | created with the project. Collection names are configurable so tests can
    | run against a prefixed copy without touching real data.
    |
    */

    'firestore' => [
        'database' => env('FIREBASE_FIRESTORE_DATABASE', '(default)'),

        'collections' => [
            'users' => env('FIRESTORE_COLLECTION_USERS', 'users'),
            'equipments' => env('FIRESTORE_COLLECTION_EQUIPMENTS', 'equipments'),
            'borrow_requests' => env('FIRESTORE_COLLECTION_BORROW_REQUESTS', 'borrowRequests'),
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | ID token verification
    |--------------------------------------------------------------------------
    |
    | "leeway" is the tolerance in seconds for clock skew between this machine
    | and Google's servers. Keep it small; it exists because local clocks drift.
    | Verification results are cached in the configured Laravel cache store so
    | Google's public keys are not fetched on every request.
    |
    */

    'auth' => [
        'leeway' => (int) env('FIREBASE_AUTH_LEEWAY', 60),
        'cache_store' => env('FIREBASE_CACHE_STORE', 'file'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Web app configuration (public)
    |--------------------------------------------------------------------------
    |
    | These values are sent to the browser for the Firebase JavaScript SDK. They
    | are public by design; access is restricted by Firebase security rules, not
    | by keeping the API key secret.
    |
    */

    'web' => [
        'apiKey' => env('FIREBASE_API_KEY'),
        'authDomain' => env('FIREBASE_AUTH_DOMAIN'),
        'projectId' => env('FIREBASE_PROJECT_ID'),
        'storageBucket' => env('FIREBASE_STORAGE_BUCKET'),
        'messagingSenderId' => env('FIREBASE_MESSAGING_SENDER_ID'),
        'appId' => env('FIREBASE_APP_ID'),
    ],

];
