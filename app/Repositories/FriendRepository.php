<?php

namespace App\Repositories;

use App\Models\Friendship;
use App\Models\User;
use App\Notifications\FriendRequestReceivedNotification;
use App\Repositories\Contracts\FriendRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use LogicException;
use RuntimeException;

class FriendRepository implements FriendRepositoryInterface
{
    /**
     * Send a friend request from $sender to $recipient.
     *
     * Both users must be email-verified and currently active.
     *
     * @throws RuntimeException if either user is not verified or not active.
     * @throws LogicException if the users are the same or a request already exists.
     */
    public function sendRequest(User $sender, User $recipient): Friendship
    {
        if ($sender->id === $recipient->id) {
            throw new LogicException('A user cannot send a friend request to themselves.');
        }

        if (! $sender->hasVerifiedEmail() || ! $sender->isActive()) {
            throw new RuntimeException('The sender must be verified and active to send a friend request.');
        }

        if (! $recipient->hasVerifiedEmail() || ! $recipient->isActive()) {
            throw new RuntimeException('The recipient must be verified and active to receive a friend request.');
        }

        $existing = Friendship::between($sender->id, $recipient->id)->first();

        if ($existing) {
            throw new LogicException('A friendship or request already exists between these users.');
        }

        $friendship = Friendship::create([
            'user_id' => $sender->id,
            'friend_id' => $recipient->id,
            'status' => 'pending',
        ]);

        $recipient->notify(new FriendRequestReceivedNotification($sender));

        return $friendship;
    }

    /**
     * Accept a pending friend request.
     *
     * Only the recipient of the request ($acceptor must be friend_id) may accept it.
     * The acceptor must also be currently active.
     *
     * @throws RuntimeException if the acceptor is not active or is not the intended recipient.
     */
    public function acceptRequest(Friendship $friendship, User $acceptor): Friendship
    {
        if ($friendship->friend_id !== $acceptor->id) {
            throw new RuntimeException('Only the recipient of the friend request can accept it.');
        }

        if (! $acceptor->isActive()) {
            throw new RuntimeException('The user must be active to accept a friend request.');
        }

        $friendship->status = 'accepted';
        $friendship->save();

        return $friendship;
    }

    /**
     * Decline a pending friend request.
     *
     * Only the recipient of the request ($decliner must be friend_id) may decline it.
     *
     * @throws RuntimeException if the decliner is not the intended recipient.
     */
    public function declineRequest(Friendship $friendship, User $decliner): Friendship
    {
        if ($friendship->friend_id !== $decliner->id) {
            throw new RuntimeException('Only the recipient of the friend request can decline it.');
        }

        $friendship->status = 'declined';
        $friendship->save();

        return $friendship;
    }

    /**
     * Remove an existing friendship between two users (either direction).
     */
    public function removeFriend(User $user, User $friend): void
    {
        Friendship::between($user->id, $friend->id)->delete();
    }

    /**
     * Return paginated accepted friends of the given user, optionally filtered by name.
     *
     * @return LengthAwarePaginator<User>
     */
    public function getFriends(User $user, ?string $search = null, int $perPage = 10): LengthAwarePaginator
    {
        $friendIds = Friendship::where(function ($q) use ($user) {
            $q->where('user_id', $user->id)->orWhere('friend_id', $user->id);
        })
            ->accepted()
            ->get()
            ->map(fn ($f) => $f->user_id === $user->id ? $f->friend_id : $f->user_id);

        $query = User::whereIn('id', $friendIds)
            ->orderBy('name');

        if ($search) {
            $query->where('name', 'like', '%'.$search.'%');
        }

        return $query->paginate($perPage);
    }

    /**
     * Return all incoming pending friend requests for the given user.
     *
     * @return Collection<int, Friendship>
     */
    public function getPendingRequests(User $user): Collection
    {
        return $user->receivedFriendRequests()
            ->pending()
            ->with('user')
            ->get();
    }

    /**
     * Determine whether two users have an accepted friendship.
     */
    public function areFriends(User $user, User $other): bool
    {
        return Friendship::between($user->id, $other->id)
            ->accepted()
            ->exists();
    }

    /**
     * Find a pending friendship record between two users (either direction).
     */
    public function findRequest(User $userA, User $userB): ?Friendship
    {
        return Friendship::between($userA->id, $userB->id)
            ->pending()
            ->first();
    }
}
