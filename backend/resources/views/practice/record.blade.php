<x-user-layout>
    <div class="max-w-3xl mx-auto space-y-8">
        
        <!-- Welcome / Intro Header -->
        <div class="text-center space-y-2">
            <h2 class="text-2xl font-bold text-white bg-gradient-to-r from-indigo-400 to-cyan-400 bg-clip-text text-transparent">Voice Recorder Playground</h2>
            <p class="text-sm text-slate-400">Test your microphone, record a snippet, visualize the live waveform, and upload your voice file.</p>
        </div>

        <!-- Glassmorphic Main Recorder Card -->
        <div class="bg-slate-950/40 backdrop-blur border border-slate-800 rounded-3xl p-8 space-y-8">
            
            <!-- Waveform Canvas Display -->
            <div class="relative bg-slate-900 border border-slate-800/80 rounded-2xl h-48 overflow-hidden flex items-center justify-center">
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
                    <span class="w-2.5 h-2.5 rounded-full bg-slate-700" id="timer-dot"></span>
                    <span class="text-2xl font-bold text-white font-mono" id="timer-text">00:00.0</span>
                </div>

                <!-- Custom Audio Playback Player -->
                <div id="audio-playback-container" class="hidden flex-1 max-w-md w-full bg-slate-900 border border-slate-800 rounded-xl px-4 py-2 flex items-center gap-3">
                    <button type="button" id="btn-play-pause" class="w-8 h-8 rounded-full bg-indigo-600 hover:bg-indigo-700 flex items-center justify-center text-white transition shrink-0">
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
            <div class="flex flex-wrap items-center justify-center gap-4">
                <!-- Start Recording -->
                <button type="button" id="btn-start" class="inline-flex items-center gap-2 px-6 py-3.5 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold rounded-xl text-sm transition duration-200 shadow-md">
                    <span class="w-2.5 h-2.5 rounded-full bg-white animate-pulse"></span>
                    Start Recording
                </button>

                <!-- Stop Recording -->
                <button type="button" id="btn-stop" disabled class="inline-flex items-center gap-2 px-6 py-3.5 bg-rose-600 hover:bg-rose-700 disabled:opacity-40 disabled:cursor-not-allowed text-white font-semibold rounded-xl text-sm transition duration-200 shadow-md">
                    <svg class="w-4 h-4 fill-current" viewBox="0 0 24 24">
                        <path d="M6 6h12v12H6z"/>
                    </svg>
                    Stop Recording
                </button>

                <!-- Reset Recording -->
                <button type="button" id="btn-reset" disabled class="inline-flex items-center gap-2 px-6 py-3.5 bg-slate-800 hover:bg-slate-750 disabled:opacity-40 disabled:cursor-not-allowed text-slate-300 font-semibold rounded-xl text-sm transition duration-200">
                    Reset
                </button>

                <!-- Upload Recording -->
                <button type="button" id="btn-upload" disabled class="inline-flex items-center gap-2 px-6 py-3.5 bg-gradient-to-r from-indigo-500 to-cyan-500 hover:from-indigo-600 hover:to-cyan-600 disabled:opacity-40 disabled:cursor-not-allowed text-white font-semibold rounded-xl text-sm transition duration-200 shadow-lg">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path>
                    </svg>
                    Upload Recording
                </button>
            </div>

            <!-- Feedback / Upload Response Console -->
            <div id="response-console" class="hidden bg-slate-900 border border-slate-800 rounded-2xl p-5 space-y-2">
                <span class="text-xs font-semibold uppercase tracking-wider text-slate-500 block">Server Upload Details</span>
                <div class="text-xs font-mono text-emerald-400" id="response-text"></div>
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
        let $timerText, $timerDot, $overlayText, $responseConsole, $responseText;
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
            $responseConsole = $('#response-console');
            $responseText = $('#response-text');

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
                ctx.strokeStyle = '#312e81'; // Deep Indigo
                ctx.beginPath();
                ctx.moveTo(0, canvas.height / 2 / window.devicePixelRatio);
                ctx.lineTo(canvas.width / window.devicePixelRatio, canvas.height / 2 / window.devicePixelRatio);
                ctx.stroke();
            }
            drawStaticWave();

            // Start Recording Trigger
            $btnStart.on('click', async () => {
                audioChunks = [];
                
                try {
                    // Request microphone access
                    mediaStream = await navigator.mediaDevices.getUserMedia({ audio: true });
                    
                    // Initialize MediaRecorder
                    mediaRecorder = new MediaRecorder(mediaStream);
                    mediaRecorder.ondataavailable = event => {
                        if (event.data.size > 0) audioChunks.push(event.data);
                    };

                    mediaRecorder.onstop = () => {
                        audioBlob = new Blob(audioChunks, { type: 'audio/webm' });
                        const audioUrl = URL.createObjectURL(audioBlob);
                        $audioPlayer.attr('src', audioUrl);

                        // Enable upload and reset buttons
                        $btnUpload.prop('disabled', false);
                        $btnReset.prop('disabled', false);

                        // Show custom player
                        $playerContainer.removeClass('hidden');
                    };

                    // Initialize Web Audio API Analyser
                    audioCtx = new (window.AudioContext || window.webkitAudioContext)();
                    const source = audioCtx.createMediaStreamSource(mediaStream);
                    analyser = audioCtx.createAnalyser();
                    analyser.fftSize = 256;
                    source.connect(analyser);

                    // Start recording
                    mediaRecorder.start();
                    
                    // UI Updates
                    $btnStart.prop('disabled', true).addClass('opacity-50');
                    $btnStop.prop('disabled', false);
                    $btnReset.prop('disabled', true);
                    $btnUpload.prop('disabled', true);
                    $playerContainer.addClass('hidden');
                    $responseConsole.addClass('hidden');
                    
                    $overlayText.text('Recording Voice...')
                        .removeClass('text-slate-500')
                        .addClass('text-indigo-400');
                    $timerDot.removeClass('bg-slate-700')
                        .addClass('bg-rose-500 animate-ping');

                    // Start stopwatch
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

                    // Start visualizer animation
                    drawLiveWaveform();

                } catch (err) {
                    console.error('Microphone access denied or error: ', err);
                    alert('Microphone permission is required to record voice.');
                }
            });

            // Draw dynamic live waveform
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
                        barHeight = dataArray[i] / 2;

                        // Build sleek blue/cyan gradient bar
                        ctx.fillStyle = `rgb(${80 + barHeight}, ${99 - barHeight/2}, 245)`;
                        
                        // Draw centered bars
                        ctx.fillRect(x, (height - barHeight) / 2, barWidth - 2, barHeight);
                        x += barWidth;
                    }
                };
                draw();
            }

            // Stop Recording Trigger
            $btnStop.on('click', () => {
                if (mediaRecorder && mediaRecorder.state !== 'inactive') {
                    mediaRecorder.stop();
                }

                // Stop mic stream
                if (mediaStream) {
                    mediaStream.getTracks().forEach(track => track.stop());
                }

                // Clear timers
                clearInterval(timerInterval);
                cancelAnimationFrame(animationFrameId);

                // UI cleanup
                $btnStart.prop('disabled', false).removeClass('opacity-50');
                $btnStop.prop('disabled', true);
                $btnReset.prop('disabled', false);

                $overlayText.text('Recording Complete')
                    .attr('class', 'absolute text-indigo-400 text-xs font-semibold uppercase tracking-widest pointer-events-none');
                $timerDot.attr('class', 'w-2.5 h-2.5 rounded-full bg-emerald-500');
                
                drawStaticWave();
            });

            // Reset Recorder
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
                $responseConsole.addClass('hidden');

                drawStaticWave();
            });

            // Custom Player Controls
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

            // Upload recorded blob to Laravel backend using jQuery AJAX
            $btnUpload.on('click', () => {
                if (!audioBlob) return;

                $btnUpload.prop('disabled', true).text('Uploading...');

                const formData = new FormData();
                formData.append('audio_file', audioBlob, 'recording.webm');
                formData.append('duration', recordingDuration);

                $.ajax({
                    url: '{{ route("audio-recordings.store") }}',
                    type: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(data) {
                        $btnUpload.text('Upload Recording').prop('disabled', false);
                        $responseConsole.removeClass('hidden');
                        if (data.success) {
                            $responseText.text(JSON.stringify(data.data, null, 4))
                                .attr('class', 'text-xs font-mono text-emerald-400 whitespace-pre-wrap');
                        } else {
                            $responseText.text('Error: ' + data.message)
                                .attr('class', 'text-xs font-mono text-rose-400 whitespace-pre-wrap');
                        }
                    },
                    error: function(xhr, status, error) {
                        $btnUpload.text('Upload Recording').prop('disabled', false);
                        $responseConsole.removeClass('hidden');
                        $responseText.text('Error: ' + error)
                            .attr('class', 'text-xs font-mono text-rose-450 whitespace-pre-wrap');
                    }
                });
            });
        });
    </script>
</x-user-layout>
