<?php

namespace App\Events;

use App\Models\Conversation;
use App\Models\Message;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MessageRead implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public Message $message;
    public Conversation $conversation;
    public int $readBy;

    /**
     * Créer une nouvelle instance de l'event
     *
     * @param Message $message Le message qui a été lu
     * @param Conversation $conversation La conversation concernée
     * @param int $readBy L'ID de l'utilisateur qui a lu le message
     */
    public function __construct(Message $message, Conversation $conversation, int $readBy)
    {
        $this->message = $message;
        $this->conversation = $conversation;
        $this->readBy = $readBy;
    }

    /**
     * Channel privé de la conversation
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('conversation.' . $this->conversation->id),
        ];
    }

    /**
     * Nom de l'event
     */
    public function broadcastAs(): string
    {
        //return 'message.read';
        return 'messageRead';
    }

    /**
     * Données envoyées avec l'event
     */
    public function broadcastWith(): array
    {
        return [
            'message_id' => $this->message->id,
            'conversation_id' => $this->conversation->id,
            'read_by' => $this->readBy,
            'read_at' => $this->message->read_at?->toISOString(),
        ];
    }

    /**
     * Tags pour le monitoring
     */
    public function tags(): array
    {
        return [
            'message:' . $this->message->id,
            'read_by:' . $this->readBy,
        ];
    }
}
