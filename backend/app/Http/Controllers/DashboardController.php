<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Test;
use App\Models\Setting;
use App\Models\AudioRecording;
use App\Models\ReadAloudResult;
use App\Models\InterviewResult;
use App\Models\AnalysisReport;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Display candidate dashboard metrics.
     */
    public function candidateDashboard()
    {
        $userId = auth()->id();

        // 1. Practices completed count
        $totalCompleted = AudioRecording::where('user_id', $userId)
            ->where('status', 'completed')
            ->count();

        // 2. Category averages
        $readAloudAvg = (int) round(
            ReadAloudResult::whereHas('audioRecording', function ($query) use ($userId) {
                $query->where('user_id', $userId);
            })->avg('overall_score') ?? 0
        );

        $interviewAvg = (int) round(
            InterviewResult::whereHas('audioRecording', function ($query) use ($userId) {
                $query->where('user_id', $userId);
            })->avg('overall_score') ?? 0
        );

        // 3. User report
        $report = AnalysisReport::where('user_id', $userId)->first();

        // 4. Past 5 completed attempts
        $pastAttempts = AudioRecording::where('user_id', $userId)
            ->where('status', 'completed')
            ->with(['question.test', 'readAloudResult', 'interviewResult'])
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        return view('dashboard', compact(
            'totalCompleted',
            'readAloudAvg',
            'interviewAvg',
            'report',
            'pastAttempts'
        ));
    }

    /**
     * Display admin system-wide dashboard metrics.
     */
    public function adminDashboard()
    {
        $totalUsers = User::count();
        $activeTestsCount = Test::where('is_active', true)->count();
        $completedAnalysesCount = AudioRecording::where('status', 'completed')->count();
        $activeSettingsCount = Setting::count();

        return view('admin.dashboard', compact(
            'totalUsers',
            'activeTestsCount',
            'completedAnalysesCount',
            'activeSettingsCount'
        ));
    }
}
