<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Conversation extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'last_message_at' => 'datetime',
    ];

    /**
     * Relation : Client participant
     */
    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    /**
     * Relation : Freelance participant
     */
    public function freelance()
    {
        return $this->belongsTo(Freelance::class);
    }

    /**
     * Relation : Messages de la conversation
     */
    public function messages()
    {
        return $this->hasMany(Message::class);
    }

    /**
     * Récupérer le dernier message de la conversation
     */
    public function lastMessage()
    {
        return $this->hasOne(Message::class)->latestOfMany();
    }

    /**
     * Récupérer l'autre participant de la conversation
     * @param int $userId ID de l'utilisateur actuel
     */
    public function getOtherParticipant(int $userId)
    {
        $user = User::find($userId);

        if (!$user) {
            return null;
        }

        if ($user->user_type === 'client' && $user->client->id === $this->client_id) {
            return $this->freelance->user;
        }

        if ($user->user_type === 'freelance' && $user->freelance->id === $this->freelance_id) {
            return $this->client->user;
        }

        return null;
    }

    /**
     * Vérifier si un utilisateur est participant de la conversation
     */
    public function hasParticipant(int $userId): bool
    {
        $user = User::find($userId);

        if (!$user) {
            return false;
        }

        if ($user->user_type === 'client') {
            return $this->client_id === $user->client->id;
        }

        if ($user->user_type === 'freelance') {
            return $this->freelance_id === $user->freelance->id;
        }

        return false;
    }

    /**
     * Scope : Conversations actives
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope : Conversations archivées
     */
    public function scopeArchived($query)
    {
        return $query->where('status', 'archived');
    }

    /**
     * Scope : Conversations d'un client
     */
    public function scopeForClient($query, int $clientId)
    {
        return $query->where('client_id', $clientId);
    }

    /**
     * Scope : Conversations d'un freelance
     */
    public function scopeForFreelance($query, int $freelanceId)
    {
        return $query->where('freelance_id', $freelanceId);
    }

    /**
     * Scope : Conversations avec messages non lus
     */
    public function scopeWithUnreadMessages($query, int $userId)
    {
        return $query->whereHas('messages', function ($q) use ($userId) {
            $q->where('sender_id', '!=', $userId)
                ->where('is_read', false);
        });
    }

    /**
     * Compter les messages non lus pour un utilisateur
     */
    public function countUnreadMessages(int $userId): int
    {
        return $this->messages()
            ->where('sender_id', '!=', $userId)
            ->where('is_read', false)
            ->count();
    }
}
