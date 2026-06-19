<x-admin-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-xl font-bold leading-tight text-white">{{ $test->title }}</h2>
                <p class="text-xs text-slate-400 mt-1">{{ $test->type === 'READ_ALOUD' ? 'Read Aloud Test' : 'AI Interview Test' }}</p>
            </div>
            <a href="{{ route('admin.tests.index') }}" class="text-slate-400 hover:text-white text-xs font-semibold uppercase tracking-wider transition duration-150">
                &larr; Back to Tests
            </a>
        </div>
    </x-slot>

    <div class="max-w-6xl mx-auto grid grid-cols-1 lg:grid-cols-3 gap-8">
        
        <!-- Left Side: Manage Sections -->
        <div class="space-y-6">
            <!-- Add Section Card -->
            <div class="bg-slate-950/40 backdrop-blur border border-slate-800 rounded-2xl p-6 space-y-4">
                <h3 class="text-sm font-semibold text-white uppercase tracking-wider">Add Test Section</h3>
                <form method="POST" action="{{ route('admin.tests.sections.store', $test->id) }}" class="space-y-4">
                    @csrf
                    <div>
                        <label class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">Section Title</label>
                        <input type="text" name="title" required class="w-full bg-slate-900 border border-slate-800 rounded-xl px-3 py-2 text-white text-xs focus:outline-none focus:border-indigo-500 transition duration-200" placeholder="e.g. Part 1: Introduction">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">Description (Optional)</label>
                        <textarea name="description" rows="2" class="w-full bg-slate-900 border border-slate-800 rounded-xl px-3 py-2 text-white text-xs focus:outline-none focus:border-indigo-500 transition duration-200" placeholder="e.g. Introduce yourself..."></textarea>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">Sort Order</label>
                        <input type="number" name="sort_order" value="0" class="w-full bg-slate-900 border border-slate-800 rounded-xl px-3 py-2 text-white text-xs focus:outline-none focus:border-indigo-500 transition duration-200">
                    </div>
                    <button type="submit" class="w-full inline-flex items-center justify-center px-4 py-2 bg-slate-900 border border-slate-800 hover:bg-slate-850 text-indigo-400 font-semibold rounded-xl text-xs uppercase tracking-wider transition duration-150">
                        Add Section
                    </button>
                </form>
            </div>

            <!-- Existing Sections List -->
            <div class="bg-slate-950/40 backdrop-blur border border-slate-800 rounded-2xl p-6 space-y-4">
                <h3 class="text-sm font-semibold text-white uppercase tracking-wider">Sections</h3>
                <div class="space-y-3">
                    @forelse ($test->sections as $sect)
                        <div class="bg-slate-900 border border-slate-800/80 rounded-xl p-3 flex items-start justify-between">
                            <div>
                                <span class="text-xs font-bold text-slate-300">{{ $sect->title }}</span>
                                <p class="text-[10px] text-slate-500 mt-1">{{ $sect->description ?? 'No description' }}</p>
                                <span class="text-[9px] bg-slate-950 px-2 py-0.5 rounded border border-slate-850 text-slate-500 mt-1 inline-block">Order: {{ $sect->sort_order }}</span>
                            </div>
                            <form method="POST" action="{{ route('admin.tests.sections.destroy', [$test->id, $sect->id]) }}" onsubmit="return confirm('Are you sure you want to delete this section? This will delete all questions within it.');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-rose-400 hover:text-rose-350 p-1 bg-transparent border-0 cursor-pointer">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                    </svg>
                                </button>
                            </form>
                        </div>
                    @empty
                        <p class="text-xs text-slate-500 text-center">No sections created yet.</p>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Right Side: Manage Questions (takes 2 columns) -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Add Question Card -->
            <div class="bg-slate-950/40 backdrop-blur border border-slate-800 rounded-2xl p-6 space-y-4">
                <h3 class="text-sm font-semibold text-white uppercase tracking-wider">Add Question / Paragraph</h3>
                <form method="POST" action="{{ route('admin.tests.questions.store', $test->id) }}" class="space-y-4">
                    @csrf
                    <input type="hidden" name="test_id" value="{{ $test->id }}">
                    <input type="hidden" name="question_type" value="{{ $test->type }}">

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">Bind to Section (Optional)</label>
                            <select name="test_section_id" class="w-full bg-slate-900 border border-slate-800 rounded-xl px-3 py-2.5 text-white text-xs focus:outline-none focus:border-indigo-500 transition duration-200">
                                <option value="">No Section (Direct Question)</option>
                                @foreach ($test->sections as $sect)
                                    <option value="{{ $sect->id }}">{{ $sect->title }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">Sort Order</label>
                            <input type="number" name="sort_order" value="0" class="w-full bg-slate-900 border border-slate-800 rounded-xl px-3 py-2 text-white text-xs focus:outline-none focus:border-indigo-500 transition duration-200">
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">
                            {{ $test->type === 'READ_ALOUD' ? 'Text Paragraph (for candidate to read)' : 'Interview Question Prompt' }}
                        </label>
                        <textarea name="question_text" rows="4" required class="w-full bg-slate-900 border border-slate-800 rounded-xl px-4 py-3 text-white text-xs focus:outline-none focus:border-indigo-500 transition duration-200" placeholder="{{ $test->type === 'READ_ALOUD' ? 'Type the exact paragraph of text candidate should read...' : 'Type the interview question candidates must answer verbally...' }}"></textarea>
                    </div>

                    <div class="flex items-center justify-end">
                        <button type="submit" class="inline-flex items-center px-5 py-2.5 bg-indigo-650 hover:bg-indigo-700 text-white font-semibold rounded-xl transition duration-200 text-xs uppercase tracking-wider">
                            Add Question
                        </button>
                    </div>
                </form>
            </div>

            <!-- Existing Questions List -->
            <div class="bg-slate-950/40 backdrop-blur border border-slate-800 rounded-2xl p-6 space-y-4">
                <h3 class="text-sm font-semibold text-white uppercase tracking-wider">Questions List</h3>
                <div class="space-y-4">
                    @forelse ($test->questions as $quest)
                        <div class="bg-slate-900 border border-slate-800/80 rounded-xl p-4 space-y-3">
                            <div class="flex items-start justify-between gap-4">
                                <div class="flex-1">
                                    @if ($quest->section)
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[9px] font-semibold bg-indigo-950 text-indigo-400 border border-indigo-900/60 mb-2">
                                            {{ $quest->section->title }}
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[9px] font-semibold bg-slate-950 text-slate-500 border border-slate-850 mb-2">
                                             Direct Question
                                        </span>
                                    @endif
                                    <p class="text-xs text-slate-200 leading-relaxed font-mono whitespace-pre-line">{{ $quest->question_text }}</p>
                                    <div class="mt-2 flex items-center gap-4 text-[10px] text-slate-500">
                                        <span>Order: {{ $quest->sort_order }}</span>
                                    </div>
                                </div>
                                <div class="flex items-center gap-3 shrink-0">
                                    <a href="{{ route('admin.tests.questions.edit', [$test->id, $quest->id]) }}" class="text-slate-400 hover:text-white text-xs font-semibold uppercase tracking-wider transition duration-150">
                                        Edit
                                    </a>
                                    <form method="POST" action="{{ route('admin.tests.questions.destroy', [$test->id, $quest->id]) }}" onsubmit="return confirm('Are you sure you want to delete this question?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-rose-450 hover:text-rose-400 text-xs font-semibold uppercase tracking-wider transition duration-150 bg-transparent border-0 cursor-pointer">
                                            Delete
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @empty
                        <p class="text-xs text-slate-500 text-center py-6">No questions added yet.</p>
                    @endforelse
                </div>
            </div>

        </div>

    </div>
</x-admin-layout>
