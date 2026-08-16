<?php

namespace App\Services;

use Google\Cloud\Firestore\CollectionReference;
use Google\Cloud\Firestore\DocumentSnapshot;
use Google\Cloud\Firestore\FieldValue;
use Google\Cloud\Firestore\FieldValue\ServerTimestampValue;
use Google\Cloud\Firestore\FirestoreClient;

/**
 * Access layer for the Firestore collections used by the borrowing system.
 *
 * Controllers depend on this class rather than on FirestoreClient directly, so
 * collection names and the shared timestamp handling live in one place.
 * The document shapes are documented in docs/firestore-schema.md.
 */
class FirestoreService
{
    /**
     * Document id used for the placeholder documents that describe a collection's shape.
     */
    public const SCHEMA_DOCUMENT_ID = '_schema';

    public function __construct(protected FirestoreClient $client) {}

    public function client(): FirestoreClient
    {
        return $this->client;
    }

    public function users(): CollectionReference
    {
        return $this->collection('users');
    }

    public function equipments(): CollectionReference
    {
        return $this->collection('equipments');
    }

    public function borrowRequests(): CollectionReference
    {
        return $this->collection('borrow_requests');
    }

    /**
     * Resolve a configured collection by its key in config/firebase.php.
     */
    public function collection(string $key): CollectionReference
    {
        $name = config("firebase.firestore.collections.{$key}");

        if (! is_string($name) || $name === '') {
            throw new \InvalidArgumentException("Unknown Firestore collection [{$key}].");
        }

        return $this->client->collection($name);
    }

    /**
     * Fetch a user document by Firebase uid, or null when the user has never signed in.
     */
    public function findUser(string $uid): ?DocumentSnapshot
    {
        $snapshot = $this->users()->document($uid)->snapshot();

        return $snapshot->exists() ? $snapshot : null;
    }

    /**
     * Create the user document on first sign-in, or refresh the profile fields on later ones.
     *
     * The role is deliberately only written on creation so that an administrator
     * promoted in the console is not demoted the next time they log in.
     *
     * @param  array<string, mixed>  $attributes
     */
    public function upsertUser(string $uid, array $attributes): DocumentSnapshot
    {
        $document = $this->users()->document($uid);
        $exists = $document->snapshot()->exists();

        $payload = array_merge($attributes, [
            'uid' => $uid,
            'updatedAt' => FieldValue::serverTimestamp(),
        ]);

        if (! $exists) {
            $payload['role'] ??= 'user';
            $payload['disabled'] ??= false;
            $payload['createdAt'] = FieldValue::serverTimestamp();
        } else {
            unset($payload['role'], $payload['disabled']);
        }

        $document->set($payload, ['merge' => true]);

        return $document->snapshot();
    }

    /**
     * Read the role of a user, defaulting to the least privileged role.
     */
    public function roleFor(string $uid): string
    {
        $user = $this->findUser($uid);

        $role = $user?->get('role');

        return in_array($role, ['admin', 'user'], true) ? $role : 'user';
    }

    /**
     * Server-side timestamp sentinel, so callers do not need to import FieldValue.
     */
    public function now(): ServerTimestampValue
    {
        return FieldValue::serverTimestamp();
    }
}
