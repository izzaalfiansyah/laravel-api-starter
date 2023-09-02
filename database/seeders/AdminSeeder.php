<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Nette\Utils\Random;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admin = \App\Models\User::create([
            'email' => 'superadmin@admin.com',
            'password' => 'superadmin',
            'nama' => 'Muhammad Izza Alfiansyah',
            'role' => '1',
        ]);
    }
}
