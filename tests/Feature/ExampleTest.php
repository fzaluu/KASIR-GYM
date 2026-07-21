<?php

namespace Tests\Feature;

use App\Http\Controllers\MemberController;
use App\Models\HargaPaket;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A basic test example.
     */
    public function test_the_application_returns_a_successful_response(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }

    public function test_member_registration_default_uses_member_package_price(): void
    {
        HargaPaket::create([
            'nama_paket' => 'member',
            'harga' => 25000,
        ]);

        $controller = new MemberController();
        $response = $controller->index(new Request());

        $this->assertSame(25000, $response->getData()['hargaBulanan']);
    }
}
