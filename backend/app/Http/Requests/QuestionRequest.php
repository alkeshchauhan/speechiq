<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class QuestionRequest extends FormRequest
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
            'test_id' => 'required|exists:tests,id',
            'test_section_id' => 'nullable|exists:test_sections,id',
            'question_text' => 'required|string',
            'question_type' => 'required|in:READ_ALOUD,AI_INTERVIEW',
            'sort_order' => 'nullable|integer',
        ];
    }
}
