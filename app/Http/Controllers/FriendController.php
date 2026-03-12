<?php

namespace App\Http\Controllers;

use App\Models\Friendship;
use App\Repositories\FriendRepository;
use App\Repositories\UserRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use LogicException;
use RuntimeException;

class FriendController extends Controller
{
    public function __construct(
        private readonly FriendRepository $friendRepository,
        private readonly UserRepository $userRepository,
    ) {}

    /**
     * Return the friendship status between the authenticated user and the given user.
     * Possible statuses: 'none' | 'pending_sent' | 'pending_received' | 'accepted'
     */
    public function status(Request $request, int $id): JsonResponse
    {
        $me = $request->user();

        if ($me->id === $id) {
            return response()->json(['status' => 'self']);
        }

        $friendship = Friendship::between($me->id, $id)->first();

        if (! $friendship) {
            return response()->json(['status' => 'none']);
        }

        if ($friendship->status === 'accepted') {
            return response()->json(['status' => 'accepted', 'friendship_id' => $friendship->id]);
        }

        if ($friendship->status === 'pending') {
            $statusLabel = $friendship->user_id === $me->id ? 'pending_sent' : 'pending_received';
            return response()->json([
                'status'        => $statusLabel,
                'friendship_id' => $friendship->id,
            ]);
        }

        return response()->json(['status' => $friendship->status, 'friendship_id' => $friendship->id]);
    }

    /**
     * Send a friend request to the given user.
     */
    public function sendRequest(Request $request, int $id): JsonResponse
    {
        $sender = $request->user();
        $recipient = $this->userRepository->find($id);

        if (! $recipient) {
            return response()->json(['message' => 'User not found.'], 404);
        }

        try {
            $friendship = $this->friendRepository->sendRequest($sender, $recipient);
        } catch (LogicException $e) {
            return response()->json(['message' => $e->getMessage()], 409);
        } catch (RuntimeException $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }

        return response()->json([
            'message'       => 'Friend request sent.',
            'friendship_id' => $friendship->id,
            'status'        => 'pending_sent',
        ], 201);
    }

    /**
     * Accept a pending friend request (only the recipient can accept).
     */
    public function acceptRequest(Request $request, int $friendshipId): JsonResponse
    {
        $friendship = Friendship::find($friendshipId);

        if (! $friendship) {
            return response()->json(['message' => 'Friendship not found.'], 404);
        }

        try {
            $this->friendRepository->acceptRequest($friendship, $request->user());
        } catch (RuntimeException $e) {
            return response()->json(['message' => $e->getMessage()], 403);
        }

        return response()->json(['message' => 'Friend request accepted.', 'status' => 'accepted']);
    }

    /**
     * Decline a pending friend request (only the recipient can decline).
     */
    public function declineRequest(Request $request, int $friendshipId): JsonResponse
    {
        $friendship = Friendship::find($friendshipId);

        if (! $friendship) {
            return response()->json(['message' => 'Friendship not found.'], 404);
        }

        try {
            $this->friendRepository->declineRequest($friendship, $request->user());
        } catch (RuntimeException $e) {
            return response()->json(['message' => $e->getMessage()], 403);
        }

        return response()->json(['message' => 'Friend request declined.', 'status' => 'declined']);
    }

    /**
     * Remove an existing friendship or cancel a sent request.
     */
    public function remove(Request $request, int $id): JsonResponse
    {
        $me = $request->user();
        $other = $this->userRepository->find($id);

        if (! $other) {
            return response()->json(['message' => 'User not found.'], 404);
        }

        $this->friendRepository->removeFriend($me, $other);

        return response()->json(['message' => 'Friendship removed.', 'status' => 'none']);
    }

    /**
     * Return all accepted friends of the authenticated user.
     */
    public function friends(Request $request): JsonResponse
    {
        $friends = $this->friendRepository->getFriends($request->user());

        return response()->json($friends->map(fn ($u) => [
            'id'             => $u->id,
            'name'           => $u->name,
            'email'          => $u->email,
            'last_active_at' => $u->last_active_at?->toIso8601String(),
            'is_active'      => $u->isActive(),
        ])->values());
    }

    /**
     * Return paginated accepted friends of the authenticated user, with optional name search.
     */
    public function friendsPaginated(Request $request): JsonResponse
    {
        $search = $request->query('search');
        $perPage = (int) $request->query('per_page', 10);

        $paginator = $this->friendRepository->getFriendsPaginated($request->user(), $search ?: null, $perPage);

        return response()->json([
            'data'          => collect($paginator->items())->map(fn ($u) => [
                'id'             => $u->id,
                'name'           => $u->name,
                'email'          => $u->email,
                'last_active_at' => $u->last_active_at?->toIso8601String(),
                'is_active'      => $u->isActive(),
            ])->values(),
            'current_page'  => $paginator->currentPage(),
            'last_page'     => $paginator->lastPage(),
            'per_page'      => $paginator->perPage(),
            'total'         => $paginator->total(),
        ]);
    }

    /**
     * Return all incoming pending friend requests for the authenticated user.
     */
    public function pendingRequests(Request $request): JsonResponse
    {
        $requests = $this->friendRepository->getPendingRequests($request->user());

        return response()->json($requests->map(fn ($f) => [
            'friendship_id' => $f->id,
            'user'          => [
                'id'    => $f->user->id,
                'name'  => $f->user->name,
                'email' => $f->user->email,
            ],
            'created_at' => $f->created_at->toIso8601String(),
        ])->values());
    }
}

