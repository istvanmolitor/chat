<?php

namespace Tests\Feature\Message;

use App\Models\Message;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UnreadMessagesTest extends TestCase
{
    use RefreshDatabase;

    public function test_unread_endpoint_returns_only_unread_messages_for_authenticated_user(): void
    {
        $sender = User::factory()->create();
        $receiver = User::factory()->create();

        $firstUnread = Message::create([
            'sender_id' => $sender->id,
            'receiver_id' => $receiver->id,
            'body' => 'First unread',
        ]);

        $secondUnread = Message::create([
            'sender_id' => $sender->id,
            'receiver_id' => $receiver->id,
            'body' => 'Second unread',
        ]);

        $alreadyRead = Message::create([
            'sender_id' => $sender->id,
            'receiver_id' => $receiver->id,
            'body' => 'Already read',
            'read_at' => now(),
        ]);

        $wrongDirection = Message::create([
            'sender_id' => $receiver->id,
            'receiver_id' => $sender->id,
            'body' => 'Wrong direction',
        ]);

        $response = $this->actingAs($receiver)->getJson('/api/messages/'.$sender->id.'/unread');

        $response
            ->assertOk()
            ->assertJsonCount(2, 'data')
            ->assertJsonPath('data.0.id', $firstUnread->id)
            ->assertJsonPath('data.1.id', $secondUnread->id);

        $this->assertDatabaseHas('messages', [
            'id' => $firstUnread->id,
            'sender_id' => $sender->id,
            'receiver_id' => $receiver->id,
        ]);

        $this->assertDatabaseMissing('messages', [
            'id' => $firstUnread->id,
            'read_at' => null,
        ]);

        $this->assertDatabaseMissing('messages', [
            'id' => $secondUnread->id,
            'read_at' => null,
        ]);

        $this->assertDatabaseHas('messages', [
            'id' => $alreadyRead->id,
            'read_at' => $alreadyRead->read_at,
        ]);

        $this->assertDatabaseHas('messages', [
            'id' => $wrongDirection->id,
            'read_at' => null,
        ]);
    }

    public function test_unread_endpoint_does_not_return_messages_after_they_are_marked_as_read(): void
    {
        $sender = User::factory()->create();
        $receiver = User::factory()->create();

        Message::create([
            'sender_id' => $sender->id,
            'receiver_id' => $receiver->id,
            'body' => 'Unread',
        ]);

        $this->actingAs($receiver)->getJson('/api/messages/'.$sender->id.'/unread')->assertOk();

        $secondResponse = $this->actingAs($receiver)->getJson('/api/messages/'.$sender->id.'/unread');

        $secondResponse
            ->assertOk()
            ->assertJsonCount(0, 'data');
    }
}
