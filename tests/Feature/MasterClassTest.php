<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\MasterClass;
use App\Models\User;
use App\Models\CreativityType;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Carbon\Carbon;

class MasterClassTest extends TestCase
{
    use RefreshDatabase;

   /**
     * Тест базовых атрибутов и логики времени
     */
    public function test_master_class_logic_and_attributes()
    {
        $instructor = User::create([
            'full_name' => 'Тестовый Мастер',
            'email' => 'master_unique@example.com', // Используем уникальный email
            'password' => bcrypt('Password123!'),
            'phone' => '+79991112299',
            'role' => 'instructor'
        ]);

        $type = CreativityType::create([
            'name' => 'Лепка',
            'description' => 'Работа с глиной'
        ]);

        $mc = MasterClass::create([
            'instructor_id' => $instructor->id,
            'type_id' => $type->id,
            'title' => 'Лепка из глины',
            'description' => 'Учимся лепить горшки',
            'date' => '2026-06-01',
            'start_time' => '14:00', // Убираем секунды, если CHECK падает на них
            'max_participants' => 10,
            'price' => 1500
        ]);

        $this->assertEquals('Лепка из глины', $mc->title);
        $this->assertEquals(1500, $mc->price);
        $this->assertEquals('16:00', $mc->end_time);
        $this->assertEquals('01.06.2026 14:00', $mc->formatted_date_time);
    }
}