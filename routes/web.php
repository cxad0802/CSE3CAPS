<?php

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
Route::get('/login', function () {
    return view('auth.login');
});
Route::get('/catalog', function () {
    return view('customer.catalog');
});
// Admin Dashboard Route
Route::get('/admin/dashboard', function () {
    return view('admin.dashboard');
});
// Borrow Request Form Route
Route::get('/borrow/request', function () {
    return view('borrow.create');
});
// Admin Loan Requests Route
Route::get('/admin/requests', function () {
    return view('admin.requests');
});
// Customer My Requests Route
Route::get('/my-requests', function () {
    return view('customer.requests');
});