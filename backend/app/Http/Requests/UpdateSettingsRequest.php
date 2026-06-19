<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSettingsRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->hasRole('admin');
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'GEMINI_API_KEY' => 'nullable|string',
            'GEMINI_MODEL' => 'required|string',
            'AI_API_URL' => 'required|url',
            'AI_API_TOKEN' => 'nullable|string',
            'ENABLE_AI_INTERVIEW' => 'nullable|in:1,0,on,off,true,false',
            'ENABLE_READ_ALOUD' => 'nullable|in:1,0,on,off,true,false',
            'ENABLE_TTS' => 'nullable|in:1,0,on,off,true,false',
            'ENABLE_STT' => 'nullable|in:1,0,on,off,true,false',
        ];
    }
}
