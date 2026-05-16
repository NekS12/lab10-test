<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email', 'max:255'],
            'password' => ['required', 'string', 'min:8'],
        ], [
            'email.required' => 'Пожалуйста, введите email',
            'email.email' => 'Введите корректный email адрес',
            'email.max' => 'Email не может быть длиннее 255 символов',
            'password.required' => 'Пожалуйста, введите пароль',
            'password.min' => 'Пароль должен содержать минимум 8 символов',
        ]);

        if (Auth::attempt($credentials, $request->filled('remember'))) {
            $request->session()->regenerate();

            $user = Auth::user();
            if ($user->isInstructor()) {
                return redirect()->intended(route('cabinet.index'))
                    ->with('message', 'Добро пожаловать в личный кабинет, '.$user->full_name.'!');
            }

            return redirect()->intended(route('home'))
                ->with('message', 'Добро пожаловать, '.$user->full_name.'!');
        }

        throw ValidationException::withMessages([
            'email' => 'Неверный email или пароль.',
        ]);
    }

    public function showRegister()
    {
        return view('register');
    }

    public function register(Request $request)
    {
        $validated = $request->validate([
            'full_name' => [
                'required',
                'string',
                'max:255',
                'regex:/^[а-яА-ЯёЁa-zA-Z\s\-]+$/u',
            ],
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                'unique:users,email',
                'regex:/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/',
            ],
            'password' => [
                'required',
                'confirmed',
                Password::min(8)
                    ->letters()
                    ->mixedCase()
                    ->numbers()
                    ->symbols(),
            ],
            'phone' => [
                'required',
                'string',
                'regex:/^[\+]?[0-9]{1,4}?[\s\-]?\(?[0-9]{3}\)?[\s\-]?[0-9]{3}[\s\-]?[0-9]{4,6}$/',
                'unique:users,phone',
            ],
        ], [
            // ФИО
            'full_name.required' => 'Поле ФИО обязательно для заполнения.',
            'full_name.regex' => 'ФИО может содержать только буквы, пробелы и дефисы.',
            'full_name.max' => 'ФИО не может быть длиннее 255 символов.',

            // Email
            'email.required' => 'Поле Email обязательно для заполнения.',
            'email.email' => 'Введите корректный email адрес (например: user@example.com).',
            'email.regex' => 'Email должен быть в формате example@domain.ru',
            'email.unique' => 'Пользователь с таким email уже зарегистрирован.',
            'email.max' => 'Email не может быть длиннее 255 символов.',

            // Пароль
            'password.required' => 'Поле Пароль обязательно для заполнения.',
            'password.confirmed' => 'Подтверждение пароля не совпадает с паролем.',
            'password.min' => 'Пароль должен содержать минимум 8 символов.',
            'password.letters' => 'Пароль должен содержать хотя бы одну букву.',
            'password.mixed' => 'Пароль должен содержать как строчные, так и прописные буквы.',
            'password.numbers' => 'Пароль должен содержать хотя бы одну цифру.',
            'password.symbols' => 'Пароль должен содержать хотя бы один специальный символ (@, #, $, %, !, и т.д.).',

            // Телефон
            'phone.required' => 'Поле Номер телефона обязательно для заполнения.',
            'phone.regex' => 'Номер телефона должен быть в формате +79********* или 89*********.',
            'phone.unique' => 'Пользователь с таким номером телефона уже зарегистрирован.',
        ]);

        $user = User::create([
            'full_name' => trim($validated['full_name']),
            'email' => strtolower(trim($validated['email'])),
            'password' => bcrypt($validated['password']),
            'phone' => preg_replace('/[^0-9+]/', '', $validated['phone']),
            'role' => 'visitor',
        ]);

        return redirect()->route('login')
            ->with('success', 'Регистрация успешна!.');
    }

    public function logout(Request $request)
    {
        $userName = Auth::user()->full_name ?? 'Пользователь';

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/')->with('message', 'До свидания, '.$userName.'! Вы вышли из системы.');
    }
}
