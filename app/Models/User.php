<?php

namespace App\Models;

use App\Notifications\VerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<UserFactory> */
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'last_active_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'last_active_at' => 'datetime',
        ];
    }

    /**
     * Determine if the user was active within the last ACTIVE_THRESHOLD_MINUTES minutes.
     */
    public function isActive(): bool
    {
        return $this->last_active_at !== null
            && $this->last_active_at->greaterThan(now()->subMinutes(config('app.user_active_threshold_minutes')));
    }

    /**
     * Send the email verification notification using the SPA-aware notification.
     */
    public function sendEmailVerificationNotification(): void
    {
        $this->notify(new VerifyEmail);
    }

    // -------------------------------------------------------------------------
    // Friendship relations
    // -------------------------------------------------------------------------

    /**
     * Friendship records where this user sent the request.
     *
     * @return HasMany<Friendship>
     */
    public function sentFriendRequests(): HasMany
    {
        return $this->hasMany(Friendship::class, 'user_id');
    }

    /**
     * Friendship records where this user received the request.
     *
     * @return HasMany<Friendship>
     */
    public function receivedFriendRequests(): HasMany
    {
        return $this->hasMany(Friendship::class, 'friend_id');
    }

    /**
     * Return all accepted friends (both directions).
     *
     * @return Collection<int, User>
     */
    public function friends(): Collection
    {
        $sent = $this->sentFriendRequests()
            ->accepted()
            ->with('friend')
            ->get()
            ->pluck('friend');

        $received = $this->receivedFriendRequests()
            ->accepted()
            ->with('user')
            ->get()
            ->pluck('user');

        return $sent->merge($received)->values();
    }
}
