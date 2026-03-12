<?php

namespace Database\Seeders;

use App\Models\Friendship;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class FriendshipSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed 5 accepted friendships for the test user.
     */
    public function run(): void
    {
        $testUser = User::where('email', 'test@example.com')->firstOrFail();

        $friends = User::where('id', '!=', $testUser->id)
            ->inRandomOrder()
            ->limit(5)
            ->get();

        foreach ($friends as $friend) {
            Friendship::firstOrCreate(
                ['user_id' => $testUser->id, 'friend_id' => $friend->id],
                ['status' => 'accepted']
            );

            Friendship::firstOrCreate(
                ['user_id' => $friend->id, 'friend_id' => $testUser->id],
                ['status' => 'accepted']
            );
        }
    }
}

