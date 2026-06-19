<?php

namespace App\Http\Controllers;

use App\Services\AnalysisReportService;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    protected AnalysisReportService $reportService;

    public function __construct(AnalysisReportService $reportService)
    {
        $this->reportService = $reportService;
    }

    /**
     * Show candidate performance report dashboard.
     */
    public function index()
    {
        $userId = auth()->id();
        
        // Compile/update the latest stats synchronously for real-time accuracy
        $report = $this->reportService->generateUserReport($userId);
        
        return view('report.index', compact('report'));
    }

    /**
     * Display a print-ready view for PDF export.
     */
    public function download(int $reportId)
    {
        $report = $this->reportService->find($reportId);
        
        if (!$report || ($report->user_id !== auth()->id() && !auth()->user()->hasRole('admin'))) {
            abort(404, 'Report not found.');
        }

        return view('report.print', compact('report'));
    }

    /**
     * Display a listing of all candidates reports to administrators.
     */
    public function adminIndex()
    {
        if (!auth()->user()->hasRole('admin')) {
            abort(403, 'Unauthorized action.');
        }

        $reports = \App\Models\AnalysisReport::with('user')->paginate(15);
        $totalReports = \App\Models\AnalysisReport::count();
        $averageOverallScore = (int) round(\App\Models\AnalysisReport::avg('overall_score') ?? 0);

        return view('admin.reports.index', compact('reports', 'totalReports', 'averageOverallScore'));
    }
}
