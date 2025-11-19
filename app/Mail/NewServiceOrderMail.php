<?php

namespace App\Mail;

use App\Models\Client;
use App\Models\Freelance;
use App\Models\Order;
use App\Models\Service;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class NewServiceOrderMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $service;
    public $order;
    public $client;
    public $freelance;

    public function __construct(Service $service, Order $order, Client $client, Freelance $freelance)
    {
        $this->service = $service;
        $this->order = $order;
        $this->client = $client;
        $this->freelance = $freelance;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Nouvelle commande pour votre service - ' . $this->service->title,
            replyTo: config('mail.from.address'),
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.new-service-order',
            with: [
                'service' => $this->service,
                'order' => $this->order,
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
