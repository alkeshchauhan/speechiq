<x-admin-layout>
    <x-slot name="header">
        <h2 class="text-xl font-bold leading-tight text-white">Create Test</h2>
    </x-slot>

    <div class="max-w-xl mx-auto">
        <div class="bg-slate-950/40 backdrop-blur border border-slate-800 rounded-2xl p-6">
            <form method="POST" action="{{ route('admin.tests.store') }}" class="space-y-6">
                @csrf

                <div>
                    <label class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">Test Title</label>
                    <input type="text" name="title" value="{{ old('title') }}" required class="w-full bg-slate-900 border border-slate-800 rounded-xl px-4 py-3 text-white text-sm focus:outline-none focus:border-indigo-500 transition duration-200" placeholder="e.g. Cambridge Pronunciation Test">
                    @error('title')
                        <p class="text-xs text-rose-400 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">Test Type</label>
                    <select name="type" required class="w-full bg-slate-900 border border-slate-800 rounded-xl px-4 py-3 text-white text-sm focus:outline-none focus:border-indigo-500 transition duration-200">
                        <option value="READ_ALOUD" {{ old('type') === 'READ_ALOUD' ? 'selected' : '' }}>Read Aloud Paragraph Practice</option>
                        <option value="AI_INTERVIEW" {{ old('type') === 'AI_INTERVIEW' ? 'selected' : '' }}>Interactive AI Mock Interview</option>
                    </select>
                    @error('type')
                        <p class="text-xs text-rose-400 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">Description</label>
                    <textarea name="description" rows="4" class="w-full bg-slate-900 border border-slate-800 rounded-xl px-4 py-3 text-white text-sm focus:outline-none focus:border-indigo-500 transition duration-200" placeholder="Describe what the test evaluates...">{{ old('description') }}</textarea>
                    @error('description')
                        <p class="text-xs text-rose-400 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex items-center justify-between">
                    <label class="block text-xs font-semibold text-slate-400 uppercase tracking-wider">Is Active / Visible</label>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" name="is_active" value="1" class="sr-only peer" checked>
                        <div class="w-9 h-5 bg-slate-850 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-slate-400 after:border-slate-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-indigo-600 peer-checked:after:bg-white"></div>
                    </label>
                </div>

                <div class="flex items-center justify-end gap-4 pt-4 border-t border-slate-800">
                    <a href="{{ route('admin.tests.index') }}" class="text-slate-400 hover:text-white text-xs font-semibold uppercase tracking-wider transition duration-150">
                        Cancel
                    </a>
                    <button type="submit" class="inline-flex items-center px-5 py-2.5 bg-indigo-650 hover:bg-indigo-700 text-white font-semibold rounded-xl transition duration-200 text-xs uppercase tracking-wider">
                        Create Test
                    </button>
                </div>

            </form>
        </div>
    </div>
</x-admin-layout>
