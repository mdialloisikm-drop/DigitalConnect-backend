<?php

namespace App\Http\Controllers;

use App\Services\CallService;
use Illuminate\Http\Request;

class CallController extends Controller
{
    protected $callService;

    public function __construct(CallService $callService)
    {
        $this->callService = $callService;
    }

    public function initiateCall(Request $request, string $conversationId)
    {
        try {
            $request->validate([
                'call_type' => 'required|in:audio,video'
            ]);

            $callData = $this->callService->initiateCall(
                $conversationId,
                $request->input('call_type', 'audio')
            );

            return response()->json([
                'message' => 'Appel initié',
                'data' => $callData
            ], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    public function answerCall(Request $request, string $conversationId)
    {
        try {
            $request->validate([
                'channel_name' => 'required|string'
            ]);

            $callData = $this->callService->answerCall(
                $conversationId,
                $request->input('channel_name')
            );

            return response()->json([
                'message' => 'Appel accepté',
                'data' => $callData
            ], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    public function endCall(Request $request, string $conversationId)
    {
        try {
            $request->validate([
                'channel_name' => 'required|string',
                'duration' => 'nullable|integer|min:0'
            ]);

            $result = $this->callService->endCall(
                $conversationId,
                $request->input('channel_name'),
                $request->only(['duration'])
            );

            return response()->json($result, 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    public function rejectCall(Request $request, string $conversationId)
    {
        try {
            $request->validate([
                'channel_name' => 'required|string'
            ]);

            $result = $this->callService->rejectCall(
                $conversationId,
                $request->input('channel_name')
            );

            return response()->json($result, 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }
}
