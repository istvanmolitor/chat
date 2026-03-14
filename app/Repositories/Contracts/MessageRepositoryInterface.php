<?php

namespace App\Repositories\Contracts;

use App\Models\Message;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

interface MessageRepositoryInterface
{
    /**
     * Return all messages between two users, ordered by creation time.
     *
     * @return Collection<int, Message>
     */
    public function getConversation(User $authUser, User $other): Collection;

    /**
     * Return unread messages sent by $sender to $receiver, ordered by creation time.
     *
     * @return Collection<int, Message>
     */
    public function getUnreadMessages(User $sender, User $receiver): Collection;

    /**
     * Mark all unread messages sent by $sender to $receiver as read.
     */
    public function markAsRead(User $sender, User $receiver): void;

    /**
     * Send a new message from $sender to $receiver.
     *
     * @throws \LogicException if the sender and receiver are the same user.
     * @throws \LogicException if the two users are not accepted friends.
     */
    public function send(User $sender, User $receiver, string $body): Message;
}
