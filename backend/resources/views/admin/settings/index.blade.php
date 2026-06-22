<x-admin-layout>
    <x-slot name="header">
        <h2 class="text-xl font-bold leading-tight text-white">System Settings</h2>
    </x-slot>

    <div class="max-w-6xl mx-auto space-y-8">
        <form id="settings-form" method="POST" action="{{ route('admin.settings.update') }}" class="space-y-8">
            @csrf
            @method('PUT')

            <!-- Grid Layout -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">


                <!-- Gemini / Google AI Studio Configurations -->
                <div class="card-premium-glass rounded-2xl p-6 space-y-6">
                    <div class="flex items-center gap-3 border-b border-slate-800 pb-4">
                        <svg class="w-6 h-6 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                            xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z">
                            </path>
                        </svg>
                        <h3 class="text-lg font-semibold text-white">Gemini / AI Studio</h3>
                    </div>

                    <div class="space-y-4">
                        <div>
                            <label
                                class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">Gemini
                                API Key</label>
                            <input type="password" name="GEMINI_API_KEY"
                                value="{{ isset($settings['GEMINI_API_KEY']) && $settings['GEMINI_API_KEY']->setting_value ? '********' : '' }}"
                                class="w-full bg-slate-900 border border-slate-800 rounded-xl px-4 py-3 text-white text-sm input-premium-focus"
                                placeholder="AIzaSy...">
                        </div>

                        <div>
                            <label
                                class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">Gemini
                                Model</label>
                            <input type="text" name="GEMINI_MODEL"
                                value="{{ $settings['GEMINI_MODEL']->setting_value ?? 'gemini-2.5-flash' }}"
                                class="w-full bg-slate-900 border border-slate-800 rounded-xl px-4 py-3 text-white text-sm input-premium-focus">
                        </div>


                    </div>

                    <div class="pt-4 flex items-center gap-3 border-t border-slate-850">
                        <button type="button" onclick="testConnection('gemini')" id="btn-test-gemini"
                            class="inline-flex items-center px-4 py-2.5 text-xs font-bold uppercase tracking-wider rounded-xl transition duration-200 btn-premium-amber">
                            Test Gemini Connection
                        </button>
                        <div id="status-gemini" class="min-w-0 flex items-center">
                            <span class="hidden text-xs font-medium truncate max-w-xs" id="status-gemini-badge"></span>
                        </div>
                    </div>
                </div>

                <!-- AI Engine FastAPI Configurations -->
                <div class="card-premium-glass rounded-2xl p-6 space-y-6">
                    <div class="flex items-center gap-3 border-b border-slate-800 pb-4">
                        <svg class="w-6 h-6 text-cyan-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                            xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z">
                            </path>
                        </svg>
                        <h3 class="text-lg font-semibold text-white">AI Engine (FastAPI)</h3>
                    </div>

                    <div class="space-y-4">
                        <div>
                            <label
                                class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">FastAPI
                                API URL</label>
                            <input type="text" name="AI_API_URL"
                                value="{{ $settings['AI_API_URL']->setting_value ?? 'http://127.0.0.1:8001' }}"
                                class="w-full bg-slate-900 border border-slate-800 rounded-xl px-4 py-3 text-white text-sm input-premium-focus"
                                placeholder="http://127.0.0.1:8001">
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">AI
                                Engine Access Token</label>
                            <input type="password" name="AI_API_TOKEN"
                                value="{{ isset($settings['AI_API_TOKEN']) && $settings['AI_API_TOKEN']->setting_value ? '********' : '' }}"
                                class="w-full bg-slate-900 border border-slate-800 rounded-xl px-4 py-3 text-white text-sm input-premium-focus"
                                placeholder="Bearer Token">
                        </div>
                    </div>

                    <div class="pt-4 flex items-center gap-3">
                        <button type="button" onclick="testConnection('fastapi')" id="btn-test-fastapi"
                            class="inline-flex items-center px-4 py-2.5 text-xs font-bold uppercase tracking-wider rounded-xl transition duration-200 btn-premium-cyan">
                            Test AI Engine Connection
                        </button>
                        <div id="status-fastapi" class="min-w-0 flex items-center">
                            <span class="hidden text-xs font-medium truncate max-w-xs" id="status-fastapi-badge"></span>
                        </div>
                    </div>
                </div>

            </div>

            <!-- Features Toggle Module -->
            <div class="card-premium-glass rounded-2xl p-6">
                <div class="flex items-center gap-3 border-b border-slate-800 pb-4 mb-6">
                    <svg class="w-6 h-6 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                        xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z">
                        </path>
                    </svg>
                    <h3 class="text-lg font-semibold text-white">System Feature Flags</h3>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-5 gap-6">
                    <!-- Switch 1 -->
                    <div
                        class="bg-slate-900/60 border border-slate-800/85 rounded-xl p-4 flex flex-col justify-between h-28">
                        <div>
                            <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider block">AI
                                Interview</span>
                            <span class="text-[10px] text-slate-500">Enable interactive mock interview module.</span>
                        </div>
                        <div class="flex items-center justify-between mt-2">
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" name="ENABLE_AI_INTERVIEW" class="sr-only peer"
                                    {{ filter_var($settings['ENABLE_AI_INTERVIEW']->setting_value ?? false, FILTER_VALIDATE_BOOLEAN) ? 'checked' : '' }}>
                                <div
                                    class="w-9 h-5 bg-slate-850 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-slate-400 after:border-slate-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-indigo-600 peer-checked:after:bg-white">
                                </div>
                            </label>
                        </div>
                    </div>

                    <!-- Switch 2 -->
                    <div
                        class="bg-slate-900/60 border border-slate-800/85 rounded-xl p-4 flex flex-col justify-between h-28">
                        <div>
                            <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider block">Read
                                Aloud</span>
                            <span class="text-[10px] text-slate-500">Enable pronunciation assessment practice.</span>
                        </div>
                        <div class="flex items-center justify-between mt-2">
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" name="ENABLE_READ_ALOUD" class="sr-only peer"
                                    {{ filter_var($settings['ENABLE_READ_ALOUD']->setting_value ?? false, FILTER_VALIDATE_BOOLEAN) ? 'checked' : '' }}>
                                <div
                                    class="w-9 h-5 bg-slate-850 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-slate-400 after:border-slate-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-indigo-600 peer-checked:after:bg-white">
                                </div>
                            </label>
                        </div>
                    </div>

                    <!-- Switch 3 -->
                    <div
                        class="bg-slate-900/60 border border-slate-800/85 rounded-xl p-4 flex flex-col justify-between h-28">
                        <div>
                            <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider block">Text To
                                Speech (TTS)</span>
                            <span class="text-[10px] text-slate-500">Enable text audio reading services.</span>
                        </div>
                        <div class="flex items-center justify-between mt-2">
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" name="ENABLE_TTS" class="sr-only peer"
                                    {{ filter_var($settings['ENABLE_TTS']->setting_value ?? false, FILTER_VALIDATE_BOOLEAN) ? 'checked' : '' }}>
                                <div
                                    class="w-9 h-5 bg-slate-850 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-slate-400 after:border-slate-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-indigo-600 peer-checked:after:bg-white">
                                </div>
                            </label>
                        </div>
                    </div>

                    <!-- Switch 4 -->
                    <div
                        class="bg-slate-900/60 border border-slate-800/85 rounded-xl p-4 flex flex-col justify-between h-28">
                        <div>
                            <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider block">Speech To
                                Text (STT)</span>
                            <span class="text-[10px] text-slate-500">Enable vocal transcription features.</span>
                        </div>
                        <div class="flex items-center justify-between mt-2">
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" name="ENABLE_STT" class="sr-only peer"
                                    {{ filter_var($settings['ENABLE_STT']->setting_value ?? false, FILTER_VALIDATE_BOOLEAN) ? 'checked' : '' }}>
                                <div
                                    class="w-9 h-5 bg-slate-850 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-slate-400 after:border-slate-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-indigo-600 peer-checked:after:bg-white">
                                </div>
                            </label>
                        </div>
                    </div>

                    <!-- Switch 5 -->
                    <div
                        class="bg-slate-900/60 border border-slate-800/85 rounded-xl p-4 flex flex-col justify-between h-28">
                        <div>
                            <span
                                class="text-xs font-semibold text-slate-400 uppercase tracking-wider block">Reports</span>
                            <span class="text-[10px] text-slate-500">Enable analysis reports and progress
                                tracking.</span>
                        </div>
                        <div class="flex items-center justify-between mt-2">
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" name="ENABLE_REPORTS" class="sr-only peer"
                                    {{ filter_var($settings['ENABLE_REPORTS']->setting_value ?? false, FILTER_VALIDATE_BOOLEAN) ? 'checked' : '' }}>
                                <div
                                    class="w-9 h-5 bg-slate-850 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-slate-400 after:border-slate-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-indigo-600 peer-checked:after:bg-white">
                                </div>
                            </label>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Submit Panel -->
            <div class="flex items-center justify-end gap-4">
                <button type="submit"
                    class="inline-flex items-center px-6 py-3 btn-premium-indigo font-bold rounded-xl text-sm">
                    Save Changes
                </button>
            </div>

        </form>
    </div>

    <!-- AJAX Connection Testing Scripts -->
    <script>
        function setStatusBadge(target, success, message) {
            const $badge = $('#status-' + target + '-badge');
            if (!$badge.length) return;
            const icon = success ?
                '<svg class="w-4 h-4 mr-2 inline-block text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>' :
                '<svg class="w-4 h-4 mr-2 inline-block text-rose-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>';

            $badge.html('<span class="inline-flex items-center gap-2 px-3 py-2 rounded-full text-xs font-medium ' + (
                    success ? 'bg-emerald-950/30 text-emerald-400' : 'bg-rose-950/30 text-rose-400') + '">' + icon +
                '<span class="truncate max-w-xs">' + message + '</span></span>');
            $badge.removeClass('hidden');
        }

        function mapGeminiError(message) {
            if (!message) return message;
            const m = message.toLowerCase();
            if (m.includes('quota') || m.includes('billing') || m.includes('quota exceeded') || m.includes(
                    'insufficient')) {
                return 'Gemini returned a quota/billing error — check Google Cloud Console billing and quotas.';
            }
            if (m.includes('permission') || m.includes('unauthorized') || m.includes('401') || m.includes('forbidden')) {
                return 'Authentication failed for Gemini — verify the API key and permissions.';
            }
            return message;
        }

        function isValidUrl(str) {
            try {
                /* eslint-disable no-undef */
                new URL(str);
                return true;
            } catch (e) {
                return false;
            }
        }

        function testConnection(target) {
            const $btn = $('#btn-test-' + target);
            const $badge = $('#status-' + target + '-badge');

            $btn.prop('disabled', true).addClass('opacity-50');
            if ($badge.length) {
                $badge.removeClass('hidden');
                $badge.html(
                    '<span class="inline-flex items-center gap-2 px-3 py-2 rounded-full text-xs font-medium text-slate-400 bg-slate-800">Testing…</span>'
                );
            }

            const formData = new FormData($('#settings-form')[0]);
            const payload = {
                target
            };
            formData.forEach((value, key) => {
                if (key !== '_method') {
                    payload[key] = value;
                }
            });

            // Client-side validation
            if (target === 'gemini') {
                const key = payload.GEMINI_API_KEY || '';
                if (key === '') {
                    setStatusBadge(target, false, 'Gemini API key is empty.');
                    $btn.prop('disabled', false).removeClass('opacity-50');
                    return;
                }
            }

            if (target === 'fastapi') {
                const url = (payload.AI_API_URL || '').trim();
                if (!url) {
                    setStatusBadge(target, false, 'AI Engine URL is empty.');
                    $btn.prop('disabled', false).removeClass('opacity-50');
                    return;
                }
                if (!isValidUrl(url)) {
                    setStatusBadge(target, false, 'AI Engine URL appears invalid.');
                    $btn.prop('disabled', false).removeClass('opacity-50');
                    return;
                }
            }

            $.ajax({
                url: '{{ route('admin.settings.test-connection') }}',
                type: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                contentType: 'application/json',
                data: JSON.stringify(payload),
                dataType: 'json',
                success: function(data) {
                    $btn.prop('disabled', false).removeClass('opacity-50');
                    let message = data.message || (data.success ? 'Connection successful' : 'Connection failed');
                    if (target === 'gemini') {
                        message = mapGeminiError(message);
                    }
                    setStatusBadge(target, data.success === true, message);
                },
                error: function(xhr, status, error) {
                    $btn.prop('disabled', false).removeClass('opacity-50');
                    setStatusBadge(target, false, 'Error: ' + error);
                }
            });
        }

        // Debug helper: show a brief 'JS loaded' badge on page load so you know script executed
        $(document).ready(function() {
            try {
                console.log('settings page script loaded');
                ['gemini', 'fastapi'].forEach(function(t) {
                    const $b = $('#status-' + t + '-badge');
                    if ($b.length) {
                        $b.removeClass('hidden');
                        $b.html(
                            '<span class="inline-flex items-center gap-2 px-3 py-2 rounded-full text-xs font-medium text-slate-400 bg-slate-800">JS loaded</span>'
                        );
                        setTimeout(() => {
                            $b.addClass('hidden');
                        }, 1400);
                    }
                });
            } catch (e) {
                console.error('debug badge failed', e);
            }
        });
    </script>
</x-admin-layout>
