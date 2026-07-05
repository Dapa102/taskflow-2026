<?php

namespace App\Livewire\Member;

use Livewire\Component;
use Livewire\Attributes\Layout;

#[Layout('layouts.member')]
class MemberTeams extends Component
{
    public $detailModal = false;
    public $detailTeamName = '';
    public $detailMembers = [];
    public $detailPm = null;

    public function showDetail($teamId)
    {
        $team = \App\Models\Team::with(['owner', 'members.user'])->findOrFail($teamId);

        $this->detailTeamName = $team->name;
        $this->detailPm = $team->owner;
        $this->detailMembers = $team->members->map(fn($m) => [
            'name' => $m->user?->name ?? '—',
            'email' => $m->user?->email ?? '—',
            'phone' => $m->user?->phone ?? null,
            'role' => $m->role,
        ]);
        $this->detailModal = true;
    }

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
