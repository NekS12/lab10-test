<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;

class LabTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function test_home_page_is_accessible()
    {
        $response = $this->get('/');
        $response->assertStatus(200);
    }

    /** @test */
    public function test_login_page_is_accessible()
    {
        $response = $this->get('/login');
        $response->assertStatus(200);
    }

    /** @test */
    public function test_registration_page_is_accessible()
    {
        $response = $this->get('/register');
        $response->assertStatus(200);
    }

    /** @test */
    public function test_user_can_register()
    {
        $response = $this->post('/register', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('users', ['email' => 'test@example.com']);
    }
}