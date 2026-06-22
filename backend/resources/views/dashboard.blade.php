<x-user-layout>
    <div class="space-y-8 max-w-6xl mx-auto">
        
        <!-- Welcome Banner -->
        <div class="relative overflow-hidden rounded-2xl bg-gradient-to-r from-slate-900 via-indigo-950 to-slate-900 border border-slate-800/80 p-8 md:p-12 shadow-2xl">
            <div class="absolute -top-12 -right-12 w-64 h-64 bg-indigo-500/10 rounded-full blur-3xl pointer-events-none"></div>
            <div class="absolute -bottom-12 -left-12 w-64 h-64 bg-cyan-500/10 rounded-full blur-3xl pointer-events-none"></div>

            <div class="relative z-10 space-y-4 max-w-2xl">
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-indigo-500/10 text-indigo-400 border border-indigo-500/20">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-ping"></span>
                    Candidate Portal
                </span>
                <h1 class="text-3xl md:text-4xl font-extrabold text-white tracking-tight">
                    Welcome back, <span class="bg-gradient-to-r from-indigo-400 to-cyan-400 bg-clip-text text-transparent">{{ auth()->user()->name }}!</span>
                </h1>
                <p class="text-slate-400 text-sm md:text-base leading-relaxed">
                    Get started by practicing your reading clarity or testing your conversational communication response flow using SpeechIQ AI assessment tools.
                </p>
            </div>
        </div>

        <!-- Metrics Cards Row -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <!-- Practices Completed -->
            <div class="group relative overflow-hidden bg-slate-900/40 backdrop-blur border border-slate-800/80 rounded-2xl p-6 flex items-center justify-between transition-all duration-300 hover:-translate-y-1 hover:border-indigo-500/30 hover:shadow-xl hover:shadow-indigo-500/5">
                <!-- Ambient Light Glow -->
                <div class="absolute -top-10 -right-10 w-24 h-24 bg-indigo-500/10 rounded-full blur-xl transition-all duration-500 group-hover:scale-150 animate-pulse"></div>

                <div class="space-y-1 relative z-10">
                    <span class="text-[10px] text-slate-500 uppercase font-bold tracking-wider">Completed Practices</span>
                    <h3 class="text-3xl font-black text-white font-mono tracking-tight">{{ $totalCompleted }}</h3>
                    <p class="text-[10px] text-slate-450">Total processed audios</p>
                </div>
                <div class="p-3.5 bg-gradient-to-br from-indigo-500/20 to-indigo-500/5 text-indigo-400 border border-indigo-500/20 rounded-2xl transition-all duration-300 group-hover:scale-110 group-hover:border-indigo-500/40 relative z-10 shadow-inner">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11a7 7 0 01-7 7m0 0a7 7 0 01-7-7m7 7v4m0 0H8m4 0h4m-4-8a3 3 0 01-3-3V5a3 3 0 116 0v6a3 3 0 01-3 3z"></path>
                    </svg>
                </div>
            </div>

            <!-- Read Aloud Average -->
            <div class="group relative overflow-hidden bg-slate-900/40 backdrop-blur border border-slate-800/80 rounded-2xl p-6 flex items-center justify-between transition-all duration-300 hover:-translate-y-1 hover:border-cyan-500/30 hover:shadow-xl hover:shadow-cyan-500/5">
                <!-- Ambient Light Glow -->
                <div class="absolute -top-10 -right-10 w-24 h-24 bg-cyan-500/10 rounded-full blur-xl transition-all duration-500 group-hover:scale-150 animate-pulse"></div>

                <div class="space-y-1 relative z-10">
                    <span class="text-[10px] text-slate-500 uppercase font-bold tracking-wider">Read Aloud Average</span>
                    <h3 class="text-3xl font-black text-white font-mono tracking-tight">{{ $readAloudAvg }}%</h3>
                    <p class="text-[10px] text-slate-450">Accuracy & pronunciation</p>
                </div>
                <div class="p-3.5 bg-gradient-to-br from-cyan-500/20 to-cyan-500/5 text-cyan-400 border border-cyan-500/20 rounded-2xl transition-all duration-300 group-hover:scale-110 group-hover:border-cyan-500/40 relative z-10 shadow-inner">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                    </svg>
                </div>
            </div>

            <!-- AI Interview Average / Overall report score link -->
            <div class="group relative overflow-hidden bg-slate-900/40 backdrop-blur border border-slate-800/80 rounded-2xl p-6 flex items-center justify-between transition-all duration-300 hover:-translate-y-1 hover:border-emerald-500/30 hover:shadow-xl hover:shadow-emerald-500/5">
                <!-- Ambient Light Glow -->
                <div class="absolute -top-10 -right-10 w-24 h-24 bg-emerald-500/10 rounded-full blur-xl transition-all duration-500 group-hover:scale-150 animate-pulse"></div>

                <div class="space-y-1 relative z-10">
                    <span class="text-[10px] text-slate-500 uppercase font-bold tracking-wider">AI Interview Average</span>
                    <h3 class="text-3xl font-black text-white font-mono tracking-tight">{{ $interviewAvg }}%</h3>
                    @if($report)
                        <a href="{{ route('practice.reports.index') }}" class="text-[10px] text-emerald-400 font-bold hover:text-emerald-300 flex items-center gap-1 mt-1">
                            View Report (Overall: {{ $report->overall_score }}%) &rarr;
                        </a>
                    @else
                        <p class="text-[10px] text-slate-450">No consolidated report compiled</p>
                    @endif
                </div>
                <div class="p-3.5 bg-gradient-to-br from-emerald-500/20 to-emerald-500/5 text-emerald-400 border border-emerald-500/20 rounded-2xl transition-all duration-300 group-hover:scale-110 group-hover:border-emerald-500/40 relative z-10 shadow-inner">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 9l3 3-3 3m5 0h3M5 20h14a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Exercises Navigation Modules -->
        <div class="space-y-6">
            <h2 class="text-xl font-bold text-white flex items-center gap-2">
                <span class="w-1.5 h-6 bg-indigo-500 rounded-full"></span>
                Voice Assessment Channels
            </h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Module 6: Read Aloud -->
                <div class="group relative bg-slate-950/40 border border-slate-800 rounded-2xl p-6 hover:border-indigo-500/50 hover:bg-slate-950/80 transition-all duration-300">
                    <div class="space-y-3">
                        <div class="flex items-center gap-2.5">
                            <span class="p-2.5 bg-slate-900 rounded-xl border border-slate-850 text-indigo-400">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                                </svg>
                            </span>
                            <h3 class="text-base font-bold text-white group-hover:text-indigo-400 transition-colors duration-200">Read Aloud Practices</h3>
                        </div>
                        <p class="text-xs text-slate-400 leading-relaxed">Read standard prose fragments, and assess voice pauses, syllables modulation speed, pronunciation, accent detection, and accurate articulation scores.</p>
                        <div class="pt-4 border-t border-slate-900 mt-2 flex items-center justify-between">
                            <span class="text-[10px] text-slate-500 font-semibold uppercase tracking-wider">Pronunciation training</span>
                            <a href="{{ route('practice.read-aloud.index') }}" class="inline-flex items-center gap-1 text-xs font-bold text-indigo-400 hover:text-indigo-350">
                                Launch Practice
                                <svg class="w-3.5 h-3.5 transform group-hover:translate-x-1 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                </svg>
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Module 7: AI Interview -->
                <div class="group relative bg-slate-950/40 border border-slate-800 rounded-2xl p-6 hover:border-cyan-500/50 hover:bg-slate-950/80 transition-all duration-300">
                    <div class="space-y-3">
                        <div class="flex items-center gap-2.5">
                            <span class="p-2.5 bg-slate-900 rounded-xl border border-slate-850 text-cyan-400">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M8 9l3 3-3 3m5 0h3M5 20h14a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                            </span>
                            <h3 class="text-base font-bold text-white group-hover:text-cyan-400 transition-colors duration-200">Conversational AI Interviews</h3>
                        </div>
                        <p class="text-xs text-slate-400 leading-relaxed">Engage in interactive technical or behavioral speech simulations with SpeechIQ AI. Answer dynamically and get comprehensive grammar, content relevance, and fluency scores.</p>
                        <div class="pt-4 border-t border-slate-900 mt-2 flex items-center justify-between">
                            <span class="text-[10px] text-slate-500 font-semibold uppercase tracking-wider">Dynamic simulation</span>
                            <a href="{{ route('practice.interview.index') }}" class="inline-flex items-center gap-1 text-xs font-bold text-cyan-400 hover:text-cyan-355">
                                Start Simulation
                                <svg class="w-3.5 h-3.5 transform group-hover:translate-x-1 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                </svg>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Attempts Timeline -->
        <div class="space-y-6">
            <h2 class="text-xl font-bold text-white flex items-center gap-2">
                <span class="w-1.5 h-6 bg-indigo-500 rounded-full"></span>
                Recent Practices Attempts History
            </h2>

            @if($pastAttempts->isEmpty())
                <div class="bg-slate-950/40 border border-slate-800 rounded-2xl p-8 text-center text-xs text-slate-500 italic">
                    No attempts completed yet. Practice an exercise from the channels above.
                </div>
            @else
                <div class="bg-slate-950/40 border border-slate-800 rounded-2xl overflow-hidden shadow-xl">
                    <table class="w-full text-left text-xs">
                        <thead>
                            <tr class="bg-slate-900/60 border-b border-slate-800/80 text-slate-400 font-semibold">
                                <th class="p-4 uppercase tracking-wider">Timestamp Date</th>
                                <th class="p-4 uppercase tracking-wider">Exercise Channel</th>
                                <th class="p-4 uppercase tracking-wider">Exercise Name</th>
                                <th class="p-4 uppercase tracking-wider">Duration</th>
                                <th class="p-4 uppercase tracking-wider">Overall Score Rating</th>
                                <th class="p-4 uppercase tracking-wider text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-850">
                            @foreach($pastAttempts as $attempt)
                                @php
                                    $isReadAloud = (bool)$attempt->readAloudResult;
                                    $scoreVal = $isReadAloud ? ($attempt->readAloudResult ? $attempt->readAloudResult->overall_score : 0) : ($attempt->interviewResult ? $attempt->interviewResult->overall_score : 0);
                                    
                                    if ($scoreVal >= 85) {
                                        $badgeColor = 'text-emerald-400 bg-emerald-500/10 border-emerald-500/20';
                                    } elseif ($scoreVal >= 70) {
                                        $badgeColor = 'text-indigo-400 bg-indigo-500/10 border-indigo-500/20';
                                    } else {
                                        $badgeColor = 'text-amber-400 bg-amber-500/10 border-amber-500/20';
                                    }
                                @endphp
                                <tr class="hover:bg-slate-900/30 transition duration-150">
                                    <td class="p-4 font-mono text-slate-500">{{ $attempt->created_at->format('Y-m-d H:i') }}</td>
                                    <td class="p-4">
                                        <span class="px-2 py-0.5 rounded text-[10px] font-semibold border {{ $isReadAloud ? 'text-cyan-400 bg-cyan-500/10 border-cyan-500/20' : 'text-indigo-400 bg-indigo-500/10 border-indigo-500/20' }}">
                                            {{ $isReadAloud ? 'Read Aloud' : 'AI Interview' }}
                                        </span>
                                    </td>
                                    <td class="p-4 text-slate-200 font-medium max-w-xs truncate">
                                        {{ $attempt->question ? ($attempt->question->test ? $attempt->question->test->title : 'Dynamic Question') : 'Dynamic Question' }}
                                    </td>
                                    <td class="p-4 text-slate-450">{{ round($attempt->duration, 1) }}s</td>
                                    <td class="p-4">
                                        <span class="px-2.5 py-0.5 rounded-full font-bold border {{ $badgeColor }} font-mono">{{ $scoreVal }}%</span>
                                    </td>
                                    <td class="p-4 text-right">
                                        @if($isReadAloud)
                                            <a href="{{ route('practice.read-aloud.results', $attempt->id) }}" class="inline-flex items-center gap-1 text-xs font-bold text-indigo-400 hover:underline">
                                                Review
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                                </svg>
                                            </a>
                                        @else
                                            <a href="{{ route('practice.interview.results-view', $attempt->id) }}" class="inline-flex items-center gap-1 text-xs font-bold text-cyan-400 hover:underline">
                                                Review
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                                </svg>
                                            </a>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>

    </div>
</x-user-layout>
