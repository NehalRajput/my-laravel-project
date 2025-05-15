@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gray-50 py-6">
    <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-white rounded-xl shadow-lg overflow-hidden border border-gray-200 transition-all duration-300 hover:shadow-xl">
            <!-- Header -->
            <div class="border-b border-gray-200 bg-white px-4 py-4">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-3">
                        <div class="flex-shrink-0">
                            <div class="h-9 w-9 rounded-full bg-indigo-100 flex items-center justify-center shadow-inner">
                                <svg class="h-5 w-5 text-indigo-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                </svg>
                            </div>
                        </div>
                        <div>
                            <h1 class="text-lg font-semibold text-gray-900 tracking-tight">Create New Admin</h1>
                            <p class="text-xs text-gray-500 mt-0.5">Fill in the details below to create a new administrator</p>
                        </div>
                    </div>
                    <a href="{{ route('admin.index') }}" 
                        class="inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        <svg class="h-4 w-4 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                        </svg>
                        Back to List
                    </a>
                </div>
            </div>

            <!-- Form -->
            <form action="{{ route('admin.store') }}" method="POST" class="divide-y divide-gray-200">
                @csrf
                
                <div class="px-4 py-6 space-y-5 bg-gray-50">
                    @if ($errors->any())
                        <div class="bg-red-50 border-l-4 border-red-400 p-4 rounded">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <svg class="h-5 w-5 text-red-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <h3 class="text-sm font-medium text-red-800">Please fix the following errors:</h3>
                                    <ul class="mt-2 text-sm text-red-700 list-disc list-inside">
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- Full Name Field -->
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-1 tracking-wide">Full Name</label>
                        <input type="text" 
                            name="name" 
                            id="name" 
                            class="block w-full rounded-lg border border-gray-300 shadow-sm focus:border-indigo-600 focus:ring-indigo-600 text-sm h-10 px-4 py-2 transition duration-200 ease-in-out bg-gray-50 hover:bg-white"
                            value="{{ old('name') }}" 
                            placeholder="Enter full name"
                            required>
                        @error('name')
                            <p class="mt-1 text-xs text-red-600 animate-pulse">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Email Field -->
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-1 tracking-wide">Email Address</label>
                        <input type="email" 
                            name="email" 
                            id="email" 
                            class="block w-full rounded-lg border border-gray-300 shadow-sm focus:border-indigo-600 focus:ring-indigo-600 text-sm h-10 px-4 py-2 transition duration-200 ease-in-out bg-gray-50 hover:bg-white"
                            value="{{ old('email') }}" 
                            placeholder="Enter email address"
                            required>
                        @error('email')
                            <p class="mt-1 text-xs text-red-600 animate-pulse">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Role Selection -->
                    <div>
                        <label for="role" class="block text-sm font-medium text-gray-700 mb-1 tracking-wide">Role</label>
                        <select name="role_id" 
                            id="role" 
                            class="block w-full rounded-lg border border-gray-300 shadow-sm focus:border-indigo-600 focus:ring-indigo-600 text-sm h-10 px-4 py-2 transition duration-200 ease-in-out bg-gray-50 hover:bg-white"
                            required>
                            <option value="">Select a role</option>
                            @foreach($roles as $role)
                                <option value="{{ $role->id }}" 
                                    {{ old('role_id') == $role->id ? 'selected' : '' }}
                                    class="{{ 
                                        $role->name === 'super_admin' ? 'text-red-600' :
                                        ($role->name === 'admin' ? 'text-indigo-600' :
                                        ($role->name === 'manager' ? 'text-green-600' : 'text-blue-600'))
                                    }}">
                                    {{ ucwords(str_replace('_', ' ', $role->name)) }}
                                </option>
                            @endforeach
                        </select>
                        @error('role_id')
                            <p class="mt-1 text-xs text-red-600 animate-pulse">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Password Field -->
                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700 mb-1 tracking-wide">Password</label>
                        <div class="relative">
                            <input type="password" 
                                name="password" 
                                id="password" 
                                class="block w-full rounded-lg border border-gray-300 shadow-sm focus:border-indigo-600 focus:ring-indigo-600 text-sm h-10 px-4 py-2 pr-10 transition duration-200 ease-in-out bg-gray-50 hover:bg-white"
                                placeholder="Enter password"
                                required>
                            <button type="button" 
                                id="togglePassword"
                                class="absolute inset-y-0 right-0 px-3 flex items-center text-gray-500 hover:text-gray-600">
                                <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                            </button>
                        </div>
                        @error('password')
                            <p class="mt-1 text-xs text-red-600 animate-pulse">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Form Actions -->
                <div class="px-4 py-4 bg-white space-x-3 flex justify-end">
                    <a href="{{ route('admin.index') }}" 
                        class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        Cancel
                    </a>
                    <button type="submit" 
                        class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        Create Admin
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Toggle password visibility
    document.getElementById('togglePassword').addEventListener('click', function() {
        const passwordInput = document.getElementById('password');
        const icon = this.querySelector('svg');
        
        if (passwordInput.type === 'password') {
            passwordInput.type = 'text';
            icon.innerHTML = `
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
            `;
        } else {
            passwordInput.type = 'password';
            icon.innerHTML = `
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
            `;
        }
    });
</script>
@endpush

@endsection 