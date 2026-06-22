import os
import io
import sys
import inspect
import asyncio
from dotenv import load_dotenv

# Load environment variables from .env file
load_dotenv()

# Overriding inspect.getmodule to handle SpeechBrain lazy import exceptions gracefully on Windows
orig_getmodule = inspect.getmodule
def custom_getmodule(object, _files=None):
    try:
        return orig_getmodule(object, _files)
    except ImportError:
        return None
inspect.getmodule = custom_getmodule

import tempfile
import subprocess
import base64
import json
import difflib
import re
import logging
import httpx
from fastapi import FastAPI, File, UploadFile, Form, Request, HTTPException
from fastapi.middleware.cors import CORSMiddleware

# Set up logging
logging.basicConfig(level=logging.INFO)
logger = logging.getLogger("speechiq-ai")

# Import heavy modules at startup to avoid response timeouts
logger.info("Pre-loading SpeechBrain, Librosa, and PyTorch...")
try:
    import librosa
    import numpy as np
    import torch
    import speechbrain as sb
    from speechbrain.processing.features import STFT, spectral_magnitude
    logger.info("Successfully pre-loaded heavy modules.")
except Exception as e:
    logger.error(f"Error pre-loading heavy modules: {e}")


app = FastAPI(
    title="SpeechIQ AI Engine",
    description="Python FastAPI backend for SpeechIQ voice & accent analysis",
    version="1.0.0"
)

# CORS configuration
app.add_middleware(
    CORSMiddleware,
    allow_origins=["*"],
    allow_credentials=True,
    allow_methods=["*"],
    allow_headers=["*"],
)

AI_API_TOKEN = os.environ.get("AI_API_TOKEN", "")


def verify_api_token(request: Request) -> None:
    """Validate Bearer token when AI_API_TOKEN is configured."""
    if not AI_API_TOKEN:
        return

    auth_header = request.headers.get("Authorization", "")
    if not auth_header.startswith("Bearer "):
        raise HTTPException(status_code=401, detail="Missing or invalid Authorization header")

    token = auth_header[7:].strip()
    if token != AI_API_TOKEN:
        raise HTTPException(status_code=403, detail="Invalid API token")


def language_display(code: str) -> str:
    lang_map = {
        "en": "English",
        "es": "Spanish",
        "fr": "French",
        "de": "German",
        "it": "Italian",
        "ja": "Japanese",
        "hi": "Hindi",
        "gu": "Gujarati",
    }
    if not code:
        return "English"
    return lang_map.get(code, code.title())


def has_speech(audio_bytes: bytes, rms_threshold: float = 0.015) -> bool:
    """
    Detect if the audio bytes contain actual speech by analyzing RMS energy.
    Uses PyAV to decode audio frames and calculates RMS.
    """
    try:
        import av
        import numpy as np
        
        container = av.open(io.BytesIO(audio_bytes))
        if not container.streams.audio:
            return len(audio_bytes) > 50000
            
        stream = container.streams.audio[0]
        total_squared = 0.0
        total_samples = 0
        
        for frame in container.decode(stream):
            raw_arr = frame.to_ndarray()
            array = raw_arr.astype(np.float32)
            if np.issubdtype(raw_arr.dtype, np.integer):
                info = np.iinfo(raw_arr.dtype)
                array = array / max(abs(info.min), info.max)
            total_squared += np.sum(array ** 2)
            total_samples += array.size
            
        if total_samples > 0:
            rms = np.sqrt(total_squared / total_samples)
            return rms > rms_threshold
        
        return len(audio_bytes) > 50000
    except Exception:
        return len(audio_bytes) > 50000


_whisper_model = None

def get_whisper_model():
    global _whisper_model
    if _whisper_model is None:
        from faster_whisper import WhisperModel
        logger.info("Initializing Faster Whisper Model (tiny)...")
        _whisper_model = WhisperModel("tiny", device="cpu", compute_type="int8")
    return _whisper_model


def transcribe_audio_whisper(audio_bytes: bytes) -> tuple:
    with tempfile.NamedTemporaryFile(delete=False, suffix=".webm") as temp_file:
        temp_file.write(audio_bytes)
        temp_file_path = temp_file.name
        
    try:
        model = get_whisper_model()
        segments, info = model.transcribe(temp_file_path, beam_size=5)
        text = " ".join([segment.text for segment in segments]).strip()
        language = info.language
        return text, language
    except Exception as e:
        logger.error(f"Faster Whisper transcription failed: {e}")
        return "", "en"
    finally:
        try:
            if os.path.exists(temp_file_path):
                os.remove(temp_file_path)
        except Exception:
            pass


async def call_gemini_api(api_key: str, model: str, prompt: str, audio_bytes: bytes = None, mime_type: str = "audio/webm") -> str:
    url = f"https://generativelanguage.googleapis.com/v1beta/models/{model}:generateContent?key={api_key}"
    
    parts = []
    if audio_bytes:
        audio_b64 = base64.b64encode(audio_bytes).decode("utf-8")
        parts.append({
            "inlineData": {
                "mimeType": mime_type,
                "data": audio_b64
            }
        })
    
    parts.append({"text": prompt})
    
    payload = {
        "contents": [
            {
                "parts": parts
            }
        ]
    }
    
    if "json" in prompt.lower():
        payload["generationConfig"] = {
            "responseMimeType": "application/json"
        }
        
    logger.info(f"Calling Gemini API with model: {model}")
    max_retries = 3
    retry_delay = 1.0
    
    async with httpx.AsyncClient(timeout=60.0) as client:
        for attempt in range(max_retries + 1):
            try:
                response = await client.post(url, json=payload)
                if response.status_code == 200:
                    result = response.json()
                    try:
                        text = result["candidates"][0]["content"]["parts"][0]["text"]
                        return text
                    except (KeyError, IndexError) as e:
                        logger.error(f"Failed to parse content from Gemini response: {result}")
                        raise HTTPException(status_code=500, detail="Invalid response structure from Gemini API")
                
                if response.status_code in (429, 503) and attempt < max_retries:
                    logger.warning(f"Gemini API returned status {response.status_code}. Retrying in {retry_delay}s (Attempt {attempt + 1}/{max_retries})...")
                    await asyncio.sleep(retry_delay)
                    retry_delay *= 2
                    continue
                
                logger.error(f"Gemini API returned error status {response.status_code}: {response.text}")
                raise HTTPException(status_code=500, detail=f"Gemini API Error: {response.text}")
            except httpx.RequestError as exc:
                if attempt < max_retries:
                    logger.warning(f"Gemini API connection error: {exc}. Retrying in {retry_delay}s (Attempt {attempt + 1}/{max_retries})...")
                    await asyncio.sleep(retry_delay)
                    retry_delay *= 2
                    continue
                logger.error(f"Gemini API connection error: {exc}")
                raise HTTPException(status_code=500, detail=f"Gemini API connection error: {exc}")


def clean_word(word: str) -> str:
    return re.sub(r'[^\w\s]', '', word).lower().strip()


def align_words(target_text: str, transcript: str):
    target_words_raw = target_text.split()
    target_words_cleaned = [clean_word(w) for w in target_words_raw]
    target_indices = [i for i, w in enumerate(target_words_cleaned) if w]
    target_words_cleaned = [w for w in target_words_cleaned if w]
    
    if not transcript or "[no speech detected]" in transcript.lower():
        missing_words = [target_words_raw[i].strip(".,!?;:\"()[]{}").lower() for i in target_indices]
        missing_words = [w for w in missing_words if w]
        return 0, missing_words, [], [], 0.0

    transcript_words_raw = transcript.split()
    transcript_words_cleaned = [clean_word(w) for w in transcript_words_raw]
    transcript_indices = [i for i, w in enumerate(transcript_words_cleaned) if w]
    transcript_words_cleaned = [w for w in transcript_words_cleaned if w]
    
    matcher = difflib.SequenceMatcher(None, target_words_cleaned, transcript_words_cleaned)
    
    missing_words = []
    extra_words = []
    correct_words = []
    
    for tag, i1, i2, j1, j2 in matcher.get_opcodes():
        if tag in ('delete', 'replace'):
            for idx in range(i1, i2):
                orig_idx = target_indices[idx]
                word = target_words_raw[orig_idx].strip(".,!?;:\"()[]{}")
                if word:
                    missing_words.append(word.lower())
        if tag in ('insert', 'replace'):
            for idx in range(j1, j2):
                orig_idx = transcript_indices[idx]
                word = transcript_words_raw[orig_idx].strip(".,!?;:\"()[]{}")
                if word:
                    extra_words.append(word.lower())
                    
    # Correct words list is extracted by finding matching blocks
    for match in matcher.get_matching_blocks():
        for idx in range(match.a, match.a + match.size):
            orig_idx = target_indices[idx]
            word = target_words_raw[orig_idx].strip(".,!?;:\"()[]{}")
            if word:
                correct_words.append(word.lower())
                    
    total_target = len(target_words_cleaned)
    if total_target > 0:
        accuracy_score = int(max(0, min(100, (len(correct_words) / total_target) * 100)))
    else:
        accuracy_score = 0
        
    similarity_percentage = round(matcher.ratio() * 100, 2)
    return accuracy_score, missing_words, extra_words, correct_words, similarity_percentage




def load_audio_av(audio_bytes: bytes, target_sr: int = 16000) -> tuple:
    """
    Decodes audio bytes using PyAV, converts to mono, resamples to target_sr,
    and returns a float32 numpy array. Avoids calling external processes or librosa's
    audioread backend which can fail on systems without ffmpeg in PATH.
    """
    import av
    import io
    import numpy as np
    
    container = av.open(io.BytesIO(audio_bytes))
    if not container.streams.audio:
        raise ValueError("No audio stream found in container")
        
    stream = container.streams.audio[0]
    
    resampler = av.AudioResampler(
        format='flt',     # float32
        layout='mono',    # mono
        rate=target_sr
    )
    
    audio_frames = []
    has_frames = False
    for frame in container.decode(stream):
        has_frames = True
        resampled = resampler.resample(frame)
        for rf in resampled:
            arr = rf.to_ndarray()
            if arr.ndim > 1:
                arr = arr[0]
            audio_frames.append(arr)
            
    if has_frames:
        try:
            flushed = resampler.resample(None)
            for rf in flushed:
                arr = rf.to_ndarray()
                if arr.ndim > 1:
                    arr = arr[0]
                audio_frames.append(arr)
        except Exception as e:
            logger.warning(f"Failed to flush resampler: {e}")
            
    if not audio_frames:
        raise ValueError("No audio frames decoded")
        
    y = np.concatenate(audio_frames)
    return y, target_sr


def analyze_audio_speechbrain_librosa(audio_bytes: bytes, target_text: str = None, transcript: str = "") -> dict:
    """
    Perform local audio signal processing using SpeechBrain and Librosa.
    Estimates pronunciation accuracy, fluency quality, pause count/durations,
    speaking rate (WPM), voice confidence, and speaker accent dialect.
    """
    # Write audio bytes to a temp file
    with tempfile.NamedTemporaryFile(delete=False, suffix=".webm") as temp_file:
        temp_file.write(audio_bytes)
        temp_path = temp_file.name
        
    try:
        try:
            y, sr = load_audio_av(audio_bytes, target_sr=16000)
            logger.info("Successfully decoded audio using PyAV helper.")
        except Exception as av_err:
            logger.warning(f"PyAV decoding failed: {av_err}. Falling back to librosa.load...")
            y, sr = librosa.load(temp_path, sr=16000)
            
        duration = len(y) / sr
        
        # --- SpeechBrain Feature Extraction ---
        wav = torch.tensor(y).unsqueeze(0)
        compute_stft = STFT(sample_rate=sr)
        stft = compute_stft(wav)
        mag = spectral_magnitude(stft).squeeze(0) # Shape: (T_frames, F_bins)
        
        # Energy and stability from SpeechBrain magnitude
        sb_energy = torch.mean(mag, dim=-1) # Shape: (T_frames,)
        mean_energy = float(torch.mean(sb_energy).item())
        var_energy = float(torch.var(sb_energy).item())
        
        # --- Librosa Pause & WPM Extraction ---
        rms = librosa.feature.rms(y=y)[0]
        hop_length = 512
        frame_duration = hop_length / sr
        is_silent = rms < 0.005
        
        pauses = []
        current_pause_len = 0
        for silent in is_silent:
            if silent:
                current_pause_len += 1
            else:
                if current_pause_len * frame_duration >= 0.5:
                    pauses.append(current_pause_len * frame_duration)
                current_pause_len = 0
        if current_pause_len * frame_duration >= 0.5:
            pauses.append(current_pause_len * frame_duration)
            
        pause_count = len(pauses)
        pause_duration = float(sum(pauses))
        long_pauses = sum(1 for p in pauses if p >= 1.5)
        
        # --- Words count / WPM ---
        words_count = len(transcript.split()) if transcript else 0
        if duration > 0:
            wpm = int((words_count / duration) * 60)
            speech_rate = round(words_count / duration, 2)
        else:
            wpm = 0
            speech_rate = 0.0
        wpm = max(0, min(300, wpm))
        
        # --- Pitch analysis (F0) ---
        f0, voiced_flag, voiced_probs = librosa.pyin(
            y, fmin=librosa.note_to_hz('C2'), fmax=librosa.note_to_hz('C7'), sr=sr,
            hop_length=2048
        )

        voiced_f0 = f0[voiced_flag]
        if len(voiced_f0) > 0:
            mean_f0 = float(np.mean(voiced_f0))
            std_f0 = float(np.std(voiced_f0))
        else:
            mean_f0 = 120.0
            std_f0 = 0.0
            
        # --- Spectral features for pronunciation ---
        flatness = librosa.feature.spectral_flatness(y=y)[0]
        mean_flatness = float(np.mean(flatness))
        
        # --- Accent (from MFCCs) ---
        mfccs = librosa.feature.mfcc(y=y, sr=sr, n_mfcc=13)
        mean_mfccs = np.mean(mfccs, axis=1)
        val = mean_mfccs[1] + mean_mfccs[2] if len(mean_mfccs) > 2 else 0.0
        if val < -10:
            accent = "UK Accent"
        elif val < 10:
            accent = "Indian Accent"
        elif val < 30:
            accent = "US Accent"
        else:
            accent = "Australian Accent"
            
        # --- Score calculation based on features ---
        if target_text:
            accuracy_score, missing_words, extra_words, correct_words, similarity_percentage = align_words(target_text, transcript)
            audio_clarity = min(100, max(40, int(100 - mean_flatness * 1000)))
            pronunciation_score = int(accuracy_score * 0.70 + audio_clarity * 0.30)
        else:
            accuracy_score = 0
            similarity_percentage = 0.0
            audio_clarity = min(100, max(30, int(100 - mean_flatness * 1000)))
            pronunciation_score = audio_clarity
        
        # 2. Fluency / Speaking Quality
        if wpm >= 120 and wpm <= 170:
            pacing_score = 100
        else:
            pacing_score = max(40, 100 - abs(wpm - 145) * 0.8)
            
        pause_penalty = min(50, pause_duration * 8)
        fluency_score = int(pacing_score * 0.70 + (100 - pause_penalty) * 0.30)
        
        # 3. Confidence Estimate
        energy_stability = max(40, min(100, int(100 - (var_energy / (mean_energy ** 2 + 1e-6)) * 40)))
        pitch_stability = max(40, min(100, int(100 - abs(std_f0 - 20.0) * 1.5)))
        confidence_score = int(energy_stability * 0.50 + pitch_stability * 0.50)
        
    except Exception as e:
        logger.error(f"DSP Audio analysis failed: {e}", exc_info=True)
        accent = "Standard Accent"
        pronunciation_score = 0
        fluency_score = 0
        confidence_score = 0
        wpm = 0
        pause_count = 0
        pause_duration = 0.0
        accuracy_score = 0
        similarity_percentage = 0.0
        speech_rate = 0.0
        long_pauses = 0
        
    finally:
        try:
            if os.path.exists(temp_path):
                os.remove(temp_path)
        except Exception:
            pass
            
    return {
        "pronunciation_score": pronunciation_score,
        "fluency_score": fluency_score,
        "confidence_score": confidence_score,
        "wpm": wpm,
        "pause_count": pause_count,
        "pause_duration": round(pause_duration, 2),
        "accent": accent,
        "speech_rate": speech_rate,
        "long_pauses": long_pauses
    }




@app.post("/health-check")
def health_check(request: Request):
    """
    Verify AI Engine is up and running.
    """
    verify_api_token(request)
    return {
        "status": "healthy",
        "service": "speechiq-ai-engine",
        "features": {
            "stt": True,
            "tts": True,
            "analysis": True
        }
    }

@app.post("/read-aloud-analyze")
async def read_aloud_analyze(
    request: Request,
    audio_file: UploadFile = File(...),
    expected_text: str = Form(...),
    duration: float = Form(0.0)
):
    """
    Audio analysis endpoint for Read Aloud tests.
    Calculates accuracy, pronunciation, fluency, overall scores,
    and identifies missing or extra words and speech speed details.
    """
    verify_api_token(request)
    gemini_key = request.headers.get("X-Gemini-Key", "")
    gemini_model = request.headers.get("X-Gemini-Model", "gemini-2.5-flash")

    contents = await audio_file.read()
    await audio_file.seek(0)

    clean_target = expected_text.strip()
    words = [w.strip(".,!?;:\"()[]{}") for w in clean_target.split() if w.strip(".,!?;:\"()[]{}")]
    if not words:
        words = ["hello", "world"]

    # Detect silence using RMS energy analysis
    speech_detected = has_speech(contents)

    # Also reject very short recordings (under 2 seconds)
    if not speech_detected or (duration > 0.0 and duration < 2.0):
        return {
            "transcript": "[No speech detected]",
            "language": "English",
            "pronunciation_score": 0,
            "fluency_score": 0,
            "accuracy_score": 0,
            "overall_score": 0,
            "wpm": 0,
            "pause_count": 0,
            "pause_duration": 0.0,
            "missing_words": [w.lower() for w in words],
            "extra_words": [],
            "accent": "None",
            "correct_words": [],
            "similarity_percentage": 0.0,
            "confidence_score": 0,
            "speech_rate": 0.0,
            "long_pauses": 0,
            "feedback": "No speech detected. Please record a clear reading of the paragraph and try again.",
            "improvement_suggestions": [
                "Ensure your microphone is enabled and not muted.",
                "Read the full paragraph aloud at a steady pace.",
                "Record for at least 2 seconds with audible speech.",
            ],
        }

    # STEP 5: Transcribe using Faster Whisper
    transcript, language = transcribe_audio_whisper(contents)
    logger.info(f"Faster Whisper transcript: '{transcript}' (Language: '{language}')")

    # Run deterministic alignment
    accuracy_score, missing_words, extra_words, correct_words, similarity_percentage = align_words(clean_target, transcript)

    # Run local audio analysis using SpeechBrain and Librosa
    audio_analysis = analyze_audio_speechbrain_librosa(contents, clean_target, transcript)
    
    pronunciation_score = audio_analysis["pronunciation_score"]
    fluency_score = audio_analysis["fluency_score"]
    confidence_score = audio_analysis["confidence_score"]
    wpm = audio_analysis["wpm"]
    pause_count = audio_analysis["pause_count"]
    pause_duration = audio_analysis["pause_duration"]
    accent = audio_analysis["accent"]
    speech_rate = audio_analysis["speech_rate"]
    long_pauses = audio_analysis["long_pauses"]

    # Cap scores if alignment shows little to no matching words
    if similarity_percentage < 20:
        pronunciation_score = max(5, min(15, int(pronunciation_score * (similarity_percentage / 100.0))))
        fluency_score = max(10, min(20, int(fluency_score * (similarity_percentage / 100.0))))
        confidence_score = max(0, min(20, int(confidence_score * (similarity_percentage / 100.0))))
        
    overall_score = int(
        accuracy_score * 0.40 +
        pronunciation_score * 0.30 +
        fluency_score * 0.20 +
        confidence_score * 0.10
    )

    if similarity_percentage < 20:
        overall_score = min(20, overall_score)
        if similarity_percentage < 10:
            overall_score = min(9, overall_score)

    feedback = ""
    improvement_suggestions = []
    full_language = language_display(language)

    if gemini_key and transcript and transcript != "[No speech detected]":
        try:
            prompt = f"""
You are an expert IELTS/PTE speech coach reviewing a Read Aloud performance.

Expected paragraph:
"{clean_target}"

Candidate transcript:
"{transcript}"

Analysis scores:
- Accuracy: {accuracy_score}/100
- Pronunciation: {pronunciation_score}/100
- Fluency: {fluency_score}/100
- Confidence: {confidence_score}/100
- WPM: {wpm}
- Missing words: {missing_words}
- Extra words: {extra_words}

Provide a realistic speaking review and actionable improvement suggestions based ONLY on the actual transcript comparison and scores above. Do not inflate praise if accuracy is low.

Respond ONLY with JSON containing:
- "feedback": string (speaking review under 120 words)
- "improvement_suggestions": array of 3-5 specific suggestion strings
"""
            response_text = await call_gemini_api(
                api_key=gemini_key,
                model=gemini_model,
                prompt=prompt,
            )

            cleaned_json = response_text.strip()
            if cleaned_json.startswith("```"):
                cleaned_json = cleaned_json.split("\n", 1)[1] if "\n" in cleaned_json else cleaned_json
                if cleaned_json.endswith("```"):
                    cleaned_json = cleaned_json[:-3].strip()

            data = json.loads(cleaned_json)
            feedback = data.get("feedback", "")
            improvement_suggestions = data.get("improvement_suggestions", [])
            if not isinstance(improvement_suggestions, list):
                improvement_suggestions = [str(improvement_suggestions)]
        except Exception as e:
            logger.error(f"Read Aloud Gemini feedback failed: {e}")
            feedback = "Automated feedback is temporarily unavailable. Review the word highlights and scores above to identify areas for improvement."
            improvement_suggestions = [
                "Practice reading the missed words slowly and clearly.",
                "Maintain a steady pace between 120-170 WPM.",
                "Reduce long pauses by rehearsing the paragraph before recording.",
            ]
    elif similarity_percentage < 20:
        feedback = "Your recording did not closely match the expected paragraph. Focus on reading the exact text clearly."
        improvement_suggestions = [
            "Read the paragraph word by word without skipping sections.",
            "Avoid unrelated speech or filler words.",
            "Re-record after practicing the full passage aloud.",
        ]
    
    return {
        "transcript": transcript,
        "language": full_language,
        "pronunciation_score": pronunciation_score,
        "fluency_score": fluency_score,
        "accuracy_score": accuracy_score,
        "overall_score": overall_score,
        "wpm": wpm,
        "pause_count": pause_count,
        "pause_duration": pause_duration,
        "missing_words": missing_words,
        "extra_words": extra_words,
        "accent": accent,
        "correct_words": correct_words,
        "similarity_percentage": similarity_percentage,
        "confidence_score": confidence_score,
        "speech_rate": speech_rate,
        "long_pauses": long_pauses,
        "feedback": feedback,
        "improvement_suggestions": improvement_suggestions,
    }


from pydantic import BaseModel
from typing import List, Optional

class QuestionRequest(BaseModel):
    context: str
    history: Optional[List[dict]] = []

class FeedbackRequest(BaseModel):
    question: str
    transcript: str

class TtsRequest(BaseModel):
    text: str

@app.post("/speech-to-text")
async def speech_to_text(
    request: Request,
    audio_file: UploadFile = File(...),
):
    """
    Endpoint for speech-to-text transcription using Faster Whisper.
    """
    verify_api_token(request)

    contents = await audio_file.read()
    await audio_file.seek(0)
    speech_detected = has_speech(contents)

    if not speech_detected:
        return {"text": "", "language": "English"}

    text, language = transcribe_audio_whisper(contents)
    return {"text": text, "language": language_display(language)}

@app.post("/interview-analyze")
async def interview_analyze(
    request: Request,
    audio_file: UploadFile = File(...),
    question: str = Form(...),
    duration: float = Form(0.0)
):
    """
    Audio analysis endpoint for AI Interviews.
    Calculates grammar, vocabulary, content relevancy, confidence, pronunciation, and fluency scores.
    Uses real audio energy detection to reject silent/blank recordings.
    """
    verify_api_token(request)
    gemini_key = request.headers.get("X-Gemini-Key", "")
    gemini_model = request.headers.get("X-Gemini-Model", "gemini-2.5-flash")

    contents = await audio_file.read()
    await audio_file.seek(0)

    # === SILENCE DETECTION ===
    speech_detected = has_speech(contents)

    # Also reject very short recordings (under 2 seconds)
    if not speech_detected or (duration > 0.0 and duration < 2.0):
        return {
            "question": question,
            "transcript": "[No speech detected]",
            "language": "English",
            "grammar_score": 0,
            "vocabulary_score": 0,
            "content_score": 0,
            "communication_score": 0,
            "confidence_score": 0,
            "pronunciation_score": 0,
            "fluency_score": 0,
            "accent": "None",
            "overall_score": 0,
            "feedback": "No speech detected. Please record a clear spoken response and try again. Make sure your microphone is working and you are speaking audibly.",
            "improvement_suggestions": [
                "Check that your microphone is enabled.",
                "Speak clearly and answer the question directly.",
                "Record for at least 2 seconds.",
            ],
            "tone": "None",
            "wpm": 0,
            "pause_count": 0,
            "pause_duration": 0.0,
        }
    # === END SILENCE DETECTION ===

    # STEP 5: Transcribe using Faster Whisper
    transcript, language = transcribe_audio_whisper(contents)
    logger.info(f"Faster Whisper transcript: '{transcript}' (Language: '{language}')")

    # Run local audio analysis using SpeechBrain and Librosa
    audio_analysis = analyze_audio_speechbrain_librosa(contents, transcript=transcript)
    
    pronunciation_score = audio_analysis["pronunciation_score"]
    fluency_score = audio_analysis["fluency_score"]
    confidence_score = audio_analysis["confidence_score"]
    accent = audio_analysis["accent"]
    wpm = audio_analysis["wpm"]
    pause_count = audio_analysis["pause_count"]
    pause_duration = audio_analysis["pause_duration"]

    # Check if transcript consists of unrelated/gibberish words like "abcd", "xyz", "1234"
    cleaned_trans = re.sub(r'[^a-zA-Z0-9\s]', '', transcript).lower().strip()
    words = cleaned_trans.split()
    gibberish_words = {"abcd", "xyz", "1234", "abc", "qwer", "asdf"}
    
    is_gibberish = all(
        w in gibberish_words or len(w) == 1 or w.isdigit()
        for w in words
    ) if words else True

    grammar_score = 0
    vocabulary_score = 0
    content_score = 0
    communication_score = 0
    tone = "None"
    feedback = ""
    improvement_suggestions = []

    if is_gibberish:
        grammar_score = 0
        vocabulary_score = 0
        content_score = 0
        communication_score = 0
        pronunciation_score = min(5, pronunciation_score)
        fluency_score = min(5, fluency_score)
        confidence_score = min(5, confidence_score)
        tone = "None"
        feedback = "Unrelated words or gibberish detected. Please record a clear spoken response to the question asked."
        improvement_suggestions = [
            "Answer the interview question directly with complete sentences.",
            "Avoid random letters, numbers, or filler sounds.",
            "Practice structuring your response before recording.",
        ]
    elif gemini_key:
        try:
            prompt = f"""
You are an expert AI Interviewer and Speech Coach.
Evaluate the candidate's transcribed response to the interview question:
Question: "{question}"
Candidate Response: "{transcript}"

Perform the following:
1. Evaluate grammar quality (0-100).
2. Evaluate vocabulary richness (0-100).
3. Evaluate how well the answer addresses the question content (0-100) as "content_score".
4. Evaluate communication skills, clarity, and logic (0-100) as "communication_score".
5. Identify the tone (e.g., "Professional", "Informal", "Academic", "Confident", "Hesitant").
6. Provide constructive feedback under 100 words.
7. Provide 3-5 specific improvement suggestions as an array.

Respond ONLY with JSON containing:
- "grammar_score": int 0-100
- "vocabulary_score": int 0-100
- "content_score": int 0-100
- "communication_score": int 0-100
- "tone": string
- "feedback": string
- "improvement_suggestions": array of strings
"""
            response_text = await call_gemini_api(
                api_key=gemini_key,
                model=gemini_model,
                prompt=prompt
            )
            
            cleaned_json = response_text.strip()
            if cleaned_json.startswith("```"):
                cleaned_json = cleaned_json.split("\n", 1)[1] if "\n" in cleaned_json else cleaned_json
                if cleaned_json.endswith("```"):
                    cleaned_json = cleaned_json[:-3].strip()
                    
            data = json.loads(cleaned_json)
            
            grammar_score = int(data.get("grammar_score", 0))
            vocabulary_score = int(data.get("vocabulary_score", 0))
            content_score = int(data.get("content_score", 0))
            communication_score = int(data.get("communication_score", 0))
            tone = data.get("tone", "Professional")
            feedback = data.get("feedback", "")
            improvement_suggestions = data.get("improvement_suggestions", [])
            if not isinstance(improvement_suggestions, list):
                improvement_suggestions = [str(improvement_suggestions)]
            
        except Exception as e:
            logger.error(f"Error analyzing interview with Gemini API: {e}")
            feedback = "AI analysis is temporarily unavailable. Scores are based on audio metrics only."
            improvement_suggestions = [
                "Retry the interview when the AI service is available.",
                "Focus on clear pronunciation and steady pacing.",
            ]
    else:
        feedback = "Gemini API key is not configured. Configure it in Admin Settings for full interview analysis."
        improvement_suggestions = [
            "Ask your administrator to add a valid Gemini API key.",
        ]

    full_language = language_display(language)

    overall_score = int(
        pronunciation_score * 0.20 +
        fluency_score * 0.15 +
        grammar_score * 0.20 +
        vocabulary_score * 0.15 +
        content_score * 0.20 +
        confidence_score * 0.10
    )
    if is_gibberish:
        overall_score = min(9, overall_score)
    
    return {
        "question": question,
        "transcript": transcript,
        "language": full_language,
        "grammar_score": grammar_score,
        "vocabulary_score": vocabulary_score,
        "content_score": content_score,
        "communication_score": communication_score,
        "confidence_score": confidence_score,
        "pronunciation_score": pronunciation_score,
        "fluency_score": fluency_score,
        "accent": accent,
        "overall_score": overall_score,
        "feedback": feedback,
        "improvement_suggestions": improvement_suggestions,
        "tone": tone,
        "wpm": wpm,
        "pause_count": pause_count,
        "pause_duration": pause_duration,
    }

@app.post("/generate-question")
async def generate_question(req: QuestionRequest, request: Request):
    """
    Follow-up question generation based on prompt context and conversation history.
    """
    verify_api_token(request)
    gemini_key = request.headers.get("X-Gemini-Key", "")
    gemini_model = request.headers.get("X-Gemini-Model", "gemini-2.5-flash")

    if not gemini_key:
        raise HTTPException(status_code=400, detail="Gemini API key is required to generate interview questions.")

    try:
        prompt = f"""
You are an interviewer conducting a technical assessment.
Context of the interview:
{req.context}

Conversation History (JSON format containing previous questions and answers):
{json.dumps(req.history)}

Based on the context and history, generate a natural, professional follow-up interview question to ask the candidate.
Return ONLY the question text. Do not wrap it in quotes or add extra headers/commentary.
"""
        question = await call_gemini_api(
            api_key=gemini_key,
            model=gemini_model,
            prompt=prompt
        )
        return {"question": question.strip()}
    except HTTPException:
        raise
    except Exception as e:
        logger.error(f"Error generating question with Gemini API: {e}")
        raise HTTPException(status_code=500, detail="Failed to generate interview question.")

@app.post("/generate-feedback")
async def generate_feedback(req: FeedbackRequest, request: Request):
    """
    Overall summary feedback endpoint.
    """
    verify_api_token(request)
    gemini_key = request.headers.get("X-Gemini-Key", "")
    gemini_model = request.headers.get("X-Gemini-Model", "gemini-2.5-flash")

    if not gemini_key:
        raise HTTPException(status_code=400, detail="Gemini API key is required to generate feedback.")

    try:
        prompt = f"""
You are an expert technical interviewer and communication coach.
Review the question asked and the candidate's transcribed response:
Question: {req.question}
Response Transcript: {req.transcript}

Provide a summary feedback paragraph assessing their performance, clarity, and overall expertise shown. Keep the feedback under 100 words.
"""
        feedback = await call_gemini_api(
            api_key=gemini_key,
            model=gemini_model,
            prompt=prompt
        )
        return {"feedback": feedback.strip()}
    except HTTPException:
        raise
    except Exception as e:
        logger.error(f"Error generating feedback with Gemini API: {e}")
        raise HTTPException(status_code=500, detail="Failed to generate feedback.")

@app.post("/text-to-speech")
async def text_to_speech(req: TtsRequest, request: Request):
    """
    Real Text-to-Speech endpoint using gTTS (Google TTS).
    Returns base64-encoded MP3 audio data directly in the response.
    Falls back to a descriptive error message if TTS is unavailable.
    """
    verify_api_token(request)
    text = req.text.strip()
    if not text:
        return {"audio_base64": "", "error": "No text provided."}

    try:
        from gtts import gTTS
        import io

        tts = gTTS(text=text, lang='en', slow=False)
        audio_buffer = io.BytesIO()
        tts.write_to_fp(audio_buffer)
        audio_buffer.seek(0)

        audio_bytes = audio_buffer.read()
        audio_base64 = base64.b64encode(audio_bytes).decode("utf-8")

        logger.info(f"TTS generated successfully for text: '{text[:60]}...' ({len(audio_bytes)} bytes)")

        return {
            "audio_base64": audio_base64,
            "mime_type": "audio/mpeg",
            "size_bytes": len(audio_bytes)
        }

    except Exception as e:
        logger.error(f"TTS generation failed: {e}")
        return {
            "audio_base64": "",
            "error": f"TTS generation failed: {str(e)}"
        }

# Pre-warm / pre-load Faster Whisper Model at module load time
try:
    logger.info("Pre-warming/Pre-loading Faster Whisper Model...")
    get_whisper_model()
    logger.info("Faster Whisper Model pre-warmed successfully.")
except Exception as e:
    logger.error(f"Failed to pre-warm Faster Whisper Model: {e}")

if __name__ == "__main__":
    import uvicorn
    uvicorn.run("main:app", host="127.0.0.1", port=8001, reload=True)

