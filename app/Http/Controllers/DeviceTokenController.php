<?php

namespace App\Http\Controllers;

use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DeviceTokenController extends Controller
{
    protected $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    /**
     * Enregistrer un token FCM pour l'utilisateur connecté
     */
    public function store(Request $request)
    {
        $request->validate([
            'token' => 'required|string',
            'device_type' => 'nullable|in:web,android,ios',
            'device_name' => 'nullable|string|max:100',
        ]);

        try {
            $user = Auth::user();

            $deviceToken = $this->notificationService->registerToken(
                $user,
                $request->token,
                $request->device_type ?? 'web',
                $request->device_name
            );

            return response()->json([
                'message' => 'Token enregistré avec succès',
                'device_token' => $deviceToken
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Erreur lors de l\'enregistrement du token',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Supprimer un token FCM
     */
    public function destroy(Request $request)
    {
        $request->validate([
            'token' => 'required|string',
        ]);

        try {
            $this->notificationService->removeToken($request->token);

            return response()->json([
                'message' => 'Token supprimé avec succès'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Erreur lors de la suppression du token',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Désactiver tous les tokens de l'utilisateur (lors de la déconnexion)
     */
    public function deactivateAll()
    {
        try {
            $user = Auth::user();
            $this->notificationService->deactivateUserTokens($user);

            return response()->json([
                'message' => 'Tous les tokens ont été désactivés'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Erreur lors de la désactivation des tokens',
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
