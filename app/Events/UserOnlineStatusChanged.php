<?php

namespace App\Events;

use App\Models\User;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class UserOnlineStatusChanged implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public User $user;
    public bool $isOnline;
    public ?string $lastSeenAt;

    /**
     * Créer une nouvelle instance de l'event
     *
     * @param User $user L'utilisateur dont le statut change
     * @param bool $isOnline true si en ligne, false si hors ligne
     */
    public function __construct(User $user, bool $isOnline)
    {
        $this->user = $user;
        $this->isOnline = $isOnline;
        $this->lastSeenAt = $isOnline ? null : now()->toISOString();
    }

    /**
     * Broadcaster sur le channel privé de l'utilisateur
     * Tous ceux qui ont des conversations avec cet utilisateur peuvent écouter
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('user.' . $this->user->id),
        ];
    }

    /**
     * Nom de l'event
     */
    public function broadcastAs(): string
    {
        return 'user.online.status';
    }

    /**
     * Données envoyées avec l'event
     */
    public function broadcastWith(): array
    {
        return [
            'user_id' => $this->user->id,
            'is_online' => $this->isOnline,
            'last_seen_at' => $this->lastSeenAt,
            'timestamp' => now()->toISOString(),
        ];
    }

    /**
     * Broadcast immédiat pour que le statut soit mis à jour instantanément
     */
    public function broadcastQueue(): ?string
    {
        return null;
    }

    /**
     * Tags pour le monitoring
     */
    public function tags(): array
    {
        return [
            'user:' . $this->user->id,
            'status:' . ($this->isOnline ? 'online' : 'offline'),
        ];
    }
}
