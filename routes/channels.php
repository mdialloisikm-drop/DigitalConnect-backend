<?php

use App\Models\Conversation;
use App\Models\User;
use Illuminate\Support\Facades\Broadcast;

/*
|--------------------------------------------------------------------------
| Broadcast Channels
|--------------------------------------------------------------------------
*/


Broadcast::channel('conversation.{conversationId}', function ($user, $conversationId) {
    $conversation = Conversation::find($conversationId);

    if (!$conversation) {
        return false;
    }

    if ($user->user_type === 'client' && $user->client && $conversation->client_id === $user->client->id) {
        return true;
    }

    if ($user->user_type === 'freelance' && $user->freelance && $conversation->freelance_id === $user->freelance->id) {
        return true;
    }

    return false;
});

Broadcast::channel('user.{userId}', function ($user, $userId) {
    return (int) $user->id === (int) $userId;
});
