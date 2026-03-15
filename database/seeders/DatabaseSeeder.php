<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::factory(70)->create();
        User::factory(30)->active()->create();

        User::updateOrCreate([
            'email' => 'test@example.com',
        ], [
            'name' => 'Test User',
            'email_verified_at' => now(),
            'password' => bcrypt('password'),
            'last_active_at' => now(),
        ]);

        $this->call(FriendshipSeeder::class);
        $this->call(ConversationSeeder::class);
    }
}
