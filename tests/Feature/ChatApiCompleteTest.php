<?php

namespace Tests\Feature;

use App\Models\Friendship;
use App\Models\User;
use App\Notifications\VerifyEmail;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class ChatApiCompleteTest extends TestCase
{
    use RefreshDatabase;

    /**
     * 1. Felhasználói regisztráció és emailes megerősítés.
     */
    public function test_user_can_register_and_receive_verification_email(): void
    {
        Notification::fake();

        $response = $this->postJson('/api/register', [
            'name' => 'Teszt Elek',
            'email' => 'teszt@example.com',
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
        ]);

        $response->assertCreated();
        $this->assertDatabaseHas('users', ['email' => 'teszt@example.com', 'email_verified_at' => null]);

        $user = User::where('email', 'teszt@example.com')->first();
        Notification::assertSentTo($user, VerifyEmail::class);
    }

    /**
     * 2. Felhasználók listázása (lapozható és szűrhető).
     */
    public function test_users_can_be_listed_with_pagination_and_search(): void
    {
        // 1 perccel ezelőtt aktívak, tehát bőven a 3 perces küszöbön belül
        $now = now();
        User::factory()->count(15)->create([
            'name' => 'Kovács János',
            'last_active_at' => $now->copy()->subMinute(),
        ]);
        User::factory()->count(5)->create([
            'name' => 'Nagy Péter',
            'last_active_at' => $now->copy()->subMinute(),
        ]);

        $user = User::factory()->active()->create();
        $this->actingAs($user);

        // Szűrés névre (api/users/active)
        $response = $this->getJson('/api/users/active?search=Kovács&per_page=20');
        $response->assertOk()
            ->assertJsonCount(15, 'data');

        // Lapozás
        $response = $this->getJson('/api/users/active?per_page=10');
        $response->assertOk()
            ->assertJsonCount(10, 'data')
            ->assertJsonPath('meta.total', 20); // 15 + 5
    }

    /**
     * 3. Ismerősnek jelölés csak aktív felhasználók között.
     */
    public function test_friend_request_requires_active_recipient(): void
    {
        $now = now();
        $sender = User::factory()->create(['last_active_at' => $now]);
        // 10 perce nem aktív (küszöb 3 perc)
        $inactiveRecipient = User::factory()->create(['last_active_at' => $now->copy()->subMinutes(10)]);
        // Aktívan tartjuk
        $activeRecipient = User::factory()->create(['last_active_at' => $now]);

        $this->actingAs($sender);

        // Inaktív felhasználó jelölése (sikertelen)
        $response = $this->postJson('/api/friends/request/'.$inactiveRecipient->id);
        $response->assertStatus(422)
            ->assertJsonPath('message', 'The recipient must be verified and active to receive a friend request.');

        // Aktív felhasználó jelölése (sikeres)
        $response = $this->postJson('/api/friends/request/'.$activeRecipient->id);
        $response->assertCreated();
    }

    /**
     * 4. Üzenetküldés csak ismerősök között.
     */
    public function test_messages_can_only_be_sent_between_friends(): void
    {
        $user1 = User::factory()->active()->create();
        $user2 = User::factory()->active()->create();
        $stranger = User::factory()->active()->create();

        // Ismerősök lesznek
        Friendship::create(['user_id' => $user1->id, 'friend_id' => $user2->id, 'status' => 'accepted']);
        Friendship::create(['user_id' => $user2->id, 'friend_id' => $user1->id, 'status' => 'accepted']);

        $this->actingAs($user1);

        // Üzenet ismerősnek (POST api/messages/{user})
        $response = $this->postJson("/api/messages/{$user2->id}", ['body' => 'Szia barátom!']);
        $response->assertCreated();

        // Üzenet idegennek
        $response = $this->postJson("/api/messages/{$stranger->id}", ['body' => 'Szia idegen!']);
        $response->assertStatus(422)
            ->assertJsonPath('message', 'You can only send messages to accepted friends.');
    }
}
