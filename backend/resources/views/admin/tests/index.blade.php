<x-admin-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-4 min-w-0">
            <h2 class="text-lg sm:text-xl font-bold text-white truncate">Test Management</h2>
            <a href="{{ route('admin.tests.create') }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 sm:px-4 sm:py-2 bg-indigo-650 hover:bg-indigo-700 text-white text-xs font-semibold uppercase tracking-wider rounded-xl transition duration-200 shadow-md shrink-0">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                <span class="hidden sm:inline">Create New Test</span>
                <span class="sm:hidden">Create</span>
            </a>
        </div>
    </x-slot>

    <div class="max-w-6xl mx-auto space-y-6">
        <div class="bg-slate-950/40 backdrop-blur border border-slate-800 rounded-2xl overflow-hidden">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b border-slate-800 bg-slate-950/20">
                        <th class="px-6 py-4 text-xs font-semibold text-slate-400 uppercase tracking-wider">Test Details</th>
                        <th class="px-6 py-4 text-xs font-semibold text-slate-400 uppercase tracking-wider">Type</th>
                        <th class="px-6 py-4 text-xs font-semibold text-slate-400 uppercase tracking-wider">Sections / Questions</th>
                        <th class="px-6 py-4 text-xs font-semibold text-slate-400 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-4 text-xs font-semibold text-slate-400 uppercase tracking-wider text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/60">
                    @forelse ($tests as $test)
                        <tr class="hover:bg-slate-900/30 transition duration-150">
                            <td class="px-6 py-4">
                                <div class="font-semibold text-white text-sm">{{ $test->title }}</div>
                                <div class="text-xs text-slate-500 mt-1 truncate max-w-sm">{{ $test->description ?? 'No description' }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold {{ $test->type === 'READ_ALOUD' ? 'bg-indigo-950 text-indigo-400 border border-indigo-900' : 'bg-cyan-950 text-cyan-400 border border-cyan-900' }}">
                                    {{ $test->type === 'READ_ALOUD' ? 'Read Aloud' : 'AI Interview' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm text-slate-300 font-medium">
                                <span>{{ $test->sections->count() }} Sections</span>
                                <span class="mx-1.5 text-slate-600">•</span>
                                <span>{{ $test->questions->count() }} Questions</span>
                            </td>
                            <td class="px-6 py-4">
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold {{ $test->is_active ? 'bg-emerald-950 text-emerald-400 border border-emerald-900' : 'bg-slate-900 text-slate-400 border border-slate-800' }}">
                                    {{ $test->is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="inline-flex items-center gap-3 justify-end">
                                    <a href="{{ route('admin.tests.show', $test->id) }}" class="text-indigo-400 hover:text-indigo-300 text-xs font-semibold uppercase tracking-wider transition duration-150">
                                        Manage
                                    </a>
                                    <a href="{{ route('admin.tests.edit', $test->id) }}" class="text-slate-400 hover:text-white text-xs font-semibold uppercase tracking-wider transition duration-150">
                                        Edit
                                    </a>
                                    <form method="POST" action="{{ route('admin.tests.destroy', $test->id) }}" onsubmit="return confirm('Are you sure you want to delete this test?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-rose-400 hover:text-rose-350 text-xs font-semibold uppercase tracking-wider transition duration-150 bg-transparent border-0 cursor-pointer">
                                            Delete
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-10 text-center text-slate-500 text-sm">
                                No tests found. Create one to get started!
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-admin-layout>
