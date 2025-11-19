<?php

namespace App\Mail;

use App\Models\Client;
use App\Models\Freelance;
use App\Models\Project;
use App\Models\Proposal;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class NewProposalReceivedMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $project;
    public $proposal;
    public $client;
    public $freelance;

    public function __construct(Project $project, Proposal $proposal, Client $client, Freelance $freelance)
    {
        $this->project = $project;
        $this->proposal = $proposal;
        $this->client = $client;
        $this->freelance = $freelance;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Nouvelle proposition pour votre projet - ' . $this->project->title,
            replyTo: config('mail.from.address'),
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.new-proposal-received',
            with: [
                'project' => $this->project,
                'proposal' => $this->proposal,
                'client' => $this->client,
                'freelance' => $this->freelance
            ]
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
