<x-user-layout>
    <div class="max-w-5xl mx-auto space-y-8">
        
        <!-- Navigation Header -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between border-b border-slate-800 pb-4 gap-4">
            <div class="space-y-1 min-w-0">
                <a href="{{ route('practice.interview.index') }}" class="inline-flex items-center gap-2 text-sm font-semibold text-slate-400 hover:text-white transition duration-200">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    Exit Workspace
                </a>
                <h1 class="text-lg sm:text-xl font-bold text-white mt-1.5 truncate" title="{{ $test->title }}">{{ $test->title }}</h1>
            </div>
            
            <div class="flex items-center gap-3 shrink-0 justify-between sm:justify-start">
                <span class="text-[10px] sm:text-xs text-slate-500 font-semibold uppercase tracking-wider">Progress:</span>
                <div class="flex-1 sm:flex-initial w-24 sm:w-32 bg-slate-900 border border-slate-800 h-2 rounded-full overflow-hidden">
                    <div id="session-progress" class="bg-gradient-to-r from-indigo-500 to-cyan-500 h-full rounded-full transition-all duration-300" style="width: 33%;"></div>
                </div>
                <span id="session-progress-text" class="text-xs font-bold text-slate-300">Question 1 of 3</span>
            </div>
        </div>

        <!-- Main Workspace Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            
            <!-- Left Panel: Interview Workspace -->
            <div class="lg:col-span-2 space-y-6 flex flex-col">
                <!-- AI Agent Avatar Box -->
                <div class="card-premium-glass rounded-3xl p-6 flex items-center gap-6 relative overflow-hidden">
                    <div class="absolute -right-10 -bottom-10 w-32 h-32 bg-indigo-500/5 rounded-full blur-2xl"></div>
                    
                    <!-- Avatar circle visualizer -->
                    <div class="relative w-20 h-20 shrink-0">
                        <div id="avatar-pulse-ring" class="absolute inset-0 rounded-full bg-indigo-500/20 animate-ping"></div>
                        <div class="absolute inset-1 rounded-full bg-gradient-to-tr from-indigo-600 to-cyan-500 flex items-center justify-center shadow-lg shadow-indigo-500/20">
                            <!-- Avatar icon -->
                            <svg class="w-8 h-8 text-white animate-[pulse_3s_infinite]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                            </svg>
                        </div>
                        <span class="absolute bottom-0 right-0 w-4 h-4 rounded-full bg-emerald-500 border-2 border-slate-950"></span>
                    </div>

                    <div class="space-y-1">
                        <div class="flex items-center gap-2">
                            <h3 class="text-lg font-bold text-white">SpeechIQ Interview Agent</h3>
                            <span id="agent-status" class="text-[10px] px-2 py-0.5 rounded-full bg-indigo-500/10 border border-indigo-500/25 text-indigo-400 font-semibold uppercase tracking-wider">Speaking</span>
                        </div>
                        <p class="text-xs text-slate-400 leading-relaxed">Ask questions dynamically based on your background. Speak clearly into your microphone when active.</p>
                    </div>
                </div>

                <!-- Live Conversation Messages Container -->
                <div id="chat-timeline" class="flex-1 min-h-[350px] max-h-[500px] overflow-y-auto card-premium-glass rounded-3xl p-6 space-y-6">
                    <!-- Dynamic chat bubbles appended here by JS -->
                </div>

                <!-- Controllers and Audio Action Bar -->
                <div class="card-premium-glass rounded-3xl p-6 space-y-6">
                    <!-- Waveform Visualizer -->
                    <div class="relative bg-slate-950/80 border border-slate-800/80 rounded-2xl h-24 overflow-hidden flex items-center justify-center">
                        <canvas id="waveform-canvas" class="w-full h-full block"></canvas>
                        <div id="recorder-overlay-text" class="absolute text-slate-500 text-xs font-semibold uppercase tracking-widest pointer-events-none">
                            Microphone Inactive
                        </div>
                    </div>

                    <!-- Timer Bar -->
                    <div class="flex items-center justify-between border-b border-slate-800/60 pb-4">
                        <div class="flex items-center gap-2.5">
                            <span class="w-2.5 h-2.5 rounded-full bg-slate-700" id="timer-dot"></span>
                            <span class="text-xl font-bold text-white font-mono" id="timer-text">00:00.0</span>
                        </div>
                        <button type="button" id="btn-vocalize" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl btn-premium-slate text-xs font-semibold">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.536 8.464a5 5 0 010 7.072M18.364 5.636a9 9 0 010 12.728M12 18.75V5.25L7.75 9.5H4.5v5h3.25L12 18.75z"></path>
                            </svg>
                            Repeat Question
                        </button>
                    </div>

                    <!-- Control Buttons Grid -->
                    <div class="flex flex-wrap items-center justify-center gap-4">
                        <button type="button" id="btn-start" class="inline-flex items-center gap-2 px-6 py-3 btn-premium-indigo font-bold rounded-xl text-sm">
                            <span class="w-2 h-2 rounded-full bg-white animate-pulse"></span>
                            Start Recording
                        </button>

                        <button type="button" id="btn-stop" disabled class="inline-flex items-center gap-2 px-6 py-3 btn-premium-rose disabled:opacity-40 disabled:cursor-not-allowed font-bold rounded-xl text-sm">
                            <svg class="w-4 h-4 fill-current" viewBox="0 0 24 24">
                                <path d="M6 6h12v12H6z"/>
                            </svg>
                            Stop Recording
                        </button>

                        <button type="button" id="btn-reset" disabled class="inline-flex items-center gap-2 px-5 py-3 btn-premium-slate disabled:opacity-40 disabled:cursor-not-allowed font-bold rounded-xl text-sm">
                            Reset
                        </button>

                        <button type="button" id="btn-upload" disabled class="inline-flex items-center gap-2 px-6 py-3 btn-premium-cyan disabled:opacity-40 disabled:cursor-not-allowed font-bold rounded-xl text-sm">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                            </svg>
                            Submit Answer
                        </button>
                    </div>
                </div>
            </div>

            <!-- Right Panel: Dynamic Scorecard Metrics -->
            <div class="lg:col-span-1 space-y-6">
                <!-- Evaluation Metrics Panel -->
                <div class="card-premium-glass rounded-3xl p-6 space-y-6 relative overflow-hidden h-full">
                    <div class="absolute -left-12 -top-12 w-32 h-32 bg-cyan-500/5 rounded-full blur-2xl"></div>
                    
                    <div class="space-y-1">
                        <h3 class="text-lg font-bold text-white">Question Scorecard</h3>
                        <p class="text-xs text-slate-400">Analysis results for the most recent response</p>
                    </div>

                    <!-- Scores layout -->
                    <div id="metrics-card-default" class="flex flex-col items-center justify-center min-h-[300px] text-center p-6 space-y-3 bg-slate-900/40 border border-slate-850 rounded-2xl">
                        <div class="w-12 h-12 rounded-full bg-slate-850 flex items-center justify-center text-slate-500">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                            </svg>
                        </div>
                        <h4 class="text-sm font-semibold text-slate-300">No Metrics Evaluated Yet</h4>
                        <p class="text-xs text-slate-500 leading-relaxed">Submit your answer to get instant evaluations on grammar, vocabulary, pronunciation, confidence, and fluency.</p>
                    </div>

                    <!-- Active metrics details (Hidden by default) -->
                    <div id="metrics-card-active" class="hidden space-y-6">
                        <!-- Overall score circle -->
                        <div class="flex items-center gap-4 bg-slate-900/60 border border-slate-800 rounded-2xl p-4">
                            <!-- Circular Progress Bar -->
                            <div class="relative w-16 h-16 flex items-center justify-center shrink-0">
                                <svg class="w-full h-full transform -rotate-90" viewBox="0 0 36 36">
                                    <path class="text-slate-800" stroke-width="3" stroke="currentColor" fill="none" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" />
                                    <path id="metrics-overall-progress" class="text-indigo-500 transition-all duration-500" stroke-dasharray="0, 100" stroke-width="3.2" stroke-linecap="round" stroke="currentColor" fill="none" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" />
                                </svg>
                                <span id="metrics-overall-score" class="absolute text-sm font-extrabold text-white">0%</span>
                            </div>
                            <div class="space-y-0.5">
                                <span class="text-[10px] text-slate-450 uppercase tracking-widest font-semibold">Overall Rating</span>
                                <h4 id="metrics-overall-text" class="text-sm font-bold text-white">Good Performance</h4>
                            </div>
                        </div>

                        <!-- Linear Scores Grid -->
                        <div class="grid grid-cols-2 gap-4">
                            <!-- Grammar -->
                            <div class="space-y-1.5 p-3 rounded-xl bg-slate-900/40 border border-slate-850">
                                <div class="flex justify-between text-xs font-semibold text-slate-400">
                                    <span>Grammar</span>
                                    <span id="score-grammar" class="text-white">0%</span>
                                </div>
                                <div class="h-1.5 w-full bg-slate-800 rounded-full overflow-hidden">
                                    <div id="bar-grammar" class="bg-indigo-500 h-full rounded-full transition-all duration-300" style="width: 0%;"></div>
                                </div>
                            </div>
                            <!-- Vocabulary -->
                            <div class="space-y-1.5 p-3 rounded-xl bg-slate-900/40 border border-slate-850">
                                <div class="flex justify-between text-xs font-semibold text-slate-400">
                                    <span>Vocabulary</span>
                                    <span id="score-vocabulary" class="text-white">0%</span>
                                </div>
                                <div class="h-1.5 w-full bg-slate-800 rounded-full overflow-hidden">
                                    <div id="bar-vocabulary" class="bg-indigo-500 h-full rounded-full transition-all duration-300" style="width: 0%;"></div>
                                </div>
                            </div>
                            <!-- Content Relevancy -->
                            <div class="space-y-1.5 p-3 rounded-xl bg-slate-900/40 border border-slate-850">
                                <div class="flex justify-between text-xs font-semibold text-slate-400">
                                    <span>Content Relevancy</span>
                                    <span id="score-content" class="text-white">0%</span>
                                </div>
                                <div class="h-1.5 w-full bg-slate-800 rounded-full overflow-hidden">
                                    <div id="bar-content" class="bg-cyan-500 h-full rounded-full transition-all duration-300" style="width: 0%;"></div>
                                </div>
                            </div>
                            <!-- Confidence -->
                            <div class="space-y-1.5 p-3 rounded-xl bg-slate-900/40 border border-slate-850">
                                <div class="flex justify-between text-xs font-semibold text-slate-400">
                                    <span>Confidence</span>
                                    <span id="score-confidence" class="text-white">0%</span>
                                </div>
                                <div class="h-1.5 w-full bg-slate-800 rounded-full overflow-hidden">
                                    <div id="bar-confidence" class="bg-cyan-500 h-full rounded-full transition-all duration-300" style="width: 0%;"></div>
                                </div>
                            </div>
                            <!-- Pronunciation -->
                            <div class="space-y-1.5 p-3 rounded-xl bg-slate-900/40 border border-slate-850">
                                <div class="flex justify-between text-xs font-semibold text-slate-400">
                                    <span>Pronunciation</span>
                                    <span id="score-pronunciation" class="text-white">0%</span>
                                </div>
                                <div class="h-1.5 w-full bg-slate-800 rounded-full overflow-hidden">
                                    <div id="bar-pronunciation" class="bg-indigo-500 h-full rounded-full transition-all duration-300" style="width: 0%;"></div>
                                </div>
                            </div>
                            <!-- Fluency -->
                            <div class="space-y-1.5 p-3 rounded-xl bg-slate-900/40 border border-slate-850">
                                <div class="flex justify-between text-xs font-semibold text-slate-400">
                                    <span>Fluency</span>
                                    <span id="score-fluency" class="text-white">0%</span>
                                </div>
                                <div class="h-1.5 w-full bg-slate-800 rounded-full overflow-hidden">
                                    <div id="bar-fluency" class="bg-indigo-500 h-full rounded-full transition-all duration-300" style="width: 0%;"></div>
                                </div>
                            </div>
                        </div>

                        <!-- Accent & Feedback text -->
                        <div class="space-y-4 pt-4 border-t border-slate-800/80">
                            <div class="flex items-center justify-between text-xs">
                                <span class="font-semibold text-slate-400">Accent Detected:</span>
                                <span id="metrics-accent" class="px-2 py-0.5 rounded bg-slate-800 border border-slate-700 text-white font-mono">Neutral Accent</span>
                            </div>
                            <div class="space-y-1">
                                <span class="text-xs font-semibold text-slate-400">Constructive Suggestions:</span>
                                <p id="metrics-suggestions" class="text-xs text-slate-300 leading-relaxed">Great structure. Try using more transitions to sound even more fluent.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>

    </div>

    <!-- Processing Modal -->
    <div id="processing-modal" class="fixed inset-0 z-50 hidden flex items-center justify-center bg-slate-950/85 backdrop-blur-md">
        <div class="bg-slate-900 border border-slate-800 rounded-3xl p-8 max-w-sm w-full mx-4 shadow-2xl text-center space-y-6">
            <div class="relative w-20 h-20 mx-auto">
                <div class="absolute inset-0 rounded-full border-4 border-slate-800"></div>
                <div class="absolute inset-0 rounded-full border-4 border-indigo-500 border-t-transparent animate-spin"></div>
                <div class="absolute inset-4 rounded-full bg-slate-950 flex items-center justify-center">
                    <svg class="w-6 h-6 text-indigo-400 animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11a7 7 0 01-7 7m0 0a7 7 0 01-7-7m7 7v4m0 0H8m4 0h4m-4-8a3 3 0 01-3-3V5a3 3 0 116 0v6a3 3 0 01-3 3z"></path>
                    </svg>
                </div>
            </div>
            <div class="space-y-2">
                <h3 class="text-lg font-bold text-white">SpeechIQ Interviewer</h3>
                <p class="text-slate-400 text-xs leading-relaxed" id="processing-msg">Uploading your voice answer...</p>
            </div>
            <div class="h-1.5 w-full bg-slate-800 rounded-full overflow-hidden">
                <div class="h-full bg-gradient-to-r from-indigo-500 to-cyan-500 rounded-full animate-[pulse_1.5s_infinite] w-3/4 mx-auto"></div>
            </div>
        </div>
    </div>

    <!-- Core Script Logic -->
    <script>
        // Media Recorder & Visual Context vars
        let mediaRecorder = null;
        let audioChunks = [];
        let audioBlob = null;
        let recordingDuration = 0;
        let timerInterval = null;
        let startTime = null;

        let audioCtx = null;
        let analyser = null;
        let mediaStream = null;
        let animationFrameId = null;

        const canvas = $('#waveform-canvas')[0];
        const ctx = canvas.getContext('2d');

        // Control Elements
        const btnStart = $('#btn-start');
        const btnStop = $('#btn-stop');
        const btnReset = $('#btn-reset');
        const btnUpload = $('#btn-upload');
        const btnVocalize = $('#btn-vocalize');
        
        const timerText = $('#timer-text');
        const timerDot = $('#timer-dot');
        const overlayText = $('#recorder-overlay-text');
        
        const processingModal = $('#processing-modal');
        const processingMsg = $('#processing-msg');
        const chatTimeline = $('#chat-timeline');
        
        const agentStatus = $('#agent-status');
        const pulseRing = $('#avatar-pulse-ring');
        
        // Progress bar Elements
        const progressBar = $('#session-progress');
        const progressText = $('#session-progress-text');

        // Metric Scorecard Elements
        const cardDefault = $('#metrics-card-default');
        const cardActive = $('#metrics-card-active');
        const overallProgress = $('#metrics-overall-progress');
        const overallScoreVal = $('#metrics-overall-score');
        const overallText = $('#metrics-overall-text');
        const accentText = $('#metrics-accent');
        const suggestionsText = $('#metrics-suggestions');
        
        const scoreGrammar = $('#score-grammar');
        const barGrammar = $('#bar-grammar');
        const scoreVocabulary = $('#score-vocabulary');
        const barVocabulary = $('#bar-vocabulary');
        const scoreContent = $('#score-content');
        const barContent = $('#bar-content');
        const scoreConfidence = $('#score-confidence');
        const barConfidence = $('#bar-confidence');
        const scorePronunciation = $('#score-pronunciation');
        const barPronunciation = $('#bar-pronunciation');
        const scoreFluency = $('#score-fluency');
        const barFluency = $('#bar-fluency');

        // Conversation state tracker
        const interviewContext = "{{ $test->description }}";
        let conversationHistory = []; // [{'role': 'assistant', 'content': 'question_text'}, {'role': 'user', 'content': 'transcript_text'}]
        let activeQuestionText = "{{ $question->question_text }}";
        let questionIndex = 1;
        const totalQuestions = 3;

        // Resize Canvas context
        function resizeCanvas() {
            canvas.width = canvas.offsetWidth * window.devicePixelRatio;
            canvas.height = canvas.offsetHeight * window.devicePixelRatio;
            ctx.scale(window.devicePixelRatio, window.devicePixelRatio);
        }
        $(window).on('resize', resizeCanvas);
        resizeCanvas();

        function drawStaticWave() {
            ctx.clearRect(0, 0, canvas.width, canvas.height);
            ctx.lineWidth = 2;
            ctx.strokeStyle = '#1e1b4b'; // Deep dark indigo
            ctx.beginPath();
            ctx.moveTo(0, canvas.height / 2 / window.devicePixelRatio);
            ctx.lineTo(canvas.width / window.devicePixelRatio, canvas.height / 2 / window.devicePixelRatio);
            ctx.stroke();
        }
        drawStaticWave();

        // Real TTS via gTTS backend — hidden audio element
        const ttsAudio = new Audio();

        // TTS Speech Synthesis vocalizer using real gTTS backend
        async function speakActiveQuestion() {
            // Set speaking state
            agentStatus.text("Speaking");
            agentStatus.attr('class', "text-[10px] px-2 py-0.5 rounded-full bg-indigo-500/10 border border-indigo-500/25 text-indigo-400 font-semibold uppercase tracking-wider animate-pulse");
            pulseRing.removeClass('hidden');

            // Extract clean question text
            let speakText = activeQuestionText;
            if (speakText.includes("Starting query: ")) {
                speakText = speakText.split("Starting query: ")[1];
            }
            speakText = speakText.trim();

            try {
                // Call Laravel TTS endpoint → which calls FastAPI → gTTS
                const data = await $.ajax({
                    url: '{{ route("practice.tts") }}',
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    data: JSON.stringify({ text: speakText })
                });

                if (data.success && data.audio_data) {
                    // Play the real gTTS audio (base64 data URI)
                    ttsAudio.src = data.audio_data;
                    ttsAudio.onended = () => {
                        agentStatus.text("Listening");
                        agentStatus.attr('class', "text-[10px] px-2 py-0.5 rounded-full bg-emerald-500/10 border border-emerald-500/25 text-emerald-400 font-semibold uppercase tracking-wider");
                        pulseRing.addClass('hidden');
                    };
                    ttsAudio.onerror = () => {
                        agentStatus.text("Listening");
                        agentStatus.attr('class', "text-[10px] px-2 py-0.5 rounded-full bg-emerald-500/10 border border-emerald-500/25 text-emerald-400 font-semibold uppercase tracking-wider");
                        pulseRing.addClass('hidden');
                    };
                    ttsAudio.play();
                    return;
                }
            } catch (err) {
                console.warn('Real TTS failed, falling back to browser TTS:', err);
            }

            // Fallback: browser speechSynthesis
            if ('speechSynthesis' in window) {
                window.speechSynthesis.cancel();
                const utterance = new SpeechSynthesisUtterance(speakText);
                utterance.lang = 'en-US';
                utterance.onend = () => {
                    agentStatus.text("Listening");
                    agentStatus.attr('class', "text-[10px] px-2 py-0.5 rounded-full bg-emerald-500/10 border border-emerald-500/25 text-emerald-400 font-semibold uppercase tracking-wider");
                    pulseRing.addClass('hidden');
                };
                window.speechSynthesis.speak(utterance);
            } else {
                agentStatus.text("Listening");
                agentStatus.attr('class', "text-[10px] px-2 py-0.5 rounded-full bg-emerald-500/10 border border-emerald-500/25 text-emerald-400 font-semibold uppercase tracking-wider");
                pulseRing.addClass('hidden');
            }
        }

        // Initialize Speak on load
        $(function() {
            appendQuestionBubble(activeQuestionText);
            speakActiveQuestion();
        });

        btnVocalize.on('click', speakActiveQuestion);

        // Chat Timeline Helpers
        function appendQuestionBubble(text) {
            let cleanText = text;
            if (cleanText.includes("Starting query: ")) {
                cleanText = cleanText.split("Starting query: ")[1];
            }

            const bubbleHtml = `
                <div class="flex gap-4 items-start">
                    <div class="w-10 h-10 shrink-0 rounded-xl bg-gradient-to-tr from-indigo-600 to-cyan-500 flex items-center justify-center text-white text-xs font-bold font-mono shadow-md">
                        AI
                    </div>
                    <div class="bg-indigo-950/30 border border-indigo-900/40 rounded-2xl rounded-tl-none p-4 max-w-xl text-slate-100 text-sm leading-relaxed font-medium">
                        ${cleanText}
                    </div>
                </div>
            `;
            chatTimeline.append(bubbleHtml);
            chatTimeline.scrollTop(chatTimeline[0].scrollHeight);
        }

        function appendResponseBubble(recordingId) {
            const bubbleId = `bubble-user-${recordingId}`;
            const bubbleHtml = `
                <div id="${bubbleId}" class="flex gap-4 items-start justify-end">
                    <div class="bg-slate-900/60 border border-slate-800 rounded-2xl rounded-tr-none p-4 max-w-xl text-slate-100 text-sm leading-relaxed space-y-3">
                        <div id="transcript-text-${recordingId}" class="italic text-slate-400 flex items-center gap-2 text-xs">
                            <svg class="w-3.5 h-3.5 animate-spin text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            Transcribing & Analyzing answer...
                        </div>
                        
                        <div id="quick-feedback-${recordingId}" class="hidden flex items-center gap-3 pt-2 border-t border-slate-850">
                            <span id="bubble-score-${recordingId}" class="text-xs px-2 py-0.5 rounded bg-emerald-500/10 border border-emerald-500/25 text-emerald-400 font-bold">Overall: 0%</span>
                            <button type="button" onclick="showScorecard(${recordingId})" class="text-[10px] text-indigo-400 font-bold uppercase tracking-wider hover:text-indigo-300">View Metrics</button>
                        </div>
                    </div>
                    <div class="w-10 h-10 shrink-0 rounded-xl bg-slate-800 border border-slate-700 flex items-center justify-center text-slate-200 text-xs font-bold shadow-md">
                        ME
                    </div>
                </div>
            `;
            chatTimeline.append(bubbleHtml);
            chatTimeline.scrollTop(chatTimeline[0].scrollHeight);
            return bubbleId;
        }

        // Action: Start Recording
        btnStart.on('click', async () => {
            audioChunks = [];
            
            try {
                mediaStream = await navigator.mediaDevices.getUserMedia({ audio: true });
                mediaRecorder = new MediaRecorder(mediaStream);
                
                mediaRecorder.ondataavailable = event => {
                    if (event.data.size > 0) audioChunks.push(event.data);
                };

                mediaRecorder.onstop = () => {
                    audioBlob = new Blob(audioChunks, { type: 'audio/webm' });
                    btnUpload.prop('disabled', false);
                    btnReset.prop('disabled', false);
                };

                audioCtx = new (window.AudioContext || window.webkitAudioContext)();
                const source = audioCtx.createMediaStreamSource(mediaStream);
                analyser = audioCtx.createAnalyser();
                analyser.fftSize = 256;
                source.connect(analyser);

                mediaRecorder.start();
                
                // UI updates
                btnStart.prop('disabled', true);
                btnStart.addClass('opacity-50');
                btnStop.prop('disabled', false);
                btnReset.prop('disabled', true);
                btnUpload.prop('disabled', true);
                
                overlayText.text('Interviewer listening...');
                overlayText.removeClass('text-slate-500');
                overlayText.addClass('text-indigo-400');
                timerDot.removeClass('bg-slate-700');
                timerDot.addClass('bg-rose-500 animate-ping');

                // Timer stopwatch
                startTime = Date.now();
                recordingDuration = 0;
                timerInterval = setInterval(() => {
                    const elapsed = Date.now() - startTime;
                    recordingDuration = elapsed / 1000;
                    
                    const minutes = Math.floor(elapsed / 60000);
                    const seconds = Math.floor((elapsed % 60000) / 1000);
                    const tenths = Math.floor((elapsed % 1000) / 100);
                    
                    timerText.text(
                        (minutes < 10 ? '0' : '') + minutes + ':' +
                        (seconds < 10 ? '0' : '') + seconds + '.' + tenths
                    );
                }, 100);

                drawLiveWaveform();

            } catch (err) {
                console.error('Microphone access denied: ', err);
                alert('Microphone permission is required to record voice answer.');
            }
        });

        // Draw active waveform
        function drawLiveWaveform() {
            const bufferLength = analyser.frequencyBinCount;
            const dataArray = new Uint8Array(bufferLength);

            const draw = () => {
                animationFrameId = requestAnimationFrame(draw);
                analyser.getByteFrequencyData(dataArray);

                const width = canvas.width / window.devicePixelRatio;
                const height = canvas.height / window.devicePixelRatio;

                ctx.fillStyle = '#0f172a'; // Slate 900
                ctx.fillRect(0, 0, width, height);

                const barWidth = (width / bufferLength) * 1.5;
                let barHeight;
                let x = 0;

                for (let i = 0; i < bufferLength; i++) {
                    barHeight = dataArray[i] / 2.5;
                    ctx.fillStyle = `rgb(${79 + barHeight}, ${70 + barHeight/2}, 229)`; // Indigo gradient
                    ctx.fillRect(x, (height - barHeight) / 2, barWidth - 2, barHeight);
                    x += barWidth;
                }
            };
            draw();
        }

        // Action: Stop Recording
        btnStop.on('click', () => {
            if (mediaRecorder && mediaRecorder.state !== 'inactive') {
                mediaRecorder.stop();
            }

            if (mediaStream) {
                mediaStream.getTracks().forEach(track => track.stop());
            }

            clearInterval(timerInterval);
            cancelAnimationFrame(animationFrameId);

            btnStart.prop('disabled', false);
            btnStart.removeClass('opacity-50');
            btnStop.prop('disabled', true);
            btnReset.prop('disabled', false);

            overlayText.text('Answer Completed');
            overlayText.attr('class', 'absolute text-indigo-400 text-xs font-semibold uppercase tracking-widest pointer-events-none');
            timerDot.attr('class', 'w-2.5 h-2.5 rounded-full bg-emerald-500');
            
            drawStaticWave();
        });

        // Action: Reset
        btnReset.on('click', () => {
            audioChunks = [];
            audioBlob = null;
            recordingDuration = 0;

            timerText.text('00:00.0');
            timerDot.attr('class', 'w-2.5 h-2.5 rounded-full bg-slate-700');
            overlayText.text('Microphone Inactive');
            overlayText.attr('class', 'absolute text-slate-500 text-xs font-semibold uppercase tracking-widest pointer-events-none');

            btnUpload.prop('disabled', true);
            btnReset.prop('disabled', true);

            drawStaticWave();
        });

        // Cached results records map
        let cachedResults = {};

        // Action: Submit for Analysis
        btnUpload.on('click', () => {
            if (!audioBlob) return;

            btnUpload.prop('disabled', true);
            btnReset.prop('disabled', true);
            showProcessingModal('Uploading recording to SpeechIQ backend...');

            // Generate a temporary unique ID
            const tempId = Date.now();
            const bubbleId = appendResponseBubble(tempId);

            const formData = new FormData();
            formData.append('audio_file', audioBlob, 'interview_response.webm');
            formData.append('duration', recordingDuration);
            formData.append('question_text', activeQuestionText);

            const submitUrl = '{{ route("practice.interview.submit", $test->id) }}';

            $.ajax({
                url: submitUrl,
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                data: formData,
                processData: false,
                contentType: false
            })
            .done(data => {
                if (data.success) {
                    const realId = data.recording_id;

                    // Update DOM element IDs from tempId to realId
                    const bubbleUser = $(`#bubble-user-${tempId}`);
                    if (bubbleUser.length) bubbleUser.attr('id', `bubble-user-${realId}`);

                    const transcriptText = $(`#transcript-text-${tempId}`);
                    if (transcriptText.length) transcriptText.attr('id', `transcript-text-${realId}`);

                    const quickFeedback = $(`#quick-feedback-${tempId}`);
                    if (quickFeedback.length) quickFeedback.attr('id', `quick-feedback-${realId}`);

                    const bubbleScore = $(`#bubble-score-${tempId}`);
                    if (bubbleScore.length) bubbleScore.attr('id', `bubble-score-${realId}`);

                    const viewMetricsBtn = quickFeedback.length ? quickFeedback.find('button') : null;
                    if (viewMetricsBtn && viewMetricsBtn.length) {
                        viewMetricsBtn.attr('onclick', `showScorecard(${realId})`);
                    }

                    showProcessingModal('Enqueuing audio. Contacting AI Engine...');
                    startPolling(realId);
                } else {
                    hideProcessingModal();
                    alert('Error: ' + data.message);
                    btnUpload.prop('disabled', false);
                }
            })
            .fail((xhr, status, err) => {
                hideProcessingModal();
                alert('Upload failed: ' + err);
                btnUpload.prop('disabled', false);
            });
        });

        // Polling queue status
        let pollInterval = null;

        function startPolling(recordingId) {
            const statusUrl = '{{ route("practice.interview.status", ":id") }}'.replace(':id', recordingId);
            let checkCount = 0;

            pollInterval = setInterval(() => {
                checkCount++;
                $.getJSON(statusUrl)
                    .done(data => {
                        if (data.success) {
                            if (data.status === 'processing') {
                                showProcessingModal('SpeechIQ: Running transcription and metric evaluations...');
                            } else if (data.status === 'completed') {
                                clearInterval(pollInterval);
                                showProcessingModal('Analysis complete! Updating scorecard metrics...');
                                fetchResults(recordingId);
                            } else if (data.status === 'failed') {
                                clearInterval(pollInterval);
                                hideProcessingModal();
                                alert('AI Analysis failed. Please reset and record response again.');
                                btnUpload.prop('disabled', false);
                            }
                        }
                    })
                    .fail((xhr, status, err) => {
                        console.error('Polling error:', err);
                    });

                // Fail-safe timeout after 60 seconds
                if (checkCount > 30) {
                    clearInterval(pollInterval);
                    hideProcessingModal();
                    alert('Analysis timed out. Please check your Laravel queue worker.');
                    btnUpload.prop('disabled', false);
                }
            }, 2000);
        }

        // Fetch detailed results
        function fetchResults(recordingId) {
            const resultsUrl = '{{ route("practice.interview.results", ":id") }}'.replace(':id', recordingId);
            $.getJSON(resultsUrl)
                .done(data => {
                    hideProcessingModal();
                    if (data.success) {
                        const result = data.result;
                        cachedResults[recordingId] = result;
                        
                        // Push into conversation history array for follow-up context
                        conversationHistory.push({
                            'role': 'assistant',
                            'content': activeQuestionText
                        });
                        conversationHistory.push({
                            'role': 'user',
                            'content': result.transcript
                        });

                        // Update Chat bubble transcript text
                        const tx = $(`#transcript-text-${recordingId}`);
                        tx.text(`"${result.transcript}"`);
                        tx.attr('class', "text-slate-100 italic text-sm");
                        
                        // Display score and metric trigger
                        $(`#bubble-score-${recordingId}`).text(`Overall: ${result.overall_score}%`);
                        $(`#quick-feedback-${recordingId}`).removeClass('hidden');

                        // Show Right metrics card
                        showScorecard(recordingId);

                        // Reset Recording controllers
                        btnReset.click();
                        
                        // Progress to next question trigger
                        setupNextQuestionAction();
                    }
                })
                .fail((xhr, status, err) => {
                    console.error('Fetch results error:', err);
                });
        }

        // Display Score details on Right Panel
        window.showScorecard = function(recordingId) {
            const result = cachedResults[recordingId];
            if (!result) return;

            cardDefault.addClass('hidden');
            cardActive.removeClass('hidden');

            // Set scores
            overallScoreVal.text(`${result.overall_score}%`);
            overallProgress.attr('stroke-dasharray', `${result.overall_score}, 100`);

            if (result.overall_score >= 85) {
                overallText.text("Excellent Articulation");
                overallText.attr('class', "text-sm font-bold text-emerald-400");
            } else if (result.overall_score >= 70) {
                overallText.text("Good Competency");
                overallText.attr('class', "text-sm font-bold text-indigo-400");
            } else {
                overallText.text("Requires Practice");
                overallText.attr('class', "text-sm font-bold text-amber-500");
            }

            scoreGrammar.text(`${result.grammar_score}%`);
            barGrammar.css('width', `${result.grammar_score}%`);
            
            scoreVocabulary.text(`${result.vocabulary_score}%`);
            barVocabulary.css('width', `${result.vocabulary_score}%`);
            
            scoreContent.text(`${result.content_score}%`);
            barContent.css('width', `${result.content_score}%`);
            
            scoreConfidence.text(`${result.confidence_score}%`);
            barConfidence.css('width', `${result.confidence_score}%`);
            
            scorePronunciation.text(`${result.pronunciation_score}%`);
            barPronunciation.css('width', `${result.pronunciation_score}%`);
            
            scoreFluency.text(`${result.fluency_score}%`);
            barFluency.css('width', `${result.fluency_score}%`);

            accentText.text(result.accent || 'Neutral Accent');
            suggestionsText.text(result.feedback || 'Good response structure.');
        };

        // Determine if next question is generated or session is finalized
        function setupNextQuestionAction() {
            // Append action container dynamically
            const actBox = $('<div></div>')
                .attr('id', 'next-question-trigger-container')
                .addClass('flex justify-center pt-2');

            if (questionIndex < totalQuestions) {
                actBox.html(`
                    <button type="button" id="btn-next-question" onclick="triggerNextQuestion()" class="inline-flex items-center gap-2 px-6 py-2.5 bg-gradient-to-r from-indigo-500 to-cyan-500 hover:from-indigo-600 hover:to-cyan-600 text-white font-semibold rounded-xl text-sm transition duration-200 shadow-md">
                        Get Next Question
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
                        </svg>
                    </button>
                `);
            } else {
                actBox.html(`
                    <button type="button" onclick="triggerFinishInterview()" class="inline-flex items-center gap-2 px-6 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white font-semibold rounded-xl text-sm transition duration-200 shadow-md">
                        Finish Interview & View Feedback
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </button>
                `);
            }

            chatTimeline.append(actBox);
            chatTimeline.scrollTop(chatTimeline[0].scrollHeight);
        }

        // Action: Fetch Dynamic Next Question from FastAPI
        window.triggerNextQuestion = function() {
            // Remove the trigger button
            $('#next-question-trigger-container').remove();

            showProcessingModal('Generating dynamic follow-up question...');

            const nextUrl = '{{ route("practice.interview.next-question", $test->id) }}';

            $.ajax({
                url: nextUrl,
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                data: JSON.stringify({
                    context: interviewContext,
                    history: conversationHistory
                })
            })
            .done(data => {
                hideProcessingModal();
                if (data.success) {
                    // Update index details
                    questionIndex++;
                    activeQuestionText = data.question;

                    // Update session bar indicators
                    const pct = Math.min(100, (questionIndex / totalQuestions) * 100);
                    progressBar.css('width', `${pct}%`);
                    progressText.text(`Question ${questionIndex} of ${totalQuestions}`);

                    // Print next bubble
                    appendQuestionBubble(activeQuestionText);

                    // Speak out question
                    speakActiveQuestion();
                } else {
                    alert('Error generating question: ' + data.message);
                }
            })
            .fail((xhr, status, err) => {
                hideProcessingModal();
                alert('Connection error. Could not contact AI Engine.');
            });
        };

        // Action: Finish session
        window.triggerFinishInterview = function() {
            showProcessingModal('Finalizing interview summary reports...');
            setTimeout(() => {
                hideProcessingModal();
                // Redirect user back to dashboard with positive success notification
                window.location.href = '{{ route("practice.interview.index") }}?success=1';
            }, 1500);
        };

        function showProcessingModal(message) {
            processingMsg.text(message);
            processingModal.removeClass('hidden');
        }

        function hideProcessingModal() {
            processingModal.addClass('hidden');
        }
    </script>
</x-user-layout>
