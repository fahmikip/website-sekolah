<?php

namespace Tests\Feature\Feature;

use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FoundationTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    use RefreshDatabase;

    public function test_public_home_uses_school_profile_from_database(): void
    {
        $this->seed(RolesAndPermissionsSeeder::class);
        $response = $this->get('/');
        $response->assertOk()->assertSee('SMARTECH Nusantara')->assertSee('Belajar hari ini');
    }

    public function test_guest_cannot_open_dashboard(): void
    {
        $this->get('/dashboard')->assertRedirect('/login');
    }
}
