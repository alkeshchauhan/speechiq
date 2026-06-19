<x-admin-layout>
    <x-slot name="header">
        <h2 class="text-xl font-bold leading-tight text-white">System Settings</h2>
    </x-slot>

    <div class="max-w-6xl mx-auto space-y-8">
        <form method="POST" action="{{ route('admin.settings.update') }}" class="space-y-8">
            @csrf
            @method('PUT')

            <!-- Grid Layout -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                

                <!-- Gemini / Google AI Studio Configurations -->
                <div class="card-premium-glass rounded-2xl p-6 space-y-6">
                    <div class="flex items-center gap-3 border-b border-slate-800 pb-4">
                        <svg class="w-6 h-6 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path>
                        </svg>
                        <h3 class="text-lg font-semibold text-white">Gemini / AI Studio</h3>
                    </div>
 
                    <div class="space-y-4">
                        <div>
                            <label class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">Gemini API Key</label>
                            <input type="password" name="GEMINI_API_KEY" value="{{ isset($settings['GEMINI_API_KEY']) && $settings['GEMINI_API_KEY']->setting_value ? '********' : '' }}" class="w-full bg-slate-900 border border-slate-800 rounded-xl px-4 py-3 text-white text-sm input-premium-focus" placeholder="AIzaSy...">
                        </div>
 
                        <div>
                            <label class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">Gemini Model</label>
                            <input type="text" name="GEMINI_MODEL" value="{{ $settings['GEMINI_MODEL']->setting_value ?? 'gemini-1.5-flash' }}" class="w-full bg-slate-900 border border-slate-800 rounded-xl px-4 py-3 text-white text-sm input-premium-focus">
                        </div>
 

                    </div>
 
                    <div class="pt-4 flex items-center justify-between gap-4 border-t border-slate-850">
                        <button type="button" onclick="testConnection('gemini')" id="btn-test-gemini" class="inline-flex items-center px-4 py-2.5 text-xs font-bold uppercase tracking-wider rounded-xl transition duration-200 btn-premium-amber">
                            Test Gemini Connection
                        </button>
                        <span id="status-gemini" class="text-xs font-medium truncate max-w-xs"></span>
                    </div>
                </div>
 
                <!-- AI Engine FastAPI Configurations -->
                <div class="card-premium-glass rounded-2xl p-6 space-y-6">
                    <div class="flex items-center gap-3 border-b border-slate-800 pb-4">
                        <svg class="w-6 h-6 text-cyan-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"></path>
                        </svg>
                        <h3 class="text-lg font-semibold text-white">AI Engine (FastAPI)</h3>
                    </div>
 
                    <div class="space-y-4">
                        <div>
                            <label class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">FastAPI API URL</label>
                            <input type="text" name="AI_API_URL" value="{{ $settings['AI_API_URL']->setting_value ?? 'http://127.0.0.1:8001' }}" class="w-full bg-slate-900 border border-slate-800 rounded-xl px-4 py-3 text-white text-sm input-premium-focus" placeholder="http://127.0.0.1:8001">
                        </div>
 
                        <div>
                            <label class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">AI Engine Access Token</label>
                            <input type="password" name="AI_API_TOKEN" value="{{ isset($settings['AI_API_TOKEN']) && $settings['AI_API_TOKEN']->setting_value ? '********' : '' }}" class="w-full bg-slate-900 border border-slate-800 rounded-xl px-4 py-3 text-white text-sm input-premium-focus" placeholder="Bearer Token">
                        </div>
                    </div>
 
                    <div class="pt-4 flex items-center justify-between gap-4">
                        <button type="button" onclick="testConnection('fastapi')" id="btn-test-fastapi" class="inline-flex items-center px-4 py-2.5 text-xs font-bold uppercase tracking-wider rounded-xl transition duration-200 btn-premium-cyan">
                            Test AI Engine Connection
                        </button>
                        <span id="status-fastapi" class="text-xs font-medium truncate max-w-xs"></span>
                    </div>
                </div>
 
            </div>
 
            <!-- Features Toggle Module -->
            <div class="card-premium-glass rounded-2xl p-6">
                <div class="flex items-center gap-3 border-b border-slate-800 pb-4 mb-6">
                    <svg class="w-6 h-6 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                    </svg>
                    <h3 class="text-lg font-semibold text-white">System Feature Flags</h3>
                </div>
 
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-6">
                    <!-- Switch 1 -->
                    <div class="bg-slate-900/60 border border-slate-800/85 rounded-xl p-4 flex flex-col justify-between h-28">
                        <div>
                            <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider block">AI Interview</span>
                            <span class="text-[10px] text-slate-500">Enable interactive mock interview module.</span>
                        </div>
                        <div class="flex items-center justify-between mt-2">
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" name="ENABLE_AI_INTERVIEW" class="sr-only peer" {{ filter_var($settings['ENABLE_AI_INTERVIEW']->setting_value ?? false, FILTER_VALIDATE_BOOLEAN) ? 'checked' : '' }}>
                                <div class="w-9 h-5 bg-slate-850 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-slate-400 after:border-slate-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-indigo-600 peer-checked:after:bg-white"></div>
                            </label>
                        </div>
                    </div>
 
                    <!-- Switch 2 -->
                    <div class="bg-slate-900/60 border border-slate-800/85 rounded-xl p-4 flex flex-col justify-between h-28">
                        <div>
                            <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider block">Read Aloud</span>
                            <span class="text-[10px] text-slate-500">Enable pronunciation assessment practice.</span>
                        </div>
                        <div class="flex items-center justify-between mt-2">
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" name="ENABLE_READ_ALOUD" class="sr-only peer" {{ filter_var($settings['ENABLE_READ_ALOUD']->setting_value ?? false, FILTER_VALIDATE_BOOLEAN) ? 'checked' : '' }}>
                                <div class="w-9 h-5 bg-slate-850 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-slate-400 after:border-slate-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-indigo-600 peer-checked:after:bg-white"></div>
                            </label>
                        </div>
                    </div>
 
                    <!-- Switch 3 -->
                    <div class="bg-slate-900/60 border border-slate-800/85 rounded-xl p-4 flex flex-col justify-between h-28">
                        <div>
                            <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider block">Text To Speech (TTS)</span>
                            <span class="text-[10px] text-slate-500">Enable text audio reading services.</span>
                        </div>
                        <div class="flex items-center justify-between mt-2">
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" name="ENABLE_TTS" class="sr-only peer" {{ filter_var($settings['ENABLE_TTS']->setting_value ?? false, FILTER_VALIDATE_BOOLEAN) ? 'checked' : '' }}>
                                <div class="w-9 h-5 bg-slate-850 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-slate-400 after:border-slate-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-indigo-600 peer-checked:after:bg-white"></div>
                            </label>
                        </div>
                    </div>
 
                    <!-- Switch 4 -->
                    <div class="bg-slate-900/60 border border-slate-800/85 rounded-xl p-4 flex flex-col justify-between h-28">
                        <div>
                            <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider block">Speech To Text (STT)</span>
                            <span class="text-[10px] text-slate-500">Enable vocal transcription features.</span>
                        </div>
                        <div class="flex items-center justify-between mt-2">
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" name="ENABLE_STT" class="sr-only peer" {{ filter_var($settings['ENABLE_STT']->setting_value ?? false, FILTER_VALIDATE_BOOLEAN) ? 'checked' : '' }}>
                                <div class="w-9 h-5 bg-slate-850 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-slate-400 after:border-slate-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-indigo-600 peer-checked:after:bg-white"></div>
                            </label>
                        </div>
                    </div>
                </div>
            </div>
 
            <!-- Submit Panel -->
            <div class="flex items-center justify-end gap-4">
                <button type="submit" class="inline-flex items-center px-6 py-3 btn-premium-indigo font-bold rounded-xl text-sm">
                    Save Changes
                </button>
            </div>

        </form>
    </div>

    <!-- AJAX Connection Testing Scripts -->
    <script>
        function testConnection(target) {
            const btn = document.getElementById('btn-test-' + target);
            const statusEl = document.getElementById('status-' + target);
            
            btn.disabled = true;
            btn.classList.add('opacity-50');
            statusEl.innerText = 'Testing connection...';
            statusEl.className = 'text-xs font-medium text-slate-450';

            fetch('{{ route("admin.settings.test-connection") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ target: target })
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Server responded with ' + response.status);
                }
                return response.json();
            })
            .then(data => {
                btn.disabled = false;
                btn.classList.remove('opacity-50');
                if (data.success) {
                    statusEl.innerText = data.message;
                    statusEl.className = 'text-xs font-medium text-emerald-400';
                } else {
                    statusEl.innerText = data.message;
                    statusEl.className = 'text-xs font-medium text-rose-450';
                }
            })
            .catch(error => {
                btn.disabled = false;
                btn.classList.remove('opacity-50');
                statusEl.innerText = 'Error: ' + error.message;
                statusEl.className = 'text-xs font-medium text-rose-450';
            });
        }
    </script>
</x-admin-layout>
