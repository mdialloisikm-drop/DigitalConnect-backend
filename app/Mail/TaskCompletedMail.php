<?php

namespace App\Mail;

use App\Models\Client;
use App\Models\Freelance;
use App\Models\Project;
use App\Models\ProjectTask;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class TaskCompletedMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $project;
    public $task;
    public $client;
    public $freelance;

    public function __construct(Project $project, ProjectTask $task, Client $client, Freelance $freelance)
    {
        $this->project = $project;
        $this->task = $task;
        $this->client = $client;
        $this->freelance = $freelance;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Tâche terminée - ' . $this->project->title,
            replyTo: config('mail.from.address'),
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.task-completed',
            with: [
                'project' => $this->project,
                'task' => $this->task,
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
