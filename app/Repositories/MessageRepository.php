<?php

namespace App\Repositories;

use App\Models\Message;
use App\Models\User;
use App\Repositories\Contracts\FriendRepositoryInterface;
use App\Repositories\Contracts\MessageRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use LogicException;

class MessageRepository implements MessageRepositoryInterface
{
    public function __construct(
        private readonly FriendRepositoryInterface $friendRepository,
    ) {}

    /**
     * Return all messages between two users, ordered by creation time.
     *
     * @return Collection<int, Message>
     */
    public function getConversation(User $authUser, User $other): Collection
    {
        return Message::with(['sender:id,name', 'receiver:id,name'])
            ->where(function ($q) use ($authUser, $other) {
                $q->where('sender_id', $authUser->id)->where('receiver_id', $other->id);
            })
            ->orWhere(function ($q) use ($authUser, $other) {
                $q->where('sender_id', $other->id)->where('receiver_id', $authUser->id);
            })
            ->orderBy('created_at')
            ->get();
    }

    /**
     * Return unread messages sent by $sender to $receiver, ordered by creation time.
     *
     * @return Collection<int, Message>
     */
    public function getUnreadMessages(User $sender, User $receiver): Collection
    {
        return Message::with(['sender:id,name', 'receiver:id,name'])
            ->where('sender_id', $sender->id)
            ->where('receiver_id', $receiver->id)
            ->whereNull('read_at')
            ->orderBy('created_at')
            ->get();
    }

    /**
     * Mark all unread messages sent by $sender to $receiver as read.
     */
    public function markAsRead(User $sender, User $receiver): void
    {
        Message::where('sender_id', $sender->id)
            ->where('receiver_id', $receiver->id)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);
    }

    /**
     * Send a new message from $sender to $receiver.
     *
     * @throws LogicException if the sender and receiver are the same user.
     * @throws LogicException if the two users are not accepted friends.
     */
    public function send(User $sender, User $receiver, string $body): Message
    {
        if ($sender->id === $receiver->id) {
            throw new LogicException('Cannot send a message to yourself.');
        }

        if (! $this->friendRepository->areFriends($sender, $receiver)) {
            throw new LogicException('You can only send messages to accepted friends.');
        }

        return Message::create([
            'sender_id' => $sender->id,
            'receiver_id' => $receiver->id,
            'body' => $body,
        ]);
    }
}
