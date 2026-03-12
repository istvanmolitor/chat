<?php

namespace App\Repositories\Contracts;

use App\Models\Friendship;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

interface FriendRepositoryInterface
{
    /**
     * Send a friend request from $sender to $recipient.
     *
     * @throws \RuntimeException  if either user is not verified or not active.
     * @throws \LogicException    if the users are the same or a request already exists.
     */
    public function sendRequest(User $sender, User $recipient): Friendship;

    /**
     * Accept a pending friend request.
     *
     * @throws \RuntimeException  if the acceptor is not active or is not the intended recipient.
     */
    public function acceptRequest(Friendship $friendship, User $acceptor): Friendship;

    /**
     * Decline a pending friend request.
     *
     * @throws \RuntimeException if the decliner is not the intended recipient.
     */
    public function declineRequest(Friendship $friendship, User $decliner): Friendship;

    /**
     * Remove an existing friendship between two users (either direction).
     */
    public function removeFriend(User $user, User $friend): void;

    /**
     * Return paginated accepted friends of the given user, optionally filtered by name.
     *
     * @return LengthAwarePaginator<User>
     */
    public function getFriends(User $user, ?string $search = null, int $perPage = 10): LengthAwarePaginator;

    /**
     * Return all incoming pending friend requests for the given user.
     *
     * @return Collection<int, Friendship>
     */
    public function getPendingRequests(User $user): Collection;

    /**
     * Determine whether two users have an accepted friendship.
     */
    public function areFriends(User $user, User $other): bool;

    /**
     * Find a pending friendship record between two users (either direction).
     */
    public function findRequest(User $userA, User $userB): ?Friendship;
}

