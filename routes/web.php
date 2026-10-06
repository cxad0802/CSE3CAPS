<?php

use App\Http\Controllers\Admin\BorrowRequestController as AdminBorrowRequestController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\EquipmentController as AdminEquipmentController;
use App\Http\Controllers\Auth\FirebaseSessionController;
use App\Http\Controllers\BorrowRequestController;
use App\Http\Controllers\EquipmentController;
use App\Http\Controllers\FirebaseHealthController;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/catalog');

// Note: Development aid - reports whether the Firebase Admin SDK and Firestore are reachable.
if (app()->environment('local')) {
    Route::get('/_health/firebase', FirebaseHealthController::class)->name('health.firebase');
}

/*
|--------------------------------------------------------------------------
| Guest
|--------------------------------------------------------------------------
|
| The browser signs in against Firebase with the JavaScript SDK and posts the
| resulting ID token to POST /login, which exchanges it for a Laravel session.
| The password never reaches this application.
|
*/

Route::get('/login', fn () => view('auth.login'))->name('login');
Route::post('/login', [FirebaseSessionController::class, 'store'])->name('session.store');
Route::post('/logout', [FirebaseSessionController::class, 'destroy'])->name('logout');

// Browsing the catalogue needs no account; raising a request does.
Route::get('/catalog', [EquipmentController::class, 'index'])->name('catalog');

/*
|--------------------------------------------------------------------------
| Signed-in students
|--------------------------------------------------------------------------
*/

Route::middleware('firebase.auth')->group(function () {
    Route::get('/borrow/request', [BorrowRequestController::class, 'create'])->name('borrow.create');
    Route::post('/borrow/request', [BorrowRequestController::class, 'store'])->name('borrow.store');

    Route::get('/my-requests', [BorrowRequestController::class, 'index'])->name('requests.mine');
    Route::post('/my-requests/{id}/cancel', [BorrowRequestController::class, 'cancel'])->name('requests.cancel');
    Route::post('/my-requests/{id}/return', [BorrowRequestController::class, 'returnItem'])->name('requests.return');
});

/*
|--------------------------------------------------------------------------
| Administrators
|--------------------------------------------------------------------------
|
| firebase.admin checks the role claim that firebase:make-admin writes, so a
| student who guesses an /admin URL is refused rather than shown the screen.
|
*/

Route::middleware(['firebase.auth', 'firebase.admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::get('/dashboard', DashboardController::class)->name('dashboard');

        Route::get('/requests', [AdminBorrowRequestController::class, 'index'])->name('requests');
        Route::post('/requests/{id}/approve', [AdminBorrowRequestController::class, 'approve'])->name('requests.approve');
        Route::post('/requests/{id}/reject', [AdminBorrowRequestController::class, 'reject'])->name('requests.reject');
        Route::post('/requests/{id}/return', [AdminBorrowRequestController::class, 'markReturned'])->name('requests.return');

        Route::get('/inventory', [AdminEquipmentController::class, 'index'])->name('inventory');
        Route::get('/inventory/create', [AdminEquipmentController::class, 'create'])->name('inventory.create');
        Route::post('/inventory/store', [AdminEquipmentController::class, 'store'])->name('inventory.store');
        // The id travels as a query parameter so the link in the table stays a
        // plain href, matching the rest of the inventory screen.
        Route::get('/inventory/edit', [AdminEquipmentController::class, 'edit'])->name('inventory.edit');
        Route::post('/inventory/update/{id}', [AdminEquipmentController::class, 'update'])->name('inventory.update');
        Route::post('/inventory/delete/{id}', [AdminEquipmentController::class, 'destroy'])->name('inventory.destroy');
    });
