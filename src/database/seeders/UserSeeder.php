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
            ['email' => 'admin@taskflow.com'],
            ['name' => 'Super Admin', 'password' => Hash::make('taskflowadmin123'), 'role' => 'super_admin', 'is_active' => true]
        );

        // User PM & Member ditambahkan melalui website (Super Admin > Users > Create)
        // User::firstOrCreate(
        //     ['email' => 'pm1@test.com'],
        //     ['name' => 'Budi Santoso', 'password' => Hash::make('password'), 'role' => 'pm', 'is_active' => true]
        // );

        // $members = [
        //     ['name' => 'Ahmad Fauzi', 'email' => 'member1@test.com'],
        //     ['name' => 'Dewi Lestari', 'email' => 'member2@test.com'],
        // ];
        // foreach ($members as $m) {
        //     User::firstOrCreate(
        //         ['email' => $m['email']],
        //         ['name' => $m['name'], 'password' => Hash::make('password'), 'role' => 'member', 'is_active' => true]
        //     );
        // }
    }
}
