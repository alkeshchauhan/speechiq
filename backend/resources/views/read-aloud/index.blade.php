<x-user-layout>
    <div class="space-y-8 max-w-6xl mx-auto">
        <!-- Header -->
        <div class="relative overflow-hidden rounded-2xl bg-gradient-to-r from-slate-900 via-indigo-950 to-slate-900 border border-slate-800/80 p-8 md:p-12 shadow-2xl">
            <!-- Decorative blur objects -->
            <div class="absolute -top-12 -right-12 w-64 h-64 bg-indigo-500/10 rounded-full blur-3xl pointer-events-none"></div>
            <div class="absolute -bottom-12 -left-12 w-64 h-64 bg-cyan-500/10 rounded-full blur-3xl pointer-events-none"></div>

            <div class="relative z-10 space-y-4 max-w-2xl">
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-indigo-500/10 text-indigo-400 border border-indigo-500/20">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 11a7 7 0 01-7 7m0 0a7 7 0 01-7-7m7 7v4m0 0H8m4 0h4m-4-8a3 3 0 01-3-3V5a3 3 0 116 0v6a3 3 0 01-3 3z"></path>
                    </svg>
                    Speech Training
                </span>
                <h1 class="text-3xl md:text-4xl font-extrabold text-white tracking-tight">
                    Read Aloud <span class="bg-gradient-to-r from-indigo-400 to-cyan-400 bg-clip-text text-transparent">Pronunciation Practice</span>
                </h1>
                <p class="text-slate-400 text-sm md:text-base leading-relaxed">
                    Improve your speaking clarity, fluency, and pronunciation score. Choose a paragraph below, read it aloud, and let our AI engine analyze your voice in real-time.
                </p>
            </div>
        </div>

        <!-- Tests Grid -->
        <div class="space-y-6">
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-bold text-white flex items-center gap-2">
                    <span class="w-1.5 h-6 bg-indigo-500 rounded-full"></span>
                    Available Paragraphs
                </h2>
                <span class="text-xs text-slate-500">{{ $tests->count() }} modules found</span>
            </div>

            @if($tests->isEmpty())
                <div class="bg-slate-950/40 border border-slate-800 rounded-2xl p-12 text-center space-y-4">
                    <div class="w-16 h-16 bg-slate-900 border border-slate-850 rounded-full flex items-center justify-center mx-auto text-slate-500">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                        </svg>
                    </div>
                    <div class="space-y-1">
                        <h3 class="text-lg font-semibold text-white">No tests active</h3>
                        <p class="text-sm text-slate-400 max-w-sm mx-auto">There are no Read Aloud practice paragraphs configured or active at this moment.</p>
                    </div>
                </div>
            @else
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($tests as $test)
                        @php
                            $firstQuestion = $test->questions()->first();
                            $text = $firstQuestion ? $firstQuestion->question_text : '';
                            $wordCount = str_word_count($text);
                            
                            // Estimate difficulty based on word count
                            if ($wordCount < 40) {
                                $difficulty = 'Easy';
                                $difficultyColor = 'text-emerald-400 bg-emerald-500/10 border-emerald-500/20';
                            } elseif ($wordCount < 80) {
                                $difficulty = 'Medium';
                                $difficultyColor = 'text-amber-400 bg-amber-500/10 border-amber-500/20';
                            } else {
                                $difficulty = 'Hard';
                                $difficultyColor = 'text-rose-400 bg-rose-500/10 border-rose-500/20';
                            }
                        @endphp
                        <div class="group relative flex flex-col justify-between bg-slate-950/40 border border-slate-800/80 rounded-2xl p-6 hover:border-indigo-500/50 hover:bg-slate-950/80 hover:shadow-lg hover:shadow-indigo-500/5 transition-all duration-300">
                            <div class="space-y-4">
                                <div class="flex items-center justify-between">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium border {{ $difficultyColor }}">
                                        {{ $difficulty }}
                                    </span>
                                    <span class="text-xs font-semibold text-slate-500 flex items-center gap-1">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                        </svg>
                                        {{ $wordCount }} words
                                    </span>
                                </div>

                                <div class="space-y-2">
                                    <h3 class="text-lg font-bold text-white group-hover:text-indigo-400 transition-colors duration-200 line-clamp-1">
                                        {{ $test->title }}
                                    </h3>
                                    <p class="text-slate-400 text-xs leading-relaxed line-clamp-3">
                                        {{ $test->description ?: 'No description provided for this reading task.' }}
                                    </p>
                                </div>
                            </div>

                            <div class="pt-6">
                                <a href="{{ route('practice.read-aloud.show', $test->id) }}" 
                                   class="w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl text-sm font-semibold text-white bg-slate-900 hover:bg-indigo-600 border border-slate-850 hover:border-indigo-500 shadow-md group-hover:shadow-indigo-500/10 transition-all duration-300">
                                    Start Practice
                                    <svg class="w-4 h-4 transform group-hover:translate-x-1 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
                                    </svg>
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</x-user-layout>
