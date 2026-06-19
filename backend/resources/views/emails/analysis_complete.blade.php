<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SpeechIQ — Analysis Ready</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Outfit', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background-color: #0f172a;
            color: #e2e8f0;
            padding: 40px 20px;
        }
        .container {
            max-width: 560px;
            margin: 0 auto;
            background: #1e293b;
            border-radius: 20px;
            overflow: hidden;
            border: 1px solid #334155;
        }
        .header {
            background: linear-gradient(135deg, #4f46e5 0%, #06b6d4 100%);
            padding: 36px 32px;
            text-align: center;
        }
        .header-logo {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 16px;
        }
        .header-logo-dot {
            width: 36px;
            height: 36px;
            background: rgba(255,255,255,0.2);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
        }
        .header-logo span {
            font-size: 22px;
            font-weight: 700;
            color: #fff;
            letter-spacing: -0.5px;
        }
        .header h1 {
            font-size: 26px;
            font-weight: 700;
            color: #fff;
            line-height: 1.3;
        }
        .header p {
            font-size: 14px;
            color: rgba(255,255,255,0.8);
            margin-top: 8px;
        }
        .body {
            padding: 32px;
        }
        .greeting {
            font-size: 16px;
            color: #94a3b8;
            margin-bottom: 24px;
            line-height: 1.6;
        }
        .greeting strong {
            color: #f1f5f9;
        }
        /* Score Card */
        .score-card {
            background: #0f172a;
            border: 1px solid #334155;
            border-radius: 16px;
            padding: 24px;
            text-align: center;
            margin-bottom: 24px;
        }
        .score-badge {
            display: inline-block;
            font-size: 56px;
            font-weight: 800;
            background: linear-gradient(135deg, #6366f1, #06b6d4);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            line-height: 1;
            margin-bottom: 8px;
        }
        .score-label {
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            color: #64748b;
            font-weight: 600;
        }
        .score-module {
            display: inline-block;
            margin-top: 12px;
            padding: 4px 14px;
            border-radius: 999px;
            background: #1e293b;
            border: 1px solid #4f46e5;
            color: #818cf8;
            font-size: 12px;
            font-weight: 600;
        }
        /* Score bar */
        .score-bar-wrap {
            background: #1e293b;
            border-radius: 999px;
            height: 8px;
            margin: 16px 0 8px;
            overflow: hidden;
        }
        .score-bar {
            height: 100%;
            background: linear-gradient(90deg, #6366f1, #06b6d4);
            border-radius: 999px;
            width: {{ $overallScore }}%;
        }
        .score-note {
            font-size: 12px;
            color: #64748b;
        }
        /* CTA */
        .cta-wrap {
            text-align: center;
            margin: 28px 0;
        }
        .cta-btn {
            display: inline-block;
            background: linear-gradient(135deg, #4f46e5, #06b6d4);
            color: #fff !important;
            font-size: 14px;
            font-weight: 700;
            padding: 14px 32px;
            border-radius: 12px;
            text-decoration: none;
            letter-spacing: 0.3px;
        }
        /* Info list */
        .info-list {
            background: #0f172a;
            border: 1px solid #334155;
            border-radius: 12px;
            padding: 16px 20px;
            margin-bottom: 24px;
        }
        .info-list p {
            font-size: 13px;
            color: #94a3b8;
            line-height: 1.8;
        }
        .info-list p + p {
            border-top: 1px solid #1e293b;
            padding-top: 8px;
            margin-top: 8px;
        }
        .info-list strong {
            color: #e2e8f0;
        }
        /* Footer */
        .footer {
            padding: 20px 32px;
            border-top: 1px solid #334155;
            text-align: center;
        }
        .footer p {
            font-size: 12px;
            color: #475569;
            line-height: 1.6;
        }
        .footer a {
            color: #6366f1;
            text-decoration: none;
        }
    </style>
</head>
<body>
    <div class="container">

        <!-- Header -->
        <div class="header">
            <div class="header-logo">
                <div class="header-logo-dot">🎙️</div>
                <span>SpeechIQ</span>
            </div>
            <h1>Your Analysis is Ready!</h1>
            <p>Your {{ $moduleType }} evaluation has been completed.</p>
        </div>

        <!-- Body -->
        <div class="body">
            <p class="greeting">
                Hi <strong>{{ $userName }}</strong>,<br><br>
                Your speech analysis has been processed by our AI engine. Here's a quick look at your performance:
            </p>

            <!-- Score Card -->
            <div class="score-card">
                <div class="score-badge">{{ $overallScore }}</div>
                <div class="score-label">Overall Score / 100</div>
                <div class="score-bar-wrap">
                    <div class="score-bar"></div>
                </div>
                <div class="score-note">
                    @if($overallScore >= 80)
                        🏆 Excellent performance! You're in the top tier.
                    @elseif($overallScore >= 60)
                        👍 Good work! Keep practising to reach the next level.
                    @elseif($overallScore >= 40)
                        📈 You're improving. Focus on pronunciation and fluency.
                    @else
                        💪 Keep practising! Every session helps you improve.
                    @endif
                </div>
                <div class="score-module">{{ $moduleType }}</div>
            </div>

            <!-- Info -->
            <div class="info-list">
                <p><strong>Module:</strong> {{ $moduleType }}</p>
                <p><strong>Status:</strong> ✅ Analysis Complete</p>
                <p><strong>Platform:</strong> SpeechIQ AI Speech Assessment</p>
            </div>

            <!-- CTA Button -->
            <div class="cta-wrap">
                <a href="{{ $resultsUrl }}" class="cta-btn">
                    📊 View Full Analysis Report →
                </a>
            </div>

            <p style="font-size: 13px; color: #64748b; text-align: center; line-height: 1.6;">
                This notification was sent because you completed a speech analysis session on SpeechIQ.<br>
                If this wasn't you, you can safely ignore this email.
            </p>
        </div>

        <!-- Footer -->
        <div class="footer">
            <p>
                &copy; {{ date('Y') }} SpeechIQ AI Speech Assessment Platform<br>
                Powered by Google Gemini · Faster Whisper · SpeechBrain
            </p>
        </div>
    </div>
</body>
</html>
