<?php

use App\Http\Controllers\FirebaseHealthController;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;

Route::get('/', function () {
    return view('welcome');
});

// Development aid: reports whether the Firebase Admin SDK and Firestore are reachable.
if (app()->environment('local')) {
    Route::get('/_health/firebase', FirebaseHealthController::class)->name('health.firebase');
}

// --- YOUR UI ROUTES (Used for UI testing) ---
Route::get('/login', function () { return view('auth.login'); })->name('login');
Route::get('/catalog', function () { return view('customer.catalog'); })->name('catalog');
Route::get('/admin/dashboard', function () { return view('admin.dashboard'); })->name('admin.dashboard');
Route::get('/borrow/request', function () { return view('borrow.create'); });
Route::get('/admin/requests', function () { return view('admin.requests'); });
Route::get('/my-requests', function () { return view('customer.requests'); });
Route::get('/admin/inventory/create', function () { return view('admin.equipment_create'); });
Route::get('/admin/inventory', function () { return view('admin.inventory'); });

// --- FIRESTORE SUBMISSION ROUTE (Used for UI testing) ---
Route::post('/admin/inventory/store', function (Request $request) {
    $data = $request->except(['_token']);
    $data['created_at'] = now()->toDateTimeString();
    
    try {
        $firestore = app('firebase.firestore')->database();
        $equipmentsCollection = $firestore->collection('Equipments');
        $equipmentsCollection->document($data['asset_id'])->set($data);
        
        return redirect('/admin/inventory')->with('success', 'Equipment added successfully to Firestore!');
    } catch (\Exception $e) {
        dd('Firebase Connection Error: ' . $e->getMessage());
    }
})->name('admin.inventory.store');