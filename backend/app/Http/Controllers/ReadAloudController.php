<?php

namespace App\Http\Controllers;

use App\Services\TestService;
use App\Services\QuestionService;
use App\Services\AudioRecordingService;
use App\Services\ReadAloudResultService;
use App\Jobs\AnalyzeReadAloudJob;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ReadAloudController extends Controller
{
    protected TestService $testService;
    protected QuestionService $questionService;
    protected AudioRecordingService $audioRecordingService;
    protected ReadAloudResultService $readAloudResultService;

    public function __construct(
        TestService $testService,
        QuestionService $questionService,
        AudioRecordingService $audioRecordingService,
        ReadAloudResultService $readAloudResultService
    ) {
        $this->testService = $testService;
        $this->questionService = $questionService;
        $this->audioRecordingService = $audioRecordingService;
        $this->readAloudResultService = $readAloudResultService;
    }

    /**
     * List all Read Aloud tests.
     */
    public function index()
    {
        $tests = $this->testService->all()->where('type', 'READ_ALOUD')->where('is_active', true);
        return view('read-aloud.index', compact('tests'));
    }

    /**
     * Show a specific Read Aloud test.
     */
    public function show(int $testId)
    {
        $test = $this->testService->find($testId);
        if (!$test || $test->type !== 'READ_ALOUD' || !$test->is_active) {
            abort(404, 'Test not found.');
        }

        // Get the first question in the test
        $question = $test->questions()->first();
        if (!$question) {
            return redirect()->route('practice.read-aloud.index')->with('error', 'This test does not contain any paragraphs yet.');
        }

        return view('read-aloud.show', compact('test', 'question'));
    }

    /**
     * Upload recording and dispatch queue job.
     */
    public function submit(Request $request, int $testId, int $questionId): JsonResponse
    {
        $request->validate([
            'audio_file' => 'required|file|max:15000',
            'duration' => 'required|numeric',
        ]);

        $file = $request->file('audio_file');
        $userId = auth()->id();
        $duration = (float) $request->input('duration');

        // Retrieve target question text
        $question = $this->questionService->find($questionId);
        if (!$question) {
            return response()->json(['success' => false, 'message' => 'Question not found.'], 404);
        }

        // 1. Store audio recording in DB and file system
        $recording = $this->audioRecordingService->storeAudio($file, $userId, $questionId, $duration);

        // 2. Dispatch the background queue job
        AnalyzeReadAloudJob::dispatch($recording, $question->question_text);

        return response()->json([
            'success' => true,
            'recording_id' => $recording->id,
            'message' => 'Recording uploaded! Analyzing your voice...'
        ]);
    }

    /**
     * Check analysis status.
     */
    public function status(int $recordingId): JsonResponse
    {
        $recording = $this->audioRecordingService->find($recordingId);
        if (!$recording || $recording->user_id !== auth()->id()) {
            return response()->json(['success' => false, 'message' => 'Recording not found.'], 404);
        }

        $result = $this->readAloudResultService->getByRecording($recordingId);

        return response()->json([
            'success' => true,
            'status' => $recording->status, // pending, processing, completed, failed
            'result_id' => $result ? $result->id : null,
        ]);
    }

    /**
     * Show analysis results.
     */
    public function results(int $recordingId)
    {
        $recording = $this->audioRecordingService->find($recordingId);
        if (!$recording || $recording->user_id !== auth()->id()) {
            abort(404, 'Recording not found.');
        }

        $result = $this->readAloudResultService->getByRecording($recordingId);
        if (!$result) {
            return redirect()->route('practice.read-aloud.show', $recording->question->test_id)
                ->with('error', 'Analysis results are not ready yet.');
        }

        $question = $recording->question;

        return view('read-aloud.results', compact('recording', 'result', 'question'));
    }
}
