<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \App\Models\User::create([
            'email' => 'uskun@gmail.com',
            'password' => '12345678',
            'nama' => 'Kuni Zakiyah',
            'role' => '2',
        ]);
    }
}
