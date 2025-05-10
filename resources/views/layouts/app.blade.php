<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Online Shop Game Digital') }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100">
    <!-- Navigation -->
    <nav class="bg-white shadow-lg">
        <div class="max-w-7xl mx-auto px-4">
            <div class="flex justify-between h-16">
                <div class="flex">
                    <div class="flex-shrink-0 flex items-center">
                        <a href="{{ url('/') }}" class="text-xl font-bold text-indigo-600">
                            {{ config('app.name', 'Online Shop Game Digital') }}
                        </a>
                    </div>
                </div>

                <div class="flex items-center">
                    @auth
                        @if(auth()->user()->role === 'admin')
                            <div class="hidden md:flex space-x-4">
                                <a href="{{ route('admin.dashboard') }}" class="text-gray-700 hover:text-indigo-600 px-3 py-2">
                                    Dashboard
                                </a>
                                <a href="{{ route('admin.products.index') }}" class="text-gray-700 hover:text-indigo-600 px-3 py-2">
                                    Manajemen Produk
                                </a>
                                @if(auth()->user()->isAdmin())
                                    <li class="nav-item">
                                        <a class="nav-link {{ request()->routeIs('admin.settings.*') ? 'active' : '' }}"
                                           href="{{ route('admin.settings.index') }}">
                                            <i class="fas fa-cog"></i>
                                            <span>Pengaturan</span>
                                        </a>
                                    </li>
                                @endif
                            </div>
                        @endif
                        <div class="ml-3 relative">
                            <div class="flex items-center space-x-4">
                                <span class="text-gray-700">{{ auth()->user()->name }}</span>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="text-gray-700 hover:text-indigo-600">
                                        Logout
                                    </button>
                                </form>
                            </div>
                        </div>
                    @else
                        <a href="{{ route('login') }}" class="text-gray-700 hover:text-indigo-600 px-3 py-2">
                            Login
                        </a>
                        <a href="{{ route('register') }}" class="text-gray-700 hover:text-indigo-600 px-3 py-2">
                            Register
                        </a>
                    @endauth
                </div>
            </div>
        </div>
    </nav>

    <!-- Page Content -->
    <main class="py-4">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @yield('content')
        </div>
    </main>

    <!-- Footer -->
    <footer class="bg-white shadow-lg mt-8">
        <div class="max-w-7xl mx-auto py-4 px-4">
            <p class="text-center text-gray-500">
                &copy; {{ date('Y') }} {{ config('app.name', 'Online Shop Game Digital') }}. All rights reserved.
            </p>
        </div>
    </footer>

    @stack('scripts')
</body>
</html>
