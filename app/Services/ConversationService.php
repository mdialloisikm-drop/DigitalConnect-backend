<?php

namespace App\Services;

use App\Models\Conversation;
use App\Models\Client;
use App\Models\Freelance;
use App\Models\Proposal;
use Illuminate\Support\Facades\DB;

class ConversationService
{
    /**
     * Créer ou récupérer une conversation entre un client et un freelance
     */
    public function getOrCreateConversation(int $clientId, int $freelanceId)
    {
        $client = Client::findOrFail($clientId);
        $freelance = Freelance::findOrFail($freelanceId);

        $conversation = Conversation::where('client_id', $clientId)
            ->where('freelance_id', $freelanceId)
            ->first();

        if ($conversation) {
            return $conversation;
        }

        return Conversation::create([
            'client_id' => $clientId,
            'freelance_id' => $freelanceId,
            'status' => 'active',
        ]);
    }

    /**
     * ✅ SOLUTION FINALE: Utiliser lastMessage() définie dans le modèle Conversation
     */
    public function getUserConversations()
    {
        $user = auth('api')->user();

        if ($user->user_type === 'client') {
            return Conversation::where('client_id', $user->client->id)
                ->with([
                    'freelance.user:id,full_name,avatar,email',
                    'lastMessage.sender:id,full_name,avatar', // ✅ Utilise la relation lastMessage()
                ])
                ->withCount(['messages as unread_count' => function ($query) use ($user) {
                    $query->where('sender_id', '!=', $user->id)
                        ->where('is_read', false);
                }])
                ->orderBy('last_message_at', 'desc')
                ->get();
        } elseif ($user->user_type === 'freelance') {
            return Conversation::where('freelance_id', $user->freelance->id)
                ->with([
                    'client.user:id,full_name,avatar,email',
                    'lastMessage.sender:id,full_name,avatar', // ✅ Utilise la relation lastMessage()
                ])
                ->withCount(['messages as unread_count' => function ($query) use ($user) {
                    $query->where('sender_id', '!=', $user->id)
                        ->where('is_read', false);
                }])
                ->orderBy('last_message_at', 'desc')
                ->get();
        }

        throw new \Exception('Type d\'utilisateur non autorisé pour la messagerie.');
    }

    /**
     * Récupérer une conversation spécifique
     */
    public function getConversation(string $id)
    {
        $user = auth('api')->user();
        $conversation = Conversation::with([
            'client.user:id,full_name,avatar,email,phone,city,country',
            'freelance.user:id,full_name,avatar,email,phone,city,country',
        ])->findOrFail($id);

        $this->checkAccess($conversation, $user);
        return $conversation;
    }

    /**
     * Récupérer la conversation entre l'utilisateur connecté et un autre utilisateur
     */
    public function getConversationWith(string $userType, int $userId)
    {
        $currentUser = auth('api')->user();

        if ($currentUser->user_type === 'client') {
            if ($userType !== 'freelance') {
                throw new \Exception('Un client ne peut avoir de conversation qu\'avec un freelance.');
            }

            return Conversation::where('client_id', $currentUser->client->id)
                ->where('freelance_id', $userId)
                ->with([
                    'client.user:id,full_name,avatar,email',
                    'freelance.user:id,full_name,avatar,email',
                ])
                ->first();
        }

        if ($currentUser->user_type === 'freelance') {
            if ($userType !== 'client') {
                throw new \Exception('Un freelance ne peut avoir de conversation qu\'avec un client.');
            }

            return Conversation::where('client_id', $userId)
                ->where('freelance_id', $currentUser->freelance->id)
                ->with([
                    'client.user:id,full_name,avatar,email',
                    'freelance.user:id,full_name,avatar,email',
                ])
                ->first();
        }

        throw new \Exception('Type d\'utilisateur non autorisé.');
    }

    /**
     * Démarrer une conversation avec un utilisateur
     */
    public function startConversationWith(string $userType, int $userId)
    {
        $currentUser = auth('api')->user();

        if ($currentUser->user_type === 'client') {
            if ($userType !== 'freelance') {
                throw new \Exception('Un client ne peut avoir de conversation qu\'avec un freelance.');
            }

            return $this->getOrCreateConversation($currentUser->client->id, $userId);
        }

        if ($currentUser->user_type === 'freelance') {
            if ($userType !== 'client') {
                throw new \Exception('Un freelance ne peut avoir de conversation qu\'avec un client.');
            }

            return $this->getOrCreateConversation($userId, $currentUser->freelance->id);
        }

        throw new \Exception('Type d\'utilisateur non autorisé.');
    }

    /**
     * Archiver une conversation
     */
    public function archiveConversation(string $id)
    {
        $user = auth('api')->user();
        $conversation = Conversation::findOrFail($id);
        $this->checkAccess($conversation, $user);
        $conversation->update(['status' => 'archived']);
        return $conversation;
    }

    /**
     * Désarchiver une conversation
     */
    public function unarchiveConversation(string $id)
    {
        $user = auth('api')->user();
        $conversation = Conversation::findOrFail($id);
        $this->checkAccess($conversation, $user);
        $conversation->update(['status' => 'active']);
        return $conversation;
    }

    /**
     * Obtenir le nombre total de messages non lus
     */
    public function getTotalUnreadCount()
    {
        $user = auth('api')->user();

        if ($user->user_type === 'client') {
            return Conversation::where('client_id', $user->client->id)
                ->withCount(['messages as unread_count' => function ($query) use ($user) {
                    $query->where('sender_id', '!=', $user->id)->where('is_read', false);
                }])
                ->get()
                ->sum('unread_count');
        } elseif ($user->user_type === 'freelance') {
            return Conversation::where('freelance_id', $user->freelance->id)
                ->withCount(['messages as unread_count' => function ($query) use ($user) {
                    $query->where('sender_id', '!=', $user->id)->where('is_read', false);
                }])
                ->get()
                ->sum('unread_count');
        }

        return 0;
    }

    /**
     * Supprimer une conversation
     */
    public function deleteConversation(string $id)
    {
        $user = auth('api')->user();
        $conversation = Conversation::findOrFail($id);
        $this->checkAccess($conversation, $user);

        DB::beginTransaction();
        try {
            $conversation->delete();
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw new \Exception('Erreur lors de la suppression de la conversation : ' . $e->getMessage());
        }
    }

    /**
     * Vérifier l'accès d'un utilisateur à une conversation
     */
    private function checkAccess(Conversation $conversation, $user)
    {
        $hasAccess = false;

        if ($user->user_type === 'client' && $conversation->client_id === $user->client->id) {
            $hasAccess = true;
        } elseif ($user->user_type === 'freelance' && $conversation->freelance_id === $user->freelance->id) {
            $hasAccess = true;
        }

        if (!$hasAccess) {
            throw new \Exception('Vous n\'avez pas accès à cette conversation.');
        }
    }

    /**
     * Créer automatiquement une conversation lors de l'acceptation d'une proposition
     */
    public function createConversationFromProposal(Proposal $proposal)
    {
        $existingConversation = Conversation::where('client_id', $proposal->project->client_id)
            ->where('freelance_id', $proposal->freelance_id)
            ->first();

        if ($existingConversation) {
            return $existingConversation;
        }

        return Conversation::create([
            'client_id' => $proposal->project->client_id,
            'freelance_id' => $proposal->freelance_id,
            'status' => 'active',
        ]);
    }
}
