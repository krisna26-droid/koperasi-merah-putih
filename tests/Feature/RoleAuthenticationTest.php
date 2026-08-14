<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RoleAuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_access_private_routes_and_redirected_to_login(): void
    {
        $response = $this->get('/pengurus/dashboard');
        $response->assertRedirect('/login');

        $responseKetua = $this->get('/ketua/dashboard');
        $responseKetua->assertRedirect('/login');
    }

    public function test_admin_can_access_pengurus_dashboard_but_forbidden_from_ketua_dashboard(): void
    {
        $admin = User::create([
            'username' => 'admin_test',
            'name' => 'Admin Test',
            'email' => 'admin@test.com',
            'password' => bcrypt('password'),
            'peran' => 'Admin',
        ]);

        $response = $this->actingAs($admin)->get('/pengurus/dashboard');
        $response->assertStatus(200);

        $responseForbidden = $this->actingAs($admin)->get('/ketua/dashboard');
        $responseForbidden->assertStatus(403);
    }

    public function test_ketua_can_access_ketua_dashboard_but_forbidden_from_pengurus_dashboard(): void
    {
        $ketua = User::create([
            'username' => 'ketua_test',
            'name' => 'Ketua Test',
            'email' => 'ketua@test.com',
            'password' => bcrypt('password'),
            'peran' => 'Ketua',
        ]);

        $response = $this->actingAs($ketua)->get('/ketua/dashboard');
        $response->assertStatus(200);

        $responseForbidden = $this->actingAs($ketua)->get('/pengurus/dashboard');
        $responseForbidden->assertStatus(403);
    }

    public function test_login_redirects_to_correct_dashboard_based_on_role(): void
    {
        $admin = User::create([
            'username' => 'admin_login',
            'name' => 'Admin Login',
            'email' => 'admin_login@test.com',
            'password' => bcrypt('password123'),
            'peran' => 'Admin',
        ]);

        $response = $this->post('/login', [
            'username' => 'admin_login',
            'password' => 'password123',
        ]);

        $response->assertRedirect(route('pengurus.dashboard', absolute: false));
    }
}