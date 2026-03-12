<?php

namespace App\Repositories;

use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

class UserRepository
{
    /**
     * Return all users who were active within the last ACTIVE_THRESHOLD_MINUTES minutes.
     *
     * @return Collection<int, User>
     */
    public function getActiveUsers(): Collection
    {
        return User::query()
            ->whereNotNull('last_active_at')
            ->where('last_active_at', '>=', now()->subMinutes(config('app.user_active_threshold_minutes')))
            ->orderByDesc('last_active_at')
            ->get();
    }

    /**
     * Return a paginated list of active users.
     */
    public function getActiveUsersPaginated(int $perPage = 10): \Illuminate\Contracts\Pagination\LengthAwarePaginator
    {
        return User::query()
            ->whereNotNull('last_active_at')
            ->where('last_active_at', '>=', now()->subMinutes(config('app.user_active_threshold_minutes')))
            ->orderByDesc('last_active_at')
            ->paginate($perPage);
    }

    /**
     * Update the last_active_at timestamp for the given user to now.
     */
    public function updateLastActiveAt(User $user): void
    {
        $user->last_active_at = now();
        $user->saveQuietly();
    }

    /**
     * Find a user by their primary key.
     */
    public function find(int $id): ?User
    {
        return User::find($id);
    }

    /**
     * Return all users.
     *
     * @return Collection<int, User>
     */
    public function all(): Collection
    {
        return User::all();
    }
}

