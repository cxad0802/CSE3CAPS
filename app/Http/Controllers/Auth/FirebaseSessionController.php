<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\FirestoreService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Kreait\Firebase\Contract\Auth as FirebaseAuth;
use Kreait\Firebase\Exception\Auth\FailedToVerifyToken;
use Kreait\Firebase\Exception\Auth\RevokedIdToken;
use Throwable;

/**
 * Exchanges a Firebase ID token for a Laravel session.
 *
 * The browser signs in with the Firebase JavaScript SDK and posts the resulting
 * ID token here; the password never reaches this application. The token is
 * verified with the Admin SDK and reduced to the few values the server-rendered
 * pages need. The token itself is not stored: it expires within the hour, while
 * the session is what guards the routes. See docs/firebase-setup.md.
 */
class FirebaseSessionController extends Controller
{
    /**
     * Session key holding the signed-in identity. Read by the firebase.auth and
     * firebase.admin middleware and by the layouts.
     */
    public const SESSION_KEY = 'firebase';

    public function store(Request $request, FirebaseAuth $auth, FirestoreService $firestore): RedirectResponse
    {
        $validated = $request->validate([
            'id_token' => ['required', 'string'],
        ]);

        try {
            $verified = $auth->verifyIdToken(
                $validated['id_token'],
                // Revocation is deliberately not checked here. The SDK subtracts
                // the leeway from the token's auth_time before comparing it with
                // the account's tokensValidAfterTime, so a token minted within
                // the leeway of the account being created looks older than the
                // account itself and is reported as revoked. Firebase already
                // refuses to issue a token to a disabled account, and the token
                // is exchanged for a session here and then discarded.
                checkIfRevoked: false,
                // Tolerates clock drift between this machine and Google.
                leewayInSeconds: config('firebase.auth.leeway'),
            );
        } catch (FailedToVerifyToken|RevokedIdToken $e) {
            return back()->withErrors([
                'email' => 'Your sign-in could not be verified. Please try again.',
            ]);
        }

        $claims = $verified->claims();
        $uid = (string) $claims->get('sub');
        $email = (string) $claims->get('email', '');

        // firebase:make-admin writes the role as a custom claim, so it arrives
        // inside the verified token. Firestore holds the same value, but reading
        // it from the token keeps sign-in working independently of Firestore.
        $role = $claims->get('role') === 'admin' ? 'admin' : 'user';
        $displayName = (string) ($claims->get('name') ?: strstr($email, '@', true) ?: $email);

        try {
            $firestore->upsertUser($uid, [
                'email' => $email,
                'displayName' => $displayName,
                'photoUrl' => $claims->get('picture'),
            ]);
        } catch (Throwable $e) {
            // The profile document is a convenience for the admin screens. A
            // failed write must not lock out a user whose token is valid.
            Log::warning('Could not write the Firestore user document on sign-in.', [
                'uid' => $uid,
                'reason' => $e->getMessage(),
            ]);
        }

        $request->session()->regenerate();
        $request->session()->put(self::SESSION_KEY, [
            'uid' => $uid,
            'email' => $email,
            'displayName' => $displayName,
            'role' => $role,
        ]);

        return redirect()->intended($role === 'admin'
            ? route('admin.dashboard')
            : route('catalog'));
    }

    public function destroy(Request $request): RedirectResponse
    {
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
