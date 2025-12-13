<?php

namespace App\Events;

use App\Models\Conversation;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MessagesMarkedAsRead implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public Conversation $conversation;
    public int $readBy;
    public int $messagesCount;

    /**
     * Créer une nouvelle instance de l'event
     *
     * @param Conversation $conversation La conversation concernée
     * @param int $readBy L'ID de l'utilisateur qui a lu les messages
     * @param int $messagesCount Le nombre de messages marqués comme lus
     */
    public function __construct(Conversation $conversation, int $readBy, int $messagesCount)
    {
        $this->conversation = $conversation;
        $this->readBy = $readBy;
        $this->messagesCount = $messagesCount;
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
        //return 'messages.marked.read';
        return 'MessagesMarkedAsRead';
    }

    /**
     * Données envoyées avec l'event
     */
    public function broadcastWith(): array
    {
        return [
            'conversation_id' => $this->conversation->id,
            'read_by' => $this->readBy,
            'messages_count' => $this->messagesCount,
            'read_at' => now()->toISOString(),
        ];
    }

    /**
     * Tags pour le monitoring
     */
    public function tags(): array
    {
        return [
            'conversation:' . $this->conversation->id,
            'read_by:' . $this->readBy,
        ];
    }
}
