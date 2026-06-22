SPEECHIQ FINAL AI ARCHITECTURE

IMPORTANT

Gemini API is the primary AI service.

Gemini API Key will be configured from Admin Settings Module.

Do not hardcode Gemini API Key.

Store in database settings table.

====================================================

AI RESPONSIBILITY MATRIX

====================================================

GEMINI API

Purpose:

* AI Interview Questions
* Grammar Analysis
* Vocabulary Analysis
* Tone Analysis
* Communication Skills Analysis
* Feedback Generation
* Improvement Suggestions
* Next Question Generation
* Report Summary

====================================================

FASTER WHISPER

Purpose:

* Speech To Text
* Language Detection
* Transcript Generation

Output:

Transcript

Language

====================================================

SPEECHBRAIN

Purpose:

* Pronunciation Analysis
* Accent Detection
* Confidence Estimation
* Speaking Quality Analysis

Output:

Pronunciation Score

Accent

Confidence Score

====================================================

LIBROSA

Purpose:

* Speech Rate
* Pause Detection
* Long Pause Detection
* Silence Detection
* Fluency Analysis

Output:

WPM

Pause Count

Pause Duration

Fluency Score

====================================================

READ ALOUD COMPLETE FLOW

1.

Admin creates paragraph.

2.

User reads paragraph.

3.

Audio recorded.

4.

Laravel stores audio.

5.

Queue job dispatched.

6.

Audio sent to FastAPI.

====================================================

WHISPER

Generate:

Transcript

Language

====================================================

TEXT COMPARISON ENGINE

Compare:

Expected Text

vs

Transcript

Generate:

Correct Words

Incorrect Words

Missing Words

Extra Words

Similarity %

Accuracy %

====================================================

SPEECHBRAIN

Generate:

Pronunciation Score

Accent

Confidence Score

====================================================

LIBROSA

Generate:

Speech Rate

WPM

Pause Count

Pause Duration

Fluency Score

====================================================

GEMINI

Generate:

Feedback

Improvement Suggestions

Speaking Review

====================================================

READ ALOUD SCORE FORMULA

Accuracy = 40%

Pronunciation = 30%

Fluency = 20%

Confidence = 10%

====================================================

AI INTERVIEW FLOW

1.

Gemini generates question.

2.

User answers.

3.

Audio uploaded.

4.

Whisper generates transcript.

5.

SpeechBrain generates:

Pronunciation

Confidence

Accent

6.

Librosa generates:

WPM

Pause Analysis

Fluency

7.

Gemini analyzes transcript.

Generate:

Grammar Score

Vocabulary Score

Content Score

Tone

Communication Score

Feedback

Next Question

====================================================

INTERVIEW SCORE FORMULA

Pronunciation = 20%

Fluency = 15%

Grammar = 20%

Vocabulary = 15%

Content = 20%

Confidence = 10%

====================================================

FINAL REPORT FORMAT

Language

Accent

Tone

Confidence Score

Pronunciation Score

Fluency Score

Accuracy Score

Grammar Score

Vocabulary Score

Content Score

Communication Score

WPM

Pause Count

Pause Duration

Overall Score

Feedback

Improvement Suggestions

====================================================

STRICT VALIDATION RULES

If transcript similarity is below 20%

Overall score must remain below 20.

If user says:

abcd

xyz

1234

Then:

Accuracy = 0

Overall Score < 10

Never generate random scores.

Never generate placeholder scores.

Never assign high scores to unrelated speech.

All scores must be based on actual transcript comparison and audio analysis.

====================================================

ADMIN SETTINGS MODULE

Store:

GEMINI_API_KEY

GEMINI_MODEL

AI_ENGINE_URL

AI_ENGINE_TOKEN

ENABLE_AI_INTERVIEW

ENABLE_READ_ALOUD

ENABLE_REPORTS

ENABLE_TTS

Default Model:

gemini-2.5-flash

====================================================

LARAVEL RESPONSIBILITY

Authentication

Subscription

Admin Panel

Settings

Audio Upload

Queue Jobs

Reports

Analytics

Dashboard

====================================================

FASTAPI RESPONSIBILITY

Whisper

SpeechBrain

Librosa

Gemini Integration

Score Calculation

Audio Analysis

Report JSON Generation

====================================================

FINAL GOAL

User should receive IELTS/PTE-style report with realistic scoring and detailed improvement suggestions.
