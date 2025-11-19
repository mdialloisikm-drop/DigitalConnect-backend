<?php

namespace App\Mail;

use App\Models\Freelance;
use App\Models\Project;
use App\Models\Proposal;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ProposalAcceptedMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $project;
    public $proposal;
    public $freelance;

    public function __construct(Project $project, Proposal $proposal, Freelance $freelance)
    {
        $this->project = $project;
        $this->proposal = $proposal;
        $this->freelance = $freelance;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Votre proposition a été acceptée - ' . $this->project->title,
            replyTo: config('mail.from.address'),
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.proposal-accepted',
            with: [
                'project' => $this->project,
                'proposal' => $this->proposal,
                'freelance' => $this->freelance
            ]
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
