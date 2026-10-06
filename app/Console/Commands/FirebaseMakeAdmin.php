<?php

namespace App\Console\Commands;

use App\Services\FirestoreService;
use Illuminate\Console\Command;
use Kreait\Firebase\Auth\UserRecord;
use Kreait\Firebase\Contract\Auth as FirebaseAuth;
use Kreait\Firebase\Exception\Auth\UserNotFound;
use Throwable;

/**
 * Creates (or promotes) an administrator account before the sign-up UI exists.
 *
 * The account lives in two places and both are written here:
 *  - Firebase Authentication, which owns the email and password, plus a
 *    "role" custom claim so the role travels inside the ID token.
 *  - The users/{uid} Firestore document, which the backend reads for the role.
 *
 * Safe to run repeatedly: an existing account is promoted, not recreated.
 */
class FirebaseMakeAdmin extends Command
{
    protected $signature = 'firebase:make-admin
                            {email : Email address of the administrator}
                            {--password= : Password for a new account (prompted when omitted)}
                            {--name= : Display name, defaults to the part before @}
                            {--demote : Set the role back to "user" instead of "admin"}';

    protected $description = 'Create or promote a Firebase account with the admin role';

    public function handle(FirebaseAuth $auth, FirestoreService $firestore): int
    {
        $email = trim((string) $this->argument('email'));
        $role = $this->option('demote') ? 'user' : 'admin';

        if (filter_var($email, FILTER_VALIDATE_EMAIL) === false) {
            $this->components->error("[{$email}] is not a valid email address.");

            return self::FAILURE;
        }

        try {
            $user = $this->findOrCreateUser($auth, $email);

            // The claim lets the frontend and any future middleware read the role
            // straight from the verified ID token, with no extra Firestore read.
            $auth->setCustomUserClaims($user->uid, ['role' => $role]);

            $firestore->users()->document($user->uid)->set([
                'uid' => $user->uid,
                'email' => $email,
                'displayName' => $user->displayName ?: $this->displayName($email),
                'role' => $role,
                'disabled' => false,
                'createdAt' => $firestore->now(),
                'updatedAt' => $firestore->now(),
            ], ['merge' => true]);
        } catch (Throwable $e) {
            $this->components->error($e->getMessage());
            $this->line('  See docs/firebase-setup.md for the required configuration.');

            return self::FAILURE;
        }

        $this->newLine();
        $this->components->twoColumnDetail('uid', "<fg=cyan>{$user->uid}</>");
        $this->components->twoColumnDetail('email', $email);
        $this->components->twoColumnDetail('role', "<fg=green>{$role}</>");
        $this->newLine();
        $this->components->info('Done. The user must sign out and back in for the new claim to appear in their ID token.');

        return self::SUCCESS;
    }

    protected function findOrCreateUser(FirebaseAuth $auth, string $email): UserRecord
    {
        try {
            $user = $auth->getUserByEmail($email);
            $this->components->twoColumnDetail("account <fg=cyan>{$email}</>", '<fg=yellow>already exists</>');

            return $user;
        } catch (UserNotFound) {
            // Falls through to creation below.
        }

        $password = $this->option('password') ?: $this->secret('Password for the new account (min 6 characters)');

        if (strlen((string) $password) < 6) {
            throw new \RuntimeException('Firebase requires a password of at least 6 characters.');
        }

        $user = $auth->createUser([
            'email' => $email,
            'password' => $password,
            'displayName' => $this->option('name') ?: $this->displayName($email),
            // Verified on creation because this account is provisioned by an
            // operator, not by someone signing up through the browser.
            'emailVerified' => true,
        ]);

        $this->components->twoColumnDetail("account <fg=cyan>{$email}</>", '<fg=green>created</>');

        return $user;
    }

    protected function displayName(string $email): string
    {
        return ucfirst(strstr($email, '@', true) ?: $email);
    }
}
