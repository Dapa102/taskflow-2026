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
        $pm1 = User::where('email', 'pm1@test.com')->first();
        $pm2 = User::where('email', 'pm2@test.com')->first();
        $member1 = User::where('email', 'member1@test.com')->first();
        $member2 = User::where('email', 'member2@test.com')->first();
        $member3 = User::where('email', 'member3@test.com')->first();
        $member4 = User::where('email', 'member4@test.com')->first();

        if (!$pm1 || !$pm2) return;

        $teamDev = Team::firstOrCreate(
            ['name' => 'Tim Developer', 'owner_id' => $pm1->id],
            ['invite_code' => 'DEV2026']
        );
        TeamMember::firstOrCreate(
            ['team_id' => $teamDev->id, 'user_id' => $pm1->id],
            ['role' => 'admin', 'joined_at' => now()]
        );
        if ($member1) TeamMember::firstOrCreate(
            ['team_id' => $teamDev->id, 'user_id' => $member1->id],
            ['role' => 'member', 'joined_at' => now()]
        );
        if ($member2) TeamMember::firstOrCreate(
            ['team_id' => $teamDev->id, 'user_id' => $member2->id],
            ['role' => 'member', 'joined_at' => now()]
        );

        $teamDesain = Team::firstOrCreate(
            ['name' => 'Tim Desain', 'owner_id' => $pm2->id],
            ['invite_code' => 'DESIGN2026']
        );
        TeamMember::firstOrCreate(
            ['team_id' => $teamDesain->id, 'user_id' => $pm2->id],
            ['role' => 'admin', 'joined_at' => now()]
        );
        if ($member3) TeamMember::firstOrCreate(
            ['team_id' => $teamDesain->id, 'user_id' => $member3->id],
            ['role' => 'member', 'joined_at' => now()]
        );
        if ($member4) TeamMember::firstOrCreate(
            ['team_id' => $teamDesain->id, 'user_id' => $member4->id],
            ['role' => 'member', 'joined_at' => now()]
        );
    }
}
