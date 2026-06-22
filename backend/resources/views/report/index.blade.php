<x-user-layout>
    <div class="space-y-8 max-w-6xl mx-auto">
        
        <!-- Header Panel -->
        <div class="relative overflow-hidden rounded-2xl bg-gradient-to-r from-slate-900 via-indigo-950 to-slate-900 border border-slate-800/80 p-8 md:p-12 shadow-2xl">
            <div class="absolute -top-12 -right-12 w-64 h-64 bg-indigo-500/10 rounded-full blur-3xl pointer-events-none"></div>
            <div class="absolute -bottom-12 -left-12 w-64 h-64 bg-cyan-500/10 rounded-full blur-3xl pointer-events-none"></div>

            <div class="relative z-10 flex flex-col md:flex-row items-start md:items-center justify-between gap-6">
                <div class="space-y-4 max-w-2xl">
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-indigo-500/10 text-indigo-400 border border-indigo-500/20">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 17v-2a4 4 0 00-4-4H5m14 6v-3a4 4 0 00-3-3.87m-4-12a4 4 0 01-4 4H12m8 8a4 4 0 01-4 4v0"></path>
                        </svg>
                        Performance Metrics
                    </span>
                    <h1 class="text-3xl md:text-4xl font-extrabold text-white tracking-tight">
                        Voice Progress & <span class="bg-gradient-to-r from-indigo-400 to-cyan-400 bg-clip-text text-transparent">Analysis Report</span>
                    </h1>
                    <p class="text-slate-400 text-sm md:text-base leading-relaxed">
                        Track your verbal pacing, pronunciation, accuracy, and vocabulary maturity metrics over time. Review improvement suggestions generated dynamically from test completions.
                    </p>
                </div>
                
                @if($report && $report->total_tests_taken > 0)
                    <a href="{{ route('practice.reports.download', $report->id) }}" target="_blank" class="inline-flex items-center gap-2 px-6 py-3.5 bg-gradient-to-r from-indigo-500 to-cyan-500 hover:from-indigo-600 hover:to-cyan-600 text-white font-semibold rounded-xl text-sm transition duration-200 shadow-lg shrink-0">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                        </svg>
                        Download PDF Report
                    </a>
                @endif
            </div>
        </div>

        @if(!$report || $report->total_tests_taken === 0)
            <!-- Empty state dashboard onboarding -->
            <div class="bg-slate-950/40 border border-slate-800 rounded-3xl p-12 text-center space-y-4">
                <div class="w-16 h-16 bg-slate-900 border border-slate-850 rounded-full flex items-center justify-center mx-auto text-slate-500">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                    </svg>
                </div>
                <div class="space-y-1">
                    <h3 class="text-lg font-semibold text-white">No analysis report data available</h3>
                    <p class="text-sm text-slate-400 max-w-sm mx-auto">Please complete at least one Read Aloud paragraph practice or AI Interview voice response to generate statistics reports.</p>
                </div>
                <div class="flex items-center justify-center gap-4 pt-4">
                    <a href="{{ route('practice.read-aloud.index') }}" class="px-5 py-2.5 rounded-xl bg-slate-900 border border-slate-850 hover:bg-slate-800 text-xs font-semibold text-white transition">Read Aloud Practice</a>
                    <a href="{{ route('practice.interview.index') }}" class="px-5 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-xs font-semibold text-white transition">AI Interview Practice</a>
                </div>
            </div>
        @else
            <!-- Dashboard Main Metrics Grid -->
            <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
                
                <!-- Overall Score circle and category averages -->
                <div class="lg:col-span-1 space-y-6">
                    <div class="bg-slate-950/40 border border-slate-800 rounded-3xl p-6 space-y-8 flex flex-col items-center justify-center text-center">
                        <div class="space-y-1">
                            <h3 class="text-sm font-semibold text-slate-400 uppercase tracking-wider">Overall Rating Index</h3>
                        </div>
                        
                        <!-- SVG Progress Ring -->
                        <div class="relative w-36 h-36 flex items-center justify-center">
                            <svg class="w-full h-full transform -rotate-90" viewBox="0 0 36 36">
                                <path class="text-slate-900" stroke-width="2.5" stroke="currentColor" fill="none" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" />
                                <path class="text-indigo-500" stroke-dasharray="{{ $report->overall_score }}, 100" stroke-width="2.8" stroke-linecap="round" stroke="currentColor" fill="none" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" />
                            </svg>
                            <span class="absolute text-3xl font-black text-white font-mono">{{ $report->overall_score }}%</span>
                        </div>

                        <div class="space-y-1">
                            @if($report->overall_score >= 85)
                                <span class="px-3 py-1 rounded-full text-xs font-bold text-emerald-400 bg-emerald-500/10 border border-emerald-500/20">Highly Articulate</span>
                            @elseif($report->overall_score >= 70)
                                <span class="px-3 py-1 rounded-full text-xs font-bold text-indigo-400 bg-indigo-500/10 border border-indigo-500/20">Proficient Flow</span>
                            @else
                                <span class="px-3 py-1 rounded-full text-xs font-bold text-amber-400 bg-amber-500/10 border border-amber-500/20">Practice Needed</span>
                            @endif
                            <p class="text-[10px] text-slate-500 pt-2">Calculated from {{ $report->total_tests_taken }} completed voice sessions</p>
                        </div>
                    </div>

                    <!-- Category stats breakdown -->
                    <div class="bg-slate-950/40 border border-slate-800 rounded-3xl p-6 space-y-4">
                        <h4 class="text-xs font-bold text-slate-400 uppercase tracking-wider border-b border-slate-850 pb-2">Category Averages</h4>
                        
                        <div class="space-y-3">
                            <div class="space-y-1">
                                <div class="flex justify-between text-xs text-slate-300">
                                    <span>Read Aloud</span>
                                    <span class="font-bold text-white">{{ number_format($report->read_aloud_average, 2) }}%</span>
                                </div>
                                <div class="h-1.5 w-full bg-slate-900 border border-slate-800 rounded-full overflow-hidden">
                                    <div class="bg-cyan-500 h-full rounded-full" style="width: {{ $report->read_aloud_average }}%;"></div>
                                </div>
                            </div>
                            
                            <div class="space-y-1">
                                <div class="flex justify-between text-xs text-slate-300">
                                    <span>AI Interview</span>
                                    <span class="font-bold text-white">{{ number_format($report->interview_average, 2) }}%</span>
                                </div>
                                <div class="h-1.5 w-full bg-slate-900 border border-slate-800 rounded-full overflow-hidden">
                                    <div class="bg-indigo-500 h-full rounded-full" style="width: {{ $report->interview_average }}%;"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Performance History Chart Timeline -->
                <div class="lg:col-span-3 space-y-6">
                    <div class="bg-slate-950/40 border border-slate-800 rounded-3xl p-6 space-y-6">
                        <div class="flex items-center justify-between border-b border-slate-850 pb-4">
                            <h3 class="text-md font-bold text-white flex items-center gap-2">
                                <span class="w-1 h-4 bg-indigo-500 rounded-full"></span>
                                Performance Progress History
                            </h3>
                            <span class="text-xs text-slate-500">Timeline tracking</span>
                        </div>
                        
                        <!-- Chart container -->
                        <div class="relative h-64 w-full">
                            <canvas id="progress-timeline-chart"></canvas>
                        </div>
                    </div>

                    <!-- Unified Verbal Competency Index -->
                    <div class="bg-slate-950/40 border border-slate-800 rounded-3xl p-6 space-y-6">
                        <div class="flex items-center justify-between border-b border-slate-850 pb-4">
                            <h3 class="text-md font-bold text-white flex items-center gap-2">
                                <span class="w-1 h-4 bg-indigo-500 rounded-full"></span>
                                Unified Verbal Competency Index (IELTS/PTE Averages)
                            </h3>
                            <span class="text-xs text-slate-500">Average metric scores</span>
                        </div>

                        <!-- Linguistic Details Row -->
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 bg-slate-900/35 border border-slate-850 rounded-2xl p-4">
                            <div class="space-y-1">
                                <span class="text-[10px] text-slate-500 uppercase font-bold tracking-wider">Primary Language</span>
                                <p class="text-sm font-semibold text-white">{{ $report->primary_language }}</p>
                            </div>
                            <div class="space-y-1">
                                <span class="text-[10px] text-slate-500 uppercase font-bold tracking-wider">Accent Dialect</span>
                                <p class="text-sm font-semibold text-white">{{ $report->primary_accent }}</p>
                            </div>
                            <div class="space-y-1">
                                <span class="text-[10px] text-slate-500 uppercase font-bold tracking-wider">Spoken Tone</span>
                                <p class="text-sm font-semibold text-white">{{ $report->primary_tone }}</p>
                            </div>
                        </div>

                        <!-- Scores Grid -->
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                            <!-- Grammar -->
                            <div class="space-y-1.5 p-3 rounded-xl bg-slate-900/20 border border-slate-850/80">
                                <div class="flex justify-between text-xs font-semibold text-slate-400">
                                    <span>Grammar</span>
                                    <span class="text-white">{{ number_format($report->grammar_average, 2) }}%</span>
                                </div>
                                <div class="h-1.5 w-full bg-slate-900 border border-slate-800 rounded-full overflow-hidden">
                                    <div class="bg-indigo-500 h-full rounded-full" style="width: {{ $report->grammar_average }}%;"></div>
                                </div>
                            </div>
                            <!-- Vocabulary -->
                            <div class="space-y-1.5 p-3 rounded-xl bg-slate-900/20 border border-slate-850/80">
                                <div class="flex justify-between text-xs font-semibold text-slate-400">
                                    <span>Vocabulary</span>
                                    <span class="text-white">{{ number_format($report->vocabulary_average, 2) }}%</span>
                                </div>
                                <div class="h-1.5 w-full bg-slate-900 border border-slate-800 rounded-full overflow-hidden">
                                    <div class="bg-indigo-500 h-full rounded-full" style="width: {{ $report->vocabulary_average }}%;"></div>
                                </div>
                            </div>
                            <!-- Accuracy -->
                            <div class="space-y-1.5 p-3 rounded-xl bg-slate-900/20 border border-slate-850/80">
                                <div class="flex justify-between text-xs font-semibold text-slate-400">
                                    <span>Accuracy</span>
                                    <span class="text-white">{{ number_format($report->accuracy_average, 2) }}%</span>
                                </div>
                                <div class="h-1.5 w-full bg-slate-900 border border-slate-800 rounded-full overflow-hidden">
                                    <div class="bg-cyan-500 h-full rounded-full" style="width: {{ $report->accuracy_average }}%;"></div>
                                </div>
                            </div>
                            <!-- Content Relevancy -->
                            <div class="space-y-1.5 p-3 rounded-xl bg-slate-900/20 border border-slate-850/80">
                                <div class="flex justify-between text-xs font-semibold text-slate-400">
                                    <span>Content Relevancy</span>
                                    <span class="text-white">{{ number_format($report->content_average, 2) }}%</span>
                                </div>
                                <div class="h-1.5 w-full bg-slate-900 border border-slate-800 rounded-full overflow-hidden">
                                    <div class="bg-cyan-500 h-full rounded-full" style="width: {{ $report->content_average }}%;"></div>
                                </div>
                            </div>
                            <!-- Confidence -->
                            <div class="space-y-1.5 p-3 rounded-xl bg-slate-900/20 border border-slate-850/80">
                                <div class="flex justify-between text-xs font-semibold text-slate-400">
                                    <span>Confidence</span>
                                    <span class="text-white">{{ number_format($report->confidence_average, 2) }}%</span>
                                </div>
                                <div class="h-1.5 w-full bg-slate-900 border border-slate-800 rounded-full overflow-hidden">
                                    <div class="bg-indigo-500 h-full rounded-full" style="width: {{ $report->confidence_average }}%;"></div>
                                </div>
                            </div>
                            <!-- Pronunciation -->
                            <div class="space-y-1.5 p-3 rounded-xl bg-slate-900/20 border border-slate-850/80">
                                <div class="flex justify-between text-xs font-semibold text-slate-400">
                                    <span>Pronunciation</span>
                                    <span class="text-white">{{ number_format($report->pronunciation_average, 2) }}%</span>
                                </div>
                                <div class="h-1.5 w-full bg-slate-900 border border-slate-800 rounded-full overflow-hidden">
                                    <div class="bg-indigo-500 h-full rounded-full" style="width: {{ $report->pronunciation_average }}%;"></div>
                                </div>
                            </div>
                            <!-- Fluency -->
                            <div class="space-y-1.5 p-3 rounded-xl bg-slate-900/20 border border-slate-850/80">
                                <div class="flex justify-between text-xs font-semibold text-slate-400">
                                    <span>Fluency</span>
                                    <span class="text-white">{{ number_format($report->fluency_average, 2) }}%</span>
                                </div>
                                <div class="h-1.5 w-full bg-slate-900 border border-slate-800 rounded-full overflow-hidden">
                                    <div class="bg-cyan-500 h-full rounded-full" style="width: {{ $report->fluency_average }}%;"></div>
                                </div>
                            </div>
                            <!-- Communication -->
                            <div class="space-y-1.5 p-3 rounded-xl bg-slate-900/20 border border-slate-850/80">
                                <div class="flex justify-between text-xs font-semibold text-slate-400">
                                    <span>Communication</span>
                                    <span class="text-white">{{ number_format($report->communication_average, 2) }}%</span>
                                </div>
                                <div class="h-1.5 w-full bg-slate-900 border border-slate-800 rounded-full overflow-hidden">
                                    <div class="bg-cyan-500 h-full rounded-full" style="width: {{ $report->communication_average }}%;"></div>
                                </div>
                            </div>
                        </div>

                        <!-- Timing and pause stats -->
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 border-t border-slate-850 pt-4">
                            <div class="flex justify-between text-xs p-2 bg-slate-900/10 border border-slate-850/50 rounded-lg">
                                <span class="font-semibold text-slate-400">Speech Rate:</span>
                                <span class="font-bold text-white font-mono">{{ number_format($report->wpm_average, 2) }} WPM</span>
                            </div>
                            <div class="flex justify-between text-xs p-2 bg-slate-900/10 border border-slate-850/50 rounded-lg">
                                <span class="font-semibold text-slate-400">Average Pause Count:</span>
                                <span class="font-bold text-white font-mono">{{ number_format($report->pause_count_average, 2) }} pauses</span>
                            </div>
                            <div class="flex justify-between text-xs p-2 bg-slate-900/10 border border-slate-850/50 rounded-lg">
                                <span class="font-semibold text-slate-400">Average Pause Duration:</span>
                                <span class="font-bold text-white font-mono">{{ number_format($report->pause_duration_average, 2) }} sec</span>
                            </div>
                        </div>
                    </div>

                    <!-- Improvement Areas / SWOT checklist -->
                    <div class="bg-slate-950/40 border border-slate-800 rounded-3xl p-6 space-y-6">
                        <div class="border-b border-slate-850 pb-4">
                            <h3 class="text-md font-bold text-white flex items-center gap-2">
                                <span class="w-1 h-4 bg-indigo-500 rounded-full"></span>
                                Dynamically Aggregated SWOT Suggestions
                            </h3>
                        </div>

                        @if(empty($report->improvement_areas))
                            <p class="text-xs text-slate-500 italic">No SWOT analysis results generated yet. Perform more tests to details mapping.</p>
                        @else
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                @foreach($report->improvement_areas as $area)
                                    @php
                                        $isCritical = $area['status'] === 'critical';
                                        $isModerate = $area['status'] === 'moderate';
                                        
                                        if ($isCritical) {
                                            $borderClass = 'border-rose-900/30 bg-rose-950/5';
                                            $badgeClass = 'bg-rose-500/10 text-rose-400 border border-rose-500/20';
                                            $badgeText = 'Requires Action';
                                        } elseif ($isModerate) {
                                            $borderClass = 'border-amber-900/30 bg-amber-950/5';
                                            $badgeClass = 'bg-amber-500/10 text-amber-400 border border-amber-500/20';
                                            $badgeText = 'Needs Improvement';
                                        } else {
                                            $borderClass = 'border-emerald-900/30 bg-emerald-950/5';
                                            $badgeClass = 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/20';
                                            $badgeText = 'Core Strength';
                                        }
                                    @endphp
                                    
                                    <div class="p-4 border rounded-2xl {{ $borderClass }} space-y-3 flex flex-col justify-between">
                                        <div class="space-y-1">
                                            <div class="flex items-center justify-between">
                                                <h4 class="text-sm font-bold text-white">{{ $area['metric'] }}</h4>
                                                <span class="text-[9px] px-2 py-0.5 rounded-full font-bold uppercase tracking-wider {{ $badgeClass }}">{{ $badgeText }}</span>
                                            </div>
                                            <p class="text-xs text-slate-350 leading-relaxed">{{ $area['comment'] }}</p>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>

            </div>
        @endif

    </div>

    <!-- Chart.js integration dependencies -->
    @if($report && count($report->progress_data ?? []) > 0)
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script>
            $(function() {
                const ctx = $('#progress-timeline-chart')[0].getContext('2d');
                
                // Parse timeline parameters from PHP variable
                const rawData = @json($report->progress_data);
                
                const labels = rawData.map(item => item.date);
                const scores = rawData.map(item => item.score);
                const types = rawData.map(item => item.type);
                const titles = rawData.map(item => item.label);

                // Chart.js Configuration
                new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: labels,
                        datasets: [{
                            label: 'Overall score timeline (%)',
                            data: scores,
                            borderColor: '#6366f1', // Indigo 500
                            backgroundColor: 'rgba(99, 102, 241, 0.05)',
                            fill: true,
                            tension: 0.35,
                            borderWidth: 3,
                            pointBackgroundColor: '#818cf8',
                            pointBorderColor: '#0f172a',
                            pointBorderWidth: 2,
                            pointRadius: 5,
                            pointHoverRadius: 7
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                display: false
                            },
                            tooltip: {
                                backgroundColor: '#0f172a',
                                titleColor: '#fff',
                                bodyColor: '#cbd5e1',
                                borderColor: '#334155',
                                borderWidth: 1,
                                padding: 12,
                                displayColors: false,
                                callbacks: {
                                    title: function(context) {
                                        const index = context[0].dataIndex;
                                        return `${labels[index]} (${types[index]})`;
                                    },
                                    label: function(context) {
                                        const index = context.dataIndex;
                                        return [
                                            `Score: ${scores[index]}%`,
                                            `Task: ${titles[index]}`
                                        ];
                                    }
                                }
                            }
                        },
                        scales: {
                            x: {
                                grid: {
                                    color: 'rgba(51, 65, 85, 0.15)',
                                    drawBorder: false
                                },
                                ticks: {
                                    color: '#64748b',
                                    font: {
                                        size: 10
                                    }
                                }
                            },
                            y: {
                                min: 0,
                                max: 100,
                                grid: {
                                    color: 'rgba(51, 65, 85, 0.15)',
                                    drawBorder: false
                                },
                                ticks: {
                                    color: '#64748b',
                                    stepSize: 20,
                                    font: {
                                        size: 10
                                    }
                                }
                            }
                        }
                    }
                });
            });
        </script>
    @endif
</x-user-layout>
