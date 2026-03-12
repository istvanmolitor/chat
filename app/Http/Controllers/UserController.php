<?php

namespace App\Http\Controllers;

use App\Repositories\UserRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function __construct(private readonly UserRepository $userRepository) {}

    /**
     * Return the list of currently active users (active within the last 3 minutes).
     */
    public function activeUsers(): JsonResponse
    {
        $users = $this->userRepository->getActiveUsers();

        return response()->json([
            'active_users' => $users,
            'count'        => $users->count(),
        ]);
    }

    /**
     * Return a paginated list of currently active users.
     */
    public function activeUsersPaginated(Request $request): JsonResponse
    {
        $perPage = min((int) $request->query('per_page', 10), 100);
        $search = $request->query('search') ?: null;
        $paginator = $this->userRepository->getActiveUsersPaginated($perPage, $search);

        return response()->json($paginator);
    }

    /**
     * Return public profile data for a single user.
     */
    public function profile(int $id): JsonResponse
    {
        $user = $this->userRepository->find($id);

        if (! $user) {
            return response()->json(['message' => 'User not found.'], 404);
        }

        return response()->json([
            'id'             => $user->id,
            'name'           => $user->name,
            'email'          => $user->email,
            'last_active_at' => $user->last_active_at?->toIso8601String(),
            'is_active'      => $user->isActive(),
        ]);
    }
}

