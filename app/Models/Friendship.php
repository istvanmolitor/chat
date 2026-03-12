<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Friendship extends Model
{
    protected $fillable = [
        'user_id',
        'friend_id',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'user_id'    => 'integer',
            'friend_id'  => 'integer',
        ];
    }

    // -------------------------------------------------------------------------
    // Relations
    // -------------------------------------------------------------------------

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function friend(): BelongsTo
    {
        return $this->belongsTo(User::class, 'friend_id');
    }

    // -------------------------------------------------------------------------
    // Scopes
    // -------------------------------------------------------------------------

    public function scopeAccepted(Builder $query): Builder
    {
        return $query->where('status', 'accepted');
    }

    public function scopePending(Builder $query): Builder
    {
        return $query->where('status', 'pending');
    }

    /**
     * Return the accepted friendship record between two users (either direction).
     */
    public static function between(int $userA, int $userB): Builder
    {
        return static::query()
            ->where(function (Builder $q) use ($userA, $userB) {
                $q->where('user_id', $userA)->where('friend_id', $userB);
            })
            ->orWhere(function (Builder $q) use ($userA, $userB) {
                $q->where('user_id', $userB)->where('friend_id', $userA);
            });
    }
}

