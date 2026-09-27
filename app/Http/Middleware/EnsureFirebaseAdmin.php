<?php

namespace App\Http\Middleware;

use App\Http\Controllers\Auth\FirebaseSessionController;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Restricts a route to accounts whose role claim is "admin".
 *
 * Always stack this behind firebase.auth, which guarantees the session exists.
 * The role is promoted with: php artisan firebase:make-admin <email>
 */
class EnsureFirebaseAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->session()->get(FirebaseSessionController::SESSION_KEY.'.role') !== 'admin') {
            abort(403, 'This area is restricted to administrators.');
        }

        return $next($request);
    }
}
