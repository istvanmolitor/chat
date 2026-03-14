<?php

namespace Tests\Feature;

use App\Models\Friendship;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SendMessageCommandTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_can_send_a_message_via_command(): void
    {
        $sender = User::factory()->create(['email' => 'sender@example.com']);
        $receiver = User::factory()->create(['email' => 'receiver@example.com']);
        $body = 'Hello world!';

        // A MessageRepository ellenőrzi a barátságot, ezért létre kell hozni
        Friendship::create([
            'user_id' => $sender->id,
            'friend_id' => $receiver->id,
            'status' => 'accepted',
        ]);

        $this->artisan("app:send-message {$sender->email} {$receiver->email} '{$body}'")
            ->expectsOutput("Üzenet sikeresen elküldve tőle: {$sender->email}, neki: {$receiver->email}")
            ->assertSuccessful();

        $this->assertDatabaseHas('messages', [
            'sender_id' => $sender->id,
            'receiver_id' => $receiver->id,
            'body' => $body,
        ]);
    }

    public function test_it_fails_if_sender_not_found(): void
    {
        $receiver = User::factory()->create(['email' => 'receiver@example.com']);

        $this->artisan("app:send-message non-existent@example.com {$receiver->email} 'Hello'")
            ->expectsOutput('A küldő nem található: non-existent@example.com')
            ->assertFailed();
    }

    public function test_it_fails_if_receiver_not_found(): void
    {
        $sender = User::factory()->create(['email' => 'sender@example.com']);

        $this->artisan("app:send-message {$sender->email} non-existent@example.com 'Hello'")
            ->expectsOutput('A címzett nem található: non-existent@example.com')
            ->assertFailed();
    }

    public function test_it_fails_if_not_friends(): void
    {
        $sender = User::factory()->create(['email' => 'sender@example.com']);
        $receiver = User::factory()->create(['email' => 'receiver@example.com']);

        // Nincs barátság létrehozva

        $this->artisan("app:send-message {$sender->email} {$receiver->email} 'Hello'")
            ->expectsOutput('You can only send messages to accepted friends.')
            ->assertFailed();
    }
}
