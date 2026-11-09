<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $users = [
            [
                'username' => 'ketua',
                'name' => 'Ketua Koperasi',
                'email' => 'ketua@koperasimerahputih.id',
                'peran' => 'Ketua',
            ],
            [
                'username' => 'pengurus',
                'name' => 'Pengurus Operasional',
                'email' => 'pengurus@koperasimerahputih.id',
                'peran' => 'Admin',
            ],
            [
                'username' => 'bendahara',
                'name' => 'Bendahara Koperasi',
                'email' => 'bendahara@koperasimerahputih.id',
                'peran' => 'Admin',
            ],
            // Akun lama tetap disediakan agar referensi transaksi lama tidak yatim.
            [
                'username' => 'admin',
                'name' => 'Admin Koperasi',
                'email' => 'admin@koperasimerahputih.id',
                'peran' => 'Admin',
            ],
        ];

        foreach ($users as $user) {
            User::updateOrCreate(
                ['username' => $user['username']],
                [
                    ...$user,
                    'password' => 'password123',
                ]
            );
        }
    }
}
