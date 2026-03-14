<?php

namespace App\Http\Controllers;

use App\Http\Requests\Message\SendMessageRequest;
use App\Http\Resources\MessageResource;
use App\Models\User;
use App\Repositories\Contracts\MessageRepositoryInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use LogicException;

class MessageController extends Controller
{
    public function __construct(
        private readonly MessageRepositoryInterface $messageRepository,
    ) {}

    /**
     * Return the conversation between the authenticated user and the given user.
     */
    public function conversation(Request $request, User $user): AnonymousResourceCollection
    {
        $authUser = $request->user();

        $messages = $this->messageRepository->getConversation($authUser, $user);

        $this->messageRepository->markAsRead($user, $authUser);

        return MessageResource::collection($messages);
    }

    /**
     * Return unread messages from the given user to the authenticated user.
     */
    public function unread(Request $request, User $user): AnonymousResourceCollection
    {
        $authUser = $request->user();

        $messages = $this->messageRepository->getUnreadMessages($user, $authUser);

        $this->messageRepository->markAsRead($user, $authUser);

        return MessageResource::collection($messages);
    }

    /**
     * Send a new message to the given user.
     */
    public function send(SendMessageRequest $request, User $user): JsonResponse
    {
        try {
            $message = $this->messageRepository->send($request->user(), $user, $request->input('body'));
        } catch (LogicException $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }

        return (new MessageResource($message))
            ->response()
            ->setStatusCode(201);
    }
}
