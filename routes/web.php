<?php

use App\Http\Controllers\FirebaseHealthController;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;

Route::get('/', function () {
    return view('welcome');
});

// Note: Development aid - reports whether the Firebase Admin SDK and Firestore are reachable.
if (app()->environment('local')) {
    Route::get('/_health/firebase', FirebaseHealthController::class)->name('health.firebase');
}

// --- UI ROUTES ---
Route::get('/login', function () { return view('auth.login'); })->name('login');

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

Route::any('/logout', function () {
    return redirect('/login');
})->name('logout');


// --- STUDENT CATALOG ROUTE ---
Route::get('/catalog', function () { 
    try {
        $firestore = app('firebase.firestore')->database();
        $equipmentsRef = $firestore->collection('equipments')
                                  ->where('status', '=', 'available')
                                  ->documents();
        
        $catalog = [];
        foreach ($equipmentsRef as $doc) {
            if ($doc->id() === '_schema' || !$doc->exists()) continue;
            
            $data = $doc->data();
            $data['id'] = $doc->id();
            $catalog[] = $data;
        }

        return view('customer.catalog', compact('catalog'));
    } catch (\Exception $e) {
        dd('Firebase Connection Error: ' . $e->getMessage());
    }
})->name('catalog');


// --- ADMIN ROUTES ---

// Note: Dashboard route calculating dynamic counts, alerts, warranties, and popular items
Route::get('/admin/dashboard', function () { 
    try {
        $firestore = app('firebase.firestore')->database();
        
        $equipments = $firestore->collection('equipments')->documents();
        $requests = $firestore->collection('borrowRequests')->documents();
        
        $totalAssets = 0;
        $availableAssets = 0;
        $inMaintenance = 0;
        $checkedOut = 0;
        
        $equipmentData = [];
        $criticalAlerts = [];
        $expiringWarranties = [];
        
        $now = new DateTime();
        $thirtyDaysFromNow = (new DateTime())->modify('+30 days');

        // Note: Process Equipments for KPI, Maintenance Alerts, and Warranties
        foreach ($equipments as $doc) {
            if ($doc->id() === '_schema' || !$doc->exists()) continue;
            
            $data = $doc->data();
            $data['id'] = $doc->id();
            $equipmentData[$doc->id()] = $data;
            
            $totalAssets++;
            $status = strtolower($data['status'] ?? '');
            
            if ($status === 'available') $availableAssets++;
            if ($status === 'maintenance') {
                $inMaintenance++;
                $criticalAlerts[] = [
                    'title' => ($data['name'] ?? 'Unknown Asset') . ' (' . $doc->id() . ')',
                    'message' => 'Status: Maintenance. ' . ($data['description'] ?? 'Requires attention.'),
                    'time' => 'Action Required',
                    'type' => 'maintenance'
                ];
            }

            // Note: Check Warranty Expirations
            if (!empty($data['warranty_expiration'])) {
                $expDate = new DateTime($data['warranty_expiration']);
                if ($expDate >= $now && $expDate <= $thirtyDaysFromNow) {
                    $expiringWarranties[] = [
                        'id' => $doc->id(),
                        'name' => $data['name'] ?? 'Unknown',
                        'expiration' => $expDate->format('M d, Y'),
                        'action' => 'Renew'
                    ];
                } elseif ($expDate < $now) {
                    $expiringWarranties[] = [
                        'id' => $doc->id(),
                        'name' => $data['name'] ?? 'Unknown',
                        'expiration' => $expDate->format('M d, Y'),
                        'action' => 'Expired'
                    ];
                }
            }
        }
        
        // Note: Process Borrow Requests for Overdue Alerts and Popular Categories
        $categoryCounts = [];
        
        foreach ($requests as $doc) {
            if ($doc->id() === '_schema' || !$doc->exists()) continue;
            $data = $doc->data();
            $status = strtolower($data['status'] ?? '');
            
            if (in_array($status, ['approved', 'active'])) {
                $checkedOut++;
                
                // Note: Check for overdue items
                if (!empty($data['borrowTo'])) {
                    $returnDate = new DateTime($data['borrowTo']);
                    if ($returnDate < $now) {
                        $daysOverdue = $now->diff($returnDate)->days;
                        $criticalAlerts[] = [
                            'title' => ($data['equipmentName'] ?? 'Unknown Asset') . ' (' . ($data['equipmentId'] ?? '') . ')',
                            'message' => 'Overdue by ' . $daysOverdue . ' days. Borrower: ' . ($data['userEmail'] ?? ''),
                            'time' => 'Overdue',
                            'type' => 'overdue'
                        ];
                    }
                }
            }

            // Note: Tally frequently borrowed categories (exclude rejected/cancelled)
            if (!in_array($status, ['cancelled', 'rejected'])) {
                $eqId = $data['equipmentId'] ?? '';
                if (isset($equipmentData[$eqId])) {
                    $cat = ucfirst($equipmentData[$eqId]['category'] ?? 'Other');
                    $categoryCounts[$cat] = ($categoryCounts[$cat] ?? 0) + 1;
                }
            }
        }

        // Note: Sort and format top 4 frequently borrowed categories
        arsort($categoryCounts);
        $topCategories = array_slice($categoryCounts, 0, 4, true);
        $maxCount = !empty($topCategories) ? max($topCategories) : 1;
        
        $frequentlyBorrowed = [];
        $colors = ['bg-red-700', 'bg-red-600 opacity-80', 'bg-red-900 opacity-60', 'bg-red-200'];
        $i = 0;
        foreach ($topCategories as $cat => $count) {
            $frequentlyBorrowed[] = [
                'name' => $cat,
                'count' => $count,
                'percentage' => round(($count / $maxCount) * 100),
                'colorClass' => $colors[$i % count($colors)]
            ];
            $i++;
        }

        // Note: Limit alerts and warranties to top 5 for the dashboard UI
        $criticalAlerts = array_slice($criticalAlerts, 0, 5);
        $expiringWarranties = array_slice($expiringWarranties, 0, 5);

        return view('admin.dashboard', compact(
            'totalAssets', 'availableAssets', 'inMaintenance', 'checkedOut', 
            'criticalAlerts', 'expiringWarranties', 'frequentlyBorrowed'
        ));
    } catch (\Exception $e) {
        dd('Firebase Connection Error: ' . $e->getMessage());
    }
})->name('admin.dashboard');

Route::get('/admin/inventory/create', function () { return view('admin.equipment_create'); });

Route::get('/admin/inventory', function () {
    try {
        $firestore = app('firebase.firestore')->database();
        $equipmentsRef = $firestore->collection('equipments')->documents();
        
        $inventory = [];
        foreach ($equipmentsRef as $doc) {
            if ($doc->id() === '_schema' || !$doc->exists()) continue;
            
            $data = $doc->data();
            $data['id'] = $doc->id();
            $inventory[] = $data;
        }

        return view('admin.inventory', compact('inventory'));
    } catch (\Exception $e) {
        dd('Firebase Connection Error: ' . $e->getMessage());
    }
});

// Note: Ensure warranty_expiration is captured during creation
Route::post('/admin/inventory/store', function (Request $request) {
    $data = $request->except(['_token']);
    
    $mappedData = [
        'name' => $data['equipment_name'],
        'category' => $data['category'],
        'status' => $data['initial_status'],
        'description' => $data['notes'] ?? '',
        'warranty_expiration' => $data['warranty_expiration'] ?? null,
        'totalQuantity' => 1,
        'availableQuantity' => 1,
        'createdAt' => now()->toDateTimeString(),
    ];
    
    try {
        $firestore = app('firebase.firestore')->database();
        $equipmentsCollection = $firestore->collection('equipments');
        $equipmentsCollection->document($data['asset_id'])->set($mappedData);
        
        return redirect('/admin/inventory')->with('success', 'Equipment added successfully!');
    } catch (\Exception $e) {
        dd('Firebase Connection Error: ' . $e->getMessage());
    }
})->name('admin.inventory.store');

Route::get('/admin/inventory/edit', function (Request $request) {
    $id = $request->query('id');
    if (!$id) return redirect('/admin/inventory')->withErrors(['error' => 'No equipment ID provided.']);

    try {
        $firestore = app('firebase.firestore')->database();
        $doc = $firestore->collection('equipments')->document($id)->snapshot();
        
        if (!$doc->exists()) {
            return redirect('/admin/inventory')->withErrors(['error' => 'Equipment not found.']);
        }
        
        $equipment = $doc->data();
        $equipment['id'] = $doc->id();
        
        return view('admin.inventory-edit', compact('equipment'));
    } catch (\Exception $e) {
        dd('Firebase Connection Error: ' . $e->getMessage());
    }
});

// Note: Ensure warranty_expiration can be updated
Route::post('/admin/inventory/update/{id}', function (Request $request, $id) {
    try {
        $firestore = app('firebase.firestore')->database();
        
        $updateData = [
            ['path' => 'name', 'value' => $request->input('equipment_name')],
            ['path' => 'category', 'value' => $request->input('category')],
            ['path' => 'status', 'value' => $request->input('status')],
            ['path' => 'description', 'value' => $request->input('notes', '')],
            ['path' => 'updatedAt', 'value' => now()->toDateTimeString()],
        ];

        // Only update warranty if provided in the form
        if ($request->filled('warranty_expiration')) {
            $updateData[] = ['path' => 'warranty_expiration', 'value' => $request->input('warranty_expiration')];
        }

        $firestore->collection('equipments')->document($id)->update($updateData);
        
        return redirect('/admin/inventory')->with('success', 'Equipment updated successfully.');
    } catch (\Exception $e) {
        return back()->withErrors(['error' => $e->getMessage()]);
    }
});

Route::post('/admin/inventory/delete/{id}', function ($id) {
    try {
        $firestore = app('firebase.firestore')->database();
        $firestore->collection('equipments')->document($id)->delete();
        
        return back()->with('success', 'Equipment deleted successfully.');
    } catch (\Exception $e) {
        return back()->withErrors(['error' => $e->getMessage()]);
    }
});


// --- SHOW BORROW REQUEST FORM ---
Route::get('/borrow/request', function (Request $request) { 
    $equipmentId = $request->query('equipmentId', '');
    $equipmentName = $request->query('name', '');
    return view('borrow.create', compact('equipmentId', 'equipmentName')); 
});

// --- SUBMIT BORROW REQUEST TO FIRESTORE ---
Route::post('/borrow/request', function (Request $request) {
    try {
        $firestore = app('firebase.firestore')->database();
        $newDocRef = $firestore->collection('borrowRequests')->newDocument();
        
        $newDocRef->set([
            'equipmentId' => $request->input('equipmentId', 'UNKNOWN'), 
            'equipmentName' => $request->input('equipmentName', 'Unknown Item'), 
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
            if ($doc->id() === '_schema' || !$doc->exists()) continue;

            $data = $doc->data();
            $data['id'] = $doc->id();
            
            $status = strtolower($data['status'] ?? 'pending');
            $data['status'] = $status;
            
            $isApproved = in_array($status, ['approved', 'active']);
            
            if ($currentFilter === 'pending' && $status !== 'pending') continue;
            if ($currentFilter === 'approved' && !$isApproved) continue;

            $myRequests[] = $data;
        }

        return view('customer.requests', compact('myRequests', 'currentFilter'));

    } catch (\Exception $e) {
        dd('Firebase Connection Error: ' . $e->getMessage());
    }
});

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
            if ($doc->id() === '_schema' || !$doc->exists()) continue;

            $data = $doc->data();
            $data['id'] = $doc->id();
            
            $borrower = $data['userEmail'] ?? 'Unknown Borrower';
            $equipment = $data['equipmentName'] ?? 'Unknown Item';
            $status = strtolower($data['status'] ?? 'pending');
            
            $data['borrower_name'] = $borrower;
            $data['equipment_name'] = $equipment;
            $data['status'] = $status;
            
            $data['created_at'] = $data['createdAt'] ?? 'N/A';
            $data['duration'] = ($data['borrowFrom'] ?? 'N/A') . ' to ' . ($data['borrowTo'] ?? 'N/A');

            if ($status === 'pending') $pendingCount++;
            
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