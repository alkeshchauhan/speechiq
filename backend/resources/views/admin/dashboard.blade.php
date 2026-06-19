<x-admin-layout>
    <x-slot name="header">
        <h2 class="text-xl font-bold leading-tight text-white">Admin Dashboard</h2>
    </x-slot>

    <!-- Metrics Grid Row -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- Users Aggregate -->
        <div class="bg-slate-950/40 backdrop-blur border border-slate-800 rounded-2xl p-6">
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Total Users</span>
                <span class="p-2 bg-indigo-950 text-indigo-400 rounded-xl">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                    </svg>
                </span>
            </div>
            <div class="mt-4">
                <h3 class="text-3xl font-black text-white font-mono">{{ $totalUsers }}</h3>
                <p class="text-[10px] text-slate-500 mt-1">Registered accounts</p>
            </div>
        </div>

        <!-- Tests Aggregate -->
        <div class="bg-slate-950/40 backdrop-blur border border-slate-800 rounded-2xl p-6">
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Active Tests</span>
                <span class="p-2 bg-cyan-950 text-cyan-400 rounded-xl">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                    </svg>
                </span>
            </div>
            <div class="mt-4">
                <h3 class="text-3xl font-black text-white font-mono">{{ $activeTestsCount }}</h3>
                <p class="text-[10px] text-slate-500 mt-1">Assessments configured</p>
            </div>
        </div>

        <!-- Completed Analyses -->
        <div class="bg-slate-950/40 backdrop-blur border border-slate-800 rounded-2xl p-6">
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Completed Analyses</span>
                <span class="p-2 bg-emerald-950 text-emerald-400 rounded-xl">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </span>
            </div>
            <div class="mt-4">
                <h3 class="text-3xl font-black text-white font-mono">{{ $completedAnalysesCount }}</h3>
                <p class="text-[10px] text-slate-500 mt-1">Processed voice records</p>
            </div>
        </div>

        <!-- System Settings -->
        <div class="bg-slate-950/40 backdrop-blur border border-slate-800 rounded-2xl p-6">
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider">System Config</span>
                <span class="p-2 bg-amber-950 text-amber-400 rounded-xl">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                    </svg>
                </span>
            </div>
            <div class="mt-4">
                <h3 class="text-3xl font-black text-white font-mono">{{ $activeSettingsCount }}</h3>
                <p class="text-[10px] text-slate-500 mt-1">Database settings variables</p>
            </div>
        </div>
    </div>
</x-admin-layout>
