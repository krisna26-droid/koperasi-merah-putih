<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['username' => 'admin'],
            [
                'name' => 'Pengurus Operasional',
                'email' => 'admin@koperasimerahputih.id',
                'password' => Hash::make('password123'),
                'peran' => 'Admin',
            ]
        );

        User::updateOrCreate(
            ['username' => 'ketua'],
            [
                'name' => 'Ketua Koperasi',
                'email' => 'ketua@koperasimerahputih.id',
                'password' => Hash::make('password123'),
                'peran' => 'Ketua',
            ]
        );
    }
}