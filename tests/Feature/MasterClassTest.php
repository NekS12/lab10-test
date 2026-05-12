<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\MasterClass;
use Illuminate\Foundation\Testing\RefreshDatabase;

class MasterClassTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_see_master_classes_list()
    {
        $response = $this->get('/master-classes'); // Убедитесь, что роут существует
        $response->assertStatus(200);
    }

    public function test_master_class_has_attributes()
    {
        $mc = MasterClass::factory()->make([
            'title' => 'Лепка из глины',
            'price' => 1500
        ]);
        
        $this->assertEquals('Лепка из глины', $mc->title);
        $this->assertEquals(1500, $mc->price);
    }
}