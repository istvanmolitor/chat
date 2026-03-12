<?php

namespace Database\Seeders;

use App\Models\Friendship;
use App\Models\Message;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class ConversationSeeder extends Seeder
{
    use WithoutModelEvents;

    private array $messages = [
        'Hey, how are you?',
        'What\'s up?',
        'Did you see the game last night?',
        'Are you free this weekend?',
        'I was just thinking about you!',
        'Long time no talk!',
        'How\'s everything going?',
        'Can we catch up sometime?',
        'Just wanted to say hi!',
        'Have you tried that new place downtown?',
        'Let me know when you\'re free.',
        'Miss hanging out with you.',
        'Hope you\'re doing well!',
        'We should grab coffee soon.',
        'What are you up to?',
        'Did you get my last message?',
        'That sounds awesome!',
        'Count me in!',
        'Sure, I\'d love that.',
        'Sounds like a plan!',
        'Definitely, let\'s do it.',
        'I\'ll check my schedule.',
        'Can\'t wait!',
        'That\'s great news!',
        'Let me know the details.',
    ];

    /**
     * Seed conversations between the test user and their friends.
     */
    public function run(): void
    {
        $testUser = User::where('email', 'test@example.com')->firstOrFail();

        $friendIds = Friendship::where('user_id', $testUser->id)
            ->where('status', 'accepted')
            ->pluck('friend_id');

        $friends = User::whereIn('id', $friendIds)->get();

        foreach ($friends as $friend) {
            $messageCount = rand(5, 15);
            $timestamp = Carbon::now()->subDays(rand(1, 30));

            for ($i = 0; $i < $messageCount; $i++) {
                $isTestUserSending = (bool) rand(0, 1);

                $senderId   = $isTestUserSending ? $testUser->id : $friend->id;
                $receiverId = $isTestUserSending ? $friend->id   : $testUser->id;

                $timestamp = $timestamp->addMinutes(rand(1, 120));
                $isRead = $timestamp->lt(Carbon::now()->subMinutes(5));

                Message::create([
                    'sender_id'   => $senderId,
                    'receiver_id' => $receiverId,
                    'body'        => $this->messages[array_rand($this->messages)],
                    'read_at'     => $isRead ? $timestamp->copy()->addMinutes(rand(1, 30)) : null,
                    'created_at'  => $timestamp,
                    'updated_at'  => $timestamp,
                ]);
            }
        }
    }
}

