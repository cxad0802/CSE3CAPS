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

// --- YOUR UI ROUTES ---
Route::get('/login', function () { return view('auth.login'); })->name('login');

// --- FIRESTORE LOGIN ROUTE ---
Route::post('/login', function (Request $request) {
    $email = $request->input('email');
    
    try {
        $firestore = app('firebase.firestore')->database();
        
        $usersRef = $firestore->collection('users');
        $query = $usersRef->where('email', '=', $email)->documents();

        $userData = null;
        foreach ($query as $document) {
            if ($document->exists()) {
                $userData = $document->data();
                break;
            }
        }

        if ($userData) {
            $role = strtolower($userData['role'] ?? 'student');
            
            if ($role === 'admin') {
                return redirect('/admin/inventory');
            } else {
                return redirect('/my-requests');
            }
        }

        return back()->withErrors(['email' => 'Account not found in Firestore.']);

    } catch (\Exception $e) {
        dd('Firebase Connection Error: ' . $e->getMessage());
    }
});

// --- LOGOUT ROUTE ---
Route::any('/logout', function () {
    return redirect('/login');
})->name('logout');


Route::get('/catalog', function () { return view('customer.catalog'); })->name('catalog');
Route::get('/admin/dashboard', function () { return view('admin.dashboard'); })->name('admin.dashboard');
Route::get('/admin/inventory/create', function () { return view('admin.equipment_create'); });
Route::get('/admin/inventory', function () { return view('admin.inventory'); });
Route::get('/admin/inventory/edit', function () { return view('admin.inventory-edit'); });

// --- SHOW BORROW REQUEST FORM ---
Route::get('/borrow/request', function () { 
    return view('borrow.create'); 
});

// --- SUBMIT BORROW REQUEST TO FIRESTORE ---
Route::post('/borrow/request', function (Request $request) {
    try {
        $firestore = app('firebase.firestore')->database();
        $newDocRef = $firestore->collection('borrowRequests')->newDocument();
        
        $newDocRef->set([
            'equipmentId' => $request->input('equipmentId', 'IT-LPT-8821'), 
            'equipmentName' => 'MacBook Pro 14" (M2 Pro)', 
            'borrowFrom' => $request->input('borrowFrom'),
            'borrowTo' => $request->input('borrowTo'),
            'purpose' => $request->input('purpose'),
            'details' => $request->input('details', ''),
            'status' => 'pending',
            'createdAt' => now()->toDateTimeString(),
            'userEmail' => 'student@cse3caps.test',
            'userId' => 'student-id-placeholder',
            'quantity' => 1
        ]);
        
        return redirect('/my-requests')->with('success', 'Your request has been submitted and is pending approval.');
        
    } catch (\Exception $e) {
        return back()->withErrors(['error' => 'Firebase Connection Error: ' . $e->getMessage()]);
    }
});

// --- STUDENT REQUESTS ROUTE ---
Route::get('/my-requests', function (Request $request) {
    $currentFilter = $request->query('filter', 'all');

    try {
        $firestore = app('firebase.firestore')->database();
        $requestsRef = $firestore->collection('borrowRequests')->documents();
        
        $myRequests = [];

        foreach ($requestsRef as $doc) {
            if ($doc->id() === '_schema' || !$doc->exists()) {
                continue;
            }

            $data = $doc->data();
            $data['id'] = $doc->id();
            
            $status = strtolower($data['status'] ?? 'pending');
            $data['status'] = $status;
            
            $isApproved = in_array($status, ['approved', 'active']);
            
            if ($currentFilter === 'pending' && $status !== 'pending') {
                continue;
            }
            if ($currentFilter === 'approved' && !$isApproved) {
                continue;
            }

            $myRequests[] = $data;
        }

        return view('customer.requests', compact('myRequests', 'currentFilter'));

    } catch (\Exception $e) {
        dd('Firebase Connection Error: ' . $e->getMessage());
    }
});

// --- CANCEL REQUEST ROUTE (STUDENT) ---
Route::post('/my-requests/{id}/cancel', function ($id) {
    try {
        $firestore = app('firebase.firestore')->database();
        $firestore->collection('borrowRequests')->document($id)->update([
            ['path' => 'status', 'value' => 'cancelled']
        ]);
        return back()->with('success', 'Request cancelled successfully.');
    } catch (\Exception $e) {
        return back()->withErrors(['error' => $e->getMessage()]);
    }
});

// --- RETURN EQUIPMENT ROUTE (STUDENT) ---
Route::post('/my-requests/{id}/return', function ($id) {
    try {
        $firestore = app('firebase.firestore')->database();
        $firestore->collection('borrowRequests')->document($id)->update([
            ['path' => 'status', 'value' => 'returned'],
            ['path' => 'returnedAt', 'value' => now()->toDateTimeString()]
        ]);
        return back()->with('success', 'Equipment returned successfully.');
    } catch (\Exception $e) {
        return back()->withErrors(['error' => $e->getMessage()]);
    }
});

// --- ADMIN REQUESTS ROUTE ---
Route::get('/admin/requests', function (Request $request) {
    $currentFilter = $request->query('filter', 'all');
    $searchQuery = strtolower($request->query('search', ''));
    $page = max((int) $request->query('page', 1), 1);
    $perPage = 10; 

    try {
        $firestore = app('firebase.firestore')->database();
        $requestsRef = $firestore->collection('borrowRequests')->documents();
        
        $allRequests = [];
        $pendingCount = 0;

        foreach ($requestsRef as $doc) {
            if ($doc->id() === '_schema' || !$doc->exists()) {
                continue;
            }

            $data = $doc->data();
            $data['id'] = $doc->id();
            
            $borrower = $data['userEmail'] ?? 'Unknown Borrower';
            $equipment = $data['equipmentName'] ?? 'Unknown Item';
            $status = strtolower($data['status'] ?? 'pending');
            
            $data['borrower_name'] = $borrower;
            $data['equipment_name'] = $equipment;
            $data['status'] = $status;
            
            // Note: Map Firestore dates properly so the view can read them
            $data['created_at'] = $data['createdAt'] ?? 'N/A';
            $data['duration'] = ($data['borrowFrom'] ?? 'N/A') . ' to ' . ($data['borrowTo'] ?? 'N/A');

            if ($status === 'pending') {
                $pendingCount++;
            }
            
            // Note: Map 'active' logic to 'approved' if using active tab
            $statusMatch = ($currentFilter === 'all') || ($status === $currentFilter) || ($currentFilter === 'active' && $status === 'approved');
            
            $searchMatch = true;
            if (!empty($searchQuery)) {
                $searchMatch = str_contains(strtolower($borrower), $searchQuery) || 
                               str_contains(strtolower($equipment), $searchQuery) || 
                               str_contains(strtolower($data['id']), $searchQuery);
            }

            if ($statusMatch && $searchMatch) {
                $allRequests[] = $data;
            }
        }

        $totalResults = count($allRequests);
        $offset = ($page - 1) * $perPage;
        $paginatedRequests = array_slice($allRequests, $offset, $perPage);
        $totalPages = ceil($totalResults / $perPage);

        return view('admin.requests', compact(
            'paginatedRequests', 'pendingCount', 'currentFilter', 
            'searchQuery', 'page', 'totalPages', 'totalResults'
        ));

    } catch (\Exception $e) {
        dd('Firebase Connection Error: ' . $e->getMessage());
    }
});

// --- ADMIN ACTION ROUTES (APPROVE/REJECT/RETURN) ---
Route::post('/admin/requests/{id}/approve', function ($id) {
    try {
        $firestore = app('firebase.firestore')->database();
        $firestore->collection('borrowRequests')->document($id)->update([
            ['path' => 'status', 'value' => 'approved']
        ]);
        return back()->with('success', 'Request approved successfully.');
    } catch (\Exception $e) {
        return back()->withErrors(['error' => $e->getMessage()]);
    }
});

Route::post('/admin/requests/{id}/reject', function ($id) {
    try {
        $firestore = app('firebase.firestore')->database();
        $firestore->collection('borrowRequests')->document($id)->update([
            ['path' => 'status', 'value' => 'rejected']
        ]);
        return back()->with('success', 'Request rejected.');
    } catch (\Exception $e) {
        return back()->withErrors(['error' => $e->getMessage()]);
    }
});

Route::post('/admin/requests/{id}/return', function ($id) {
    try {
        $firestore = app('firebase.firestore')->database();
        $firestore->collection('borrowRequests')->document($id)->update([
            ['path' => 'status', 'value' => 'returned'],
            ['path' => 'returnedAt', 'value' => now()->toDateTimeString()]
        ]);
        return back()->with('success', 'Equipment marked as returned.');
    } catch (\Exception $e) {
        return back()->withErrors(['error' => $e->getMessage()]);
    }
});

// --- FIRESTORE SUBMISSION ROUTE (INVENTORY) ---
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