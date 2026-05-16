<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\InstructorController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// ========================
// 🔐 АУТЕНТИФИКАЦИЯ
// ========================

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);

Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register']);

Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// ========================
// 🏠 ПУБЛИЧНЫЕ СТРАНИЦЫ
// ========================

// Главная страница (до и после авторизации)
Route::get('/', [HomeController::class, 'index'])->name('home');

// Страница вида творчества (категория)
Route::get('/category/{id}', [CategoryController::class, 'show'])->name('category.show');

// ========================
// 📝 ЗАПИСЬ НА МАСТЕР-КЛАСС (требует авторизации)
// ========================

Route::middleware('auth')->prefix('booking')->name('booking.')->group(function () {
    // Страница подтверждения записи
    Route::get('/{id}/confirm', [BookingController::class, 'confirmPage'])->name('confirm');

    // Обработка подтверждения или отмены
    Route::post('/{id}/process', [BookingController::class, 'process'])->name('process');
});

// ========================
// 👨 ЛИЧНЫЙ КАБИНЕТ ВЕДУЩЕГО (требуется роль instructor)
// ========================

Route::middleware(['auth', 'role:instructor'])->prefix('cabinet')->name('cabinet.')->group(function () {

    // Главная страница личного кабинета (список своих МК + участники)
    Route::get('/', [InstructorController::class, 'index'])->name('index');

    // Форма создания нового мастер-класса
    Route::get('/create', [InstructorController::class, 'create'])->name('create');

    // Сохранение нового мастер-класса
    Route::post('/store', [InstructorController::class, 'store'])->name('store');

    // Форма редактирования мастер-класса (только описание и цена)
    Route::get('/{id}/edit', [InstructorController::class, 'edit'])->name('edit');

    // Обновление мастер-класса
    Route::put('/{id}/update', [InstructorController::class, 'update'])->name('update');
});
