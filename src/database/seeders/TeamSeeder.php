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
        $pm = User::where('email', 'pm1@test.com')->first();
        $member1 = User::where('email', 'member1@test.com')->first();
        $member2 = User::where('email', 'member2@test.com')->first();

        if (!$pm || !$member1 || !$member2) return;

        $team = Team::firstOrCreate(
            ['name' => 'Tim Developer', 'owner_id' => $pm->id],
            ['invite_code' => 'DEV2026']
        );

        TeamMember::firstOrCreate(
            ['team_id' => $team->id, 'user_id' => $pm->id],
            ['role' => 'admin', 'joined_at' => now()]
        );
        TeamMember::firstOrCreate(
            ['team_id' => $team->id, 'user_id' => $member1->id],
            ['role' => 'member', 'joined_at' => now()]
        );
        TeamMember::firstOrCreate(
            ['team_id' => $team->id, 'user_id' => $member2->id],
            ['role' => 'member', 'joined_at' => now()]
        );
    }
}
