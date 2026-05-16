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
        'full_name' => 'Иван Иванов', 
        'email' => 'test_user@example.com',
        'phone' => '+79991234567',    
        'password' => 'Password123!', 
        'password_confirmation' => 'Password123!',
    ]);

    $response->assertRedirect(route('login'));
    $this->assertDatabaseHas('users', [
        'email' => 'test_user@example.com'
    ]);
}
}