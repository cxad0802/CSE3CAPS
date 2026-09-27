@extends('layouts.app')

@section('content')
<div class="bg-white p-8 rounded-lg shadow-md w-full max-w-md border-t-4 border-red-700">
    <div class="text-center mb-6">
        <!-- Mockup logo -->
        <div class="mx-auto w-12 h-12 bg-red-50 text-red-700 rounded-full mb-2 flex items-center justify-center">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
            </svg>
        </div>
        <h2 class="text-2xl font-bold">UniBorrow IT</h2>
        <p class="text-gray-500 text-sm mt-1">Institutional Equipment Allocation System</p>
    </div>

    <form action="{{ route('session.store') }}" method="POST" class="space-y-4" data-firebase-login>
        @csrf
        <input type="hidden" name="id_token" value="">

        <p data-login-error class="text-sm text-red-600 text-left">{{ $errors->first() }}</p>

        <div>
            <label for="email" class="block text-sm font-medium text-gray-700 text-left">University email</label>
            <input id="email" name="email" type="email" required autofocus autocomplete="username" placeholder="e.g. 12345678@students.latrobe.edu.au" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-red-500 focus:border-red-500">
        </div>

        <div>
            <label for="password" class="block text-sm font-medium text-gray-700 text-left">Password</label>
            <input id="password" name="password" type="password" required autocomplete="current-password" placeholder="••••••••" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-red-500 focus:border-red-500">
        </div>

        <div class="flex items-center justify-between">
            <div class="flex items-center">
                <input type="checkbox" class="h-4 w-4 text-red-600 border-gray-300 rounded focus:ring-red-500">
                <label class="ml-2 block text-sm text-gray-900">Remember me</label>
            </div>
            <a href="#" class="text-sm font-medium text-red-600 hover:text-red-500">Forgot password?</a>
        </div>

        <button type="submit" class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-red-700 hover:bg-red-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 disabled:opacity-60">
            Sign In
        </button>
    </form>

    <div class="mt-6 text-center text-sm border-t border-gray-100 pt-4">
        <p class="text-gray-600">Need technical assistance? <a href="#" class="font-medium text-red-600 hover:text-red-500">Contact Support</a></p>
    </div>
</div>
@endsection