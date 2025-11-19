<?php

namespace App\Services;

use App\Models\DeviceToken;
use App\Models\User;
use Kreait\Firebase\Factory;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification;
use Illuminate\Support\Facades\Log;

class NotificationService
{
    protected $messaging;

    public function __construct()
    {
        try {
            $factory = (new Factory)->withServiceAccount(config('fcm.credentials.file'));
            $this->messaging = $factory->createMessaging();
        } catch (\Exception $e) {
            Log::error('Erreur initialisation Firebase: ' . $e->getMessage());
            $this->messaging = null;
        }
    }

    /**
     * Envoyer une notification à un utilisateur spécifique
     */
    public function sendToUser(User $user, string $title, string $body, array $data = [])
    {
        if (!$this->messaging) {
            Log::warning('Firebase messaging non initialisé');
            return false;
        }

        $tokens = DeviceToken::where('user_id', $user->id)
            ->where('is_active', true)
            ->pluck('token')
            ->toArray();

        if (empty($tokens)) {
            Log::info("Aucun token FCM pour l'utilisateur {$user->id}");
            return false;
        }

        return $this->sendToTokens($tokens, $title, $body, $data);
    }

    /**
     * Envoyer une notification à plusieurs tokens
     */
    public function sendToTokens(array $tokens, string $title, string $body, array $data = [])
    {
        if (!$this->messaging) {
            return false;
        }

        try {
            $notification = Notification::create($title, $body);

            $results = [];
            foreach ($tokens as $token) {
                try {
                    $message = CloudMessage::withTarget('token', $token)
                        ->withNotification($notification)
                        ->withData($data);

                    $this->messaging->send($message);
                    $results[] = ['token' => $token, 'success' => true];

                    Log::info("Notification envoyée avec succès au token: " . substr($token, 0, 20) . "...");
                } catch (\Kreait\Firebase\Exception\Messaging\NotFound $e) {
                    // Token invalide ou expiré, le désactiver
                    DeviceToken::where('token', $token)->update(['is_active' => false]);
                    Log::warning("Token FCM invalide désactivé: " . substr($token, 0, 20) . "...");
                    $results[] = ['token' => $token, 'success' => false, 'error' => 'Token invalide'];
                } catch (\Exception $e) {
                    Log::error("Erreur envoi notification: " . $e->getMessage());
                    $results[] = ['token' => $token, 'success' => false, 'error' => $e->getMessage()];
                }
            }

            return $results;
        } catch (\Exception $e) {
            Log::error('Erreur lors de l\'envoi de notifications: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Enregistrer un nouveau token pour un utilisateur
     */
    public function registerToken(User $user, string $token, string $deviceType = 'web', ?string $deviceName = null)
    {
        try {
            // Vérifier si le token existe déjà
            $existingToken = DeviceToken::where('token', $token)->first();

            if ($existingToken) {
                // Mettre à jour le token existant
                $existingToken->update([
                    'user_id' => $user->id,
                    'is_active' => true,
                    'last_used_at' => now(),
                    'device_name' => $deviceName ?? $existingToken->device_name,
                ]);
                return $existingToken;
            }

            // Créer un nouveau token
            return DeviceToken::create([
                'user_id' => $user->id,
                'token' => $token,
                'device_type' => $deviceType,
                'device_name' => $deviceName,
                'is_active' => true,
                'last_used_at' => now(),
            ]);
        } catch (\Exception $e) {
            Log::error('Erreur lors de l\'enregistrement du token: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Supprimer un token
     */
    public function removeToken(string $token)
    {
        return DeviceToken::where('token', $token)->delete();
    }

    /**
     * Désactiver tous les tokens d'un utilisateur
     */
    public function deactivateUserTokens(User $user)
    {
        return DeviceToken::where('user_id', $user->id)
            ->update(['is_active' => false]);
    }

    // ===================================
    // NOTIFICATIONS SPÉCIFIQUES
    // ===================================

    /**
     * Notification: Nouvelle proposition reçue (Client)
     */
    public function notifyNewProposal($client, $project, $freelance)
    {
        $title = "Nouvelle proposition reçue";
        $body = "{$freelance->user->name} a soumis une proposition pour votre projet '{$project->title}'";
        $data = [
            'type' => 'new_proposal',
            'project_id' => (string)$project->id,
            'proposal_id' => (string)$project->proposals()->latest()->first()->id,
            'url' => '/client/projects/' . $project->id . '/proposals'
        ];

        return $this->sendToUser($client->user, $title, $body, $data);
    }

    /**
     * Notification: Proposition acceptée (Freelance)
     */
    public function notifyProposalAccepted($freelance, $project)
    {
        $title = "Proposition acceptée !";
        $body = "Votre proposition pour le projet '{$project->title}' a été acceptée";
        $data = [
            'type' => 'proposal_accepted',
            'project_id' => (string)$project->id,
            'url' => '/freelance/projects/' . $project->id
        ];

        return $this->sendToUser($freelance->user, $title, $body, $data);
    }

    /**
     * Notification: Proposition rejetée (Freelance)
     */
    public function notifyProposalRejected($freelance, $project)
    {
        $title = "Proposition non retenue";
        $body = "Votre proposition pour le projet '{$project->title}' n'a pas été retenue";
        $data = [
            'type' => 'proposal_rejected',
            'project_id' => (string)$project->id,
            'url' => '/freelance/proposals'
        ];

        return $this->sendToUser($freelance->user, $title, $body, $data);
    }

    /**
     * Notification: Nouveau message reçu
     */
    public function notifyNewMessage($recipient, $sender, $conversationId)
    {
        $title = "Nouveau message";
        $body = "{$sender->name} vous a envoyé un message";
        $data = [
            'type' => 'new_message',
            'conversation_id' => (string)$conversationId,
            'sender_id' => (string)$sender->id,
            'url' => '/messages/' . $conversationId
        ];

        return $this->sendToUser($recipient, $title, $body, $data);
    }

    /**
     * Notification: Livrable uploadé (Client)
     */
    public function notifyDeliverableUploaded($client, $project, $freelance)
    {
        $title = "Nouveau livrable disponible";
        $body = "{$freelance->user->name} a uploadé un livrable pour le projet '{$project->title}'";
        $data = [
            'type' => 'deliverable_uploaded',
            'project_id' => (string)$project->id,
            'url' => '/client/projects/' . $project->id
        ];

        return $this->sendToUser($client->user, $title, $body, $data);
    }

    /**
     * Notification: Commande de service (Freelance)
     */
    public function notifyServiceOrdered($freelance, $service, $client)
    {
        $title = "Nouvelle commande de service";
        $body = "{$client->user->name} a commandé votre service '{$service->title}'";
        $data = [
            'type' => 'service_ordered',
            'service_id' => (string)$service->id,
            'url' => '/freelance/orders'
        ];

        return $this->sendToUser($freelance->user, $title, $body, $data);
    }

    /**
     * Notification: Contrat complété (Freelance)
     */
    public function notifyContractCompleted($freelance, $project)
    {
        $title = "Projet terminé";
        $body = "Le projet '{$project->title}' a été marqué comme terminé";
        $data = [
            'type' => 'contract_completed',
            'project_id' => (string)$project->id,
            'url' => '/freelance/projects/' . $project->id
        ];

        return $this->sendToUser($freelance->user, $title, $body, $data);
    }
}
