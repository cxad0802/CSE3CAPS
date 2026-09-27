<?php

use App\Http\Controllers\Auth\FirebaseSessionController;
use App\Http\Controllers\FirebaseHealthController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// Development aid: reports whether the Firebase Admin SDK and Firestore are
// reachable. Never exposed outside the local environment.
if (app()->environment('local')) {
    Route::get('/_health/firebase', FirebaseHealthController::class)->name('health.firebase');
}

// Sign-in itself happens in the browser; this application only receives the
// resulting ID token. See FirebaseSessionController.
Route::get('/login', function () {
    return view('auth.login');
})->name('login');
Route::post('/session', [FirebaseSessionController::class, 'store'])->name('session.store');
Route::post('/logout', [FirebaseSessionController::class, 'destroy'])->name('logout');

// Browsing the catalogue does not require an account.
Route::get('/catalog', function () {
    return view('customer.catalog');
})->name('catalog');

Route::middleware('firebase.auth')->group(function () {
    // Borrow Request Form Route
    Route::get('/borrow/request', function () {
        return view('borrow.create');
    })->name('borrow.create');

    // Customer My Requests Route
    Route::get('/my-requests', function () {
        return view('customer.requests');
    })->name('requests.mine');
});

Route::middleware(['firebase.auth', 'firebase.admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        // Admin Dashboard Route
        Route::get('/dashboard', function () {
            return view('admin.dashboard');
        })->name('dashboard');

        // Admin Loan Requests Route
        Route::get('/requests', function () {
            return view('admin.requests');
        })->name('requests');
    });
