<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Fitub Admin Panel')</title>

    {{-- Tailwind CSS --}}
    <script src="https://cdn.tailwindcss.com"></script>

    {{-- Alpine.js for interactivity --}}
    <script src="//unpkg.com/alpinejs" defer></script>

    {{-- Custom Google Font for a cleaner look --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    <style>
        /* Apply the custom font */
        body {
            font-family: 'Inter', sans-serif;
        }
    </style>
</head>

<body class="bg-gray-100 antialiased">
    <div x-data="{ sidebarOpen: true }" class="flex h-screen bg-gray-100">

        <!-- ===== Sidebar ===== -->
        <aside
            :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
            class="fixed inset-y-0 left-0 z-30 w-64 h-screen bg-gray-900 text-gray-300 transform transition-transform duration-300 ease-in-out lg:translate-x-0 lg:static lg:inset-0">

            <!-- Sidebar Header -->
            <div class="flex items-center justify-center h-20 border-b border-gray-800">
                <a href="{{ url('/admin/dashboard') }}" class="text-white text-2xl font-bold tracking-wider">
                    <span class="text-indigo-400">FIT</span>UB
                </a>
            </div>

            <!-- Sidebar Links -->
            <nav class="mt-8 px-4">
                {{-- Dashboard Link --}}
                <a href="{{ url('/admin/dashboard') }}" class="flex items-center px-4 py-2.5 rounded-lg transition-colors duration-200 gap-x-3
                    {{ request()->is('admin/dashboard') ? 'bg-gray-800 text-white' : 'hover:bg-gray-800 hover:text-white' }}">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor"><path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z" /></svg>
                    <span>Dashboard</span>
                </a>

                {{-- Manage Users Link --}}
                <a href="{{ route('admin.users.index') }}" class="mt-2 flex items-center px-4 py-2.5 rounded-lg transition-colors duration-200 gap-x-3
                    {{ request()->routeIs('admin.users.*') ? 'bg-gray-800 text-white' : 'hover:bg-gray-800 hover:text-white' }}">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor"><path d="M9 6a3 3 0 11-6 0 3 3 0 016 0zM17 6a3 3 0 11-6 0 3 3 0 016 0zM12.93 17c.046-.327.07-.66.07-1a6.97 6.97 0 00-1.5-4.33A5 5 0 0112 11v-1a4.978 4.978 0 00-4.51-4.986A5.002 5.002 0 002 6a5 5 0 00-4 4v1a5 5 0 005 5h4.93z" /></svg>
                    <span>Manage Users</span>
                </a>

                {{-- Pending Approvals Link --}}
                <a href="{{ route('admin.pending') }}" class="mt-2 flex items-center px-4 py-2.5 rounded-lg transition-colors duration-200 gap-x-3
                    {{ request()->routeIs('admin.pending') ? 'bg-gray-800 text-white' : 'hover:bg-gray-800 hover:text-white' }}">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000-16zm1-11a1 1 0 10-2 0v2H7a1 1 0 100 2h2v2a1 1 0 102 0v-2h2a1 1 0 100-2h-2V7z" clip-rule="evenodd" />
                    </svg>
                    <span>Pending Approvals</span>
                    @if(\App\Models\User::where('status', 'pending')->count() > 0)
                        <span class="ml-auto bg-red-600 text-[10px] px-2 py-0.5 rounded-full font-bold">
                            {{ \App\Models\User::where('status', 'pending')->count() }}
                        </span>
                    @endif
                </a>

                {{-- Customer Inquiries Link --}}
                <a href="{{ route('admin.inquiries.index') }}" class="mt-2 flex items-center px-4 py-2.5 rounded-lg transition-colors duration-200 gap-x-3
                    {{ request()->routeIs('admin.inquiries.*') ? 'bg-gray-800 text-white' : 'hover:bg-gray-800 hover:text-white' }}">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor"><path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z" /><path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z" /></svg>
                    <span>Customer Inquiries</span>
                </a>

                {{-- Payments Link --}}
                <a href="{{ route('admin.payments.index') }}" class="mt-2 flex items-center px-4 py-2.5 rounded-lg transition-colors duration-200 gap-x-3
                    {{ request()->routeIs('admin.payments.*') ? 'bg-gray-800 text-white' : 'hover:bg-gray-800 hover:text-white' }}">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                        <path d="M4 4a2 2 0 012-2h8a2 2 0 012 2v2H4V4z" />
                        <path fill-rule="evenodd" d="M18 9H2v7a2 2 0 002 2h12a2 2 0 002-2V9zm-10 4a1 1 0 100 2h4a1 1 0 100-2H8z" clip-rule="evenodd" />
                    </svg>
                    <span>Payments</span>
                </a>
            </nav>

             <!-- Logout Button at the bottom -->
            <div class="mt-auto p-4">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="w-full flex items-center gap-x-3 py-2.5 px-4 rounded-lg transition-colors duration-200 text-red-400 hover:bg-red-900/50 hover:text-red-300">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M3 3a1 1 0 00-1 1v12a1 1 0 102 0V4a1 1 0 00-1-1zm10.293 9.293a1 1 0 001.414 1.414l3-3a1 1 0 000-1.414l-3-3a1 1 0 10-1.414 1.414L14.586 9H7a1 1 0 100 2h7.586l-1.293 1.293z" clip-rule="evenodd" /></svg>
                        <span>Logout</span>
                    </button>
                </form>
            </div>
        </aside>

        <!-- ===== Main Content ===== -->
        <div class="flex-1 flex flex-col overflow-hidden">
            <!-- Top Header -->
            <header class="flex justify-between items-center p-6 bg-white border-b">
                <!-- Hamburger Menu for Mobile -->
                <div class="flex items-center">
                    <button @click="sidebarOpen = !sidebarOpen" class="text-gray-500 focus:outline-none lg:hidden">
                        <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M4 6H20M4 12H20M4 18H11" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    </button>
                </div>

                <!-- User Profile Dropdown -->
                <div x-data="{ dropdownOpen: false }" class="relative">
                    @auth
                    <button @click="dropdownOpen = !dropdownOpen" class="relative z-10 flex items-center gap-2">
                        <span class="font-semibold text-gray-700">{{ Auth::user()->name }}</span>
                         <div class="h-10 w-10 rounded-full overflow-hidden border-2 border-gray-300">
                            <img class="h-full w-full object-cover" src="{{ 'https://ui-avatars.com/api/?name=' . urlencode(Auth::user()->name) . '&background=random&color=fff' }}" alt="Your avatar">
                        </div>
                    </button>

                    <div x-show="dropdownOpen" @click.away="dropdownOpen = false" x-transition class="absolute right-0 mt-2 py-2 w-48 bg-white rounded-md shadow-xl z-20" style="display: none;">
                        <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-indigo-500 hover:text-white">Profile</a>
                        <div class="border-t my-1"></div>
                        <form method="POST" action="{{ route('logout') }}">
                           @csrf
                           <button type="submit" class="w-full text-left block px-4 py-2 text-sm text-gray-700 hover:bg-indigo-500 hover:text-white">Logout</button>
                        </form>
                    </div>
                    @endauth
                </div>
            </header>

            <!-- Page Content -->
            <main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-100">
                <div class="container mx-auto px-6 py-8">
                    {{-- Yahan aapka content (index.blade.php) aayega --}}
                    @yield('content')
                </div>
            </main>
        </div>
    </div>
</body>
</html>
