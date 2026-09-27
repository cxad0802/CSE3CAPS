@extends('layouts.admin')

@section('content')
<div class="max-w-7xl mx-auto px-8 py-10">

    <!-- Header Section -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-end mb-8 gap-4">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 mb-2">Loan Requests</h1>
            <p class="text-gray-500 text-sm">Manage and process equipment borrowing requests.</p>
        </div>
    </div>

    <!-- Tabs -->
    @php
        $tabs = ['all' => 'All Requests', 'pending' => 'Pending', 'approved' => 'Active Loans', 'returned' => 'Returned'];
    @endphp
    <div class="border-b border-gray-200 mb-6">
        <nav class="flex space-x-8" aria-label="Tabs">
            @foreach ($tabs as $value => $label)
                <a href="{{ route('admin.requests', $value === 'all' ? [] : ['status' => $value]) }}"
                   class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm flex items-center gap-2 {{ $status === $value ? 'border-red-600 text-red-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                    {{ $label }}
                    @if ($value === 'pending' && $pendingCount > 0)
                        <span class="bg-yellow-100 text-yellow-700 py-0.5 px-2 rounded-full text-xs">{{ $pendingCount }}</span>
                    @endif
                </a>
            @endforeach
        </nav>
    </div>

    @if (session('status'))
        <div class="mb-6 rounded-lg bg-green-50 border border-green-200 px-4 py-3 text-sm text-green-800">
            {{ session('status') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="mb-6 rounded-lg bg-red-50 border border-red-200 px-4 py-3 text-sm text-red-700">
            {{ $errors->first() }}
        </div>
    @endif

    <!-- Data Table -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left text-gray-600">
                <thead class="text-xs text-gray-700 uppercase bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th scope="col" class="px-6 py-4 font-bold">Req ID / Date</th>
                        <th scope="col" class="px-6 py-4 font-bold">Borrower</th>
                        <th scope="col" class="px-6 py-4 font-bold">Equipment</th>
                        <th scope="col" class="px-6 py-4 font-bold">Duration</th>
                        <th scope="col" class="px-6 py-4 font-bold">Status</th>
                        <th scope="col" class="px-6 py-4 font-bold text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse ($requests as $borrowRequest)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4">
                                <div class="font-bold text-gray-900">{{ $borrowRequest->reference() }}</div>
                                <div class="text-xs text-gray-500 mt-0.5">{{ $borrowRequest->createdAt?->format('M j, H:i') ?? 'Unknown date' }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="font-medium text-gray-900">{{ $borrowRequest->userEmail }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="font-medium text-gray-900">{{ $borrowRequest->equipmentName }}</div>
                                <div class="text-xs text-gray-500 mt-0.5">{{ $borrowRequest->quantity }} {{ Str::plural('unit', $borrowRequest->quantity) }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-gray-900">{{ $borrowRequest->period() }}</div>
                                @if ($borrowRequest->days())
                                    <div class="text-xs text-gray-500 mt-0.5">{{ $borrowRequest->days() }} {{ Str::plural('day', $borrowRequest->days()) }}</div>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium border {{ $borrowRequest->statusClasses() }}">
                                    {{ $borrowRequest->statusLabel() }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right">
                                @if ($borrowRequest->status === 'pending')
                                    <div class="flex justify-end gap-2">
                                        <form action="{{ route('admin.requests.approve', $borrowRequest->id) }}" method="POST">
                                            @csrf
                                            <button type="submit" class="p-1.5 text-white bg-green-600 hover:bg-green-700 rounded" title="Approve">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                            </button>
                                        </form>
                                        <form action="{{ route('admin.requests.reject', $borrowRequest->id) }}" method="POST" class="flex items-center gap-2">
                                            @csrf
                                            <input type="text" name="review_note" maxlength="500" placeholder="Reason (optional)" class="w-36 border border-gray-300 rounded px-2 py-1 text-xs focus:ring-red-500 focus:border-red-500">
                                            <button type="submit" class="p-1.5 text-white bg-red-600 hover:bg-red-700 rounded" title="Reject">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                            </button>
                                        </form>
                                    </div>
                                @elseif ($borrowRequest->status === 'approved')
                                    <form action="{{ route('admin.requests.returned', $borrowRequest->id) }}" method="POST" class="flex justify-end">
                                        @csrf
                                        <button type="submit" class="px-3 py-1.5 text-xs font-medium text-gray-700 bg-white border border-gray-300 hover:bg-gray-50 rounded shadow-sm">
                                            Mark Returned
                                        </button>
                                    </form>
                                @else
                                    <span class="text-xs text-gray-400">No action needed</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-16 text-center text-gray-500">
                                @if ($status === 'all')
                                    No requests have been raised yet.
                                @else
                                    No {{ $tabs[$status] }} requests.
                                @endif
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="px-6 py-4 border-t border-gray-200 bg-white">
            <span class="text-sm text-gray-500">
                Showing <span class="font-medium text-gray-900">{{ count($requests) }}</span>
                {{ Str::plural('request', count($requests)) }}
            </span>
        </div>
    </div>

</div>
@endsection
