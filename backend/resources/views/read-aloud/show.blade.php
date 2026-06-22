<x-user-layout>
    <div class="max-w-4xl mx-auto space-y-8">
        
        <!-- Navigation Header -->
        <div class="flex items-center justify-between border-b border-slate-800 pb-4">
            <a href="{{ route('practice.read-aloud.index') }}" class="inline-flex items-center gap-2 text-sm font-semibold text-slate-450 hover:text-white transition duration-200">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Back to dashboard
            </a>
            <span class="text-xs px-3 py-1 rounded-full bg-slate-950 border border-slate-850 text-slate-400 font-semibold uppercase tracking-wider">
                Module {{ $test->id }}
            </span>
        </div>

        <!-- Main Card -->
        <div class="card-premium-glass rounded-3xl p-6 md:p-10 space-y-8">
            
            <!-- Instructions and Title -->
            <div class="space-y-2">
                <h1 class="text-2xl font-bold text-white">{{ $test->title }}</h1>
                <p class="text-sm text-slate-400">Read the following paragraph clearly and at a normal pace. Click start to begin recording.</p>
            </div>

            <!-- Reading Paragraph Display -->
            <div class="relative bg-slate-900/50 border border-slate-800/80 rounded-2xl p-8 shadow-inner overflow-hidden">
                <div class="absolute top-0 right-0 w-24 h-24 bg-indigo-500/5 rounded-full blur-2xl"></div>
                <p class="text-lg md:text-xl text-slate-100 leading-relaxed font-medium tracking-wide selection:bg-indigo-600 selection:text-white" id="target-paragraph">
                    {{ $question->question_text }}
                </p>
            </div>

            <!-- Waveform Canvas Display -->
            <div class="relative bg-slate-950/80 border border-slate-800/80 rounded-2xl h-36 overflow-hidden flex items-center justify-center">
                <canvas id="waveform-canvas" class="w-full h-full block"></canvas>
                
                <!-- Status text overlay -->
                <div id="recorder-overlay-text" class="absolute text-slate-500 text-xs font-semibold uppercase tracking-widest pointer-events-none">
                    Microphone Inactive
                </div>
            </div>

            <!-- Timer and Audio Player Panel -->
            <div class="flex flex-col sm:flex-row items-center justify-between gap-6 border-y border-slate-800/60 py-6">
                <!-- Live Stopwatch -->
                <div class="flex items-center gap-3">
                    <span class="w-2.5 h-2.5 rounded-full bg-slate-750" id="timer-dot"></span>
                    <span class="text-2xl font-extrabold text-white font-mono" id="timer-text">00:00.0</span>
                </div>

                <!-- Custom Audio Playback Player -->
                <div id="audio-playback-container" class="hidden flex-1 max-w-md w-full bg-slate-900 border border-slate-800 rounded-xl px-4 py-2 flex items-center gap-3">
                    <button type="button" id="btn-play-pause" class="w-8 h-8 rounded-full btn-premium-indigo flex items-center justify-center text-white transition shrink-0">
                        <!-- Play Icon -->
                        <svg id="icon-play" class="w-4 h-4 fill-current" viewBox="0 0 24 24">
                            <path d="M8 5v14l11-7z"/>
                        </svg>
                        <!-- Pause Icon -->
                        <svg id="icon-pause" class="w-4 h-4 fill-current hidden" viewBox="0 0 24 24">
                            <path d="M6 19h4V5H6v14zm8-14v14h4V5h-4z"/>
                        </svg>
                    </button>
                    <!-- Timeline Slider -->
                    <input type="range" id="audio-seeker" value="0" min="0" max="100" class="flex-1 accent-indigo-500 bg-slate-800 h-1 rounded-lg cursor-pointer appearance-none">
                    <!-- Audio Tag (Hidden) -->
                    <audio id="audio-player" class="hidden"></audio>
                    <!-- Audio Duration -->
                    <span class="text-[10px] text-slate-400 font-mono shrink-0" id="audio-duration-text">00:00</span>
                </div>
            </div>

            <!-- Controls Buttons Grid -->
            <div class="flex flex-wrap items-center justify-center gap-4 pt-2">
                <!-- Start Recording -->
                <button type="button" id="btn-start" class="inline-flex items-center gap-2 px-6 py-3.5 btn-premium-indigo font-bold rounded-xl text-sm">
                    <span class="w-2.5 h-2.5 rounded-full bg-white animate-pulse"></span>
                    Start Recording
                </button>

                <!-- Stop Recording -->
                <button type="button" id="btn-stop" disabled class="inline-flex items-center gap-2 px-6 py-3.5 btn-premium-rose disabled:opacity-40 disabled:cursor-not-allowed font-bold rounded-xl text-sm">
                    <svg class="w-4 h-4 fill-current" viewBox="0 0 24 24">
                        <path d="M6 6h12v12H6z"/>
                    </svg>
                    Stop Recording
                </button>

                <!-- Reset Recording -->
                <button type="button" id="btn-reset" disabled class="inline-flex items-center gap-2 px-6 py-3.5 btn-premium-slate disabled:opacity-40 disabled:cursor-not-allowed font-bold rounded-xl text-sm">
                    Reset
                </button>

                <!-- Submit for Analysis -->
                <button type="button" id="btn-upload" disabled class="inline-flex items-center gap-2 px-6 py-3.5 btn-premium-cyan disabled:opacity-40 disabled:cursor-not-allowed font-bold rounded-xl text-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                    </svg>
                    Submit for Analysis
                </button>
            </div>

        </div>

    </div>

    <!-- Processing Modal Overlay -->
    <div id="processing-modal" class="fixed inset-0 z-50 hidden flex items-center justify-center bg-slate-950/85 backdrop-blur-md">
        <div class="bg-slate-900 border border-slate-800 rounded-3xl p-8 max-w-sm w-full mx-4 shadow-2xl text-center space-y-6">
            <!-- Spinner -->
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
                <h3 class="text-lg font-bold text-white">Speech IQ Analyzer</h3>
                <p class="text-slate-400 text-xs leading-relaxed" id="processing-msg">Uploading voice recording...</p>
            </div>
            <!-- Progress strip -->
            <div class="h-1.5 w-full bg-slate-800 rounded-full overflow-hidden">
                <div class="h-full bg-gradient-to-r from-indigo-500 to-cyan-500 rounded-full animate-[pulse_1.5s_infinite] w-3/4 mx-auto"></div>
            </div>
        </div>
    </div>

    <!-- Recorder JS Implementation -->
    <script>
        let mediaRecorder = null;
        let audioChunks = [];
        let audioBlob = null;
        let recordingDuration = 0;
        let timerInterval = null;
        let startTime = null;

        // Web Audio Context vars
        let audioCtx = null;
        let analyser = null;
        let mediaStream = null;
        let animationFrameId = null;

        let $canvas, canvas, ctx;
        let $btnStart, $btnStop, $btnReset, $btnUpload;
        let $timerText, $timerDot, $overlayText, $processingModal, $processingMsg;
        let $playerContainer, $audioPlayer, audioPlayer, $btnPlayPause, $iconPlay, $iconPause, $seeker, $durationText;

        $(function() {
            $canvas = $('#waveform-canvas');
            canvas = $canvas[0];
            ctx = canvas.getContext('2d');

            // Elements
            $btnStart = $('#btn-start');
            $btnStop = $('#btn-stop');
            $btnReset = $('#btn-reset');
            $btnUpload = $('#btn-upload');
            $timerText = $('#timer-text');
            $timerDot = $('#timer-dot');
            $overlayText = $('#recorder-overlay-text');
            $processingModal = $('#processing-modal');
            $processingMsg = $('#processing-msg');

            // Playback Elements
            $playerContainer = $('#audio-playback-container');
            $audioPlayer = $('#audio-player');
            audioPlayer = $audioPlayer[0];
            $btnPlayPause = $('#btn-play-pause');
            $iconPlay = $('#icon-play');
            $iconPause = $('#icon-pause');
            $seeker = $('#audio-seeker');
            $durationText = $('#audio-duration-text');

            // Initialize Canvas size
            function resizeCanvas() {
                canvas.width = $canvas.outerWidth() * window.devicePixelRatio;
                canvas.height = $canvas.outerHeight() * window.devicePixelRatio;
                ctx.scale(window.devicePixelRatio, window.devicePixelRatio);
                if (mediaRecorder && mediaRecorder.state === 'recording') {
                    // Do nothing
                } else {
                    drawStaticWave();
                }
            }
            $(window).on('resize', resizeCanvas);
            resizeCanvas();

            // Draw initial static wave
            function drawStaticWave() {
                ctx.clearRect(0, 0, canvas.width, canvas.height);
                ctx.lineWidth = 2;
                ctx.strokeStyle = '#1e1b4b'; // Very deep indigo
                ctx.beginPath();
                ctx.moveTo(0, canvas.height / 2 / window.devicePixelRatio);
                ctx.lineTo(canvas.width / window.devicePixelRatio, canvas.height / 2 / window.devicePixelRatio);
                ctx.stroke();
            }
            drawStaticWave();

            // Start Recording
            $btnStart.on('click', async () => {
                audioChunks = [];
                
                try {
                    mediaStream = await navigator.mediaDevices.getUserMedia({ audio: true });
                    mediaRecorder = new MediaRecorder(mediaStream);
                    
                    mediaRecorder.ondataavailable = event => {
                        if (event.data.size > 0) audioChunks.push(event.data);
                    };

                    mediaRecorder.onstop = () => {
                        audioBlob = new Blob(audioChunks, { type: 'audio/webm' });
                        $audioPlayer.attr('src', URL.createObjectURL(audioBlob));

                        $btnUpload.prop('disabled', false);
                        $btnReset.prop('disabled', false);
                        $playerContainer.removeClass('hidden');
                    };

                    audioCtx = new (window.AudioContext || window.webkitAudioContext)();
                    const source = audioCtx.createMediaStreamSource(mediaStream);
                    analyser = audioCtx.createAnalyser();
                    analyser.fftSize = 256;
                    source.connect(analyser);

                    mediaRecorder.start();
                    
                    // UI updates
                    $btnStart.prop('disabled', true).addClass('opacity-50');
                    $btnStop.prop('disabled', false);
                    $btnReset.prop('disabled', true);
                    $btnUpload.prop('disabled', true);
                    $playerContainer.addClass('hidden');
                    
                    $overlayText.text('Recording Voice...')
                        .removeClass('text-slate-500')
                        .addClass('text-indigo-400');
                    $timerDot.removeClass('bg-slate-700')
                        .addClass('bg-rose-500 animate-ping');

                    // Stopwatch
                    startTime = Date.now();
                    recordingDuration = 0;
                    timerInterval = setInterval(() => {
                        const elapsed = Date.now() - startTime;
                        recordingDuration = elapsed / 1000;
                        
                        const minutes = Math.floor(elapsed / 60000);
                        const seconds = Math.floor((elapsed % 60000) / 1000);
                        const tenths = Math.floor((elapsed % 1000) / 100);
                        
                        $timerText.text(
                            (minutes < 10 ? '0' : '') + minutes + ':' +
                            (seconds < 10 ? '0' : '') + seconds + '.' + tenths
                        );
                    }, 100);

                    drawLiveWaveform();

                } catch (err) {
                    console.error('Microphone access denied or error: ', err);
                    alert('Microphone permission is required to record voice.');
                }
            });

            // Draw Live Waveform
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

            // Stop Recording
            $btnStop.on('click', () => {
                if (mediaRecorder && mediaRecorder.state !== 'inactive') {
                    mediaRecorder.stop();
                }

                if (mediaStream) {
                    mediaStream.getTracks().forEach(track => track.stop());
                }

                clearInterval(timerInterval);
                cancelAnimationFrame(animationFrameId);

                $btnStart.prop('disabled', false).removeClass('opacity-50');
                $btnStop.prop('disabled', true);
                $btnReset.prop('disabled', false);

                $overlayText.text('Recording Complete')
                    .attr('class', 'absolute text-indigo-400 text-xs font-semibold uppercase tracking-widest pointer-events-none');
                $timerDot.attr('class', 'w-2.5 h-2.5 rounded-full bg-emerald-500');
                
                drawStaticWave();
            });

            // Reset
            $btnReset.on('click', () => {
                audioChunks = [];
                audioBlob = null;
                recordingDuration = 0;

                $audioPlayer.attr('src', '');
                $timerText.text('00:00.0');
                $timerDot.attr('class', 'w-2.5 h-2.5 rounded-full bg-slate-700');
                $overlayText.text('Microphone Inactive')
                    .attr('class', 'absolute text-slate-500 text-xs font-semibold uppercase tracking-widest pointer-events-none');

                $btnUpload.prop('disabled', true);
                $btnReset.prop('disabled', true);
                $playerContainer.addClass('hidden');

                drawStaticWave();
            });

            // Custom Player Event Listeners
            $btnPlayPause.on('click', () => {
                if (audioPlayer.paused) {
                    audioPlayer.play();
                    $iconPlay.addClass('hidden');
                    $iconPause.removeClass('hidden');
                } else {
                    audioPlayer.pause();
                    $iconPlay.removeClass('hidden');
                    $iconPause.addClass('hidden');
                }
            });

            $audioPlayer.on('timeupdate', () => {
                const pct = (audioPlayer.currentTime / audioPlayer.duration) * 100;
                $seeker.val(pct || 0);
                
                const mins = Math.floor(audioPlayer.currentTime / 60);
                const secs = Math.floor(audioPlayer.currentTime % 60);
                $durationText.text((mins < 10 ? '0' : '') + mins + ':' + (secs < 10 ? '0' : '') + secs);
            });

            $seeker.on('input', () => {
                const time = ($seeker.val() / 100) * audioPlayer.duration;
                audioPlayer.currentTime = time;
            });

            $audioPlayer.on('ended', () => {
                $iconPlay.removeClass('hidden');
                $iconPause.addClass('hidden');
                $seeker.val(0);
            });

            // Submit for AI Analysis using jQuery AJAX
            $btnUpload.on('click', () => {
                if (!audioBlob) return;

                $btnUpload.prop('disabled', true);
                showProcessingModal('Uploading recording to Laravel server...');

                const formData = new FormData();
                formData.append('audio_file', audioBlob, 'recording.webm');
                formData.append('duration', recordingDuration);

                const submitUrl = '{{ route("practice.read-aloud.submit", [$test->id, $question->id]) }}';

                $.ajax({
                    url: submitUrl,
                    type: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(data) {
                        if (data.success) {
                            showProcessingModal('Enqueuing audio. Contacting AI Engine...');
                            startPolling(data.recording_id);
                        } else {
                            hideProcessingModal();
                            alert('Error: ' + data.message);
                            $btnUpload.prop('disabled', false);
                        }
                    },
                    error: function(xhr, status, error) {
                        hideProcessingModal();
                        alert('Upload failed: ' + error);
                        $btnUpload.prop('disabled', false);
                    }
                });
            });

            let pollInterval = null;

            function startPolling(recordingId) {
                const statusUrl = '{{ route("practice.read-aloud.status", ":id") }}'.replace(':id', recordingId);
                let checkCount = 0;

                pollInterval = setInterval(() => {
                    checkCount++;
                    $.ajax({
                        url: statusUrl,
                        type: 'GET',
                        dataType: 'json',
                        success: function(data) {
                            if (data.success) {
                                if (data.status === 'processing') {
                                    showProcessingModal('SpeechIQ: Processing transcription and scoring metrics...');
                                } else if (data.status === 'completed') {
                                    clearInterval(pollInterval);
                                    showProcessingModal('Analysis complete! Redirecting to dashboard...');
                                    setTimeout(() => {
                                        window.location.href = '{{ route("practice.read-aloud.results", ":id") }}'.replace(':id', recordingId);
                                    }, 1000);
                                } else if (data.status === 'failed') {
                                    clearInterval(pollInterval);
                                    hideProcessingModal();
                                    alert('AI analysis failed. Please try recording again.');
                                    $btnUpload.prop('disabled', false);
                                }
                            }
                        },
                        error: function(xhr, status, error) {
                            console.error('Polling error:', error);
                        }
                    });

                    // Fail-safe timeout after 60 seconds
                    if (checkCount > 30) {
                        clearInterval(pollInterval);
                        hideProcessingModal();
                        alert('Analysis timed out. Please check your Laravel queue process.');
                        $btnUpload.prop('disabled', false);
                    }
                }, 2000);
            }

            function showProcessingModal(message) {
                $processingMsg.text(message);
                $processingModal.removeClass('hidden');
            }

            function hideProcessingModal() {
                $processingModal.addClass('hidden');
            }
        });
    </script>
</x-user-layout>
