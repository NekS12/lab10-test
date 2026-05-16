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

        // Создаем тип творчества (нужен для внешнего ключа)
        $this->type = CreativityType::create([
            'name' => 'Рисование',
            'description' => 'Описание для рисования',
        ]);

        // Создаем пользователя с ролью ведущего
        $this->instructor = User::create([
            'full_name' => 'Мастер Йода',
            'email' => 'yoda@test.ru',
            'password' => bcrypt('Password123!'),
            'phone' => '+79990001122',
            'role' => 'instructor', // Важно для middleware 'role:instructor'
        ]);
    }

    /**
     * Проверка доступа к кабинету.
     */
    public function test_instructor_can_access_cabinet()
    {
        $response = $this->actingAs($this->instructor)
            ->get(route('cabinet.index'));

        $response->assertStatus(200);
        $response->assertViewIs('cabinet');
    }

    /**
     * Тест успешного создания мастер-класса.
     */
    public function test_instructor_can_create_master_class()
    {
        $tomorrow = Carbon::now()->addDay()->format('Y-m-d');

        $data = [
            'type_id' => $this->type->id,
            'title' => 'Новый мастер-класс',
            'description' => 'Описание мастер-класса длиной более десяти символов',
            'date' => $tomorrow,
            'start_time' => '16:00',
            'max_participants' => 10,
            'price' => 500,
        ];

        $response = $this->actingAs($this->instructor)
            ->post(route('cabinet.store'), $data);

        $response->assertRedirect(route('cabinet.index'));
        $this->assertDatabaseHas('master_classes', ['title' => 'Новый мастер-класс']);
    }

    /**
     * Проверка бизнес-логики: не более 3-х МК в день.
     */
    public function test_instructor_cannot_create_more_than_three_classes_per_day()
    {
        $date = Carbon::now()->addDays(2)->format('Y-m-d');

        // Создаем 3 мастер-класса вручную на одну дату
        for ($i = 0; $i < 3; $i++) {
            MasterClass::create([
                'instructor_id' => $this->instructor->id,
                'type_id' => $this->type->id,
                'title' => "МК $i",
                'description' => 'Какое-то описание',
                'date' => $date,
                'start_time' => '09:00',
                'max_participants' => 5,
                'price' => 100,
            ]);
        }

        // Пытаемся создать 4-й
        $response = $this->actingAs($this->instructor)
            ->from(route('cabinet.create'))
            ->post(route('cabinet.store'), [
                'type_id' => $this->type->id,
                'title' => '4-й лишний',
                'description' => 'Описание для теста лимита',
                'date' => $date,
                'start_time' => '15:00',
                'max_participants' => 10,
                'price' => 500,
            ]);

        $response->assertRedirect(route('cabinet.create'));
        $response->assertSessionHasErrors('date');
    }

    /**
     * Проверка защиты: нельзя редактировать чужой мастер-класс.
     */
    public function test_instructor_cannot_edit_others_master_class()
    {
        // Другой ведущий
        $otherInstructor = User::create([
            'full_name' => 'Другой Мастер',
            'email' => 'other@test.ru',
            'password' => bcrypt('Password123!'),
            'phone' => '+70000000000',
            'role' => 'instructor',
        ]);

        // Чужой МК
        $otherClass = MasterClass::create([
            'instructor_id' => $otherInstructor->id,
            'type_id' => $this->type->id,
            'title' => 'Чужой МК',
            'description' => 'Описание чужого МК',
            'date' => Carbon::now()->addDay()->format('Y-m-d'),
            'start_time' => '13:00',
            'max_participants' => 5,
            'price' => 1000,
        ]);

        // Пытаемся зайти на страницу редактирования чужого МК
        $response = $this->actingAs($this->instructor)
            ->get(route('cabinet.edit', $otherClass->id));

        $response->assertStatus(404); // Должно вернуть 404, так как используется findOrFail через scope инструктора
    }
}