PROJECT NAME: SpeechIQ

TECH STACK

* Laravel 12
* PHP 8.3+
* MySQL 8
* Redis Queue
* Python FastAPI
* OpenAI API
* Faster Whisper
* SpeechBrain
* Librosa
* FFmpeg
* Tailwind CSS
* Blade Templates

IMPORTANT DEVELOPMENT RULES

* Generate production-ready code only.
* Follow Laravel 12 standards.
* Use Service Pattern.
* Use Repository Pattern.
* Use Form Request Validation.
* Use Queue Jobs for all audio processing.
* Use API-first architecture between Laravel and Python.
* Do not hardcode API keys.
* All AI settings must come from database settings module.

====================================================

MODULE 1 - PROJECT SETUP

Generate:

* Laravel 12 project structure
* Authentication
* Roles & Permissions
* Admin Layout
* User Layout
* Global Helper Functions
* Settings Module
* Repository Pattern Base Classes
* Service Pattern Base Classes

Tables:

users
roles
permissions
user_roles

====================================================

MODULE 2 - SETTINGS MODULE

Purpose:

Store all API keys and application settings from Admin Panel.

Table:

settings

Columns:

id
setting_key
setting_value
setting_type
is_encrypted
created_at
updated_at

Required Settings:

OPENAI_API_KEY

OPENAI_MODEL

OPENAI_TTS_MODEL

OPENAI_TRANSCRIBE_MODEL

AI_API_URL

AI_API_TOKEN

ENABLE_AI_INTERVIEW

ENABLE_READ_ALOUD

ENABLE_TTS

ENABLE_STT

GEMINI_API_KEY

GEMINI_MODEL

USE_GEMINI

Admin Features:

* Settings Listing
* Settings Edit
* API Key Masking
* Encryption Support
* Test API Connection Button

Create:

Setting Model

Setting Repository

Setting Service

Setting Controller

Setting Seeder

Admin CRUD

====================================================

MODULE 3 - SUBSCRIPTION SYSTEM (SKIPPED / REMOVED)

====================================================

MODULE 4 - TEST MANAGEMENT

Tables:

tests

test_sections

questions

Features:

Admin can create:

* Read Aloud Test
* AI Interview Test

Question Types:

READ_ALOUD

AI_INTERVIEW

====================================================

MODULE 5 - AUDIO RECORDING MODULE

Features:

* Browser Audio Recording
* Audio Upload
* Waveform
* Playback
* Timer

Tables:

audio_recordings

Columns:

id

user_id

question_id

audio_path

duration

status

created_at

====================================================

MODULE 6 - READ ALOUD MODULE

Flow:

Admin creates paragraph.

User sees paragraph.

User records voice.

Audio uploaded.

Laravel dispatches queue job.

Python API analyzes audio.

Store result.

Tables:

read_aloud_results

Fields:

audio_recording_id

transcript

pronunciation_score

fluency_score

accuracy_score

wpm

pause_count

pause_duration

missing_words

extra_words

accent

overall_score

====================================================

MODULE 7 - AI INTERVIEW MODULE

Flow:

OpenAI generates question.

Question shown.

User records answer.

Audio uploaded.

Speech To Text.

Transcript generated.

Analysis performed.

Feedback generated.

Next question generated.

Tables:

interview_results

Fields:

audio_recording_id

question

transcript

grammar_score

vocabulary_score

content_score

confidence_score

pronunciation_score

fluency_score

accent

overall_score

feedback

====================================================

MODULE 8 - REPORTING MODULE

Tables:

analysis_reports

Features:

* Overall Score
* Progress History
* Improvement Tracking
* Download PDF

====================================================

MODULE 9 - DASHBOARD

Admin Dashboard

* Users
* Tests
* Revenue
* Reports

User Dashboard

* Practice History
* Test History
* Reports
* Subscription Status

====================================================

MODULE 10 - FASTAPI INTEGRATION

Create Service:

AiEngineService

Config values from Settings table.

Never use .env for AI keys.

Always load from database settings.

Functions:

speechToText()

analyzeReadAloud()

analyzeInterview()

generateQuestion()

generateFeedback()

textToSpeech()

====================================================

MODULE 11 - REDIS QUEUE

Create Jobs:

AnalyzeReadAloudJob

AnalyzeInterviewJob

GenerateReportJob

All AI analysis must run in queue.

====================================================

MODULE 12 - API DOCUMENTATION

Generate:

* Request DTOs
* Response DTOs
* Swagger Documentation
* API Examples

====================================================

PYTHON FASTAPI REQUIREMENTS

Create separate project:

speechiq-ai

Folder Structure:

app/

api/

services/

models/

utils/

uploads/

logs/

main.py

requirements.txt

====================================================

FASTAPI ENDPOINTS

POST /speech-to-text

POST /read-aloud-analyze

POST /interview-analyze

POST /generate-question

POST /generate-feedback

POST /text-to-speech

POST /health-check

====================================================

LARAVEL TO PYTHON FLOW

Laravel uploads audio.

Laravel sends request to AI API.

Python returns JSON.

Laravel stores result.

====================================================

API RESPONSE FORMAT

Read Aloud

{
"transcript": "",
"pronunciation_score": 0,
"fluency_score": 0,
"accuracy_score": 0,
"wpm": 0,
"pause_count": 0,
"pause_duration": 0,
"missing_words": [],
"extra_words": [],
"accent": "",
"overall_score": 0
}

Interview

{
"question": "",
"transcript": "",
"grammar_score": 0,
"vocabulary_score": 0,
"content_score": 0,
"confidence_score": 0,
"pronunciation_score": 0,
"fluency_score": 0,
"accent": "",
"overall_score": 0,
"feedback": ""
}
