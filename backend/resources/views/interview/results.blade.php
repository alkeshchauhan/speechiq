<x-user-layout>
    <div class="max-w-5xl mx-auto space-y-8">
        
        <!-- Navigation / Actions Header -->
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 border-b border-slate-800 pb-4">
            <div>
                <a href="{{ route('dashboard') }}" class="inline-flex items-center gap-2 text-sm font-semibold text-slate-400 hover:text-white transition duration-200">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    Back to Dashboard
                </a>
            </div>

            <div class="flex items-center gap-3">
                @if($question && $question->test_id)
                    <a href="{{ route('practice.interview.show', $question->test_id) }}" 
                       class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-sm font-semibold text-slate-300 hover:text-white bg-slate-950 border border-slate-800 hover:bg-slate-900 transition duration-200">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 1121.306 7H18"></path>
                        </svg>
                        Retake Interview
                    </a>
                @else
                    <a href="{{ route('practice.interview.index') }}" 
                       class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-sm font-semibold text-slate-300 hover:text-white bg-slate-950 border border-slate-800 hover:bg-slate-900 transition duration-200">
                        Practice List
                    </a>
                @endif
            </div>
        </div>

        <!-- Title and description -->
        <div class="space-y-2">
            <span class="px-3 py-1 rounded-full text-[10px] font-bold tracking-wider text-cyan-400 bg-cyan-500/10 border border-cyan-500/20 uppercase">
                AI Interview Review
            </span>
            <h1 class="text-2xl font-bold text-white">Interview Response Analysis</h1>
            <p class="text-sm text-slate-400">Detailed Speech IQ performance evaluation and custom suggestions by our AI Interviewer.</p>
        </div>

        <!-- Score overview card -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            
            <!-- Overall Score Circle Card -->
            <div class="bg-slate-950/40 border border-slate-800 rounded-3xl p-8 flex flex-col items-center justify-center text-center space-y-6">
                <h3 class="text-sm font-bold text-slate-400 uppercase tracking-widest">Speech IQ Score</h3>
                
                <div class="relative flex items-center justify-center w-40 h-40">
                    <!-- Outer shadow blur -->
                    <div class="absolute inset-2 bg-cyan-500/10 rounded-full blur-xl animate-pulse"></div>
                    <!-- Radial SVG -->
                    <svg class="w-full h-full transform -rotate-90" viewBox="0 0 100 100">
                        <circle cx="50" cy="50" r="40" stroke="#1e293b" stroke-width="7" fill="transparent" />
                        <circle cx="50" cy="50" r="40" stroke="url(#cyanIndigoGrad)" stroke-width="7" fill="transparent"
                                stroke-dasharray="251.2" stroke-dashoffset="{{ 251.2 - (251.2 * $result->overall_score) / 100 }}"
                                stroke-linecap="round" />
                        <defs>
                            <linearGradient id="cyanIndigoGrad" x1="0%" y1="0%" x2="100%" y2="100%">
                                <stop offset="0%" stop-color="#06b6d4" />
                                <stop offset="100%" stop-color="#6366f1" />
                            </linearGradient>
                        </defs>
                    </svg>
                    <div class="absolute text-center">
                        <span class="text-5xl font-black text-white tracking-tight">{{ $result->overall_score }}</span>
                        <span class="text-slate-500 text-xs block font-bold uppercase mt-1">Overall</span>
                    </div>
                </div>

                <div class="px-4 py-2 rounded-xl bg-cyan-500/10 text-cyan-400 border border-cyan-500/20 text-xs font-bold uppercase tracking-wider">
                    {{ $result->accent ?: 'Voice Analyzed' }}
                </div>
            </div>

            <!-- Detailed metric scorebars -->
            <div class="lg:col-span-2 bg-slate-950/40 border border-slate-800 rounded-3xl p-8 flex flex-col justify-between space-y-6">
                <h3 class="text-sm font-bold text-slate-400 uppercase tracking-widest">Detailed Performance Metrics</h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 flex-1 justify-center">
                    <!-- Grammar -->
                    <div class="space-y-2">
                        <div class="flex items-center justify-between text-sm">
                            <span class="font-semibold text-slate-350">Grammar & Syntax</span>
                            <span class="font-bold text-indigo-400">{{ $result->grammar_score }}%</span>
                        </div>
                        <div class="h-2 w-full bg-slate-850 rounded-full overflow-hidden">
                            <div class="h-full bg-indigo-500 rounded-full" style="width: {{ $result->grammar_score }}%"></div>
                        </div>
                    </div>

                    <!-- Vocabulary -->
                    <div class="space-y-2">
                        <div class="flex items-center justify-between text-sm">
                            <span class="font-semibold text-slate-350">Vocabulary Richness</span>
                            <span class="font-bold text-cyan-400">{{ $result->vocabulary_score }}%</span>
                        </div>
                        <div class="h-2 w-full bg-slate-850 rounded-full overflow-hidden">
                            <div class="h-full bg-cyan-500 rounded-full" style="width: {{ $result->vocabulary_score }}%"></div>
                        </div>
                    </div>

                    <!-- Content Score -->
                    <div class="space-y-2">
                        <div class="flex items-center justify-between text-sm">
                            <span class="font-semibold text-slate-350">Content Relevancy</span>
                            <span class="font-bold text-emerald-400">{{ $result->content_score }}%</span>
                        </div>
                        <div class="h-2 w-full bg-slate-850 rounded-full overflow-hidden">
                            <div class="h-full bg-emerald-500 rounded-full" style="width: {{ $result->content_score }}%"></div>
                        </div>
                    </div>

                    <!-- Confidence -->
                    <div class="space-y-2">
                        <div class="flex items-center justify-between text-sm">
                            <span class="font-semibold text-slate-350">Speech Confidence</span>
                            <span class="font-bold text-amber-400">{{ $result->confidence_score }}%</span>
                        </div>
                        <div class="h-2 w-full bg-slate-850 rounded-full overflow-hidden">
                            <div class="h-full bg-amber-500 rounded-full" style="width: {{ $result->confidence_score }}%"></div>
                        </div>
                    </div>

                    <!-- Pronunciation -->
                    <div class="space-y-2">
                        <div class="flex items-center justify-between text-sm">
                            <span class="font-semibold text-slate-350">Pronunciation accuracy</span>
                            <span class="font-bold text-rose-400">{{ $result->pronunciation_score }}%</span>
                        </div>
                        <div class="h-2 w-full bg-slate-850 rounded-full overflow-hidden">
                            <div class="h-full bg-rose-500 rounded-full" style="width: {{ $result->pronunciation_score }}%"></div>
                        </div>
                    </div>

                    <!-- Fluency -->
                    <div class="space-y-2">
                        <div class="flex items-center justify-between text-sm">
                            <span class="font-semibold text-slate-350">Fluency & Pacing</span>
                            <span class="font-bold text-teal-400">{{ $result->fluency_score }}%</span>
                        </div>
                        <div class="h-2 w-full bg-slate-850 rounded-full overflow-hidden">
                            <div class="h-full bg-teal-500 rounded-full" style="width: {{ $result->fluency_score }}%"></div>
                        </div>
                    </div>
                </div>

                <!-- Recording playback -->
                <div class="border-t border-slate-850 pt-4 flex items-center gap-4">
                    <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider shrink-0">Your Answer Recording</span>
                    <audio controls class="w-full h-8 accent-cyan-500 bg-slate-900 rounded-lg">
                        <source src="{{ asset('storage/' . $recording->audio_path) }}" type="audio/webm">
                        Your browser does not support the audio element.
                    </audio>
                </div>
            </div>
        </div>

        <!-- Question & Transcript Comparison -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            <!-- The Question Asked -->
            <div class="bg-slate-950/40 border border-slate-800 rounded-3xl p-6 md:p-8 space-y-4">
                <div class="flex items-center gap-2 border-b border-slate-855 pb-3">
                    <span class="p-1.5 bg-slate-900 rounded-lg text-indigo-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </span>
                    <h3 class="font-bold text-white">Interview Question</h3>
                </div>
                <p class="text-sm text-slate-350 leading-relaxed font-medium">
                    {{ $result->question }}
                </p>
            </div>

            <!-- The Transcript Response -->
            <div class="bg-slate-950/40 border border-slate-800 rounded-3xl p-6 md:p-8 space-y-4">
                <div class="flex items-center gap-2 border-b border-slate-855 pb-3">
                    <span class="p-1.5 bg-slate-900 rounded-lg text-cyan-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M19 11a7 7 0 01-7 7m0 0a7 7 0 01-7-7m7 7v4m0 0H8m4 0h4m-4-8a3 3 0 01-3-3V5a3 3 0 116 0v6a3 3 0 01-3 3z"></path>
                        </svg>
                    </span>
                    <h3 class="font-bold text-white">Your Response Transcript</h3>
                </div>
                <p class="text-sm text-slate-300 leading-relaxed italic">
                    "{{ $result->transcript ?: 'No transcript processed.' }}"
                </p>
            </div>
        </div>

        <!-- Feedback and SWOT suggestions -->
        <div class="bg-slate-950/40 border border-slate-800 rounded-3xl p-6 md:p-8 space-y-4">
            <div class="flex items-center gap-2 border-b border-slate-855 pb-3">
                <span class="p-1.5 bg-slate-900 rounded-lg text-emerald-400">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M9.663 17h4.673M12 3v1m6.364 .364l-.707 .707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 113.536 0V21h2v-2.236a5 5 0 013.536 0z"></path>
                    </svg>
                </span>
                <h3 class="font-bold text-white">SpeechIQ AI Evaluation Feedback</h3>
            </div>
            <div class="text-sm text-slate-300 leading-relaxed space-y-2">
                @if($result->feedback)
                    <p class="whitespace-pre-line">{{ $result->feedback }}</p>
                @else
                    <p>Good response structure. Work on pacing and pronunciation of technical vocabulary to improve overall assessment rating.</p>
                @endif
            </div>
        </div>

    </div>
</x-user-layout>
