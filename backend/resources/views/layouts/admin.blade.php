<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>{{ config('app.name', 'SpeechIQ Admin') }}</title>

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
            #admin-sidebar {
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
            #admin-sidebar.open {
                transform: translateX(0) !important;
            }
            #admin-overlay {
                position: fixed !important;
                inset: 0 !important;
                z-index: 40 !important;
                background-color: rgba(2, 6, 23, 0.7) !important;
                backdrop-filter: blur(4px) !important;
                display: none !important;
                opacity: 0;
                transition: opacity 0.25s ease !important;
            }
            #admin-overlay.open {
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
                #admin-sidebar {
                    position: relative !important;
                    transform: translateX(0) !important;
                    display: flex !important;
                }
                #admin-overlay {
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
        <div id="admin-overlay"
             class="fixed inset-0 z-40 bg-slate-950/70 backdrop-blur-sm hidden opacity-0 lg:hidden"
             onclick="closeSidebar()"></div>

        <!-- Sidebar -->
        <aside id="admin-sidebar"
               class="fixed lg:relative inset-y-0 left-0 z-50 w-64 bg-slate-950 border-r border-slate-800 flex flex-col shrink-0 -translate-x-full lg:translate-x-0">
            <!-- Logo -->
            <div class="h-16 flex items-center justify-between px-5 border-b border-slate-800 gap-2">
                <div class="flex items-center gap-2">
                    <svg class="w-8 h-8 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11a7 7 0 01-7 7m0 0a7 7 0 01-7-7m7 7v4m0 0H8m4 0h4m-4-8a3 3 0 01-3-3V5a3 3 0 116 0v6a3 3 0 01-3 3z"/>
                    </svg>
                    <span class="text-xl font-bold tracking-tight bg-gradient-to-r from-indigo-400 to-cyan-400 bg-clip-text text-transparent">SpeechIQ Admin</span>
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
                <a href="{{ route('admin.dashboard') }}" onclick="closeSidebar()"
                   class="flex items-center px-4 py-3 text-sm font-medium rounded-xl transition-all duration-200 {{ request()->routeIs('admin.dashboard') ? 'bg-indigo-600 text-white' : 'text-slate-400 hover:bg-slate-900 hover:text-white' }}">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/>
                    </svg>
                    Dashboard
                </a>

                <a href="{{ route('admin.settings') }}" onclick="closeSidebar()"
                   class="flex items-center px-4 py-3 text-sm font-medium rounded-xl transition-all duration-200 {{ request()->routeIs('admin.settings') ? 'bg-indigo-600 text-white' : 'text-slate-400 hover:bg-slate-900 hover:text-white' }}">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                    Settings
                </a>

                <div class="pt-4 pb-2 px-4 text-xs font-semibold text-slate-500 uppercase tracking-wider">Modules</div>

                <a href="{{ route('admin.tests.index') }}" onclick="closeSidebar()"
                   class="flex items-center px-4 py-3 text-sm font-medium rounded-xl transition-all duration-200 {{ request()->routeIs('admin.tests.*') ? 'bg-indigo-600 text-white' : 'text-slate-400 hover:bg-slate-900 hover:text-white' }}">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 114 0"/>
                    </svg>
                    Tests
                </a>
 
                <a href="{{ route('admin.reports.index') }}" onclick="closeSidebar()"
                   class="flex items-center px-4 py-3 text-sm font-medium rounded-xl transition-all duration-200 {{ request()->routeIs('admin.reports.*') ? 'bg-indigo-600 text-white' : 'text-slate-400 hover:bg-slate-900 hover:text-white' }}">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    Reports
                </a>
            </nav>

            <!-- Bottom Profile Panel -->
            <div class="p-4 border-t border-slate-800 bg-slate-950">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full bg-indigo-600 flex items-center justify-center font-semibold text-white text-sm shrink-0">
                        {{ substr(auth()->user()->name, 0, 2) }}
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-white truncate">{{ auth()->user()->name }}</p>
                        <p class="text-xs text-slate-500 truncate">{{ auth()->user()->email }}</p>
                    </div>
                </div>
                <form method="POST" action="{{ route('logout') }}" class="mt-4">
                    @csrf
                    <button type="submit" class="w-full flex items-center justify-center gap-2 px-4 py-2 text-xs font-medium text-rose-400 bg-rose-950/20 border border-rose-950 hover:bg-rose-950/40 rounded-xl transition duration-200">
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
                <div class="flex items-center gap-4">
                    <!-- Mobile sidebar toggle -->
                    <button id="btn-sidebar-open" onclick="openSidebar()"
                            class="lg:hidden p-2 rounded-xl text-slate-400 hover:text-white hover:bg-slate-800 transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                        </svg>
                    </button>
                    <div>
                        @isset($header)
                            {{ $header }}
                        @else
                            <h2 class="text-base md:text-lg font-semibold text-white">Admin Dashboard</h2>
                        @endisset
                    </div>
                </div>
                <div class="flex items-center gap-4">
                    <span class="hidden sm:inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-emerald-950/50 text-emerald-400 border border-emerald-900">
                        <span class="w-2 h-2 mr-2 bg-emerald-400 rounded-full animate-pulse"></span>
                        AI Engine: Active
                    </span>
                </div>
            </header>

            <!-- Main Content Slot -->
            <main class="flex-1 overflow-y-auto p-4 md:p-8">
                @if (session('success'))
                    <div id="alert-success" class="mb-6 p-4 bg-emerald-500/10 border-l-4 border-emerald-500 border border-emerald-500/20 rounded-xl text-emerald-300 text-sm flex items-center justify-between shadow-lg shadow-emerald-950/20 backdrop-blur-sm transition-all duration-300">
                        <div class="flex items-center">
                            <span class="p-2 bg-emerald-500/25 text-emerald-400 rounded-lg mr-3 shadow-inner">
                                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </span>
                            <div>
                                <span class="font-bold text-white block mb-0.5">Success</span>
                                <span>{{ session('success') }}</span>
                            </div>
                        </div>
                        <button onclick="$('#alert-success').remove()" class="p-1.5 hover:bg-emerald-500/20 text-emerald-450 hover:text-white rounded-lg transition-colors ml-4 shrink-0">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>
                @endif

                @if (session('error'))
                    <div id="alert-error" class="mb-6 p-4 bg-rose-500/10 border-l-4 border-rose-500 border border-rose-500/20 rounded-xl text-rose-300 text-sm flex items-center justify-between shadow-lg shadow-rose-950/20 backdrop-blur-sm transition-all duration-300">
                        <div class="flex items-center">
                            <span class="p-2 bg-rose-500/25 text-rose-400 rounded-lg mr-3 shadow-inner">
                                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </span>
                            <div>
                                <span class="font-bold text-white block mb-0.5">Error</span>
                                <span>{{ session('error') }}</span>
                            </div>
                        </div>
                        <button onclick="$('#alert-error').remove()" class="p-1.5 hover:bg-rose-500/20 text-rose-450 hover:text-white rounded-lg transition-colors ml-4 shrink-0">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>
                @endif

                @if ($errors->any())
                    <div id="alert-validation" class="mb-6 p-4 bg-rose-500/10 border-l-4 border-rose-500 border border-rose-500/20 rounded-xl text-rose-300 text-sm flex items-start justify-between shadow-lg shadow-rose-950/20 backdrop-blur-sm transition-all duration-300">
                        <div class="flex items-start">
                            <span class="p-2 bg-rose-500/25 text-rose-400 rounded-lg mr-3 shadow-inner mt-0.5">
                                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                                </svg>
                            </span>
                            <div>
                                <span class="font-bold text-white block mb-1">Validation Errors</span>
                                <ul class="list-disc list-inside space-y-1 text-rose-200">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                        <button onclick="$('#alert-validation').remove()" class="p-1.5 hover:bg-rose-500/20 text-rose-450 hover:text-white rounded-lg transition-colors ml-4 shrink-0">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>
                @endif

                {{ $slot }}
            </main>
        </div>

        <!-- Sidebar JS -->
        <script>
            function openSidebar() {
                $('#admin-sidebar').addClass('open');
                $('#admin-overlay').addClass('open');
                $('body').css('overflow', 'hidden');
            }

            function closeSidebar() {
                $('#admin-sidebar').removeClass('open');
                $('#admin-overlay').removeClass('open');
                $('body').css('overflow', '');
            }
        </script>
    </body>
</html>
