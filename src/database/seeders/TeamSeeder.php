<?php

namespace Database\Seeders;

use App\Models\Team;
use App\Models\TeamMember;
use App\Models\User;
use Illuminate\Database\Seeder;

class TeamSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::where('email', 'user@admin.com')->first();

        if (!$user) {
            return;
        }

        $secondUser = User::firstOrCreate(
            ['email' => 'member@team.com'],
            ['name' => 'Team Member', 'password' => bcrypt('password')]
        );

        $team = Team::firstOrCreate(
            ['name' => 'Tim Developer', 'owner_id' => $user->id],
            ['invite_code' => 'DEV2026']
        );

        TeamMember::firstOrCreate(
            ['team_id' => $team->id, 'user_id' => $user->id],
            ['role' => 'admin', 'joined_at' => now()]
        );

        TeamMember::firstOrCreate(
            ['team_id' => $team->id, 'user_id' => $secondUser->id],
            ['role' => 'member', 'joined_at' => now()]
        );

        $teamTwo = Team::firstOrCreate(
            ['name' => 'Tim Desain', 'owner_id' => $user->id],
            ['invite_code' => 'DESIGN2026']
        );

        TeamMember::firstOrCreate(
            ['team_id' => $teamTwo->id, 'user_id' => $user->id],
            ['role' => 'admin', 'joined_at' => now()]
        );
    }
}
