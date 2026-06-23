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
        <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        
        <style>
            body { font-family: 'Outfit', sans-serif; }

            /* Mobile View (default) */
            #user-sidebar {
                position: fixed !important;
                top: 0 !important;
                bottom: 0 !important;
                left: 0 !important;
                z-index: 50 !important;
                width: 16rem !important;
                transform: translateX(-100%) !important;
                transition: transform 0.25s ease !important;
                display: flex !important;
            }
            #user-sidebar.open {
                transform: translateX(0) !important;
            }
            #user-overlay {
                position: fixed !important;
                inset: 0 !important;
                z-index: 40 !important;
                background-color: rgba(2, 6, 23, 0.7) !important;
                backdrop-filter: blur(4px) !important;
                display: none !important;
                opacity: 0;
                transition: opacity 0.25s ease !important;
            }
            #user-overlay.open {
                display: block !important;
                opacity: 1 !important;
            }
            #btn-sidebar-open {
                display: block !important;
            }
            .lg\:hidden {
                display: block !important;
            }

            /* Desktop View (>= 1024px) */
            @media (min-width: 1024px) {
                #user-sidebar {
                    position: relative !important;
                    transform: translateX(0) !important;
                    display: flex !important;
                }
                #user-overlay {
                    display: none !important;
                }
                #btn-sidebar-open {
                    display: none !important;
                }
                .lg\:hidden {
                    display: none !important;
                }
            }
        </style>
    </head>
    <body class="antialiased bg-slate-900 text-slate-100 min-h-screen flex">

        <!-- Mobile Overlay -->
        <div id="user-overlay"
             class="fixed inset-0 z-40 bg-slate-950/70 backdrop-blur-sm hidden opacity-0 lg:hidden"
             onclick="closeSidebar()"></div>

        <!-- Sidebar -->
        <aside id="user-sidebar"
               class="fixed lg:relative inset-y-0 left-0 z-50 w-64 bg-slate-950 border-r border-slate-800 flex flex-col shrink-0 -translate-x-full lg:translate-x-0">
            <!-- Logo -->
            <div class="h-16 flex items-center justify-between px-5 border-b border-slate-800 gap-2">
                <div class="flex items-center gap-2">
                    <svg class="w-8 h-8 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11a7 7 0 01-7 7m0 0a7 7 0 01-7-7m7 7v4m0 0H8m4 0h4m-4-8a3 3 0 01-3-3V5a3 3 0 116 0v6a3 3 0 01-3 3z"/>
                    </svg>
                    <span class="text-xl font-bold tracking-tight bg-gradient-to-r from-indigo-400 to-cyan-400 bg-clip-text text-transparent">SpeechIQ</span>
                </div>
                <!-- Close button (mobile only) -->
                <button onclick="closeSidebar()" class="lg:hidden p-2 rounded-xl text-slate-400 hover:text-white hover:bg-slate-800 transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <!-- Navigation Links -->
            <nav class="flex-1 px-4 py-6 space-y-1">
                <a href="{{ route('dashboard') }}" onclick="closeSidebar()"
                   class="flex items-center px-4 py-3 text-sm font-medium rounded-xl transition-all duration-200 {{ request()->routeIs('dashboard') ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-600/10' : 'text-slate-400 hover:bg-slate-900 hover:text-white' }}">
                    <svg class="w-5 h-5 mr-3 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                    </svg>
                    Dashboard
                </a>

                <a href="{{ route('practice.read-aloud.index') }}" onclick="closeSidebar()"
                   class="flex items-center px-4 py-3 text-sm font-medium rounded-xl transition-all duration-200 {{ request()->routeIs('practice.read-aloud.*') ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-600/10' : 'text-slate-400 hover:bg-slate-900 hover:text-white' }}">
                    <svg class="w-5 h-5 mr-3 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                    </svg>
                    Read Aloud
                </a>

                <a href="{{ route('practice.interview.index') }}" onclick="closeSidebar()"
                   class="flex items-center px-4 py-3 text-sm font-medium rounded-xl transition-all duration-200 {{ request()->routeIs('practice.interview.*') ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-600/10' : 'text-slate-400 hover:bg-slate-900 hover:text-white' }}">
                    <svg class="w-5 h-5 mr-3 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 9l3 3-3 3m5 0h3M5 20h14a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                    AI Interview
                </a>

                <a href="{{ route('practice.reports.index') }}" onclick="closeSidebar()"
                   class="flex items-center px-4 py-3 text-sm font-medium rounded-xl transition-all duration-200 {{ request()->routeIs('practice.reports.*') ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-600/10' : 'text-slate-400 hover:bg-slate-900 hover:text-white' }}">
                    <svg class="w-5 h-5 mr-3 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    Reports
                </a>

                @if(auth()->user()->hasRole('admin'))
                <a href="{{ route('admin.settings') }}" onclick="closeSidebar()"
                   class="flex items-center px-4 py-3 text-sm font-medium rounded-xl transition-all duration-200 {{ request()->routeIs('admin.settings') ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-600/10' : 'text-slate-400 hover:bg-slate-900 hover:text-white' }}">
                    <svg class="w-5 h-5 mr-3 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                    Admin Settings
                </a>
                @endif
            </nav>

            <!-- Bottom Profile Panel -->
            <div class="p-4 border-t border-slate-800 bg-slate-950/80">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-indigo-650 flex items-center justify-center font-bold text-white text-sm shrink-0 border border-indigo-500/20 shadow-inner">
                        {{ substr(auth()->user()->name, 0, 2) }}
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-semibold text-white truncate">{{ auth()->user()->name }}</p>
                        <p class="text-xs text-slate-550 truncate mt-0.5">{{ auth()->user()->email }}</p>
                    </div>
                </div>
                <form method="POST" action="{{ route('logout') }}" class="mt-4">
                    @csrf
                    <button type="submit" class="w-full flex items-center justify-center gap-2 px-4 py-2.5 text-xs font-semibold text-rose-450 bg-rose-950/20 border border-rose-950/30 hover:bg-rose-950/40 rounded-xl transition duration-200">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                        </svg>
                        Log Out
                    </button>
                </form>
            </div>
        </aside>

        <!-- Main Content Area -->
        <div class="flex-1 flex flex-col min-w-0">
            <!-- Header -->
            <header class="h-16 border-b border-slate-800 bg-slate-950/50 backdrop-blur flex items-center justify-between px-4 md:px-8 sticky top-0 z-30">
                <div class="flex items-center gap-4 min-w-0">
                    <!-- Mobile sidebar toggle -->
                    <button id="btn-sidebar-open" onclick="openSidebar()"
                            class="lg:hidden p-2 rounded-xl text-slate-400 hover:text-white hover:bg-slate-800 transition shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                        </svg>
                    </button>
                    <div class="min-w-0">
                        <h2 class="text-sm md:text-lg font-bold text-white tracking-tight truncate">
                            @if(request()->routeIs('dashboard'))
                                Dashboard
                            @elseif(request()->routeIs('practice.read-aloud.*'))
                                Read Aloud Assessment
                            @elseif(request()->routeIs('practice.interview.*'))
                                Conversational AI Interview
                            @elseif(request()->routeIs('practice.reports.*'))
                                Performance Reports
                            @else
                                SpeechIQ Portal
                            @endif
                        </h2>
                    </div>
                </div>
                <div class="flex items-center gap-4">
                    <span class="hidden sm:inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-indigo-950/50 text-indigo-400 border border-indigo-900">
                        <span class="w-2 h-2 mr-2 bg-indigo-400 rounded-full animate-pulse"></span>
                        AI Assessment Engine Active
                    </span>
                </div>
            </header>

            <!-- Main Content Slot -->
            <main class="flex-1 overflow-y-auto p-4 md:p-8">
                @if (session('success'))
                    <div id="alert-success" class="max-w-7xl mx-auto mb-6 p-4 bg-emerald-950/50 border border-emerald-900 rounded-xl text-emerald-400 text-sm flex items-center justify-between">
                        <div class="flex items-center">
                            <svg class="w-5 h-5 mr-3 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            {{ session('success') }}
                        </div>
                        <button onclick="$('#alert-success').remove()" class="text-slate-400 hover:text-white ml-4">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>
                @endif

                @if (session('error'))
                    <div id="alert-error" class="max-w-7xl mx-auto mb-6 p-4 bg-rose-950/50 border border-rose-900 rounded-xl text-rose-400 text-sm flex items-center justify-between">
                        <div class="flex items-center">
                            <svg class="w-5 h-5 mr-3 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            {{ session('error') }}
                        </div>
                        <button onclick="$('#alert-error').remove()" class="text-slate-400 hover:text-white ml-4">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>
                @endif

                <div class="max-w-7xl mx-auto">
                    {{ $slot }}
                </div>
            </main>
        </div>

        <!-- Sidebar Toggle JS -->
        <script>
            function openSidebar() {
                $('#user-sidebar').addClass('open');
                $('#user-overlay').addClass('open');
                $('body').css('overflow', 'hidden');
            }

            function closeSidebar() {
                $('#user-sidebar').removeClass('open');
                $('#user-overlay').removeClass('open');
                $('body').css('overflow', '');
            }
        </script>
    </body>
</html>
