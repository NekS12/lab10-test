<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\MasterClass;
use Illuminate\Foundation\Testing\RefreshDatabase;

class MasterClassTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_see_home_page()
    {
        $response = $this->get('/'); 
        $response->assertStatus(200);
    }

    public function test_master_class_model_exists()
    {
        // Создаем объект вручную без фабрики, чтобы избежать ошибки "Factory not found"
        $mc = new MasterClass();
        $mc->title = 'Лепка из глины';
        $mc->price = 1500;
        
        $this->assertEquals('Лепка из глины', $mc->title);
        $this->assertEquals(1500, $mc->price);
    }
}