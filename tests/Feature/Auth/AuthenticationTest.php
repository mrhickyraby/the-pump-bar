<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_screen_can_be_rendered(): void
    {
        $response = $this->get('/login');

        $response->assertStatus(200);
    }

    public function test_users_can_authenticate_using_the_login_screen(): void
    {
        $user = User::factory()->create();

        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect(route('pos.index', absolute: false));
    }

    public function test_cashier_login_redirects_to_pos_instead_of_intended_dashboard(): void
    {
        $cashier = User::factory()->create(['role' => 'staff']);

        $this->get('/dashboard')->assertRedirect('/login');

        $response = $this->post('/login', [
            'email' => $cashier->email,
            'password' => 'password',
        ]);

        $response->assertRedirect(route('pos.index', absolute: false));
    }

    public function test_authenticated_cashier_visiting_login_is_redirected_to_pos(): void
    {
        $cashier = User::factory()->create(['role' => 'staff']);

        $response = $this->actingAs($cashier)->get('/login');

        $response->assertRedirect(route('pos.index'));
    }

    public function test_cashier_visiting_home_is_redirected_to_pos(): void
    {
        $cashier = User::factory()->create(['role' => 'staff']);

        $response = $this->actingAs($cashier)->get('/');

        $response->assertRedirect(route('pos.index'));
    }

    public function test_owner_can_sign_in_after_cashier_logs_out(): void
    {
        $cashier = User::factory()->create(['role' => 'staff']);
        $owner = User::factory()->create(['role' => 'owner']);

        $this->post('/login', [
            'email' => $cashier->email,
            'password' => 'password',
        ])->assertRedirect(route('pos.index', absolute: false));

        $this->post('/logout')->assertRedirect('/');

        $response = $this->post('/login', [
            'email' => $owner->email,
            'password' => 'password',
        ]);

        $this->assertAuthenticatedAs($owner);
        $response->assertRedirect(route('dashboard', absolute: false));
    }

    public function test_users_can_not_authenticate_with_invalid_password(): void
    {
        $user = User::factory()->create();

        $this->post('/login', [
            'email' => $user->email,
            'password' => 'wrong-password',
        ]);

        $this->assertGuest();
    }

    public function test_users_can_logout(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/logout');

        $this->assertGuest();
        $response->assertRedirect('/');
    }
}
