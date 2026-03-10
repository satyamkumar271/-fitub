<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fitub - Your Fitness Partner</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style> html { scroll-behavior: smooth; } </style>
</head>
<body class="bg-gray-50 font-sans">

    <div x-data="{ mobileMenuOpen: false, userSidebarOpen: false }">
        <header class="bg-gray-900 shadow-lg sticky top-0 z-50 text-white">
            <nav class="container mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex items-center justify-between h-20">

                    <!-- 1. LEFT SECTION (LOGO) -->
                    <div class="w-1/3">
                        <a href="{{ route('frontpage') }}" class="text-3xl font-extrabold tracking-tight text-white">
                            Fitub
                        </a>
                    </div>

                    <!-- 2. CENTER SECTION (MAIN NAVIGATION LINKS) -->
                    <div class="hidden md:flex w-1/3 justify-center items-baseline space-x-6">
                        {{-- Add all your main links here. They will always stay in the center. --}}
                        <a href="{{ route('blog.index') }}" class="text-gray-300 hover:text-white transition-colors text-base font-medium">Blog</a>
                        <a href="#" class="text-gray-300 hover:text-white transition-colors text-base font-medium">About Us</a>
                        <a href="#" class="text-gray-300 hover:text-white transition-colors text-base font-medium">Contact</a>
                    </div>

                    <!-- 3. RIGHT SECTION (AUTH BUTTONS / USER AVATAR) -->
                    <div class="hidden md:flex w-1/3 justify-end items-center">
                        @guest
                            <div class="space-x-4">
                                <a href="{{ route('login') }}" class="text-gray-300 hover:text-white px-3 py-2 rounded-md text-sm font-medium">Login</a>
                                <a href="{{ route('register') }}" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-md text-sm font-bold transition-colors shadow-md">Register</a>
                            </div>
                        @else
                            <button @click="userSidebarOpen = true" class="flex items-center space-x-3 text-sm rounded-full focus:outline-none hover:opacity-90 transition">
                                <span class="font-medium text-white">{{ Auth::user()->name }}</span>
                                <img class="h-10 w-10 rounded-full object-cover ring-2 ring-offset-2 ring-offset-gray-900 ring-indigo-500" src="{{ 'https://ui-avatars.com/api/?name=' . urlencode(Auth::user()->name) . '&background=random&color=fff' }}" alt="{{ Auth::user()->name }}">
                            </button>
                        @endguest
                    </div>

                    <!-- MOBILE MENU BUTTON (HAMBURGER) -->
                    <div class="flex md:hidden">
                        <button @click="mobileMenuOpen = !mobileMenuOpen" type="button" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-white hover:bg-gray-700 focus:outline-none">
                            <span class="sr-only">Open main menu</span>
                            <svg :class="{'hidden': mobileMenuOpen, 'block': !mobileMenuOpen }" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" /></svg>
                            <svg :class="{'hidden': !mobileMenuOpen, 'block': mobileMenuOpen }" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
                        </button>
                    </div>

                </div>
            </nav>

            <!-- MOBILE MENU CONTENT -->
            <div x-show="mobileMenuOpen" @click.away="mobileMenuOpen = false" class="md:hidden" x-transition>
                <div class="px-2 pt-2 pb-3 space-y-1 sm:px-3 bg-gray-900">
                    {{-- These are the links for the mobile menu --}}
                    <a href="{{ route('blog.index') }}" class="text-gray-300 hover:bg-gray-700 hover:text-white block px-3 py-2 rounded-md text-base font-medium">Blog</a>
                    <a href="#" class="text-gray-300 hover:bg-gray-700 hover:text-white block px-3 py-2 rounded-md text-base font-medium">About Us</a>
                    <a href="#" class="text-gray-300 hover:bg-gray-700 hover:text-white block px-3 py-2 rounded-md text-base font-medium">Contact</a>
                    <a href="{{ route('gyms.index') }}" class="text-gray-300 hover:bg-gray-700 hover:text-white block px-3 py-2 rounded-md text-base font-medium">Find Gyms</a>
                    <a href="{{ route('trainers.index') }}" class="text-gray-300 hover:bg-gray-700 hover:text-white block px-3 py-2 rounded-md text-base font-medium">Find Trainers</a>

                    <div class="border-t border-gray-700 pt-4 mt-2">
                    @guest
                        <a href="{{ route('login') }}" class="text-gray-300 hover:bg-gray-700 hover:text-white block px-3 py-2 rounded-md text-base font-medium">Login</a>
                        <a href="{{ route('register') }}" class="mt-1 bg-indigo-600 text-white block w-full text-center px-3 py-2 rounded-md text-base font-bold">Register</a>
                    @else
                        <div class="flex items-center px-3 mb-3">
                            <div class="flex-shrink-0"><img class="h-10 w-10 rounded-full" src="{{ 'https://ui-avatars.com/api/?name=' . urlencode(Auth::user()->name) . '&background=random&color=fff' }}" alt=""></div>
                            <div class="ml-3"><div class="text-base font-medium leading-none text-white">{{ Auth::user()->name }}</div></div>
                        </div>
                        @if(Auth::user()->user_type === 'admin')
                            <a href="{{ route('admin.dashboard') }}" class="text-gray-300 hover:bg-gray-700 hover:text-white block px-3 py-2 rounded-md text-base font-medium">Admin Panel</a>
                        @else
                            <a href="{{ route('dashboard') }}" class="text-gray-300 hover:bg-gray-700 hover:text-white block px-3 py-2 rounded-md text-base font-medium">My Dashboard</a>
                        @endif
                        <form action="{{ route('logout') }}" method="POST"> @csrf <button type="submit" class="w-full text-left text-gray-300 hover:bg-gray-700 hover:text-white block px-3 py-2 rounded-md text-base font-medium">Logout</button></form>
                    @endguest
                    </div>
                </div>
            </div>
        </header>

        <!-- EYE-CATCHING USER SIDEBAR (No changes here) -->
        @auth
            <div x-show="userSidebarOpen" @click="userSidebarOpen = false" class="fixed inset-0 bg-gray-900/80 backdrop-blur-sm z-40" x-transition.opacity.duration.300ms style="display: none;"></div>
            <div x-show="userSidebarOpen" class="fixed inset-y-0 right-0 max-w-full flex z-50" style="display: none;">
                <div @click.away="userSidebarOpen = false" class="relative w-screen max-w-sm"
                     x-transition:enter="transform transition ease-in-out duration-300" x-transition:enter-start="translate-x-full" x-transition:enter-end="translate-x-0"
                     x-transition:leave="transform transition ease-in-out duration-300" x-transition:leave-start="translate-x-0" x-transition:leave-end="translate-x-full">
                    <div class="h-full flex flex-col bg-gray-900 text-white shadow-2xl">
                        <div class="p-6 border-b border-gray-700">
                            <div class="flex items-center justify-between">
                                <h2 class="text-lg font-semibold text-white">My Account</h2>
                                <button @click="userSidebarOpen = false" type="button" class="p-1 rounded-full text-gray-400 hover:text-white hover:bg-gray-700 focus:outline-none">
                                    <span class="sr-only">Close panel</span>
                                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" d="M6 18L18 6M6 6l12 12" /></svg>
                                </button>
                            </div>
                        </div>
                        <div class="p-6 text-center border-b border-gray-700">
                            <img class="h-24 w-24 rounded-full mx-auto ring-4 ring-indigo-500/50" src="{{ 'https://ui-avatars.com/api/?name=' . urlencode(Auth::user()->name) . '&background=random&color=fff&size=128' }}" alt="">
                            <h3 class="mt-4 text-xl font-bold">{{ Auth::user()->name }}</h3>
                            <p class="text-sm text-gray-400 capitalize">{{ Auth::user()->user_type }}</p>
                        </div>
                        <nav class="flex-1 p-6 space-y-2">
                            <a href="{{ route('frontpage') }}" class="flex items-center gap-4 px-4 py-3 rounded-lg text-gray-300 hover:bg-gray-800 hover:text-white transition-colors"><svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" /></svg><span>Home</span></a>
                            @if(Auth::user()->user_type === 'admin')
                                <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-4 px-4 py-3 rounded-lg text-gray-300 hover:bg-gray-800 hover:text-white transition-colors"><svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" /></svg><span>Admin Panel</span></a>
                            @else
                                <a href="{{ route('dashboard') }}" class="flex items-center gap-4 px-4 py-3 rounded-lg text-gray-300 hover:bg-gray-800 hover:text-white transition-colors"><svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" /></svg><span>My Dashboard</span></a>
                            @endif
                        </nav>
                        <div class="p-6 border-t border-gray-700">
                            <form action="{{ route('logout') }}" method="POST">
                                @csrf
                                <button type="submit" class="w-full flex items-center justify-center gap-3 bg-red-600/80 hover:bg-red-600 font-bold py-3 rounded-lg transition-colors"><svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" /></svg><span>Logout</span></button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        @endauth

    </div>

    <main>
        @yield('content')
    </main>

    <footer class="bg-gray-800 text-white mt-12">
        <div class="container mx-auto py-8 px-6 text-center">
            <p>© {{ date('Y') }} Fitub. All Rights Reserved.</p>
            <p class="text-sm text-gray-400 mt-1">Your Journey to Fitness Starts Here.</p>
        </div>
    </footer>

    @stack('scripts')
</body>
</html>
