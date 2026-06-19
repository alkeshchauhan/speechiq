<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SpeechIQ Voice Assessment Report - {{ auth()->user()->name }}</title>
    <!-- Tailwind CSS loaded for clean print styling -->
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @media print {
            body {
                background: white;
                color: black;
            }
            .no-print {
                display: none;
            }
            .page-break {
                page-break-before: always;
            }
        }
    </style>
</head>
<body class="bg-white text-slate-900 font-sans p-8 max-w-4xl mx-auto">

    <!-- Print Control Bar -->
    <div class="no-print flex justify-between items-center bg-slate-100 border border-slate-200 rounded-xl p-4 mb-8">
        <span class="text-sm font-semibold text-slate-700">SpeechIQ Report PDF Print Tool</span>
        <div class="flex gap-2">
            <button onclick="window.print()" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-bold rounded-lg text-xs transition">
                Print Report
            </button>
            <button onclick="window.close()" class="px-4 py-2 bg-slate-200 hover:bg-slate-350 text-slate-800 font-bold rounded-lg text-xs transition">
                Close Window
            </button>
        </div>
    </div>

    <!-- Report Document -->
    <div class="space-y-8">
        <!-- Title Header -->
        <div class="border-b-4 border-slate-900 pb-6">
            <div class="flex justify-between items-start">
                <div class="space-y-1">
                    <h1 class="text-3xl font-extrabold tracking-tight uppercase">SpeechIQ Assessment Report</h1>
                    <p class="text-sm text-slate-500">Comprehensive Verbal Quality & Competency Index</p>
                </div>
                <div class="text-right text-xs text-slate-500">
                    <p>Report ID: #SR-{{ $report->id }}-{{ time() }}</p>
                    <p>Generated: {{ date('Y-m-d H:i') }}</p>
                </div>
            </div>
        </div>

        <!-- Candidate Meta Info Block -->
        <div class="grid grid-cols-3 gap-6 bg-slate-50 border border-slate-200 rounded-xl p-6">
            <div>
                <span class="text-[10px] text-slate-500 uppercase font-bold tracking-wider">Candidate Name</span>
                <p class="text-md font-bold text-slate-950">{{ auth()->user()->name }}</p>
            </div>
            <div>
                <span class="text-[10px] text-slate-500 uppercase font-bold tracking-wider">Candidate Email</span>
                <p class="text-md font-bold text-slate-950">{{ auth()->user()->email }}</p>
            </div>
            <div>
                <span class="text-[10px] text-slate-500 uppercase font-bold tracking-wider">Total Voice Sessions</span>
                <p class="text-md font-bold text-slate-950">{{ $report->total_tests_taken }} Completed Attempts</p>
            </div>
        </div>

        <!-- Averages Summary Cards -->
        <div class="grid grid-cols-3 gap-6 text-center">
            <!-- Overall rating -->
            <div class="p-6 border-2 border-slate-900 rounded-2xl space-y-2">
                <span class="text-[10px] text-slate-500 uppercase font-bold tracking-wider">Overall Competency</span>
                <div class="text-4xl font-black text-slate-950 font-mono">{{ $report->overall_score }}%</div>
                <p class="text-xs text-slate-600">Unified average scale</p>
            </div>
            
            <!-- Read Aloud -->
            <div class="p-6 border border-slate-200 rounded-2xl space-y-2">
                <span class="text-[10px] text-slate-500 uppercase font-bold tracking-wider">Read Aloud Average</span>
                <div class="text-3xl font-bold text-slate-950 font-mono">{{ $report->read_aloud_average }}%</div>
                <p class="text-xs text-slate-600">Paragraph reading accuracy</p>
            </div>

            <!-- AI Interview -->
            <div class="p-6 border border-slate-200 rounded-2xl space-y-2">
                <span class="text-[10px] text-slate-500 uppercase font-bold tracking-wider">AI Interview Average</span>
                <div class="text-3xl font-bold text-slate-950 font-mono">{{ $report->interview_average }}%</div>
                <p class="text-xs text-slate-600">Conversational verbal score</p>
            </div>
        </div>

        <!-- Progress Timeline Table -->
        <div class="space-y-4">
            <h3 class="text-lg font-bold text-slate-900 border-b-2 border-slate-900 pb-1">Verbal Attempt History Timeline</h3>
            <table class="w-full text-left text-xs border border-slate-200">
                <thead>
                    <tr class="bg-slate-100 border-b border-slate-200">
                        <th class="p-3 font-bold">Attempt Timestamp</th>
                        <th class="p-3 font-bold">Exercise Type</th>
                        <th class="p-3 font-bold">Topic / Task Details</th>
                        <th class="p-3 font-bold text-right">Overall Rating Score</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($report->progress_data ?? [] as $timeline)
                        <tr class="border-b border-slate-200">
                            <td class="p-3 font-mono">{{ $timeline['date'] }}</td>
                            <td class="p-3">{{ $timeline['type'] }}</td>
                            <td class="p-3 italic text-slate-700">{{ $timeline['label'] }}</td>
                            <td class="p-3 font-bold text-right font-mono">{{ $timeline['score'] }}%</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- SWOT Action items -->
        <div class="space-y-4 page-break pt-4">
            <h3 class="text-lg font-bold text-slate-900 border-b-2 border-slate-900 pb-1">Verbal Areas of Improvement & SWOT Breakdown</h3>
            <div class="space-y-4">
                @foreach($report->improvement_areas ?? [] as $area)
                    @php
                        $isCritical = $area['status'] === 'critical';
                        $isModerate = $area['status'] === 'moderate';
                        $statusText = $isCritical ? 'High Priority' : ($isModerate ? 'Moderate Priority' : 'Core Competency');
                        $textColor = $isCritical ? 'text-red-800' : ($isModerate ? 'text-amber-800' : 'text-emerald-800');
                    @endphp
                    <div class="p-4 bg-slate-50 border border-slate-200 rounded-xl space-y-1">
                        <div class="flex justify-between items-center">
                            <h4 class="text-sm font-bold text-slate-950">{{ $area['metric'] }}</h4>
                            <span class="text-[9px] font-black uppercase {{ $textColor }}">{{ $statusText }}</span>
                        </div>
                        <p class="text-xs text-slate-700 leading-relaxed">{{ $area['comment'] }}</p>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Footer parameters -->
        <div class="pt-8 border-t border-slate-200 text-center text-[10px] text-slate-400">
            <p>SpeechIQ Voice Assessment Platform © 2026. All rights reserved.</p>
        </div>
    </div>

    <!-- Print on load logic -->
    <script>
        window.addEventListener('load', () => {
            // Give layout a short moment to render
            setTimeout(() => {
                window.print();
            }, 500);
        });
    </script>
</body>
</html>
