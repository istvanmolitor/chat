<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserResource;
use App\Repositories\UserRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class UserController extends Controller
{
    public function __construct(private readonly UserRepository $userRepository) {}

    /**
     * Return a paginated list of currently active users.
     */
    public function activeUsers(Request $request): ResourceCollection
    {
        $perPage = min((int) $request->query('per_page', 10), 100);
        $search = $request->query('search') ?: null;
        $paginator = $this->userRepository->activeUsers($perPage, $search);

        return UserResource::collection($paginator);
    }

    /**
     * Return public profile data for a single user.
     */
    public function profile(int $id): UserResource|JsonResponse
    {
        $user = $this->userRepository->find($id);

        if (! $user) {
            return response()->json(['message' => 'User not found.'], 404);
        }

        return new UserResource($user);
    }
}

