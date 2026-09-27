<?php

use App\Http\Controllers\Admin\BorrowRequestController as AdminBorrowRequestController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Auth\FirebaseSessionController;
use App\Http\Controllers\BorrowRequestController;
use App\Http\Controllers\EquipmentController;
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
Route::get('/catalog', [EquipmentController::class, 'index'])->name('catalog');

Route::middleware('firebase.auth')->group(function () {
    Route::get('/borrow/request', [BorrowRequestController::class, 'create'])->name('borrow.create');
    Route::post('/borrow/request', [BorrowRequestController::class, 'store'])->name('borrow.store');

    Route::get('/my-requests', [BorrowRequestController::class, 'index'])->name('requests.mine');
    Route::post('/my-requests/{id}/cancel', [BorrowRequestController::class, 'cancel'])->name('requests.cancel');
});

Route::middleware(['firebase.auth', 'firebase.admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

        Route::get('/requests', [AdminBorrowRequestController::class, 'index'])->name('requests');
        Route::post('/requests/{id}/approve', [AdminBorrowRequestController::class, 'approve'])->name('requests.approve');
        Route::post('/requests/{id}/reject', [AdminBorrowRequestController::class, 'reject'])->name('requests.reject');
        Route::post('/requests/{id}/returned', [AdminBorrowRequestController::class, 'markReturned'])->name('requests.returned');
    });
