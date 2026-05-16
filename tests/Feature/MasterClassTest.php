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

    public function test_can_see_home_page()
    {
        $response = $this->get('/'); 
        $response->assertStatus(200);
    }

    public function test_master_class_logic_and_attributes()
    {
        $instructor = User::create([
            'full_name' => 'Тестовый Мастер',
            'email' => 'master_logic@example.com',
            'password' => bcrypt('Password123!'),
            'phone' => '+79991112288',
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
            'description' => 'Учимся лепить горшки и другие вещи',
            'date' => '2026-06-01',
            'start_time' => '14:00:00',
            'max_participants' => 10,
            'price' => 1500
        ]);

        $this->assertEquals('Лепка из глины', $mc->title);
        $this->assertEquals(1500, $mc->price);

        // Проверка getEndTimeAttribute
        $this->assertEquals('16:00', $mc->end_time);
        
        // Проверка formatted_date_time
        $this->assertEquals('01.06.2026 14:00', $mc->formatted_date_time);
    }
}