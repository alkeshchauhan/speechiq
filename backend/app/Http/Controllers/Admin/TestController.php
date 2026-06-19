<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\TestRequest;
use App\Services\TestService;
use Illuminate\Http\Request;

class TestController extends Controller
{
    protected TestService $testService;

    public function __construct(TestService $testService)
    {
        $this->testService = $testService;
    }

    public function index()
    {
        $tests = $this->testService->all();
        return view('admin.tests.index', compact('tests'));
    }

    public function create()
    {
        return view('admin.tests.create');
    }

    public function store(TestRequest $request)
    {
        $data = $request->validated();
        $data['is_active'] = $request->has('is_active') ? true : false;
        
        $this->testService->create($data);

        return redirect()->route('admin.tests.index')->with('success', 'Test created successfully.');
    }

    public function show(int $id)
    {
        $test = $this->testService->find($id);
        if (!$test) {
            abort(404, 'Test not found.');
        }

        return view('admin.tests.show', compact('test'));
    }

    public function edit(int $id)
    {
        $test = $this->testService->find($id);
        if (!$test) {
            abort(404, 'Test not found.');
        }

        return view('admin.tests.edit', compact('test'));
    }

    public function update(TestRequest $request, int $id)
    {
        $data = $request->validated();
        $data['is_active'] = $request->has('is_active') ? true : false;

        $this->testService->update($id, $data);

        return redirect()->route('admin.tests.index')->with('success', 'Test updated successfully.');
    }

    public function destroy(int $id)
    {
        $this->testService->delete($id);
        return redirect()->route('admin.tests.index')->with('success', 'Test deleted successfully.');
    }
}
