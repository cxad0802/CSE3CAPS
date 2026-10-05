@extends('layouts.app')

@section('content')
<!-- Centering Wrapper to position the card in the middle of the page below the navbar -->
<div class="flex items-center justify-center min-h-[calc(100vh-64px)] py-12 px-4 w-full">
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

        <!-- Form updated for Laravel Backend Submission -->
        <form action="/login" method="POST" class="space-y-4">
            @csrf
            
            <div>
                <label class="block text-sm font-medium text-gray-700 text-left">University ID</label>
                <input type="text" name="university_id" placeholder="e.g. 12345678" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-red-500 focus:border-red-500" required>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 text-left" for="password">Password</label>
                <div class="relative mt-1">
                    <input id="password" type="password" name="password" placeholder="••••••••" class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-red-500 focus:border-red-500 pr-10" required>
                    
                    <button type="button" onclick="togglePassword()" class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600 transition-colors focus:outline-none">
                        <!-- Eye Open Icon -->
                        <svg id="eyeIcon" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                        </svg>
                        <!-- Eye Closed Icon -->
                        <svg id="eyeSlashIcon" class="w-5 h-5 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"></path>
                        </svg>
                    </button>
                </div>
            </div>

            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <input type="checkbox" name="remember" class="h-4 w-4 text-red-600 border-gray-300 rounded focus:ring-red-500">
                    <label class="ml-2 block text-sm text-gray-900">Remember me</label>
                </div>
                <a href="#" class="text-sm font-medium text-red-600 hover:text-red-500">Forgot password?</a>
            </div>

            <!-- submit type added here -->
            <button type="submit" class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-red-700 hover:bg-red-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                Sign In via SSO
            </button>
        </form>

        <div class="mt-6 text-center text-sm border-t border-gray-100 pt-4">
            <p class="text-gray-600">Need technical assistance? <a href="#" class="font-medium text-red-600 hover:text-red-500">Contact Support</a></p>
        </div>
    </div>
</div>

<script>
    function togglePassword() {
        const input = document.getElementById('password');
        const eyeIcon = document.getElementById('eyeIcon');
        const eyeSlashIcon = document.getElementById('eyeSlashIcon');
        
        if (input.type === 'password') {
            input.type = 'text';
            eyeIcon.classList.add('hidden');
            eyeSlashIcon.classList.remove('hidden');
        } else {
            input.type = 'password';
            eyeIcon.classList.remove('hidden');
            eyeSlashIcon.classList.add('hidden');
        }
    }
</script>
@endsection