<?php

namespace App\Repositories;

use App\Models\User;
use App\Repositories\Contracts\UserRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class UserRepository implements UserRepositoryInterface
{
    /**
     * Return a paginated list of active users, optionally filtered by name.
     */
    public function activeUsers(int $perPage = 10, ?string $search = null): LengthAwarePaginator
    {
        return User::query()
            ->whereNotNull('last_active_at')
            ->where('last_active_at', '>=', now()->subMinutes(config('app.user_active_threshold_minutes')))
            ->when($search, fn ($q) => $q->where('name', 'like', '%' . $search . '%'))
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

