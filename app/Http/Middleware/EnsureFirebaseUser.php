<?php

namespace App\Http\Middleware;

use App\Http\Controllers\Auth\FirebaseSessionController;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Requires a verified Firebase identity in the session.
 *
 * Laravel's own auth guard is not used: accounts live in Firebase
 * Authentication, not in the database, so the session written by
 * FirebaseSessionController is the source of truth.
 */
class EnsureFirebaseUser
{
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->session()->has(FirebaseSessionController::SESSION_KEY.'.uid')) {
            return $next($request);
        }

        // Remembered so the user lands on the page they asked for after signing in.
        $request->session()->put('url.intended', $request->fullUrl());

        return redirect()->route('login')->withErrors([
            'email' => 'Please sign in to continue.',
        ]);
    }
}
