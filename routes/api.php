<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\FriendController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

// Public auth routes
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Email verification (no auth required – link comes from email)
Route::get('/email/verify/{id}/{hash}', [AuthController::class, 'verifyEmail'])
    ->middleware('signed')
    ->name('verification.verify');

// Protected routes
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', [AuthController::class, 'user']);
    Route::get('/users/active', [UserController::class, 'activeUsers']);
    Route::get('/users/{user}', [UserController::class, 'profile']);

    // Messages
    Route::get('/messages/{user}', [MessageController::class, 'conversation']);
    Route::get('/messages/{user}/unread', [MessageController::class, 'unread']);
    Route::post('/messages/{user}', [MessageController::class, 'send']);

    // Friendships
    Route::get('/friends', [FriendController::class, 'friends']);
    Route::get('/friends/requests', [FriendController::class, 'pendingRequests']);
    Route::get('/friends/status/{id}', [FriendController::class, 'status']);
    Route::post('/friends/request/{id}', [FriendController::class, 'sendRequest']);
    Route::post('/friends/accept/{friendshipId}', [FriendController::class, 'acceptRequest']);
    Route::post('/friends/decline/{friendshipId}', [FriendController::class, 'declineRequest']);
    Route::delete('/friends/{id}', [FriendController::class, 'remove']);

    // Resend verification email
    Route::post('/email/verification-notification', [AuthController::class, 'resendVerification'])
        ->middleware('throttle:6,1')
        ->name('verification.send');
});
