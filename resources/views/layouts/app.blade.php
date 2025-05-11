<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title') - {{ config('app.name') }}</title>
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@200;300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Icons -->
    <script src="https://unpkg.com/feather-icons"></script>
    
    <!-- Custom Styles -->
    <style>
        body {
            font-family: 'Manrope', sans-serif;
        }
        
        .sidebar {
            width: 280px;
            transition: all 0.3s ease;
        }
        
        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
            }
            
            .sidebar.active {
                transform: translateX(0);
            }
        }
    </style>
</head>
<body class="bg-gray-50">
    <div class="flex min-h-screen">
        <!-- Sidebar -->
        <aside class="sidebar fixed inset-y-0 left-0 bg-white shadow-lg">
            <div class="p-4">
                <a href="{{ route('home') }}" class="flex items-center space-x-2">
                    <img src="{{ asset('images/logo.png') }}" alt="Logo" class="h-8">
                    <span class="text-xl font-bold">{{ config('app.name') }}</span>
                </a>
            </div>
            
            <nav class="mt-4">
                <a href="{{ route('home') }}" class="flex items-center px-4 py-2 text-gray-700 hover:bg-gray-100">
                    <i data-feather="shopping-bag" class="w-5 h-5 mr-3"></i>
                    Products
                </a>
                <a href="{{ route('transactions.index') }}" class="flex items-center px-4 py-2 text-gray-700 hover:bg-gray-100">
                    <i data-feather="credit-card" class="w-5 h-5 mr-3"></i>
                    My Orders
                </a>
                @auth
                    @if(auth()->user()->isAdmin())
                        <a href="{{ route('admin.dashboard') }}" class="flex items-center px-4 py-2 text-gray-700 hover:bg-gray-100">
                            <i data-feather="settings" class="w-5 h-5 mr-3"></i>
                            Admin Panel
                        </a>
                    @endif
                @endauth
            </nav>
            
            <div class="absolute bottom-0 w-full p-4 border-t">
                <div class="flex space-x-4 justify-center">
                    <a href="#" class="text-gray-600 hover:text-gray-900">
                        <i data-feather="facebook" class="w-5 h-5"></i>
                    </a>
                    <a href="#" class="text-gray-600 hover:text-gray-900">
                        <i data-feather="instagram" class="w-5 h-5"></i>
                    </a>
                    <a href="#" class="text-gray-600 hover:text-gray-900">
                        <i data-feather="twitter" class="w-5 h-5"></i>
                    </a>
                </div>
                <p class="text-center text-sm text-gray-500 mt-2">
                    &copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.
                </p>
            </div>
        </aside>

        <!-- Main Content -->
        <main class="flex-1 ml-0 md:ml-72">
            <!-- Top Navigation -->
            <header class="bg-white shadow-sm">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="flex justify-between items-center py-4">
                        <button class="md:hidden" id="sidebar-toggle">
                            <i data-feather="menu" class="w-6 h-6"></i>
                        </button>
                        
                        <div class="flex-1 max-w-lg mx-4">
                            <form action="{{ route('products.search') }}" method="GET">
                                <div class="relative">
                                    <input type="text" name="q" class="w-full pl-10 pr-4 py-2 rounded-lg border focus:outline-none focus:border-blue-500" placeholder="Search products...">
                                    <i data-feather="search" class="absolute left-3 top-3 w-4 h-4 text-gray-400"></i>
                                </div>
                            </form>
                        </div>
                        
                        <div class="flex items-center space-x-4">
                            @auth
                                <div class="relative">
                                    <button class="flex items-center space-x-2">
                                        <span>{{ Auth::user()->name }}</span>
                                        <i data-feather="chevron-down" class="w-4 h-4"></i>
                                    </button>
                                    <div class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg hidden">
                                        <form method="POST" action="{{ route('logout') }}">
                                            @csrf
                                            <button type="submit" class="block w-full px-4 py-2 text-sm text-left text-gray-700 hover:bg-gray-100">
                                                Logout
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            @else
                                <a href="{{ route('login') }}" class="text-gray-700 hover:text-gray-900">Login</a>
                                <a href="{{ route('register') }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">Register</a>
                            @endauth
                        </div>
                    </div>
                </div>
            </header>

            <!-- Page Content -->
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
                @yield('content')
            </div>
        </main>
    </div>

    <!-- WhatsApp Button -->
    <a href="https://wa.me/your-number" target="_blank" class="fixed bottom-4 right-4 bg-green-500 text-white p-3 rounded-full shadow-lg hover:bg-green-600">
        <i data-feather="message-circle" class="w-6 h-6"></i>
    </a>

    <!-- Scripts -->
    <script>
        feather.replace();
        
        // Sidebar Toggle
        document.getElementById('sidebar-toggle').addEventListener('click', function() {
            document.querySelector('.sidebar').classList.toggle('active');
        });
        
        // Dropdown Toggle
        document.querySelectorAll('.relative button').forEach(button => {
            button.addEventListener('click', function() {
                this.nextElementSibling.classList.toggle('hidden');
            });
        });
    </script>
    @stack('scripts')
</body>
</html>
