# SpeechIQ Server Deployment Requirements Checklist

This document details the system, software, hardware, and configuration requirements for deploying the **SpeechIQ** project (consisting of the Laravel 12 PHP Backend and Python AI Engine) to a live production server.

---

## 1. Hardware Recommendations
The Python AI engine runs Deep Learning models (`faster-whisper` and `speechbrain`) for speech-to-text and audio processing. This requires substantial memory and CPU/GPU processing power.

| Component | Minimum Requirement (CPU-only) | Recommended Requirement (GPU-accelerated) |
| :--- | :--- | :--- |
| **CPU** | 4 Cores (Intel/AMD) | 4 to 8 Cores (Intel/AMD) |
| **RAM** | **8 GB RAM** (Absolute minimum) | **16 GB RAM** (For smooth concurrent requests) |
| **GPU** | Not required (Inference will be slower) | **NVIDIA GPU** (e.g., T4, L4, RTX series) with 8GB+ VRAM & CUDA Support |
| **Storage** | **30 GB SSD** free space | **50 GB+ SSD** (For models, audio files, database) |
| **OS** | Ubuntu 22.04 LTS / 24.04 LTS (64-bit) | Ubuntu 22.04 LTS / 24.04 LTS (64-bit) |

---

## 2. System-Level Dependencies
These utilities must be installed directly on the operating system:

- [ ] **FFmpeg**: Required for audio conversion, trimming, and processing.
  - *Ubuntu command:* `sudo apt update && sudo apt install ffmpeg -y`
- [ ] **Supervisor**: To manage background queues for Laravel and keep the Python AI engine process alive.
  - *Ubuntu command:* `sudo apt install supervisor -y`
- [ ] **Git**: For code deployment and version control.
  - *Ubuntu command:* `sudo apt install git -y`

---

## 3. Web Server & Database
- [ ] **Web Server (Nginx or Apache)**: **Nginx** is highly recommended as it makes it easy to set up reverse proxying for the Python AI service.
- [ ] **Database (MySQL)**: **MySQL 8.0+** or **MariaDB 10.3+**.
- [ ] **SSL Certificate**: Let's Encrypt (Certbot) or custom SSL certificate for HTTPS.

---

## 4. PHP Backend Requirements (Laravel 12)
- [ ] **PHP Version**: **PHP 8.2** or **PHP 8.3**
- [ ] **PHP Extensions**:
  - `ctype`
  - `curl`
  - `dom`
  - `fileinfo`
  - `filter`
  - `hash`
  - `mbstring`
  - `openssl`
  - `pcre`
  - `pdo`
  - `pdo_mysql` (or database equivalent)
  - `session`
  - `tokenizer`
  - `xml`
  - `zip`
- [ ] **Composer**: Composer v2.x (PHP Package Manager)

---

## 5. Python AI Engine Requirements
- [ ] **Python Version**: **Python 3.9, 3.10, or 3.11** (Do not use 3.12+ yet to avoid library compatibility issues with PyTorch/faster-whisper).
- [ ] **Python Pip & Venv**: For virtual environment setup.
  - *Ubuntu command:* `sudo apt install python3-pip python3-venv -y`
- [ ] **Python Virtual Environment Dependencies**: (Installed from `requirements.txt`)
  - `fastapi` & `uvicorn` (FastAPI Web Server)
  - `torch` & `torchaudio` (PyTorch for deep learning)
  - `faster-whisper` (Speech-to-text inference engine)
  - `speechbrain` (Audio/Speech feature analysis)
  - `librosa` & `soundfile` (Audio analysis libraries)
  - `gtts` & `python-multipart`

---

## 6. Frontend Build Requirements
If you compile assets directly on the production server:
- [ ] **Node.js**: **v18.x** or **v20.x (LTS)**
- [ ] **NPM**: **v9.x** or **v10.x**
*Alternative:* Compile Vite assets (`npm run build`) locally before deploying and commit/push the `public/build` directory to your repository.

---

## 7. Production Process Setup (Supervisor Configuration)
In production, the following processes need to run continuously in the background:

1. **Laravel Queue Worker**:
   - Command: `php artisan queue:work --tries=3`
2. **Python AI Engine Server**:
   - Command: `venv/bin/python main.py` or running the uvicorn command (e.g. `uvicorn main:app --host 127.0.0.1 --port 8000`).

---

## 8. Network & Port Settings
- **Port 80 / 443**: Expose for external HTTP/HTTPS traffic (served by Nginx/Apache).
- **Port 8000** (or similar internal port): Used by the Python FastAPI server. It is recommended to keep this port blocked from the outside and reverse proxy requests from Laravel backend or Nginx internally.
