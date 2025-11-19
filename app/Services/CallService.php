<?php

namespace App\Services;

use App\Events\CallInitiated;
use App\Events\CallEnded;
use App\Models\Conversation;

class CallService
{
    public function generateAgoraToken(string $channelName, int $userId, string $role = 'publisher')
    {
        $appId = config('services.agora.app_id');
        $appCertificate = config('services.agora.app_certificate');

        if (empty($appCertificate)) {
            return null;
        }

        try {
            $expireTimeInSeconds = 3600;
            $currentTimestamp = time();
            $privilegeExpiredTs = $currentTimestamp + $expireTimeInSeconds;

            $token = \AgoraToken\RtcTokenBuilder::buildTokenWithUid(
                $appId,
                $appCertificate,
                $channelName,
                $userId,
                $role === 'publisher' ? 1 : 2,
                $privilegeExpiredTs
            );

            return $token;
        } catch (\Exception $e) {
            \Log::error('Erreur génération token Agora: ' . $e->getMessage());
            return null;
        }
    }

    public function initiateCall(string $conversationId, string $callType = 'audio')
    {
        $user = auth('api')->user();
        $conversation = Conversation::findOrFail($conversationId);
        $this->checkAccess($conversation, $user);

        $channelName = 'call_' . $conversationId . '_' . time();
        $token = $this->generateAgoraToken($channelName, $user->id, 'publisher');

        $callData = [
            'conversation_id' => $conversationId,
            'channel_name' => $channelName,
            'call_type' => $callType,
            'caller' => [
                'id' => $user->id,
                'name' => $user->full_name,
                'avatar' => $user->avatar,
                'type' => $user->user_type
            ],
            'agora_config' => [
                'app_id' => config('services.agora.app_id'),
                'token' => $token,
                'channel' => $channelName,
            ],
            'timestamp' => time()
        ];

        broadcast(new CallInitiated($callData, $conversation))->toOthers();

        return $callData;
    }

    public function answerCall(string $conversationId, string $channelName)
    {
        $user = auth('api')->user();
        $conversation = Conversation::findOrFail($conversationId);
        $this->checkAccess($conversation, $user);

        $token = $this->generateAgoraToken($channelName, $user->id, 'publisher');

        return [
            'channel_name' => $channelName,
            'agora_config' => [
                'app_id' => config('services.agora.app_id'),
                'token' => $token,
                'channel' => $channelName,
            ]
        ];
    }

    public function endCall(string $conversationId, string $channelName, array $callData = [])
    {
        $user = auth('api')->user();
        $conversation = Conversation::findOrFail($conversationId);
        $this->checkAccess($conversation, $user);

        $endData = [
            'conversation_id' => $conversationId,
            'channel_name' => $channelName,
            'ended_by' => [
                'id' => $user->id,
                'name' => $user->full_name,
            ],
            'duration' => $callData['duration'] ?? 0,
            'timestamp' => time()
        ];

        broadcast(new CallEnded($endData, $conversation))->toOthers();

        return ['message' => 'Appel terminé'];
    }

    public function rejectCall(string $conversationId, string $channelName)
    {
        $user = auth('api')->user();
        $conversation = Conversation::findOrFail($conversationId);
        $this->checkAccess($conversation, $user);

        $rejectData = [
            'conversation_id' => $conversationId,
            'channel_name' => $channelName,
            'rejected_by' => [
                'id' => $user->id,
                'name' => $user->full_name,
            ],
            'timestamp' => time()
        ];

        broadcast(new CallEnded($rejectData, $conversation))->toOthers();

        return ['message' => 'Appel refusé'];
    }

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
}
