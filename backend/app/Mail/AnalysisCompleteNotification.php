<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AnalysisCompleteNotification extends Mailable
{
    use Queueable, SerializesModels;

    public string $userName;
    public string $moduleType;   // 'Read Aloud' or 'AI Interview'
    public int    $overallScore;
    public string $resultsUrl;

    /**
     * Create a new message instance.
     */
    public function __construct(
        string $userName,
        string $moduleType,
        int    $overallScore,
        string $resultsUrl
    ) {
        $this->userName     = $userName;
        $this->moduleType   = $moduleType;
        $this->overallScore = $overallScore;
        $this->resultsUrl   = $resultsUrl;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "✅ Your SpeechIQ {$this->moduleType} Analysis is Ready!",
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.analysis_complete',
        );
    }

    /**
     * Get the attachments for the message.
     */
    public function attachments(): array
    {
        return [];
    }
}
