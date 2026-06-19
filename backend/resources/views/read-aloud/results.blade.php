<x-user-layout>
    <div class="max-w-5xl mx-auto space-y-8">
        
        <!-- Navigation / Actions Header -->
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 border-b border-slate-800 pb-4">
            <div>
                <a href="{{ route('practice.read-aloud.index') }}" class="inline-flex items-center gap-2 text-sm font-semibold text-slate-450 hover:text-white transition duration-200">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    Back to practice list
                </a>
            </div>

            <div class="flex items-center gap-3">
                <a href="{{ route('practice.read-aloud.show', $question->test_id) }}" 
                   class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-sm font-semibold text-slate-300 hover:text-white bg-slate-950 border border-slate-800 hover:bg-slate-900 transition duration-200">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 1121.306 7H18"></path>
                    </svg>
                    Retake Practice
                </a>
            </div>
        </div>

        <!-- Title and description -->
        <div class="space-y-2">
            <h1 class="text-2xl font-bold text-white">{{ $question->test->title }}</h1>
            <p class="text-sm text-slate-400">Detailed Speech IQ assessment scorecard for your reading performance.</p>
        </div>

        <!-- Score overview card -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            
            <!-- Overall Score Circle Card -->
            <div class="bg-slate-950/40 border border-slate-800 rounded-3xl p-8 flex flex-col items-center justify-center text-center space-y-6">
                <h3 class="text-sm font-bold text-slate-400 uppercase tracking-widest">Speech IQ Score</h3>
                
                <div class="relative flex items-center justify-center w-40 h-40">
                    <!-- Outer shadow blur -->
                    <div class="absolute inset-2 bg-indigo-500/10 rounded-full blur-xl animate-pulse"></div>
                    <!-- Radial SVG -->
                    <svg class="w-full h-full transform -rotate-90" viewBox="0 0 100 100">
                        <circle cx="50" cy="50" r="40" stroke="#1e293b" stroke-width="7" fill="transparent" />
                        <circle cx="50" cy="50" r="40" stroke="url(#indigoCyanGrad)" stroke-width="7" fill="transparent"
                                stroke-dasharray="251.2" stroke-dashoffset="{{ 251.2 - (251.2 * $result->overall_score) / 100 }}"
                                stroke-linecap="round" />
                        <defs>
                            <linearGradient id="indigoCyanGrad" x1="0%" y1="0%" x2="100%" y2="100%">
                                <stop offset="0%" stop-color="#6366f1" />
                                <stop offset="100%" stop-color="#06b6d4" />
                            </linearGradient>
                        </defs>
                    </svg>
                    <div class="absolute text-center">
                        <span class="text-5xl font-black text-white tracking-tight">{{ $result->overall_score }}</span>
                        <span class="text-slate-500 text-xs block font-bold uppercase mt-1">Overall</span>
                    </div>
                </div>

                <div class="px-4 py-2 rounded-xl bg-indigo-500/10 text-indigo-400 border border-indigo-500/20 text-xs font-bold uppercase tracking-wider">
                    {{ $result->accent ?: 'Voice Analyzed' }}
                </div>
            </div>

            <!-- Detailed metric scorebars -->
            <div class="lg:col-span-2 bg-slate-950/40 border border-slate-800 rounded-3xl p-8 flex flex-col justify-between space-y-6">
                <h3 class="text-sm font-bold text-slate-400 uppercase tracking-widest">Voice Metrics</h3>

                <div class="space-y-6 flex-1 flex flex-col justify-center">
                    <!-- Pronunciation -->
                    <div class="space-y-2">
                        <div class="flex items-center justify-between text-sm">
                            <span class="font-semibold text-slate-350">Pronunciation Accuracy</span>
                            <span class="font-bold text-indigo-400">{{ $result->pronunciation_score }}%</span>
                        </div>
                        <div class="h-2 w-full bg-slate-850 rounded-full overflow-hidden">
                            <div class="h-full bg-indigo-500 rounded-full" style="width: {{ $result->pronunciation_score }}%"></div>
                        </div>
                    </div>

                    <!-- Fluency -->
                    <div class="space-y-2">
                        <div class="flex items-center justify-between text-sm">
                            <span class="font-semibold text-slate-350">Fluency & Clarity</span>
                            <span class="font-bold text-cyan-400">{{ $result->fluency_score }}%</span>
                        </div>
                        <div class="h-2 w-full bg-slate-850 rounded-full overflow-hidden">
                            <div class="h-full bg-cyan-500 rounded-full" style="width: {{ $result->fluency_score }}%"></div>
                        </div>
                    </div>

                    <!-- Accuracy -->
                    <div class="space-y-2">
                        <div class="flex items-center justify-between text-sm">
                            <span class="font-semibold text-slate-350">Reading Accuracy</span>
                            <span class="font-bold text-emerald-400">{{ $result->accuracy_score }}%</span>
                        </div>
                        <div class="h-2 w-full bg-slate-850 rounded-full overflow-hidden">
                            <div class="h-full bg-emerald-500 rounded-full" style="width: {{ $result->accuracy_score }}%"></div>
                        </div>
                    </div>

                    <!-- Confidence -->
                    <div class="space-y-2">
                        <div class="flex items-center justify-between text-sm">
                            <span class="font-semibold text-slate-350">Confidence Estimate</span>
                            <span class="font-bold text-amber-400">{{ $result->confidence_score }}%</span>
                        </div>
                        <div class="h-2 w-full bg-slate-850 rounded-full overflow-hidden">
                            <div class="h-full bg-amber-500 rounded-full" style="width: {{ $result->confidence_score }}%"></div>
                        </div>
                    </div>
                </div>

                <!-- Custom mini player for original voice recording -->
                <div class="border-t border-slate-850 pt-4 flex items-center gap-4">
                    <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider shrink-0">Your Recording</span>
                    <audio controls class="w-full h-8 accent-indigo-500 bg-slate-900 rounded-lg">
                        <source src="{{ asset('storage/' . $recording->audio_path) }}" type="audio/webm">
                        Your browser does not support the audio element.
                    </audio>
                </div>
            </div>
        </div>

        <!-- Metrics cards (WPM, pauses) -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-6">
            <!-- WPM -->
            <div class="bg-slate-950/40 border border-slate-800 rounded-2xl p-6 space-y-2">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Reading Speed</span>
                <div class="flex items-baseline gap-1.5">
                    <span class="text-3xl font-extrabold text-white">{{ $result->wpm }}</span>
                    <span class="text-sm font-semibold text-slate-400">WPM</span>
                </div>
                <p class="text-xs text-slate-500 leading-tight">Speech rate: <span class="text-white font-semibold">{{ number_format($result->speech_rate ?? 0, 2) }} words/sec</span>.</p>
            </div>

            <!-- Pause Count -->
            <div class="bg-slate-950/40 border border-slate-800 rounded-2xl p-6 space-y-2">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Pauses Detected</span>
                <div class="flex items-baseline gap-1.5">
                    <span class="text-3xl font-extrabold text-white">{{ $result->pause_count }}</span>
                    <span class="text-sm font-semibold text-slate-400">times</span>
                </div>
                <p class="text-xs text-slate-500 leading-tight">Total: <span class="text-white font-semibold">{{ $result->pause_duration }}s</span>. Long pauses (>= 1.5s): <span class="text-white font-semibold">{{ $result->long_pauses ?? 0 }} times</span>.</p>
            </div>

            <!-- Words Read -->
            <div class="bg-slate-950/40 border border-slate-800 rounded-2xl p-6 space-y-2">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Words Count</span>
                <div class="flex items-baseline gap-1.5">
                    @php
                        $totalWords = str_word_count($question->question_text);
                        $correctWordsCount = count($result->correct_words ?? []);
                    @endphp
                    <span class="text-3xl font-extrabold text-white">{{ $correctWordsCount }}</span>
                    <span class="text-sm font-semibold text-slate-450">/ {{ $totalWords }}</span>
                </div>
                <p class="text-xs text-slate-500 leading-tight">Number of words pronounced relative to the paragraph.</p>
            </div>

            <!-- Similarity Percentage -->
            <div class="bg-slate-950/40 border border-slate-800 rounded-2xl p-6 space-y-2">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Text Similarity</span>
                <div class="flex items-baseline gap-1.5">
                    <span class="text-3xl font-extrabold text-white">{{ $result->similarity_percentage }}%</span>
                </div>
                <p class="text-xs text-slate-500 leading-tight">Word match similarity between transcript and reference.</p>
            </div>

            <!-- Accuracy Accent -->
            <div class="bg-slate-950/40 border border-slate-800 rounded-2xl p-6 space-y-2">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Accent / Dialect</span>
                <div class="flex items-baseline gap-1.5">
                    <span class="text-2xl font-extrabold text-white truncate max-w-full">{{ str_replace(' Accent', '', $result->accent ?: 'Standard') }}</span>
                </div>
                <p class="text-xs text-slate-500 leading-tight">AI identified primary pronunciation acoustic profile.</p>
            </div>
        </div>

        <!-- Word Highlight Panel -->
        <div class="bg-slate-950/40 border border-slate-800 rounded-3xl p-6 md:p-8 space-y-6">
            <div class="border-b border-slate-850 pb-4">
                <h3 class="text-lg font-bold text-white">Pronunciation Feedback</h3>
                <p class="text-sm text-slate-400">Words crossed out in red were skipped or mispronounced by the AI analyzer.</p>
            </div>

            <div class="text-xl md:text-2xl leading-loose font-medium text-slate-300 p-6 md:p-8 bg-slate-900/60 rounded-2xl border border-slate-850">
                @php
                    // Split original paragraph while preserving punctuation
                    $wordsWithPunc = explode(' ', $question->question_text);
                    $correctWordsLower = array_map('strtolower', $result->correct_words ?? []);
                    $missingWordsLower = array_map('strtolower', $result->missing_words ?? []);
                @endphp

                @foreach($wordsWithPunc as $word)
                    @php
                        // Strip punctuation to clean the word for checking
                        $cleanWord = strtolower(trim($word, ".,!?;:\"()[]{}"));
                        $isCorrect = in_array($cleanWord, $correctWordsLower);
                        $isMissing = in_array($cleanWord, $missingWordsLower);
                    @endphp

                    @if($isCorrect)
                        <span class="inline-block text-emerald-450 px-1 rounded bg-emerald-500/5 hover:bg-emerald-500/10 transition-colors duration-150" title="Correct">{{ $word }}</span>
                    @elseif($isMissing)
                        <span class="inline-block text-rose-400/90 line-through decoration-rose-500/80 decoration-2 px-1 rounded bg-rose-500/10 border border-rose-500/20" title="Mispronounced or missed">{{ $word }}</span>
                    @else
                        <span class="inline-block text-slate-400 px-1 rounded bg-slate-500/5" title="Unmatched">{{ $word }}</span>
                    @endif
                @endforeach
            </div>

            <!-- Extra Words Filler section -->
            @if(!empty($result->extra_words))
                <div class="bg-slate-900 border border-slate-850 rounded-2xl p-6 space-y-3">
                    <h4 class="text-xs font-bold uppercase tracking-widest text-slate-400">Filler / Extra Words Detected</h4>
                    <div class="flex flex-wrap gap-2">
                        @foreach($result->extra_words as $extra)
                            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-semibold bg-amber-500/10 text-amber-400 border border-amber-500/20">
                                <span class="w-1.5 h-1.5 rounded-full bg-amber-400"></span>
                                "{{ $extra }}"
                            </span>
                        @endforeach
                    </div>
                    <p class="text-xs text-slate-500 leading-tight">These are additional vocalized filler words detected in your audio that were not present in the script.</p>
                </div>
            @endif
        </div>

    </div>
</x-user-layout>
