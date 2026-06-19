<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'SpeechIQ') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        
        <style>
            body { font-family: 'Outfit', sans-serif; }

            /* Mobile drawer overlay */
            #mobile-overlay {
                transition: opacity 0.25s ease;
            }
            /* Mobile drawer slide */
            #mobile-drawer {
                transition: transform 0.25s ease;
            }
        </style>
    </head>
    <body class="antialiased bg-slate-900 text-slate-100 min-h-screen flex flex-col">

        <!-- Top Navbar -->
        <nav class="bg-slate-950 border-b border-slate-800 h-16 flex items-center justify-between px-4 md:px-8 sticky top-0 z-30">
            <div class="flex items-center gap-3 md:gap-8">

                <!-- Mobile Hamburger -->
                <button id="btn-mobile-menu" type="button"
                    class="md:hidden p-2 rounded-xl text-slate-400 hover:text-white hover:bg-slate-800 transition"
                    aria-label="Open menu">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                    </svg>
                </button>

                <!-- Logo -->
                <div class="flex items-center gap-2">
                    <svg class="w-8 h-8 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11a7 7 0 01-7 7m0 0a7 7 0 01-7-7m7 7v4m0 0H8m4 0h4m-4-8a3 3 0 01-3-3V5a3 3 0 116 0v6a3 3 0 01-3 3z"/>
                    </svg>
                    <span class="text-xl font-bold tracking-tight text-white bg-gradient-to-r from-indigo-400 to-cyan-400 bg-clip-text text-transparent">SpeechIQ</span>
                </div>

                <!-- Desktop Nav links -->
                <div class="hidden md:flex items-center gap-1">
                    <a href="{{ route('dashboard') }}" class="px-4 py-2 rounded-xl text-sm font-medium transition duration-200 {{ request()->routeIs('dashboard') ? 'bg-indigo-600 text-white' : 'text-slate-400 hover:text-white hover:bg-slate-900' }}">
                        Dashboard
                    </a>
                    <a href="{{ route('practice.read-aloud.index') }}" class="px-4 py-2 rounded-xl text-sm font-medium transition duration-200 {{ request()->routeIs('practice.read-aloud.*') ? 'bg-indigo-600 text-white' : 'text-slate-400 hover:text-white hover:bg-slate-900' }}">
                        Read Aloud
                    </a>
                    <a href="{{ route('practice.interview.index') }}" class="px-4 py-2 rounded-xl text-sm font-medium transition duration-200 {{ request()->routeIs('practice.interview.*') ? 'bg-indigo-600 text-white' : 'text-slate-400 hover:text-white hover:bg-slate-900' }}">
                        Interview
                    </a>
                    <a href="{{ route('practice.reports.index') }}" class="px-4 py-2 rounded-xl text-sm font-medium transition duration-200 {{ request()->routeIs('practice.reports.*') ? 'bg-indigo-600 text-white' : 'text-slate-400 hover:text-white hover:bg-slate-900' }}">
                        Reports
                    </a>
                    @if(auth()->user()->hasRole('admin'))
                        <a href="{{ route('admin.settings') }}" class="px-4 py-2 rounded-xl text-sm font-medium transition duration-200 {{ request()->routeIs('admin.settings') ? 'bg-indigo-600 text-white' : 'text-slate-400 hover:text-white hover:bg-slate-900' }}">
                            Settings
                        </a>
                    @endif
                </div>
            </div>
 
            <!-- Profile & Logout -->
            <div class="flex items-center gap-3">
                <div class="text-right hidden sm:block">
                    <p class="text-sm font-medium text-white">{{ auth()->user()->name }}</p>
                    <p class="text-xs text-slate-500 font-normal">{{ auth()->user()->hasRole('admin') ? 'Admin' : 'Candidate' }}</p>
                </div>
                
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="px-3 md:px-4 py-2 text-xs font-medium text-slate-400 hover:text-white bg-slate-900 border border-slate-800 hover:bg-slate-800 rounded-xl transition duration-200">
                        Log Out
                    </button>
                </form>
            </div>
        </nav>

        <!-- Mobile Drawer Overlay -->
        <div id="mobile-overlay" class="fixed inset-0 z-40 bg-slate-950/70 backdrop-blur-sm hidden opacity-0 md:hidden"
             onclick="closeMobileMenu()"></div>

        <!-- Mobile Drawer -->
        <aside id="mobile-drawer"
               class="fixed inset-y-0 left-0 z-50 w-72 bg-slate-950 border-r border-slate-800 -translate-x-full md:hidden flex flex-col">
            <!-- Drawer Header -->
            <div class="h-16 flex items-center justify-between px-5 border-b border-slate-800">
                <div class="flex items-center gap-2">
                    <svg class="w-7 h-7 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11a7 7 0 01-7 7m0 0a7 7 0 01-7-7m7 7v4m0 0H8m4 0h4m-4-8a3 3 0 01-3-3V5a3 3 0 116 0v6a3 3 0 01-3 3z"/>
                    </svg>
                    <span class="text-lg font-bold text-white bg-gradient-to-r from-indigo-400 to-cyan-400 bg-clip-text text-transparent">SpeechIQ</span>
                </div>
                <button onclick="closeMobileMenu()" class="p-2 rounded-xl text-slate-400 hover:text-white hover:bg-slate-800 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <!-- User Info -->
            <div class="px-5 py-4 border-b border-slate-800/60">
                <p class="text-sm font-semibold text-white">{{ auth()->user()->name }}</p>
                <p class="text-xs text-slate-500 mt-0.5">{{ auth()->user()->email }}</p>
                <span class="inline-block mt-2 text-[10px] px-2 py-0.5 rounded-full bg-indigo-950/50 border border-indigo-900 text-indigo-400 font-semibold uppercase tracking-wider">
                    {{ auth()->user()->hasRole('admin') ? 'Admin' : 'Candidate' }}
                </span>
            </div>

            <!-- Drawer Nav Links -->
            <nav class="flex-1 px-4 py-5 space-y-1">
                <a href="{{ route('dashboard') }}" onclick="closeMobileMenu()"
                   class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium transition duration-200 {{ request()->routeIs('dashboard') ? 'bg-indigo-600 text-white' : 'text-slate-400 hover:text-white hover:bg-slate-900' }}">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                    </svg>
                    Dashboard
                </a>
                <a href="{{ route('practice.read-aloud.index') }}" onclick="closeMobileMenu()"
                   class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium transition duration-200 {{ request()->routeIs('practice.read-aloud.*') ? 'bg-indigo-600 text-white' : 'text-slate-400 hover:text-white hover:bg-slate-900' }}">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                    </svg>
                    Read Aloud
                </a>
                <a href="{{ route('practice.interview.index') }}" onclick="closeMobileMenu()"
                   class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium transition duration-200 {{ request()->routeIs('practice.interview.*') ? 'bg-indigo-600 text-white' : 'text-slate-400 hover:text-white hover:bg-slate-900' }}">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11a7 7 0 01-7 7m0 0a7 7 0 01-7-7m7 7v4m0 0H8m4 0h4m-4-8a3 3 0 01-3-3V5a3 3 0 116 0v6a3 3 0 01-3 3z"/>
                    </svg>
                    AI Interview
                </a>
                <a href="{{ route('practice.reports.index') }}" onclick="closeMobileMenu()"
                   class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium transition duration-200 {{ request()->routeIs('practice.reports.*') ? 'bg-indigo-600 text-white' : 'text-slate-400 hover:text-white hover:bg-slate-900' }}">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    Reports
                </a>
                @if(auth()->user()->hasRole('admin'))
                <a href="{{ route('admin.settings') }}" onclick="closeMobileMenu()"
                   class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium transition duration-200 text-slate-400 hover:text-white hover:bg-slate-900">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                    Admin Settings
                </a>
                @endif
            </nav>

            <!-- Drawer Logout -->
            <div class="p-4 border-t border-slate-800">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="w-full flex items-center justify-center gap-2 px-4 py-2.5 text-xs font-semibold text-rose-400 bg-rose-950/20 border border-rose-900/40 hover:bg-rose-950/40 rounded-xl transition duration-200">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                        </svg>
                        Log Out
                    </button>
                </form>
            </div>
        </aside>

        <!-- Main Workspace -->
        <main class="flex-1 overflow-y-auto p-4 md:p-8">
            @if (session('success'))
                <div class="max-w-7xl mx-auto mb-6 p-4 bg-emerald-950/50 border border-emerald-900 rounded-xl text-emerald-400 text-sm flex items-center">
                    <svg class="w-5 h-5 mr-3 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    {{ session('success') }}
                </div>
            @endif

            @if (session('error'))
                <div class="max-w-7xl mx-auto mb-6 p-4 bg-rose-950/50 border border-rose-900 rounded-xl text-rose-400 text-sm flex items-center">
                    <svg class="w-5 h-5 mr-3 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    {{ session('error') }}
                </div>
            @endif

            <div class="max-w-7xl mx-auto">
                {{ $slot }}
            </div>
        </main>

        <!-- Mobile Menu JS -->
        <script>
            const mobileDrawer = document.getElementById('mobile-drawer');
            const mobileOverlay = document.getElementById('mobile-overlay');
            const btnMobileMenu = document.getElementById('btn-mobile-menu');

            function openMobileMenu() {
                mobileDrawer.classList.remove('-translate-x-full');
                mobileOverlay.classList.remove('hidden', 'opacity-0');
                document.body.style.overflow = 'hidden';
            }

            function closeMobileMenu() {
                mobileDrawer.classList.add('-translate-x-full');
                mobileOverlay.classList.add('opacity-0');
                setTimeout(() => mobileOverlay.classList.add('hidden'), 250);
                document.body.style.overflow = '';
            }

            btnMobileMenu.addEventListener('click', openMobileMenu);
        </script>
    </body>
</html>
