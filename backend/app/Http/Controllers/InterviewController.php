<?php

namespace App\Http\Controllers;

use App\Services\TestService;
use App\Services\QuestionService;
use App\Services\AudioRecordingService;
use App\Services\InterviewResultService;
use App\Services\AiEngineService;
use App\Jobs\AnalyzeInterviewJob;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class InterviewController extends Controller
{
    protected TestService $testService;
    protected QuestionService $questionService;
    protected AudioRecordingService $audioRecordingService;
    protected InterviewResultService $interviewResultService;
    protected AiEngineService $aiEngineService;

    public function __construct(
        TestService $testService,
        QuestionService $questionService,
        AudioRecordingService $audioRecordingService,
        InterviewResultService $interviewResultService,
        AiEngineService $aiEngineService
    ) {
        $this->testService = $testService;
        $this->questionService = $questionService;
        $this->audioRecordingService = $audioRecordingService;
        $this->interviewResultService = $interviewResultService;
        $this->aiEngineService = $aiEngineService;
    }

    /**
     * List all AI Interview tests.
     */
    public function index()
    {
        $tests = $this->testService->all()->where('type', 'AI_INTERVIEW')->where('is_active', true);
        return view('interview.index', compact('tests'));
    }

    /**
     * Show the interactive AI Interview practice workspace.
     */
    public function show(int $testId)
    {
        $test = $this->testService->find($testId);
        if (!$test || $test->type !== 'AI_INTERVIEW' || !$test->is_active) {
            abort(404, 'Interview test not found.');
        }

        // Get the baseline template question for setting parameters
        $question = $test->questions()->first();
        if (!$question) {
            return redirect()->route('practice.interview.index')->with('error', 'This interview does not contain any starting queries.');
        }

        return view('interview.show', compact('test', 'question'));
    }

    /**
     * Upload response audio and dispatch analysis job.
     */
    public function submit(Request $request, int $testId): JsonResponse
    {
        $request->validate([
            'audio_file' => 'required|file|max:15000',
            'duration' => 'required|numeric',
            'question_text' => 'required|string',
        ]);

        $file = $request->file('audio_file');
        $userId = auth()->id();
        $duration = (float) $request->input('duration');
        $questionText = $request->input('question_text');

        $test = $this->testService->find($testId);
        if (!$test) {
            return response()->json(['success' => false, 'message' => 'Test not found.'], 404);
        }

        // Find the first question of this test to link it inside audio_recordings
        $question = $test->questions()->first();
        $questionId = $question ? $question->id : null;

        // 1. Store audio recording in DB and public filesystem
        $recording = $this->audioRecordingService->storeAudio($file, $userId, $questionId, $duration);

        // 2. Dispatch background analysis job
        AnalyzeInterviewJob::dispatch($recording, $questionText);

        return response()->json([
            'success' => true,
            'recording_id' => $recording->id,
            'message' => 'Recording uploaded! AI is analyzing your response...'
        ]);
    }

    /**
     * Poll status of the interview response.
     */
    public function status(int $recordingId): JsonResponse
    {
        $recording = $this->audioRecordingService->find($recordingId);
        if (!$recording || $recording->user_id !== auth()->id()) {
            return response()->json(['success' => false, 'message' => 'Recording not found.'], 404);
        }

        $result = $this->interviewResultService->getByRecording($recordingId);

        return response()->json([
            'success' => true,
            'status' => $recording->status, // pending, processing, completed, failed
            'result_id' => $result ? $result->id : null,
        ]);
    }

    /**
     * Fetch the detailed analysis result values.
     */
    public function results(int $recordingId): JsonResponse
    {
        $recording = $this->audioRecordingService->find($recordingId);
        if (!$recording || $recording->user_id !== auth()->id()) {
            return response()->json(['success' => false, 'message' => 'Recording not found.'], 404);
        }

        $result = $this->interviewResultService->getByRecording($recordingId);
        if (!$result) {
            return response()->json(['success' => false, 'message' => 'Results not ready.'], 400);
        }

        return response()->json([
            'success' => true,
            'result' => $result
        ]);
    }

    /**
     * Generate the next dynamic interview question based on history.
     */
    public function generateNextQuestion(Request $request, int $testId): JsonResponse
    {
        $request->validate([
            'context' => 'required|string',
            'history' => 'nullable|array'
        ]);

        $context = $request->input('context');
        $history = $request->input('history', []);

        try {
            $nextQuestionText = $this->aiEngineService->generateQuestion($context, $history);
            return response()->json([
                'success' => true,
                'question' => $nextQuestionText
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Could not generate follow-up question: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display dynamic AI Interview attempt results review view card.
     */
    public function resultsView(int $recordingId)
    {
        $recording = $this->audioRecordingService->find($recordingId);
        if (!$recording || $recording->user_id !== auth()->id()) {
            abort(404, 'Recording not found.');
        }

        $result = $this->interviewResultService->getByRecording($recordingId);
        if (!$result) {
            return redirect()->route('dashboard')
                ->with('error', 'Analysis results are not ready yet.');
        }

        $question = $recording->question;

        return view('interview.results', compact('recording', 'result', 'question'));
    }

    /**
     * Convert text to speech using the AI Engine and return base64 audio.
     */
    public function textToSpeech(Request $request): JsonResponse
    {
        $request->validate([
            'text' => 'required|string|max:1000'
        ]);

        try {
            $audioDataUri = $this->aiEngineService->textToSpeech($request->input('text'));
            return response()->json([
                'success' => true,
                'audio_data' => $audioDataUri,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'TTS generation failed: ' . $e->getMessage()
            ], 500);
        }
    }
}
