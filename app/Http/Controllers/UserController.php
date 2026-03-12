<?php

namespace App\Http\Controllers;

use App\Repositories\UserRepository;
use Illuminate\Http\JsonResponse;

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
    public function activeUsersPaginated(\Illuminate\Http\Request $request): JsonResponse
    {
        $perPage = min((int) $request->query('per_page', 10), 100);
        $paginator = $this->userRepository->getActiveUsersPaginated($perPage);

        return response()->json($paginator);
    }
}

