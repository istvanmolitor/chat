<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Public auth routes
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Email verification (no auth required – link comes from email)
Route::get('/email/verify/{id}/{hash}', function (Request $request, string $id, string $hash) {
    $user = \App\Models\User::findOrFail($id);

    if (! hash_equals(sha1($user->getEmailForVerification()), $hash)) {
        return response()->json(['message' => 'Invalid verification link.'], 403);
    }

    if (! $user->hasVerifiedEmail()) {
        $user->markEmailAsVerified();
        event(new \Illuminate\Auth\Events\Verified($user));
    }

    return response()->json(['message' => 'Email verified successfully.']);
})->middleware('signed')->name('verification.verify');

// Protected routes
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', [AuthController::class, 'user']);
    Route::get('/users/active', [UserController::class, 'activeUsers']);
    Route::get('/users/active/paginated', [UserController::class, 'activeUsersPaginated']);
    Route::get('/users/{id}', [UserController::class, 'profile']);

    // Resend verification email
    Route::post('/email/verification-notification', function (Request $request) {
        if ($request->user()->hasVerifiedEmail()) {
            return response()->json(['message' => 'Email already verified.'], 200);
        }

        $request->user()->sendEmailVerificationNotification();

        return response()->json(['message' => 'Verification link sent.']);
    })->middleware('throttle:6,1')->name('verification.send');
});

