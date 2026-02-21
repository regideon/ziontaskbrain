<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_can_view_landing_login_and_register_pages(): void
    {
        $this->get('/')->assertOk()->assertSee('ZionTaskAI');
        $this->get('/login')->assertOk();
        $this->get('/register')->assertOk();
    }

    public function test_guest_is_redirected_to_login_for_protected_pages(): void
    {
        $this->get('/task-board')->assertRedirect('/login');
        $this->get('/agents')->assertRedirect('/login');
        $this->get('/mcp-playground')->assertRedirect('/login');
    }

    public function test_user_can_register_and_is_redirected_to_task_board(): void
    {
        $response = $this->post('/register', [
            'name' => 'Test User',
            'email' => 'newuser@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertRedirect('/task-board');
        $this->assertAuthenticated();
        $this->assertDatabaseHas('users', ['email' => 'newuser@example.com']);
    }

    public function test_user_can_login_and_logout(): void
    {
        $user = User::factory()->create([
            'email' => 'login@example.com',
            'password' => 'password123',
        ]);

        $login = $this->post('/login', [
            'email' => 'login@example.com',
            'password' => 'password123',
        ]);

        $login->assertRedirect('/task-board');
        $this->assertAuthenticatedAs($user);

        $logout = $this->post('/logout');
        $logout->assertRedirect('/');
        $this->assertGuest();
    }

    public function test_login_redirects_to_intended_protected_page(): void
    {
        User::factory()->create([
            'email' => 'intended@example.com',
            'password' => 'password123',
        ]);

        $this->get('/agents')->assertRedirect('/login');

        $response = $this->post('/login', [
            'email' => 'intended@example.com',
            'password' => 'password123',
        ]);

        $response->assertRedirect('/agents');
    }
}
