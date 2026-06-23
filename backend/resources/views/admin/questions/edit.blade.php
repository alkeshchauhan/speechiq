<x-admin-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-4 min-w-0">
            <h2 class="text-lg sm:text-xl font-bold text-white truncate">Edit Question</h2>
            <a href="{{ route('admin.tests.show', $test->id) }}" class="inline-flex items-center gap-1.5 px-2.5 py-1.5 sm:px-3 sm:py-2 rounded-lg bg-slate-900 border border-slate-800 text-slate-400 hover:text-white text-xs font-semibold transition duration-150 shrink-0 shadow-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                <span class="hidden sm:inline">Back to Test</span>
            </a>
        </div>
    </x-slot>

    <div class="max-w-xl mx-auto">
        <div class="bg-slate-950/40 backdrop-blur border border-slate-800 rounded-2xl p-6">
            <form method="POST" action="{{ route('admin.tests.questions.update', [$test->id, $question->id]) }}" class="space-y-6">
                @csrf
                @method('PUT')
                <input type="hidden" name="test_id" value="{{ $test->id }}">
                <input type="hidden" name="question_type" value="{{ $question->question_type }}">

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">Section</label>
                        <select name="test_section_id" class="w-full bg-slate-900 border border-slate-800 rounded-xl px-3 py-2 text-white text-xs focus:outline-none focus:border-indigo-500 transition duration-200">
                            <option value="">No Section (Direct Question)</option>
                            @foreach ($test->sections as $sect)
                                <option value="{{ $sect->id }}" {{ old('test_section_id', $question->test_section_id) == $sect->id ? 'selected' : '' }}>{{ $sect->title }}</option>
                            @endforeach
                        </select>
                        @error('test_section_id')
                            <p class="text-xs text-rose-450 mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">Sort Order</label>
                        <input type="number" name="sort_order" value="{{ old('sort_order', $question->sort_order) }}" class="w-full bg-slate-900 border border-slate-800 rounded-xl px-3 py-2 text-white text-xs focus:outline-none focus:border-indigo-500 transition duration-200">
                        @error('sort_order')
                            <p class="text-xs text-rose-450 mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">
                        {{ $test->type === 'READ_ALOUD' ? 'Text Paragraph (for candidate to read)' : 'Interview Question Prompt' }}
                    </label>
                    <textarea name="question_text" rows="5" required class="w-full bg-slate-900 border border-slate-800 rounded-xl px-4 py-3 text-white text-xs focus:outline-none focus:border-indigo-500 transition duration-200" placeholder="Type the text...">{{ old('question_text', $question->question_text) }}</textarea>
                    @error('question_text')
                        <p class="text-xs text-rose-450 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex items-center justify-end gap-4 pt-4 border-t border-slate-800">
                    <a href="{{ route('admin.tests.show', $test->id) }}" class="text-slate-400 hover:text-white text-xs font-semibold uppercase tracking-wider transition duration-150">
                        Cancel
                    </a>
                    <button type="submit" class="inline-flex items-center px-5 py-2.5 bg-indigo-650 hover:bg-indigo-700 text-white font-semibold rounded-xl transition duration-200 text-xs uppercase tracking-wider">
                        Update Question
                    </button>
                </div>

            </form>
        </div>
    </div>
</x-admin-layout>
