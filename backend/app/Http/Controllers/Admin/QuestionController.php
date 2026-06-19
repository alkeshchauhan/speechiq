<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\QuestionRequest;
use App\Services\QuestionService;
use App\Services\TestSectionService;
use App\Services\TestService;
use Illuminate\Http\Request;

class QuestionController extends Controller
{
    protected QuestionService $questionService;
    protected TestSectionService $testSectionService;
    protected TestService $testService;

    public function __construct(
        QuestionService $questionService,
        TestSectionService $testSectionService,
        TestService $testService
    ) {
        $this->questionService = $questionService;
        $this->testSectionService = $testSectionService;
        $this->testService = $testService;
    }

    /**
     * Store a new section for the test.
     */
    public function storeSection(Request $request, int $testId)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'sort_order' => 'nullable|integer',
        ]);

        $this->testSectionService->create([
            'test_id' => $testId,
            'title' => $request->input('title'),
            'description' => $request->input('description'),
            'sort_order' => $request->input('sort_order', 0),
        ]);

        return redirect()->route('admin.tests.show', $testId)->with('success', 'Section added successfully.');
    }

    /**
     * Delete a section from the test.
     */
    public function destroySection(int $testId, int $id)
    {
        $this->testSectionService->delete($id);
        return redirect()->route('admin.tests.show', $testId)->with('success', 'Section deleted successfully.');
    }

    /**
     * Store a new question.
     */
    public function storeQuestion(QuestionRequest $request, int $testId)
    {
        $data = $request->validated();
        $this->questionService->create($data);

        return redirect()->route('admin.tests.show', $testId)->with('success', 'Question added successfully.');
    }

    /**
     * Show edit form for question.
     */
    public function editQuestion(int $testId, int $id)
    {
        $test = $this->testService->find($testId);
        $question = $this->questionService->find($id);
        if (!$test || !$question) {
            abort(404, 'Not found.');
        }

        return view('admin.questions.edit', compact('test', 'question'));
    }

    /**
     * Update a question.
     */
    public function updateQuestion(QuestionRequest $request, int $testId, int $id)
    {
        $data = $request->validated();
        $this->questionService->update($id, $data);

        return redirect()->route('admin.tests.show', $testId)->with('success', 'Question updated successfully.');
    }

    /**
     * Delete a question.
     */
    public function destroyQuestion(int $testId, int $id)
    {
        $this->questionService->delete($id);
        return redirect()->route('admin.tests.show', $testId)->with('success', 'Question deleted successfully.');
    }
}
