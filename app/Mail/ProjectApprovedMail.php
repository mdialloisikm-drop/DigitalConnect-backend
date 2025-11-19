<?php

namespace App\Mail;

use App\Models\Client;
use App\Models\Project;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ProjectApprovedMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;
    public $project;
    public $client;

    /**
     * Create a new message instance.
     */
    public function __construct(Project $project, Client $client)
    {
        $this->project = $project;
        $this->client = $client;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Votre projet a été approuvé - ' . $this->project->title,
            replyTo: config('mail.from.address'),
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.project-approved',
            with: [
                'project' => $this->project,
                'client' => $this->client
            ]
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
