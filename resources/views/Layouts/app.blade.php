<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Laravel') }}</title>
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        [x-cloak] { 
            display: none !important; 
        }
    </style>

    @stack('styles')
</head>
<body class="min-h-screen bg-gray-50">
    @auth
    <!-- Top Navigation -->
    <nav class="bg-white shadow-sm fixed w-full top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex">
                    <!-- Logo -->
                    <div class="flex-shrink-0 flex items-center">
                        <a href="{{ route('dashboard') }}" class="flex items-center space-x-2">
                            <i class="fas fa-tasks text-indigo-600 text-xl"></i>
                            <span class="text-gray-900 font-semibold text-lg">Task Management</span>
                        </a>
                    </div>

                    <!-- Navigation Links -->
                    <div class="hidden sm:ml-6 sm:flex sm:space-x-8">
                        @if(Auth::guard('user')->check())
                            <a href="{{ route('dashboard') }}" 
                                class="inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium {{ request()->routeIs('dashboard') ? 'border-indigo-500 text-gray-900' : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700' }}">
                                <i class="fas fa-home mr-2"></i> Dashboard
                            </a>
                            <a href="{{ route('intern.tasks.index') }}" 
                                class="inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium {{ request()->routeIs('intern.tasks.*') ? 'border-indigo-500 text-gray-900' : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700' }}">
                                <i class="fas fa-list-check mr-2"></i> Tasks
                            </a>
                            <a href="{{ route('chat') }}" 
                                class="inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium relative {{ request()->routeIs('chat') ? 'border-indigo-500 text-gray-900' : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700' }}">
                                <i class="fas fa-message mr-2"></i> Messages
                                @if(auth()->user()->receivedMessages()->whereNull('read_at')->count() > 0)
                                    <span class="absolute -top-1 -right-4 bg-red-500 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center">
                                        {{ auth()->user()->receivedMessages()->whereNull('read_at')->count() }}
                                    </span>
                                @endif
                            </a>
                        @elseif(Auth::guard('admin')->check())
                            <a href="{{ route('admin.dashboard') }}" 
                                class="inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium {{ request()->routeIs('admin.dashboard') ? 'border-indigo-500 text-gray-900' : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700' }}">
                                <i class="fas fa-gauge mr-2"></i> Dashboard
                            </a>
                            <a href="{{ route('admin.interns.index') }}" 
                                class="inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium {{ request()->routeIs('admin.interns.*') ? 'border-indigo-500 text-gray-900' : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700' }}">
                                <i class="fas fa-users mr-2"></i> Interns
                            </a>
                            @can('read_admins')
                            <a href="{{ route('admin.admins.index') }}" 
                                class="inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium {{ request()->routeIs('admin.admins.*') ? 'border-indigo-500 text-gray-900' : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700' }}">
                                <i class="fas fa-user-shield mr-2"></i> Admins
                            </a>
                            @endcan
                            @can('read_tasks')
                            <a href="{{ route('admin.tasks.index') }}" 
                                class="inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium {{ request()->routeIs('admin.tasks.*') ? 'border-indigo-500 text-gray-900' : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700' }}">
                                <i class="fas fa-clipboard-list mr-2"></i> Tasks
                            </a>
                            @endcan
                            @can('read_admins')
                            <a href="{{ route('admin.permissions.index') }}" 
                                class="inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium {{ request()->routeIs('admin.permissions.*') ? 'border-indigo-500 text-gray-900' : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700' }}">
                                <i class="fas fa-key mr-2"></i> Permissions
                            </a>
                            @endcan
                            <a href="{{ route('chat') }}" 
                                class="inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium relative {{ request()->routeIs('chat') ? 'border-indigo-500 text-gray-900' : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700' }}">
                                <i class="fas fa-message mr-2"></i> Messages
                                @if(auth()->user()->receivedMessages()->whereNull('read_at')->count() > 0)
                                    <span class="absolute -top-1 -right-4 bg-red-500 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center">
                                        {{ auth()->user()->receivedMessages()->whereNull('read_at')->count() }}
                                    </span>
                                @endif
                            </a>
                        @endif
                    </div>
                </div>

                <!-- User Dropdown -->
                <div class="hidden sm:ml-6 sm:flex sm:items-center">
                    <div x-data="{ open: false }" class="ml-3 relative">
                        <div>
                            <button @click="open = !open" class="flex items-center space-x-2 text-sm focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 rounded-full">
                                <i class="fas fa-user-circle text-gray-700 text-xl"></i>
                                <span class="text-gray-700">{{ auth()->user()->name }}</span>
                            </button>
                        </div>
                        <div x-show="open" 
                             @click.away="open = false" 
                             class="origin-top-right absolute right-0 mt-2 w-48 rounded-md shadow-lg py-1 bg-white ring-1 ring-black ring-opacity-5 focus:outline-none"
                             x-cloak>
                            <form method="POST" action="{{ auth()->user()->isAdmin() ? route('admin.logout') : route('logout') }}">
                                @csrf
                                <button type="submit" class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                    <i class="fas fa-sign-out-alt mr-2"></i> Logout
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Mobile menu button -->
                <div class="-mr-2 flex items-center sm:hidden">
                    <button @click="mobileMenu = !mobileMenu" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-indigo-500">
                        <span class="sr-only">Open main menu</span>
                        <i class="fas fa-bars"></i>
                    </button>
                </div>
            </div>
        </div>
    </nav>

    <!-- Mobile menu -->
    <div x-show="mobileMenu" class="sm:hidden" x-cloak>
        <!-- Mobile navigation links here -->
    </div>
    @endauth

    <!-- Main Content -->
    <main class="pt-16">
        @yield('content')
    </main>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://js.pusher.com/8.2.0/pusher.min.js"></script>
    <script src="https://cdn.jsdelivr.net/gh/alpinejs/alpine@v2.x.x/dist/alpine.min.js" defer></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    @stack('scripts')
</body>
</html>