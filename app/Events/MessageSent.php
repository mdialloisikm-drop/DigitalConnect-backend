<?php

namespace App\Events;

use App\Models\Conversation;
use App\Models\Message;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MessageSent implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public Message $message;
    public Conversation $conversation;

    /**
     * Créer une nouvelle instance de l'event
     */
    public function __construct(Message $message, Conversation $conversation)
    {
        $this->message = $message;
        $this->conversation = $conversation;
    }

    /**
     * Définir le channel sur lequel l'event sera diffusé
     * Channel privé par conversation : conversation.{id}
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('conversation.' . $this->conversation->id),
        ];
    }

    /**
     * Nom de l'event diffusé (optionnel)
     * Par défaut : App\Events\MessageSent
     * On peut le simplifier en "message.sent"
     */
    public function broadcastAs(): string
    {
        return 'message.sent';
    }

    /**
     * Données envoyées avec l'event
     * Ces données seront reçues par le frontend
     */
    public function broadcastWith(): array
    {
        return [
            'message' => [
                'id' => $this->message->id,
                'conversation_id' => $this->message->conversation_id,
                'sender_id' => $this->message->sender_id,
                'message_type' => $this->message->message_type,
                'content' => $this->message->content,
                'is_read' => $this->message->is_read,
                'read_at' => $this->message->read_at?->toISOString(),
                'created_at' => $this->message->created_at->toISOString(),
                'updated_at' => $this->message->updated_at->toISOString(),
                'sender' => [
                    'id' => $this->message->sender->id,
                    'full_name' => $this->message->sender->full_name,
                    'avatar' => $this->message->sender->avatar,
                ],
                'attachments' => $this->message->attachments->map(function ($attachment) {
                    return [
                        'id' => $attachment->id,
                        'file_name' => $attachment->file_name,
                        'file_path' => $attachment->file_path,
                        'file_size' => $attachment->file_size,
                        'mime_type' => $attachment->mime_type,
                    ];
                }),
            ],
            'conversation' => [
                'id' => $this->conversation->id,
                'last_message_at' => $this->conversation->last_message_at?->toISOString(),
            ],
        ];
    }

    /**
     * Déterminer si l'event doit être diffusé
     * On peut ajouter des conditions ici si nécessaire
     */
    public function broadcastWhen(): bool
    {
        return true;
    }

    /**
     * Tags pour faciliter le monitoring (optionnel)
     */
    public function tags(): array
    {
        return [
            'message:' . $this->message->id,
            'conversation:' . $this->conversation->id,
        ];
    }
}
