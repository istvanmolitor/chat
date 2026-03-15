<?php

namespace Tests\Feature\Friend;

use App\Models\User;
use App\Notifications\FriendRequestReceivedNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class FriendRequestNotificationTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Ensure recipient gets e-mail notification when receiving a friend request.
     */
    public function test_friend_request_sends_email_notification_to_recipient(): void
    {
        Notification::fake();

        $sender = User::factory()->create(['last_active_at' => now()]);
        $recipient = User::factory()->create(['last_active_at' => now()]);

        $this->actingAs($sender);

        $response = $this->postJson('/api/friends/request/'.$recipient->id);

        $response
            ->assertCreated()
            ->assertJsonPath('status', 'pending_sent');

        $this->assertDatabaseHas('friendships', [
            'user_id' => $sender->id,
            'friend_id' => $recipient->id,
            'status' => 'pending',
        ]);

        Notification::assertSentTo(
            $recipient,
            FriendRequestReceivedNotification::class,
            function (FriendRequestReceivedNotification $notification) use ($sender): bool {
                return $notification->sender->is($sender);
            }
        );
    }
}
