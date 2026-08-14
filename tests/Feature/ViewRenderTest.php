<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ViewRenderTest extends TestCase
{
    use RefreshDatabase;

    protected User $adminUser;
    protected User $ketuaUser;

    protected function setUp(): void
    {
        parent::setUp();

        $this->adminUser = User::create([
            'username' => 'admin_view',
            'name' => 'Admin View',
            'email' => 'admin_view@test.com',
            'password' => bcrypt('password'),
            'peran' => 'Admin',
        ]);

        $this->ketuaUser = User::create([
            'username' => 'ketua_view',
            'name' => 'Ketua View',
            'email' => 'ketua_view@test.com',
            'password' => bcrypt('password'),
            'peran' => 'Ketua',
        ]);
    }

    public function test_pengurus_views_can_be_rendered(): void
    {
        $this->actingAs($this->adminUser);

        $this->get(route('pengurus.dashboard'))->assertStatus(200);
        $this->get(route('pengurus.anggota.index'))->assertStatus(200);
        $this->get(route('pengurus.simpanan.index'))->assertStatus(200);
        $this->get(route('pengurus.pinjaman.index'))->assertStatus(200);
        $this->get(route('pengurus.angsuran.index'))->assertStatus(200);
    }

    public function test_ketua_views_can_be_rendered(): void
    {
        $this->actingAs($this->ketuaUser);

        $this->get(route('ketua.dashboard'))->assertStatus(200);
        $this->get(route('ketua.approval.index'))->assertStatus(200);
    }
}