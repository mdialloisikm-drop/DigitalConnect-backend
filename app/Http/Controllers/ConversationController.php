<?php

namespace App\Http\Controllers;

use App\Services\ConversationService;
use Illuminate\Http\Request;

class ConversationController extends Controller
{
    protected ConversationService $conversationService;

    public function __construct(ConversationService $conversationService)
    {
        $this->conversationService = $conversationService;
    }

    /**
     * Récupérer toutes les conversations de l'utilisateur connecté
     * GET /api/conversations
     */
    public function index()
    {
        try {
            $conversations = $this->conversationService->getUserConversations();
            return response()->json($conversations, 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 403);
        }
    }

    /**
     * Afficher une conversation spécifique
     * GET /api/conversations/{id}
     */
    public function show(string $id)
    {
        try {
            $conversation = $this->conversationService->getConversation($id);
            return response()->json($conversation, 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 404);
        }
    }

    /**
     * Démarrer ou récupérer une conversation avec un utilisateur
     * POST /api/conversations/start
     * Body: { "user_type": "freelance|client", "user_id": 123 }
     */
    public function startConversation(Request $request)
    {
        $request->validate([
            'user_type' => 'required|in:client,freelance',
            'user_id' => 'required|integer|exists:users,id'
        ]);

        try {
            // Récupérer l'ID du client ou freelance selon le type
            $user = \App\Models\User::findOrFail($request->user_id);

            if ($user->user_type !== $request->user_type) {
                return response()->json(['error' => 'Le type d\'utilisateur ne correspond pas.'], 400);
            }

            $userId = $user->user_type === 'client'
                ? $user->client->id
                : $user->freelance->id;

            $conversation = $this->conversationService->startConversationWith(
                $request->user_type,
                $userId
            );

            return response()->json([
                'message' => 'Conversation créée ou récupérée avec succès',
                'conversation' => $conversation->load([
                    'client.user:id,full_name,avatar,email',
                    'freelance.user:id,full_name,avatar,email',
                ])
            ], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    /**
     * Récupérer une conversation avec un utilisateur spécifique
     * GET /api/conversations/with/{userType}/{userId}
     */
    public function getConversationWith(string $userType, int $userId)
    {
        try {
            $conversation = $this->conversationService->getConversationWith($userType, $userId);

            if (!$conversation) {
                return response()->json([
                    'message' => 'Aucune conversation trouvée',
                    'conversation' => null
                ], 200);
            }

            return response()->json($conversation, 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    /**
     * Archiver une conversation
     * POST /api/conversations/{id}/archive
     */
    public function archive(string $id)
    {
        try {
            $conversation = $this->conversationService->archiveConversation($id);
            return response()->json([
                'message' => 'Conversation archivée avec succès',
                'conversation' => $conversation
            ], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 403);
        }
    }

    /**
     * Désarchiver une conversation
     * POST /api/conversations/{id}/unarchive
     */
    public function unarchive(string $id)
    {
        try {
            $conversation = $this->conversationService->unarchiveConversation($id);
            return response()->json([
                'message' => 'Conversation désarchivée avec succès',
                'conversation' => $conversation
            ], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 403);
        }
    }

    /**
     * Obtenir le nombre de messages non lus
     * GET /api/conversations/unread/count
     */
    public function unreadCount()
    {
        try {
            $count = $this->conversationService->getTotalUnreadCount();
            return response()->json(['count' => $count], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    /**
     * Supprimer une conversation
     * DELETE /api/conversations/{id}
     */
    public function destroy(string $id)
    {
        try {
            $this->conversationService->deleteConversation($id);
            return response()->json([
                'message' => 'Conversation supprimée avec succès'
            ], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 403);
        }
    }
}
