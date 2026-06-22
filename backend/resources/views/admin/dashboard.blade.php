<x-admin-layout>
    <x-slot name="header">
        <div>
            <h2 class="text-xl font-bold leading-tight text-white">Admin Dashboard</h2>
            <p class="text-sm text-slate-400 mt-1">Overview of system health, usage, and AI integrations.</p>
        </div>
    </x-slot>

    <!-- Metrics Grid Row -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- Users Aggregate -->
        <div class="group relative overflow-hidden bg-slate-900/40 backdrop-blur border border-slate-800/80 rounded-2xl p-6 transition-all duration-300 hover:-translate-y-1 hover:border-indigo-500/30 hover:shadow-xl hover:shadow-indigo-500/5">
            <!-- Ambient Light Glow -->
            <div class="absolute -top-10 -right-10 w-24 h-24 bg-indigo-500/10 rounded-full blur-xl transition-all duration-500 group-hover:scale-150 animate-pulse"></div>
            
            <div class="flex items-center justify-between relative z-10">
                <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Total Users</span>
                <span class="p-3 bg-gradient-to-br from-indigo-500/20 to-indigo-500/5 text-indigo-400 border border-indigo-500/20 rounded-xl transition-all duration-300 group-hover:scale-110 group-hover:border-indigo-500/40">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z">
                        </path>
                    </svg>
                </span>
            </div>
            <div class="mt-4 relative z-10">
                <h3 class="text-3xl font-black text-white font-mono tracking-tight">{{ $totalUsers }}</h3>
                <p class="text-[10px] text-slate-500 mt-1">Registered accounts</p>
            </div>
        </div>

        <!-- Tests Aggregate -->
        <div class="group relative overflow-hidden bg-slate-900/40 backdrop-blur border border-slate-800/80 rounded-2xl p-6 transition-all duration-300 hover:-translate-y-1 hover:border-cyan-500/30 hover:shadow-xl hover:shadow-cyan-500/5">
            <!-- Ambient Light Glow -->
            <div class="absolute -top-10 -right-10 w-24 h-24 bg-cyan-500/10 rounded-full blur-xl transition-all duration-500 group-hover:scale-150 animate-pulse"></div>
            
            <div class="flex items-center justify-between relative z-10">
                <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Active Tests</span>
                <span class="p-3 bg-gradient-to-br from-cyan-500/20 to-cyan-500/5 text-cyan-400 border border-cyan-500/20 rounded-xl transition-all duration-300 group-hover:scale-110 group-hover:border-cyan-500/40">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2">
                        </path>
                    </svg>
                </span>
            </div>
            <div class="mt-4 relative z-10">
                <h3 class="text-3xl font-black text-white font-mono tracking-tight">{{ $activeTestsCount }}</h3>
                <p class="text-[10px] text-slate-500 mt-1">Assessments configured</p>
            </div>
        </div>

        <!-- Completed Analyses -->
        <div class="group relative overflow-hidden bg-slate-900/40 backdrop-blur border border-slate-800/80 rounded-2xl p-6 transition-all duration-300 hover:-translate-y-1 hover:border-emerald-500/30 hover:shadow-xl hover:shadow-emerald-500/5">
            <!-- Ambient Light Glow -->
            <div class="absolute -top-10 -right-10 w-24 h-24 bg-emerald-500/10 rounded-full blur-xl transition-all duration-500 group-hover:scale-150 animate-pulse"></div>
            
            <div class="flex items-center justify-between relative z-10">
                <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Completed Analyses</span>
                <span class="p-3 bg-gradient-to-br from-emerald-500/20 to-emerald-500/5 text-emerald-400 border border-emerald-500/20 rounded-xl transition-all duration-300 group-hover:scale-110 group-hover:border-emerald-500/40">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z">
                        </path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </span>
            </div>
            <div class="mt-4 relative z-10">
                <h3 class="text-3xl font-black text-white font-mono tracking-tight">{{ $completedAnalysesCount }}</h3>
                <p class="text-[10px] text-slate-500 mt-1">Processed voice records</p>
            </div>
        </div>

        <!-- System Settings -->
        <div class="group relative overflow-hidden bg-slate-900/40 backdrop-blur border border-slate-800/80 rounded-2xl p-6 transition-all duration-300 hover:-translate-y-1 hover:border-amber-500/30 hover:shadow-xl hover:shadow-amber-500/5">
            <!-- Ambient Light Glow -->
            <div class="absolute -top-10 -right-10 w-24 h-24 bg-amber-500/10 rounded-full blur-xl transition-all duration-500 group-hover:scale-150 animate-pulse"></div>
            
            <div class="flex items-center justify-between relative z-10">
                <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider">System Config</span>
                <span class="p-3 bg-gradient-to-br from-amber-500/20 to-amber-500/5 text-amber-400 border border-amber-500/20 rounded-xl transition-all duration-300 group-hover:scale-110 group-hover:border-amber-500/40">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z">
                        </path>
                    </svg>
                </span>
            </div>
            <div class="mt-4 relative z-10">
                <h3 class="text-3xl font-black text-white font-mono tracking-tight">{{ $activeSettingsCount }}</h3>
                <p class="text-[10px] text-slate-500 mt-1">Database settings variables</p>
            </div>
        </div>
    </div>
</x-admin-layout>
