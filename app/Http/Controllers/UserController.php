<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserResource;
use App\Models\User;
use App\Repositories\Contracts\UserRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class UserController extends Controller
{
    public function __construct(
        private readonly UserRepositoryInterface $userRepository
    ) {}

    /**
     * Return a paginated list of currently active users.
     */
    public function activeUsers(Request $request): ResourceCollection
    {
        $perPage = min((int) $request->query('per_page', 10), 100);
        $search = $request->query('search') ?: null;
        $paginator = $this->userRepository->activeUsers($perPage, $search, $request->user()?->id);

        return UserResource::collection($paginator);
    }

    /**
     * Return public profile data for a single user.
     */
    public function profile(User $user): UserResource
    {
        return new UserResource($user);
    }
}
