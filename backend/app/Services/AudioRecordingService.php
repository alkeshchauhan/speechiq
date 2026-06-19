<?php

namespace App\Services;

use App\Repositories\Contracts\AudioRecordingRepositoryInterface;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class AudioRecordingService extends BaseService
{
    protected AudioRecordingRepositoryInterface $audioRecordingRepository;

    public function __construct(AudioRecordingRepositoryInterface $audioRecordingRepository)
    {
        parent::__construct($audioRecordingRepository);
        $this->audioRecordingRepository = $audioRecordingRepository;
    }

    /**
     * Store an uploaded audio file and record its metadata.
     */
    public function storeAudio(UploadedFile $file, int $userId, ?int $questionId, float $duration): \App\Models\AudioRecording
    {
        // Get correct extension (fallback to mp3/wav/webm if empty)
        $ext = $file->getClientOriginalExtension() ?: 'webm';
        
        // Generate unique name for the file
        $fileName = 'user_' . $userId . '_' . time() . '_' . uniqid() . '.' . $ext;
        
        // Store the file on public disk (storage/app/public/recordings)
        $path = $file->storeAs('recordings', $fileName, 'public');

        // Insert into database
        return $this->audioRecordingRepository->create([
            'user_id' => $userId,
            'question_id' => $questionId,
            'audio_path' => $path,
            'duration' => $duration,
            'status' => 'pending',
        ]);
    }
}
