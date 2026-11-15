<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminAuthTest extends TestCase
{
    use RefreshDatabase;

    private function admin(array $attrs = []): User
    {
        return User::factory()->create(['email' => 'admin@example.com', 'password' => 'correct-horse-battery', 'role' => 'admin', 'is_active' => true, ...$attrs]);
    }

    public function test_login_returns_a_token_that_expires(): void
    {
        $this->admin();

        $res = $this->postJson('/api/v1/admin/login', ['email' => 'admin@example.com', 'password' => 'correct-horse-battery'])
            ->assertOk()
            ->assertJsonPath('user.role', 'admin')
            ->assertJsonStructure(['token', 'expires_at']);

        $this->withToken($res->json('token'))->getJson('/api/v1/admin/me')->assertOk()->assertJsonPath('data.email', 'admin@example.com');
        $this->assertNotNull(User::query()->first()->tokens()->first()->expires_at);
    }

    public function test_wrong_password_and_unknown_email_get_the_same_error(): void
    {
        $this->admin();

        $a = $this->postJson('/api/v1/admin/login', ['email' => 'admin@example.com', 'password' => 'nope'])->assertUnprocessable();
        $b = $this->postJson('/api/v1/admin/login', ['email' => 'ghost@example.com', 'password' => 'nope'])->assertUnprocessable();

        $this->assertSame($a->json('errors'), $b->json('errors'));
    }

    public function test_disabled_accounts_cannot_sign_in_or_use_old_tokens(): void
    {
        $user = $this->admin();
        $token = $user->createToken('t')->plainTextToken;
        $user->update(['is_active' => false]);

        $this->postJson('/api/v1/admin/login', ['email' => 'admin@example.com', 'password' => 'correct-horse-battery'])->assertUnprocessable();
        $this->withToken($token)->getJson('/api/v1/admin/me')->assertUnauthorized();
    }

    public function test_login_is_rate_limited(): void
    {
        $this->admin();

        foreach (range(1, 5) as $i) {
            $this->postJson('/api/v1/admin/login', ['email' => 'admin@example.com', 'password' => 'wrong'])->assertUnprocessable();
        }

        $this->postJson('/api/v1/admin/login', ['email' => 'admin@example.com', 'password' => 'correct-horse-battery'])->assertTooManyRequests();
    }

    public function test_logout_revokes_the_token(): void
    {
        $token = $this->admin()->createToken('t')->plainTextToken;

        $this->withToken($token)->postJson('/api/v1/admin/logout')->assertNoContent();
        $this->app['auth']->forgetGuards();
        $this->withToken($token)->getJson('/api/v1/admin/me')->assertUnauthorized();
    }

    public function test_expired_tokens_are_refused(): void
    {
        $token = $this->admin()->createToken('t', ['*'], now()->subMinute())->plainTextToken;

        $this->withToken($token)->getJson('/api/v1/admin/me')->assertUnauthorized();
    }

    public function test_admin_routes_need_a_token(): void
    {
        $this->getJson('/api/v1/admin/slides')->assertUnauthorized();
        $this->putJson('/api/v1/admin/settings', [])->assertUnauthorized();
    }
}
