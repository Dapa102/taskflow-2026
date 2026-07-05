<?php

namespace App\Livewire\Member;

use Livewire\Component;
use Livewire\Attributes\Layout;

#[Layout('layouts.member')]
class MemberTeams extends Component
{
    public function render()
    {
        $myTeams = \App\Models\TeamMember::where('user_id', auth()->id())
            ->with('team.owner')
            ->get();

        $memberWorkspaces = auth()->user()->memberWorkspaces()
            ->with('pm')
            ->get();

        return view('livewire.member.member-teams', [
            'myTeams' => $myTeams,
            'memberWorkspaces' => $memberWorkspaces,
        ]);
    }
}
