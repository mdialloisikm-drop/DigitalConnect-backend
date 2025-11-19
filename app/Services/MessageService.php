<?php

namespace App\Services;

use App\Events\MessageSent;
use App\Events\MessageRead;
use App\Events\MessagesMarkedAsRead;
use App\Events\UserTyping;
use App\Models\Conversation;
use App\Models\Message;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class MessageService
{
    /**
     * Récupérer les messages d'une conversation
     */
    public function getMessages(string $conversationId, int $perPage = 50)
    {
        $user = auth('api')->user();
        $conversation = Conversation::findOrFail($conversationId);
        $this->checkAccess($conversation, $user);

        return Message::where('conversation_id', $conversationId)
            ->with(['sender:id,full_name,avatar', 'attachments'])
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }

    /**
     * Envoyer un message texte
     * AVEC BROADCASTING EN TEMPS RÉEL
     */
    public function sendTextMessage(string $conversationId, string $content): Message
    {
        $user = auth('api')->user();
        $conversation = Conversation::findOrFail($conversationId);
        $this->checkAccess($conversation, $user);

        DB::beginTransaction();
        try {
            $message = Message::create([
                'conversation_id' => $conversationId,
                'sender_id' => $user->id,
                'message_type' => 'text',
                'content' => $content,
                'is_read' => false,
            ]);

            $conversation->update(['last_message_at' => now()]);

            // ✅ CORRECTION: Charger les relations AVANT le broadcast
            $message->load(['sender:id,full_name,avatar,email', 'attachments']);

            // ✅ CORRECTION: Broadcaster l'événement (toOthers pour ne pas envoyer à l'expéditeur)
            Log::info('🔹 Broadcasting message.sent', [
                'message_id' => $message->id,
                'conversation_id' => $conversationId,
                'sender_id' => $user->id
            ]);

            broadcast(new MessageSent($message, $conversation));

            DB::commit();
            return $message;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('❌ Erreur envoi message: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Envoyer un message vocal
     * AVEC BROADCASTING EN TEMPS RÉEL
     */
    public function sendVoiceMessage(string $conversationId, $voiceFile): Message
    {
        $user = auth('api')->user();
        $conversation = Conversation::findOrFail($conversationId);
        $this->checkAccess($conversation, $user);

        DB::beginTransaction();
        try {
            $path = $voiceFile->store('messages/voice', 'public');

            $message = Message::create([
                'conversation_id' => $conversationId,
                'sender_id' => $user->id,
                'message_type' => 'voice',
                'content' => json_encode([
                    'path' => $path,
                    'size' => $voiceFile->getSize(),
                    'duration' => null,
                ]),
                'is_read' => false,
            ]);

            $conversation->update(['last_message_at' => now()]);
            $message->load(['sender:id,full_name,avatar', 'attachments']);

            broadcast(new MessageSent($message, $conversation));

            DB::commit();
            return $message;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Envoyer un message avec fichier
     * AVEC BROADCASTING EN TEMPS RÉEL
     */
    public function sendFileMessage(string $conversationId, $file): Message
    {
        $user = auth('api')->user();
        $conversation = Conversation::findOrFail($conversationId);
        $this->checkAccess($conversation, $user);

        DB::beginTransaction();
        try {
            $originalName = $file->getClientOriginalName();
            $path = $file->store('messages/files', 'public');

            $message = Message::create([
                'conversation_id' => $conversationId,
                'sender_id' => $user->id,
                'message_type' => 'file',
                'content' => json_encode([
                    'path' => $path,
                    'name' => $originalName,
                    'size' => $file->getSize(),
                    'mime_type' => $file->getMimeType(),
                ]),
                'is_read' => false,
            ]);

            $conversation->update(['last_message_at' => now()]);
            $message->load(['sender:id,full_name,avatar', 'attachments']);

            broadcast(new MessageSent($message, $conversation));

            DB::commit();
            return $message;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Marquer un message comme lu
     * AVEC BROADCASTING EN TEMPS RÉEL
     */
    public function markAsRead(string $messageId): Message
    {
        $user = auth('api')->user();
        $message = Message::findOrFail($messageId);

        if ($message->sender_id === $user->id) {
            throw new \Exception('Vous ne pouvez pas marquer votre propre message comme lu.');
        }

        $conversation = $message->conversation;
        $this->checkAccess($conversation, $user);

        $message->update([
            'is_read' => true,
            'read_at' => now(),
        ]);

        broadcast(new MessageRead($message, $conversation, $user->id))->toOthers();

        return $message;
    }

    /**
     * Marquer tous les messages comme lus
     * AVEC BROADCASTING EN TEMPS RÉEL
     */
    public function markAllAsRead(string $conversationId): array
    {
        $user = auth('api')->user();
        $conversation = Conversation::findOrFail($conversationId);
        $this->checkAccess($conversation, $user);

        $messagesCount = Message::where('conversation_id', $conversationId)
            ->where('sender_id', '!=', $user->id)
            ->where('is_read', false)
            ->count();

        if ($messagesCount > 0) {
            Message::where('conversation_id', $conversationId)
                ->where('sender_id', '!=', $user->id)
                ->where('is_read', false)
                ->update([
                    'is_read' => true,
                    'read_at' => now(),
                ]);

            broadcast(new MessagesMarkedAsRead($conversation, $user->id, $messagesCount))->toOthers();
        }

        return [
            'message' => 'Tous les messages ont été marqués comme lus.',
            'count' => $messagesCount,
        ];
    }

    /**
     * Notifier que l'utilisateur est en train de taper
     * BROADCASTING EN TEMPS RÉEL
     */
    public function notifyTyping(string $conversationId, bool $isTyping = true): void
    {
        $user = auth('api')->user();
        $conversation = Conversation::findOrFail($conversationId);
        $this->checkAccess($conversation, $user);

        broadcast(new UserTyping($user, $conversation, $isTyping))->toOthers();
    }

    /**
     * Supprimer un message
     */
    public function deleteMessage(string $messageId): array
    {
        $user = auth('api')->user();
        $message = Message::findOrFail($messageId);

        if ($message->sender_id !== $user->id) {
            throw new \Exception('Vous ne pouvez supprimer que vos propres messages.');
        }

        if (in_array($message->message_type, ['voice', 'file'])) {
            $content = json_decode($message->content, true);
            if (isset($content['path'])) {
                Storage::disk('public')->delete($content['path']);
            }
        }

        $message->delete();

        return ['message' => 'Message supprimé avec succès.'];
    }

    /**
     * Rechercher des messages dans une conversation
     */
    public function searchMessages(string $conversationId, string $query)
    {
        $user = auth('api')->user();
        $conversation = Conversation::findOrFail($conversationId);
        $this->checkAccess($conversation, $user);

        return Message::where('conversation_id', $conversationId)
            ->where('message_type', 'text')
            ->where('content', 'like', "%{$query}%")
            ->with(['sender:id,full_name,avatar'])
            ->orderBy('created_at', 'desc')
            ->limit(50)
            ->get();
    }

    /**
     * Vérifier l'accès d'un utilisateur à une conversation
     */
    private function checkAccess(Conversation $conversation, $user): void
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
}
