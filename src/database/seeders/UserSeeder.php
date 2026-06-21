<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::firstOrCreate(
            ['email' => 'admin@admin.com'],
            ['name' => 'Super Admin', 'password' => Hash::make('password'), 'role' => 'admin']
        )->assignRole('super_admin');

        $pms = [
            ['name' => 'Budi Santoso', 'email' => 'pm1@test.com'],
            ['name' => 'Siti Rahayu', 'email' => 'pm2@test.com'],
        ];
        foreach ($pms as $pm) {
            User::firstOrCreate(
                ['email' => $pm['email']],
                ['name' => $pm['name'], 'password' => Hash::make('password'), 'role' => 'pm']
            );
        }

        $members = [
            ['name' => 'Ahmad Fauzi', 'email' => 'member1@test.com'],
            ['name' => 'Dewi Lestari', 'email' => 'member2@test.com'],
            ['name' => 'Rudi Hidayat', 'email' => 'member3@test.com'],
            ['name' => 'Fitri Handayani', 'email' => 'member4@test.com'],
        ];
        foreach ($members as $member) {
            User::firstOrCreate(
                ['email' => $member['email']],
                ['name' => $member['name'], 'password' => Hash::make('password'), 'role' => 'member']
            );
        }

        User::firstOrCreate(
            ['email' => 'user@admin.com'],
            ['name' => 'User Account', 'password' => Hash::make('password')]
        )->assignRole('user');
    }
}
