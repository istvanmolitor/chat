<?php

namespace App\Repositories\Contracts;

use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

interface UserRepositoryInterface
{
    /**
     * Return a paginated list of active users, optionally filtered by name.
     */
    public function activeUsers(int $perPage = 10, ?string $search = null, ?int $excludeUserId = null): LengthAwarePaginator;

    /**
     * Update the last_active_at timestamp for the given user to now.
     */
    public function updateLastActiveAt(User $user): void;

    /**
     * Find a user by their primary key.
     */
    public function find(int $id): ?User;

    /**
     * Return all users.
     *
     * @return Collection<int, User>
     */
    public function all(): Collection;
}
