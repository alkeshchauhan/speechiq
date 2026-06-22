import sys
import os

# Import the function from main
from main import analyze_audio_speechbrain_librosa

audio_path = r"d:\xampp8.2\htdocs\laravel\ai\speechiq\backend\storage\app\public\recordings\user_4_1782116589_6a38f0ed552f5.webm"

with open(audio_path, "rb") as f:
    audio_bytes = f.read()

try:
    res = analyze_audio_speechbrain_librosa(
        audio_bytes, 
        "More than technology has a competitive realism, the way you maintain, connect with each other. It has a place, physical decisions and medical communication. It has also introduced new challenges regarding its solution and screen dependencies.", 
        "More than technology has a competitive realism, the way you maintain, connect with each other. It has a place, physical decisions and medical communication. It has also introduced new challenges regarding its solution and screen dependencies."
    )
    print("SUCCESS:", res)
except Exception as e:
    import traceback
    traceback.print_exc()
