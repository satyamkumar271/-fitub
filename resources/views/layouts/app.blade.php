<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('meta_title', 'Fitub - Your Fitness Partner')</title>
    <meta name="description" content="@yield('meta_description', 'Find verified gyms, trainers, and fitness resources on Fitub.')">
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        html { scroll-behavior: smooth; }
        [x-cloak] { display: none !important; }
    </style>
    @stack('styles')
</head>
<body class="bg-gray-50 font-sans">
    @php($hasAppSidebar = auth()->check() && !request()->routeIs('frontpage'))
    @php($showGuestBackToHome = !auth()->check() && !request()->routeIs('frontpage'))

    <div
        x-data="{
            mobileMenuOpen: false,
            appSidebarOpen: false,
            init() { this.appSidebarOpen = window.innerWidth >= 1024; }
        }"
        @keydown.escape.window="mobileMenuOpen = false; appSidebarOpen = false"
        @resize.window="if (window.innerWidth >= 1024) appSidebarOpen = true"
    >
        @if($hasAppSidebar)
            <div x-cloak x-show="appSidebarOpen" x-transition.opacity @click="appSidebarOpen = false" class="fixed inset-0 bg-black/40 z-40 lg:hidden"></div>
            <aside class="fixed inset-y-0 left-0 z-50 w-64 bg-gray-900 text-gray-300 transform transition-transform duration-300 ease-in-out"
                   :class="appSidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'">
                <div class="flex items-center justify-center h-20 border-b border-gray-800">
                    <a href="{{ Auth::user()->user_type === 'admin' ? route('admin.dashboard') : route('dashboard') }}" class="text-2xl font-bold tracking-wider text-white">
                        <span class="text-indigo-400">FIT</span>UB
                    </a>
                </div>
                <nav class="mt-6 px-4 space-y-2">
                    <a href="{{ route('frontpage') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-lg transition-colors {{ request()->routeIs('frontpage') ? 'bg-gray-800 text-white' : 'hover:bg-gray-800 hover:text-white' }}">
                        <span>Home</span>
                    </a>

                    @if(Auth::user()->user_type === 'admin')
                        <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-lg transition-colors {{ request()->routeIs('admin.dashboard') ? 'bg-gray-800 text-white' : 'hover:bg-gray-800 hover:text-white' }}">
                            <span>Dashboard</span>
                        </a>
                        <a href="{{ route('admin.users.index') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-lg transition-colors {{ request()->routeIs('admin.users.*') ? 'bg-gray-800 text-white' : 'hover:bg-gray-800 hover:text-white' }}">
                            <span>Manage Users</span>
                        </a>
                        <a href="{{ route('admin.pending') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-lg transition-colors {{ request()->routeIs('admin.pending') ? 'bg-gray-800 text-white' : 'hover:bg-gray-800 hover:text-white' }}">
                            <span>Pending Approvals</span>
                        </a>
                        <a href="{{ route('admin.inquiries.index') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-lg transition-colors {{ request()->routeIs('admin.inquiries.*') ? 'bg-gray-800 text-white' : 'hover:bg-gray-800 hover:text-white' }}">
                            <span>Customer Inquiries</span>
                        </a>
                        <a href="{{ route('admin.payments.index') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-lg transition-colors {{ request()->routeIs('admin.payments.*') ? 'bg-gray-800 text-white' : 'hover:bg-gray-800 hover:text-white' }}">
                            <span>Payments</span>
                        </a>
                        <a href="{{ route('admin.reports.index') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-lg transition-colors {{ request()->routeIs('admin.reports.*') ? 'bg-gray-800 text-white' : 'hover:bg-gray-800 hover:text-white' }}">
                            <span>Reports</span>
                        </a>
                        <a href="{{ route('admin.blogs.index') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-lg transition-colors {{ request()->routeIs('admin.blogs.*') ? 'bg-gray-800 text-white' : 'hover:bg-gray-800 hover:text-white' }}">
                            <span>Blogs</span>
                        </a>
                        <a href="{{ route('admin.credits.index') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-lg transition-colors {{ request()->routeIs('admin.credits.*') ? 'bg-gray-800 text-white' : 'hover:bg-gray-800 hover:text-white' }}">
                            <span>Credit History</span>
                        </a>
                        <a href="{{ route('admin.support.index') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-lg transition-colors {{ request()->routeIs('admin.support.*') ? 'bg-gray-800 text-white' : 'hover:bg-gray-800 hover:text-white' }}">
                            <span>Support Team</span>
                        </a>
                    @else
                        <a href="{{ route('dashboard') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-lg transition-colors {{ request()->routeIs('dashboard') ? 'bg-gray-800 text-white' : 'hover:bg-gray-800 hover:text-white' }}">
                            <span>My Dashboard</span>
                        </a>

                        @if(in_array(Auth::user()->user_type, ['trainer', 'gymowner']))
                            <a href="{{ route('dashboard.leads') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-lg transition-colors {{ request()->routeIs('dashboard.leads') ? 'bg-gray-800 text-white' : 'hover:bg-gray-800 hover:text-white' }}">
                                <span>Leads & Inquiries</span>
                                @if(($sidebarUnreadCount ?? 0) > 0)
                                    <span class="ml-auto text-xs font-bold bg-red-500 text-white px-2 py-0.5 rounded-full">{{ $sidebarUnreadCount }}</span>
                                @endif
                            </a>
                            <a href="{{ route('dashboard.payments') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-lg transition-colors {{ request()->routeIs('dashboard.payments') ? 'bg-gray-800 text-white' : 'hover:bg-gray-800 hover:text-white' }}">
                                <span>My Payments</span>
                            </a>
                            <a href="{{ route('inquiries.conversations') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-lg transition-colors {{ request()->routeIs('inquiries.conversations') ? 'bg-gray-800 text-white' : 'hover:bg-gray-800 hover:text-white' }}">
                                <span>All Chats</span>
                            </a>
                        @else
                            <a href="{{ route('inquiries.mine') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-lg transition-colors {{ request()->routeIs('inquiries.mine') ? 'bg-gray-800 text-white' : 'hover:bg-gray-800 hover:text-white' }}">
                                <span>My Inquiries</span>
                                @if(($sidebarUnreadCount ?? 0) > 0)
                                    <span class="ml-auto text-xs font-bold bg-red-500 text-white px-2 py-0.5 rounded-full">{{ $sidebarUnreadCount }}</span>
                                @endif
                            </a>
                            <a href="{{ route('inquiries.conversations') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-lg transition-colors {{ request()->routeIs('inquiries.conversations') ? 'bg-gray-800 text-white' : 'hover:bg-gray-800 hover:text-white' }}">
                                <span>All Chats</span>
                            </a>
                        @endif
                        <a href="{{ route('support.index') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-lg transition-colors {{ request()->routeIs('support.*') ? 'bg-gray-800 text-white' : 'hover:bg-gray-800 hover:text-white' }}">
                            <span>Support Team</span>
                        </a>
                    @endif
                </nav>

                <div class="mt-auto p-4 border-t border-gray-800">
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit" class="w-full text-left px-4 py-2.5 rounded-lg text-red-400 hover:bg-red-900/40 hover:text-red-300 transition-colors">
                            Logout
                        </button>
                    </form>
                </div>
            </aside>
        @endif

        <div class="{{ $hasAppSidebar ? 'lg:pl-64' : '' }} min-h-screen flex flex-col">
            <header class="bg-gray-900 shadow-lg sticky top-0 z-30 text-white">
                <nav class="container mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="flex items-center justify-between h-20">
                        <div class="w-1/3">
                            <a href="{{ route('frontpage') }}" class="text-3xl font-extrabold tracking-tight text-white">Fitub</a>
                        </div>

                        @if(request()->routeIs('frontpage'))
                            <div class="hidden md:flex w-1/3 justify-center items-baseline space-x-6">
                                <a href="{{ route('blog.index') }}" class="text-gray-300 hover:text-white transition-colors text-base font-medium">Blog</a>
                                <a href="{{ route('about') }}" class="text-gray-300 hover:text-white transition-colors text-base font-medium">About Us</a>
                                <a href="{{ route('contact') }}" class="text-gray-300 hover:text-white transition-colors text-base font-medium">Contact</a>
                            </div>
                        @else
                            <div class="hidden md:block w-1/3"></div>
                        @endif

                        <div class="hidden md:flex w-1/3 justify-end items-center">
                            @guest
                                <div class="space-x-4">
                                    @if($showGuestBackToHome)
                                        <a href="{{ route('frontpage') }}" class="text-gray-300 hover:text-white px-3 py-2 rounded-md text-sm font-medium">Back to Home</a>
                                    @endif
                                    <a href="{{ route('login') }}" class="text-gray-300 hover:text-white px-3 py-2 rounded-md text-sm font-medium">Login</a>
                                    <a href="{{ route('register') }}" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-md text-sm font-bold transition-colors shadow-md">Register</a>
                                </div>
                            @else
                                <div class="flex items-center space-x-3 text-sm">
                                    @if(request()->routeIs('frontpage'))
                                        <a href="{{ Auth::user()->user_type === 'admin' ? route('admin.dashboard') : route('dashboard') }}"
                                           class="text-gray-300 hover:text-white px-3 py-2 rounded-md text-sm font-medium">
                                            Dashboard
                                        </a>
                                    @endif
                                    <span class="font-medium text-white">{{ Auth::user()->name }}</span>
                                    <img class="h-10 w-10 rounded-full object-cover ring-2 ring-offset-2 ring-offset-gray-900 ring-indigo-500" src="{{ 'https://ui-avatars.com/api/?name=' . urlencode(Auth::user()->name) . '&background=random&color=fff' }}" alt="{{ Auth::user()->name }}">
                                </div>
                            @endguest
                        </div>

                        <div class="flex md:hidden">
                            @if($hasAppSidebar)
                                <button @click="appSidebarOpen = !appSidebarOpen" type="button" class="mr-2 inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-white hover:bg-gray-700 focus:outline-none">
                                    <span class="sr-only">Open sidebar</span>
                                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" /></svg>
                                </button>
                            @endif
                            <button @click="mobileMenuOpen = !mobileMenuOpen" type="button" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-white hover:bg-gray-700 focus:outline-none">
                                <span class="sr-only">Open main menu</span>
                                <svg :class="{'hidden': mobileMenuOpen, 'block': !mobileMenuOpen }" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" /></svg>
                                <svg :class="{'hidden': !mobileMenuOpen, 'block': mobileMenuOpen }" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
                            </button>
                        </div>
                    </div>
                </nav>

                <div x-show="mobileMenuOpen" @click.away="mobileMenuOpen = false" class="md:hidden" x-transition>
                    <div class="px-2 pt-2 pb-3 space-y-1 sm:px-3 bg-gray-900">
                        @if(request()->routeIs('frontpage'))
                            <a href="{{ route('blog.index') }}" class="text-gray-300 hover:bg-gray-700 hover:text-white block px-3 py-2 rounded-md text-base font-medium">Blog</a>
                            <a href="{{ route('about') }}" class="text-gray-300 hover:bg-gray-700 hover:text-white block px-3 py-2 rounded-md text-base font-medium">About Us</a>
                            <a href="{{ route('contact') }}" class="text-gray-300 hover:bg-gray-700 hover:text-white block px-3 py-2 rounded-md text-base font-medium">Contact</a>
                            <a href="{{ route('gyms.index') }}" class="text-gray-300 hover:bg-gray-700 hover:text-white block px-3 py-2 rounded-md text-base font-medium">Find Gyms</a>
                            <a href="{{ route('trainers.index') }}" class="text-gray-300 hover:bg-gray-700 hover:text-white block px-3 py-2 rounded-md text-base font-medium">Find Trainers</a>
                        @elseif($showGuestBackToHome)
                            <a href="{{ route('frontpage') }}" class="text-gray-300 hover:bg-gray-700 hover:text-white block px-3 py-2 rounded-md text-base font-medium">Back to Home</a>
                        @endif

                        <div class="border-t border-gray-700 pt-4 mt-2">
                        @guest
                            <a href="{{ route('login') }}" class="text-gray-300 hover:bg-gray-700 hover:text-white block px-3 py-2 rounded-md text-base font-medium">Login</a>
                            <a href="{{ route('register') }}" class="mt-1 bg-indigo-600 text-white block w-full text-center px-3 py-2 rounded-md text-base font-bold">Register</a>
                        @else
                            @if(request()->routeIs('frontpage'))
                                <a href="{{ Auth::user()->user_type === 'admin' ? route('admin.dashboard') : route('dashboard') }}" class="text-gray-300 hover:bg-gray-700 hover:text-white block px-3 py-2 rounded-md text-base font-medium">Dashboard</a>
                            @endif
                            <form action="{{ route('logout') }}" method="POST">
                                @csrf
                                <button type="submit" class="w-full text-left text-gray-300 hover:bg-gray-700 hover:text-white block px-3 py-2 rounded-md text-base font-medium">Logout</button>
                            </form>
                        @endguest
                        </div>
                    </div>
                </div>
            </header>

            <main class="flex-1">
                @yield('content')
            </main>

            <footer class="bg-gray-800 text-white">
                <div class="container mx-auto py-8 px-6 text-center">
                    <p>&copy; {{ date('Y') }} Fitub. All Rights Reserved.</p>
                    <p class="text-sm text-gray-400 mt-1">Your Journey to Fitness Starts Here.</p>
                    <div class="mt-4 flex flex-wrap justify-center gap-x-4 gap-y-2 text-sm">
                        <a href="{{ route('faq') }}" class="text-gray-300 hover:text-white">FAQ</a>
                        <a href="{{ route('privacy') }}" class="text-gray-300 hover:text-white">Privacy Policy</a>
                        <a href="{{ route('terms') }}" class="text-gray-300 hover:text-white">Terms & Conditions</a>
                        <a href="{{ route('refund') }}" class="text-gray-300 hover:text-white">Refund Policy</a>
                    </div>
                </div>
            </footer>
        </div>
    </div>

    @stack('scripts')
</body>
</html>
