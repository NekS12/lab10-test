<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\MasterClass;
use App\Models\CreativityType;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Carbon\Carbon;

class InstructorTest extends TestCase
{
    use RefreshDatabase;

    protected $instructor;
    protected $type;

    protected function setUp(): void
    {
        parent::setUp();

        $this->type = CreativityType::create([
            'name' => 'Рисование',
            'description' => 'Описание для рисования',
        ]);

        $this->instructor = User::create([
            'full_name' => 'Мастер Йода',
            'email' => 'yoda@test.ru',
            'password' => bcrypt('Password123!'),
            'phone' => '+79990001122',
            'role' => 'instructor',
        ]);
    }

    public function test_instructor_can_access_cabinet()
    {
        $response = $this->actingAs($this->instructor)
            ->get(route('cabinet.index'));

        $response->assertStatus(200);
        $response->assertViewIs('cabinet');
    }

    public function test_instructor_can_create_master_class()
    {
        $tomorrow = Carbon::now()->addDay()->format('Y-m-d');

        $data = [
            'type_id' => $this->type->id,
            'title' => 'Новый мастер-класс',
            'description' => 'Описание мастер-класса длиной более десяти символов',
            'date' => $tomorrow,
            'start_time' => '11:00', // Убираем секунды здесь
            'max_participants' => 10,
            'price' => 500,
        ];

        $response = $this->actingAs($this->instructor)
            ->post(route('cabinet.store'), $data);

        // Если тест падает тут, значит в контроллере после сохранения стоит редирект не в индекс
        $response->assertStatus(302); 
        $this->assertDatabaseHas('master_classes', ['title' => 'Новый мастер-класс']);
    }

    public function test_instructor_cannot_create_more_than_three_classes_per_day()
    {
        $date = Carbon::now()->addDays(2)->format('Y-m-d');
        $allowedTimes = ['09:00', '11:00', '13:00']; // Берем первые три из ENUM

        for ($i = 0; $i < 3; $i++) {
            MasterClass::create([
                'instructor_id' => $this->instructor->id,
                'type_id' => $this->type->id,
                'title' => "МК $i",
                'description' => 'Описание мастер-класса более 10 символов',
                'date' => $date,
                'start_time' => $allowedTimes[$i], // Используем разрешенное время
                'max_participants' => 5,
                'price' => 100,
            ]);
        }

        // Пытаемся создать 4-й (на 15:00 — последнее свободное время в ENUM)
        $response = $this->actingAs($this->instructor)
            ->from(route('cabinet.create'))
            ->post(route('cabinet.store'), [
                'type_id' => $this->type->id,
                'title' => '4-й лишний',
                'description' => 'Описание для теста лимита',
                'date' => $date,
                'start_time' => '15:00', // Это время есть в ENUM, так что ошибка будет от валидатора, а не от БД
                'max_participants' => 10,
                'price' => 500,
            ]);

        $response->assertRedirect(route('cabinet.create'));
        $response->assertSessionHasErrors('date');
    }

    public function test_instructor_cannot_edit_others_master_class()
    {
        $otherInstructor = User::create([
            'full_name' => 'Другой Мастер',
            'email' => 'other_unique@test.ru',
            'password' => bcrypt('Password123!'),
            'phone' => '+7000' . rand(1111111, 9999999),
            'role' => 'instructor',
        ]);

        $otherClass = MasterClass::create([
            'instructor_id' => $otherInstructor->id,
            'type_id' => $this->type->id,
            'title' => 'Чужой МК',
            'description' => 'Описание чужого МК длиннее 10 символов',
            'date' => Carbon::now()->addDay()->format('Y-m-d'),
            'start_time' => '13:00:00',
            'max_participants' => 5,
            'price' => 1000,
        ]);

        $response = $this->actingAs($this->instructor)
            ->get(route('cabinet.edit', $otherClass->id));

        $response->assertStatus(404); 
    }
}