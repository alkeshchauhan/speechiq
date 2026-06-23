<x-admin-layout>
    <x-slot name="header">
        <h2 class="text-lg sm:text-xl font-bold text-white truncate">Candidates Performance Reports</h2>
    </x-slot>

    <div class="max-w-6xl mx-auto space-y-8">
        
        <!-- Summary Stats Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Total Reports -->
            <div class="bg-slate-950/40 backdrop-blur border border-slate-800 rounded-2xl p-6 flex items-center justify-between">
                <div class="space-y-1">
                    <span class="text-[10px] text-slate-500 uppercase font-bold tracking-wider">Reports Compiled</span>
                    <h3 class="text-3xl font-black text-white font-mono">{{ $totalReports }}</h3>
                    <p class="text-[10px] text-slate-400">Total active candidates analyzed</p>
                </div>
                <div class="p-3.5 bg-slate-900 rounded-2xl border border-slate-800/80 text-indigo-400">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                </div>
            </div>

            <!-- System average score -->
            <div class="bg-slate-950/40 backdrop-blur border border-slate-800 rounded-2xl p-6 flex items-center justify-between">
                <div class="space-y-1">
                    <span class="text-[10px] text-slate-500 uppercase font-bold tracking-wider">System Average Rating</span>
                    <h3 class="text-3xl font-black text-white font-mono">{{ $averageOverallScore }}%</h3>
                    <p class="text-[10px] text-slate-400">Average overall index across candidates</p>
                </div>
                <div class="p-3.5 bg-slate-900 rounded-2xl border border-slate-800/80 text-emerald-400">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Reports Table -->
        <div class="bg-slate-950/40 backdrop-blur border border-slate-800 rounded-2xl overflow-hidden shadow-2xl">
            <div class="p-6 border-b border-slate-800 bg-slate-950/50 flex items-center justify-between">
                <h3 class="text-sm font-bold text-white uppercase tracking-wider">Candidates Index</h3>
                <span class="text-xs text-slate-500">{{ $reports->total() }} total entries</span>
            </div>

            @if($reports->isEmpty())
                <div class="p-12 text-center text-sm text-slate-500 italic">
                    No candidate reports compiled yet in the system.
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs">
                        <thead>
                            <tr class="bg-slate-900/60 border-b border-slate-800 text-slate-400 font-semibold">
                                <th class="p-4 uppercase tracking-wider">Candidate</th>
                                <th class="p-4 uppercase tracking-wider">Total Tests</th>
                                <th class="p-4 uppercase tracking-wider">Read Aloud Avg</th>
                                <th class="p-4 uppercase tracking-wider">AI Interview Avg</th>
                                <th class="p-4 uppercase tracking-wider">Overall Rating</th>
                                <th class="p-4 uppercase tracking-wider text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-850">
                            @foreach($reports as $report)
                                @php
                                    $scoreVal = $report->overall_score;
                                    if ($scoreVal >= 85) {
                                        $badgeColor = 'text-emerald-400 bg-emerald-500/10 border-emerald-500/20';
                                    } elseif ($scoreVal >= 70) {
                                        $badgeColor = 'text-indigo-400 bg-indigo-500/10 border-indigo-500/20';
                                    } else {
                                        $badgeColor = 'text-amber-400 bg-amber-500/10 border-amber-500/20';
                                    }
                                @endphp
                                <tr class="hover:bg-slate-900/30 transition duration-150">
                                    <td class="p-4">
                                        <div class="font-medium text-white text-sm">{{ $report->user ? $report->user->name : 'Unknown User' }}</div>
                                        <div class="text-slate-500 text-xs mt-0.5">{{ $report->user ? $report->user->email : '' }}</div>
                                    </td>
                                    <td class="p-4 font-mono text-slate-300 font-medium">{{ $report->total_tests_taken }} attempts</td>
                                    <td class="p-4 font-mono text-slate-400">{{ $report->read_aloud_average }}%</td>
                                    <td class="p-4 font-mono text-slate-400">{{ $report->interview_average }}%</td>
                                    <td class="p-4">
                                        <span class="px-2.5 py-0.5 rounded-full font-bold border {{ $badgeColor }} font-mono">{{ $scoreVal }}%</span>
                                    </td>
                                    <td class="p-4 text-right">
                                        <a href="{{ route('practice.reports.download', $report->id) }}" target="_blank" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-indigo-950/40 border border-indigo-800 hover:bg-indigo-950/80 rounded-lg text-indigo-400 font-bold transition duration-150">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                                            </svg>
                                            Inspect Report Card
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                @if($reports->hasPages())
                    <div class="p-6 border-t border-slate-800 bg-slate-950/30">
                        {{ $reports->links() }}
                    </div>
                @endif
            @endif
        </div>
    </div>
</x-admin-layout>
