<?php

namespace App\Http\Controllers;

use App\Http\Requests\SendMessageRequest;
use App\Services\MessageService;
use App\Services\NotificationService;
use Illuminate\Http\Request;

class MessageController extends Controller
{
    protected $messageService;

    public function __construct(MessageService $messageService)
    {
        $this->messageService = $messageService;
    }

    public function index(Request $request, string $conversationId)
    {
        try {
            $messages = $this->messageService->getMessages($conversationId);

            return response()->json($messages);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 403);
        }
    }

    public function store(SendMessageRequest $request, string $conversationId)
    {
        try {
            $messageType = $request->input('message_type', 'text');

            $message = match ($messageType) {
                'text' => $this->messageService->sendTextMessage(
                    $conversationId,
                    $request->input('content')
                ),
                'voice' => $this->messageService->sendVoiceMessage(
                    $conversationId,
                    $request->file('voice')
                ),
                'file' => $this->messageService->sendFileMessage(
                    $conversationId,
                    $request->file('file')
                ),
                default => throw new \Exception('Type de message non valide.')
            };

            // Envoyer notification au destinataire — CORRECTION: déterminer le bon user
            $notificationService = app(NotificationService::class);
            $conversation = $message->conversation;
            $sender = auth()->user();

            // Charger participants utilisateurs
            $conversation->load(['client.user', 'freelance.user']);

            $clientUser = $conversation->client->user ?? null;
            $freelanceUser = $conversation->freelance->user ?? null;

            if ($sender->id === ($clientUser->id ?? null)) {
                $recipient = $freelanceUser;
            } elseif ($sender->id === ($freelanceUser->id ?? null)) {
                $recipient = $clientUser;
            } else {
                // fallback: si sender n'est pas retrouvé, choisir l'autre en fonction des ids internes
                $recipient = $clientUser ?? $freelanceUser;
            }

            if ($recipient) {
                $notificationService->notifyNewMessage($recipient, $sender, $conversation->id);
            }

            return response()->json([
                'message' => 'Message envoyé avec succès.',
                'data' => $message,
            ], 201);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    public function markAsRead(string $messageId)
    {
        try {
            $message = $this->messageService->markAsRead($messageId);

            return response()->json([
                'message' => 'Message marqué comme lu.',
                'data' => $message,
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    public function markAllAsRead(string $conversationId)
    {
        try {
            $result = $this->messageService->markAllAsRead($conversationId);

            return response()->json($result);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    public function typing(Request $request, string $conversationId)
    {
        try {
            $validated = $request->validate([
                'is_typing' => 'nullable|boolean'
            ]);

            $isTyping = $validated['is_typing'] ?? true;

            $this->messageService->notifyTyping($conversationId, $isTyping);

            return response()->json([
                'message' => 'Statut typing mis à jour',
                'is_typing' => $isTyping
            ], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 403);
        }
    }

    public function destroy(string $messageId)
    {
        try {
            $result = $this->messageService->deleteMessage($messageId);

            return response()->json($result);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    public function search(Request $request, string $conversationId)
    {
        try {
            $query = $request->input('query');

            if (!$query) {
                return response()->json(['error' => 'Query parameter is required.'], 400);
            }

            $messages = $this->messageService->searchMessages($conversationId, $query);

            return response()->json($messages);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }
}
