<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\BorrowRequestWorkflow;
use App\Services\FirestoreService;
use App\Support\BorrowRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use RuntimeException;

/**
 * The administrator queue: review requests and record returns.
 *
 * Decisions are delegated to BorrowRequestWorkflow, which runs each one in a
 * Firestore transaction together with the stock movement it implies.
 */
class BorrowRequestController extends Controller
{
    /**
     * Rows per page. The "Showing x to y" line in the view assumes this value.
     */
    private const PER_PAGE = 10;

    /**
     * Tab name in the UI, mapped onto the stored status it selects.
     *
     * @var array<string, string>
     */
    private const TABS = [
        'pending' => 'pending',
        'active' => 'approved',
        'returned' => 'returned',
    ];

    public function index(Request $request, FirestoreService $firestore): View
    {
        $filter = (string) $request->query('filter', 'all');
        $search = trim((string) $request->query('search', ''));
        $page = max((int) $request->query('page', 1), 1);

        // The whole collection is read once and then filtered, sorted and paged
        // in PHP. Doing it with where() plus orderBy('createdAt') would need a
        // composite index that is not deployed to the project yet.
        $requests = array_map(
            BorrowRequest::fromSnapshot(...),
            $firestore->documentsOf($firestore->borrowRequests()),
        );

        usort($requests, BorrowRequest::newestFirst(...));

        $pendingCount = count(array_filter(
            $requests,
            fn (BorrowRequest $borrowRequest) => $borrowRequest->status === 'pending',
        ));

        $status = self::TABS[$filter] ?? null;

        $matching = array_values(array_filter(
            $requests,
            fn (BorrowRequest $borrowRequest) => ($status === null || $borrowRequest->status === $status)
                && $borrowRequest->matches($search),
        ));

        $totalResults = count($matching);
        $totalPages = (int) ceil($totalResults / self::PER_PAGE);
        $page = $totalPages > 0 ? min($page, $totalPages) : 1;

        return view('admin.requests', [
            'paginatedRequests' => array_map(
                fn (BorrowRequest $borrowRequest) => $borrowRequest->forAdminQueue(),
                array_slice($matching, ($page - 1) * self::PER_PAGE, self::PER_PAGE),
            ),
            'pendingCount' => $pendingCount,
            'page' => $page,
            'totalPages' => $totalPages,
            'totalResults' => $totalResults,
        ]);
    }

    public function approve(Request $request, BorrowRequestWorkflow $workflow, string $id): RedirectResponse
    {
        return $this->decide($request, $workflow, $id, 'approved', 'Approved the loan of %s.');
    }

    public function reject(Request $request, BorrowRequestWorkflow $workflow, string $id): RedirectResponse
    {
        $validated = $request->validate([
            'review_note' => ['nullable', 'string', 'max:500'],
        ]);

        return $this->decide(
            $request,
            $workflow,
            $id,
            'rejected',
            'Rejected the request for %s.',
            $validated['review_note'] ?? null,
        );
    }

    public function markReturned(Request $request, BorrowRequestWorkflow $workflow, string $id): RedirectResponse
    {
        return $this->decide($request, $workflow, $id, 'returned', 'Recorded the return of %s.');
    }

    private function decide(
        Request $request,
        BorrowRequestWorkflow $workflow,
        string $id,
        string $to,
        string $message,
        ?string $note = null,
    ): RedirectResponse {
        try {
            $name = $workflow->transition(
                $id,
                $to,
                (string) $request->session()->get('firebase.uid'),
                $note,
            );
        } catch (RuntimeException $e) {
            return back()->withErrors(['request' => $e->getMessage()]);
        }

        return back()->with('success', sprintf($message, $name));
    }
}
