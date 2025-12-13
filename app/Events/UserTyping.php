<?php

namespace App\Events;

use App\Models\Conversation;
use App\Models\User;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class UserTyping implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public User $user;
    public Conversation $conversation;
    public bool $isTyping;

    /**
     * Créer une nouvelle instance de l'event
     *
     * @param User $user L'utilisateur qui tape
     * @param Conversation $conversation La conversation concernée
     * @param bool $isTyping true si l'utilisateur tape, false s'il a arrêté
     */
    public function __construct(User $user, Conversation $conversation, bool $isTyping = true)
    {
        $this->user = $user;
        $this->conversation = $conversation;
        $this->isTyping = $isTyping;

        // Empêcher que l'émetteur reçoive son propre whisper/broadcast
        $this->dontBroadcastToCurrentUser();
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
        //return 'user.typing';
        return 'UserTyping';
    }

    /**
     * Données envoyées avec l'event
     */
    public function broadcastWith(): array
    {
        return [
            'user_id' => $this->user->id,
            'user_name' => $this->user->full_name,
            'conversation_id' => $this->conversation->id,
            'is_typing' => $this->isTyping,
            'timestamp' => now()->toISOString(),
        ];
    }
}
