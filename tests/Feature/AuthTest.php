<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_auth_pages_are_accessible()
    {
        $this->get('/login')->assertStatus(200)->assertViewIs('login');
        $this->get('/register')->assertStatus(200)->assertViewIs('register');
    }

    public function test_new_users_can_register()
    {
        // Для MySQL используем уникальный email, чтобы не пересекаться с другими тестами
        $email = 'new_ivan'.uniqid().'@example.com';

        $response = $this->post('/register', [
            'full_name' => 'Иван Иванов',
            'email' => $email,
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
            'phone' => '+7999'.rand(1000000, 9999999), // Рандомный телефон для уникальности
        ]);

        $response->assertRedirect(route('login'));
        $this->assertDatabaseHas('users', [
            'email' => $email,
            'full_name' => 'Иван Иванов',
        ]);
    }

    public function test_registration_fails_with_invalid_phone()
    {
        $response = $this->post('/register', [
            'full_name' => 'Иван',
            'email' => 'not-an-email',
            'password' => '123',
            'phone' => 'abc',
        ]);

        $response->assertSessionHasErrors(['phone', 'email', 'password']);
    }

    public function test_users_can_authenticate()
    {
        $user = User::create([
            'full_name' => 'Тестовый Юзер',
            'email' => 'auth_test@test.ru',
            'password' => Hash::make('Password123!'),
            'phone' => '89991112233',
            'role' => 'visitor',
        ]);

        $response = $this->post('/login', [
            'email' => 'auth_test@test.ru',
            'password' => 'Password123!',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect(route('home'));
    }

    public function test_users_can_not_authenticate_with_invalid_password()
    {
        $user = User::create([
            'full_name' => 'Неудачный Вход',
            'email' => 'wrong_pass@test.ru',
            'password' => Hash::make('Password123!'),
            'phone' => '89991110000',
        ]);

        $this->post('/login', [
            'email' => 'wrong_pass@test.ru',
            'password' => 'wrong-password',
        ]);

        $this->assertGuest();
    }

    public function test_users_can_logout()
    {
        $user = User::create([
            'full_name' => 'Выходящий Юзер',
            'email' => 'logout@test.ru',
            'password' => Hash::make('Password123!'),
            'phone' => '89991115566',
        ]);

        $response = $this->actingAs($user)->post('/logout');

        $this->assertGuest();
        $response->assertRedirect('/');
    }
}
