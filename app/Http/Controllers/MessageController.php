<?php

namespace App\Http\Controllers;

use App\Models\Message;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MessageController extends Controller
{
    /**
     * Return the conversation between the authenticated user and the given user.
     */
    public function conversation(Request $request, int $userId): JsonResponse
    {
        $other = User::find($userId);

        if (! $other) {
            return response()->json(['message' => 'User not found.'], 404);
        }

        $authId = $request->user()->id;

        $messages = Message::with(['sender:id,name', 'receiver:id,name'])
            ->where(function ($q) use ($authId, $userId) {
                $q->where('sender_id', $authId)->where('receiver_id', $userId);
            })
            ->orWhere(function ($q) use ($authId, $userId) {
                $q->where('sender_id', $userId)->where('receiver_id', $authId);
            })
            ->orderBy('created_at')
            ->get()
            ->map(fn (Message $m) => [
                'id'         => $m->id,
                'body'       => $m->body,
                'sender_id'  => $m->sender_id,
                'read_at'    => $m->read_at?->toIso8601String(),
                'created_at' => $m->created_at->toIso8601String(),
            ]);

        // Mark unread messages sent by the other user as read
        Message::where('sender_id', $userId)
            ->where('receiver_id', $authId)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        return response()->json($messages);
    }

    /**
     * Send a new message to the given user.
     */
    public function send(Request $request, int $userId): JsonResponse
    {
        $other = User::find($userId);

        if (! $other) {
            return response()->json(['message' => 'User not found.'], 404);
        }

        $authId = $request->user()->id;

        if ($authId === $userId) {
            return response()->json(['message' => 'Cannot send a message to yourself.'], 422);
        }

        $request->validate([
            'body' => ['required', 'string', 'max:5000'],
        ]);

        $message = Message::create([
            'sender_id'   => $authId,
            'receiver_id' => $userId,
            'body'        => $request->input('body'),
        ]);

        return response()->json([
            'id'         => $message->id,
            'body'       => $message->body,
            'sender_id'  => $message->sender_id,
            'read_at'    => null,
            'created_at' => $message->created_at->toIso8601String(),
        ], 201);
    }
}

