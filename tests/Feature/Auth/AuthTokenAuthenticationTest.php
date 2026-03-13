<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\PersonalAccessToken;
use Tests\TestCase;

class AuthTokenAuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_returns_api_token_and_token_authenticates_requests(): void
    {
        $user = User::factory()->create([
            'password' => 'password',
        ]);

        $loginResponse = $this->postJson('/api/login', [
            'email' => $user->email,
            'password' => 'password',
            'remember' => true,
        ]);

        $loginResponse
            ->assertOk()
            ->assertJsonStructure(['message', 'user', 'token']);

        $token = $loginResponse->json('token');

        $this->withToken($token)
            ->withHeader('Origin', 'http://external.test')
            ->getJson('/api/user')
            ->assertOk()
            ->assertJsonPath('data.id', $user->id);
    }

    public function test_logout_revokes_the_current_access_token(): void
    {
        $user = User::factory()->create([
            'password' => 'password',
        ]);

        $loginResponse = $this->postJson('/api/login', [
            'email' => $user->email,
            'password' => 'password',
            'remember' => true,
        ]);

        $token = $loginResponse->json('token');

        $this->withToken($token)
            ->postJson('/api/logout')
            ->assertOk();

        $this->assertNull(PersonalAccessToken::findToken($token));

    }
}
