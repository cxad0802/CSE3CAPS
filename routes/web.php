<?php

use App\Http\Controllers\FirebaseHealthController;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Kreait\Firebase\Contract\Firestore; 

Route::get('/', function () {
    return view('welcome');
});

// Development aid: reports whether the Firebase Admin SDK and Firestore are reachable.
if (app()->environment('local')) {
    Route::get('/_health/firebase', FirebaseHealthController::class)->name('health.firebase');
}

// Existing UI Routes
Route::get('/login', function () { return view('auth.login'); });
Route::get('/catalog', function () { return view('customer.catalog'); });
Route::get('/admin/dashboard', function () { return view('admin.dashboard'); });
Route::get('/borrow/request', function () { return view('borrow.create'); });
Route::get('/admin/requests', function () { return view('admin.requests'); });
Route::get('/my-requests', function () { return view('customer.requests'); });
Route::get('/admin/inventory/create', function () { return view('admin.equipment_create'); });
Route::get('/admin/inventory', function () { return view('admin.inventory'); });

// The POST route to handle real data submission to Firebase Firestore
Route::post('/admin/inventory/store', function (Request $request) {
    // Retrieve all form data, excluding Laravel's security token
    $data = $request->except(['_token']);
    
    // Append a timestamp to track when the equipment was added
    $data['created_at'] = now()->toDateTimeString();

    try {
        // Connect to Firestore and target the 'Equipments' collection
        $firestore = app('firebase.firestore')->database();
        $equipmentsCollection = $firestore->collection('Equipments');

        // Save the data, using the Asset ID as the Document ID for easy tracking
        $equipmentsCollection->document($data['asset_id'])->set($data);

        // If successful, redirect back to the inventory page with a success message
        return redirect('/admin/inventory')->with('success', 'Equipment added successfully to Firestore!');
        
    } catch (\Exception $e) {
        // If the .env is not configured properly, it will catch and display the error here
        dd('Firebase Connection Error: ' . $e->getMessage());
    }
});