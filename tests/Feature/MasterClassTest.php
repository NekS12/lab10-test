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
     * Тест главной страницы
     */
    public function test_can_see_home_page()
    {
        $response = $this->get('/'); 
        $response->assertStatus(200);
    }

    /**
     * Тест базовых атрибутов и логики времени
     */
    public function test_master_class_logic_and_attributes()
    {
        // 1. Создаем необходимые зависимости вручную
        $instructor = User::create([
            'full_name' => 'Тестовый Мастер',
            'email' => 'master@example.com',
            'password' => bcrypt('Password123!'),
            'phone' => '+79991112233',
            'role' => 'instructor'
        ]);

        $type = CreativityType::create([
            'name' => 'Лепка',
            'description' => 'Работа с глиной'
        ]);

        // 2. Создаем мастер-класс
        $mc = MasterClass::create([
            'instructor_id' => $instructor->id,
            'type_id' => $type->id,
            'title' => 'Лепка из глины',
            'description' => 'Учимся лепить горшки',
            'date' => '2026-06-01',
            'start_time' => '14:00',
            'max_participants' => 10,
            'price' => 1500
        ]);

        // 3. Проверяем атрибуты
        $this->assertEquals('Лепка из глины', $mc->title);
        $this->assertEquals(1500, $mc->price);

        // 4. Проверяем расчет времени окончания (должно быть +2 часа по ТЗ)
        // Если в модели прописано getEndTimeAttribute, это сработает:
        $this->assertEquals('16:00', $mc->end_time);
        
        // 5. Проверяем форматированную дату
        $this->assertEquals('01.06.2026 14:00', $mc->formatted_date_time);
    }
}