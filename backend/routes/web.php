<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\TestController;
use App\Http\Controllers\Admin\QuestionController;
use App\Http\Controllers\AudioRecordingController;
use App\Http\Controllers\ReadAloudController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', [\App\Http\Controllers\DashboardController::class, 'candidateDashboard'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    
    // Audio Recording Sandbox
    Route::get('/practice/record', [AudioRecordingController::class, 'index'])->name('practice.record');
    Route::post('/audio-recordings', [AudioRecordingController::class, 'store'])->name('audio-recordings.store');

    // Read Aloud Practice
    Route::get('/practice/read-aloud', [ReadAloudController::class, 'index'])->name('practice.read-aloud.index');
    Route::get('/practice/read-aloud/{test}', [ReadAloudController::class, 'show'])->name('practice.read-aloud.show');
    Route::post('/practice/read-aloud/{test}/{question}/submit', [ReadAloudController::class, 'submit'])->name('practice.read-aloud.submit');
    Route::get('/practice/read-aloud/status/{recording}', [ReadAloudController::class, 'status'])->name('practice.read-aloud.status');
    Route::get('/practice/read-aloud/results/{recording}', [ReadAloudController::class, 'results'])->name('practice.read-aloud.results');

    // AI Interview Practice
    Route::get('/practice/interview', [\App\Http\Controllers\InterviewController::class, 'index'])->name('practice.interview.index');
    Route::get('/practice/interview/{test}', [\App\Http\Controllers\InterviewController::class, 'show'])->name('practice.interview.show');
    Route::post('/practice/interview/{test}/submit', [\App\Http\Controllers\InterviewController::class, 'submit'])->name('practice.interview.submit');
    Route::get('/practice/interview/status/{recording}', [\App\Http\Controllers\InterviewController::class, 'status'])->name('practice.interview.status');
    Route::get('/practice/interview/results/{recording}', [\App\Http\Controllers\InterviewController::class, 'results'])->name('practice.interview.results');
    Route::get('/practice/interview/results/{recording}/view', [\App\Http\Controllers\InterviewController::class, 'resultsView'])->name('practice.interview.results-view');
    Route::post('/practice/interview/{test}/next-question', [\App\Http\Controllers\InterviewController::class, 'generateNextQuestion'])->name('practice.interview.next-question');
    Route::post('/practice/tts', [\App\Http\Controllers\InterviewController::class, 'textToSpeech'])->name('practice.tts');

    // Report Module
    Route::get('/practice/reports', [\App\Http\Controllers\ReportController::class, 'index'])->name('practice.reports.index');
    Route::get('/practice/reports/{report}/download', [\App\Http\Controllers\ReportController::class, 'download'])->name('practice.reports.download');
});

// Admin Control Panel Routes
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [\App\Http\Controllers\DashboardController::class, 'adminDashboard'])->name('dashboard');

    Route::get('/settings', [SettingController::class, 'index'])->name('settings');
    Route::put('/settings', [SettingController::class, 'update'])->name('settings.update');
    Route::post('/settings/test-connection', [SettingController::class, 'testConnection'])->name('settings.test-connection');
 
    // Admin Reports dashboard
    Route::get('/reports', [\App\Http\Controllers\ReportController::class, 'adminIndex'])->name('reports.index');

    // Test Management Routes
    Route::resource('tests', TestController::class);
    Route::post('tests/{test}/sections', [QuestionController::class, 'storeSection'])->name('tests.sections.store');
    Route::delete('tests/{test}/sections/{section}', [QuestionController::class, 'destroySection'])->name('tests.sections.destroy');
    Route::post('tests/{test}/questions', [QuestionController::class, 'storeQuestion'])->name('tests.questions.store');
    Route::get('tests/{test}/questions/{question}/edit', [QuestionController::class, 'editQuestion'])->name('tests.questions.edit');
    Route::put('tests/{test}/questions/{question}', [QuestionController::class, 'updateQuestion'])->name('tests.questions.update');
    Route::delete('tests/{test}/questions/{question}', [QuestionController::class, 'destroyQuestion'])->name('tests.questions.destroy');
});

require __DIR__.'/auth.php';
