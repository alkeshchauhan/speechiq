<?php

namespace App\Http\Controllers;

use App\Services\AudioRecordingService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class AudioRecordingController extends Controller
{
    protected AudioRecordingService $audioRecordingService;

    public function __construct(AudioRecordingService $audioRecordingService)
    {
        $this->audioRecordingService = $audioRecordingService;
    }

    /**
     * Display the practice recording sandbox.
     */
    public function index()
    {
        return view('practice.record');
    }

    /**
     * Store the uploaded audio recording.
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'audio_file' => 'required|file|max:15000', // max 15MB
            'duration' => 'required|numeric',
            'question_id' => 'nullable|exists:questions,id',
        ]);

        $file = $request->file('audio_file');
        $userId = auth()->id();
        $questionId = $request->input('question_id');
        $duration = (float) $request->input('duration');

        $recording = $this->audioRecordingService->storeAudio($file, $userId, $questionId, $duration);

        return response()->json([
            'success' => true,
            'message' => 'Audio recording uploaded and stored successfully!',
            'data' => [
                'id' => $recording->id,
                'audio_path' => $recording->audio_path,
                'audio_url' => asset('storage/' . $recording->audio_path),
                'duration' => $recording->duration,
                'status' => $recording->status,
                'created_at' => $recording->created_at->toDateTimeString(),
            ]
        ]);
    }
}
