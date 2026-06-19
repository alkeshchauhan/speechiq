SPEECHIQ AI ANALYSIS FLOW

IMPORTANT RULE

Never calculate pronunciation, fluency, confidence, accent, grammar, vocabulary, or overall score directly from Gemini/OpenAI response.

Gemini/OpenAI must only be used for:

* Question Generation
* Feedback Generation
* Grammar Analysis
* Vocabulary Analysis
* Tone Analysis
* Communication Analysis

Actual speech scoring must come from audio analysis.

====================================================

READ ALOUD FLOW

STEP 1

Admin creates paragraph.

Example:

"Modern technology has completely revolutionized the way human beings connect with each other."

====================================================

STEP 2

User reads paragraph aloud.

Browser records audio.

Audio uploaded to Laravel.

Laravel stores audio.

====================================================

STEP 3

Laravel dispatches queue job.

AnalyzeReadAloudJob

====================================================

STEP 4

Laravel sends audio to FastAPI.

POST /read-aloud-analyze

Input:

audio_file

expected_text

====================================================

STEP 5

FastAPI sends audio to Faster Whisper.

Purpose:

Speech To Text

Language Detection

Output:

{
"transcript": "Modern technology has completely revolutionized..."
}

====================================================

STEP 6

Expected text and transcript must be compared.

Calculate:

* Missing Words
* Extra Words
* Correct Words
* Similarity Percentage

Formula:

Accuracy Score =
(Correct Words / Expected Words) × 100

Example:

Expected:
35 words

Transcript:
32 correct words

Accuracy:
91.4%

====================================================

STEP 7

Audio must be analyzed using SpeechBrain.

Calculate:

* Pronunciation Score
* Accent Detection
* Speaking Quality
* Confidence Estimate

Output:

{
"pronunciation": 82,
"accent": "Indian English",
"confidence": 78
}

====================================================

STEP 8

Audio must be analyzed using Librosa.

Calculate:

* Speech Rate
* Words Per Minute
* Long Pauses
* Pause Count
* Pause Duration

Output:

{
"wpm": 145,
"pause_count": 2,
"pause_duration": 1.8
}

====================================================

STEP 9

Overall score calculation.

Formula:

Overall Score =
(
Accuracy × 40%
+
Pronunciation × 30%
+
Fluency × 20%
+
Confidence × 10%
)

Never generate random scores.

Never return default values.

====================================================

VALIDATION RULE

If transcript similarity < 20%

Then:

Overall Score Maximum = 20

Example:

Expected:
Modern technology...

User says:
abcd

Transcript:
abcd

Result:

Accuracy = 0

Pronunciation = 5

Fluency = 10

Overall = 3

Never return 80+ scores.

====================================================

AI INTERVIEW FLOW

STEP 1

Gemini generates question.

Example:

Tell me about yourself.

====================================================

STEP 2

User records answer.

Audio uploaded.

====================================================

STEP 3

Faster Whisper generates transcript.

====================================================

STEP 4

SpeechBrain analyzes:

* Pronunciation
* Fluency
* Confidence
* Accent

====================================================

STEP 5

Gemini receives transcript.

Prompt:

Analyze transcript.

Return:

* Grammar Score
* Vocabulary Score
* Tone
* Communication Skills
* Detailed Feedback

Return JSON only.

====================================================

STEP 6

Generate final report.

Example:

{
"language":"English",
"accent":"Indian English",
"tone":"Professional",
"confidence":82,
"pronunciation":78,
"fluency":85,
"grammar":90,
"vocabulary":84,
"wpm":148,
"overall":81
}

====================================================

STRICT RULES

Do not generate fake scores.

Do not generate placeholder scores.

Do not assign scores based only on transcript length.

Do not assign scores using random values.

If transcript does not match expected text, accuracy score must decrease significantly.

If user speaks unrelated words such as:

abcd
xyz
1234

overall score must be below 10.

All scoring must be derived from actual audio analysis and transcript comparison.
