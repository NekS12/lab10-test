<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Тест отображения страниц.
     */
    public function test_auth_pages_are_accessible()
    {
        $this->get('/login')->assertStatus(200)->assertViewIs('login');
        $this->get('/register')->assertStatus(200)->assertViewIs('register');
    }

    /**
     * Тест успешной регистрации.
     * Учитываем строгие правила: ФИО, телефон, сложный пароль.
     */
    public function test_new_users_can_register()
    {
        $response = $this->post('/register', [
            'full_name' => 'Иван Иванов',
            'email' => 'ivan@example.com',
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
            'phone' => '+79991234567',
        ]);

        $response->assertRedirect(route('login'));
        $this->assertDatabaseHas('users', [
            'email' => 'ivan@example.com',
            'full_name' => 'Иван Иванов',
            'role' => 'visitor',
        ]);
    }

    /**
     * Тест валидации при регистрации (неверный формат телефона).
     */
    public function test_registration_fails_with_invalid_phone()
    {
        $response = $this->post('/register', [
            'full_name' => 'Иван',
            'email' => 'not-an-email',
            'password' => '123',
            'phone' => 'abc', // Не пройдет регулярное выражение
        ]);

        $response->assertSessionHasErrors(['phone', 'email', 'password']);
    }

    /**
     * Тест входа в систему.
     */
    public function test_users_can_authenticate()
    {
        // Создаем пользователя вручную (без фабрики)
        $user = User::create([
            'full_name' => 'Тестовый Юзер',
            'email' => 'test@test.ru',
            'password' => Hash::make('Password123!'),
            'phone' => '89991112233',
            'role' => 'visitor'
        ]);

        $response = $this->post('/login', [
            'email' => 'test@test.ru',
            'password' => 'Password123!',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect(route('home'));
    }

    /**
     * Тест входа с неверным паролем.
     */
    public function test_users_can_not_authenticate_with_invalid_password()
    {
        $user = User::create([
            'full_name' => 'Тестовый Юзер',
            'email' => 'test@test.ru',
            'password' => Hash::make('Password123!'),
            'phone' => '89991112233',
        ]);

        $this->post('/login', [
            'email' => 'test@test.ru',
            'password' => 'wrong-password',
        ]);

        $this->assertGuest();
    }

    /**
     * Тест выхода из системы.
     */
    public function test_users_can_logout()
    {
        $user = User::create([
            'full_name' => 'Юзер',
            'email' => 'user@test.ru',
            'password' => Hash::make('Password123!'),
            'phone' => '89991112244',
        ]);

        $response = $this->actingAs($user)->post('/logout');

        $this->assertGuest();
        $response->assertRedirect('/');
    }
}