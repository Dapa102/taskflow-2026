<?php

namespace App\Livewire\Member;

use Livewire\Component;
use App\Models\Team;
use App\Models\TeamMember;
use App\Models\Workspace;
use Livewire\Attributes\Layout;

#[Layout('layouts.member')]
class MemberTeams extends Component
{
    public $detailModal = false;
    public $detailTeamName = '';
    public $detailMembers = [];
    public $detailPm = null;

    public $wsDetailModal = false;
    public $wsDetail = null;

    public function showDetail($teamId)
    {
        $team = Team::with(['owner', 'members.user'])->findOrFail($teamId);

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

    public function showWorkspaceDetail($workspaceId)
    {
        $this->wsDetail = Workspace::with('pm', 'members')->findOrFail($workspaceId);
        $this->wsDetailModal = true;
    }

    public function render()
    {
        $myTeams = TeamMember::where('user_id', auth()->id())
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
