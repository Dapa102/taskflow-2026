<?php

namespace App\Livewire\Pm;

use Livewire\Component;
use App\Models\Team;
use Livewire\Attributes\Layout;

#[Layout('layouts.pm')]
class MyTeams extends Component
{
    public $detailModal = false;
    public $detailTeamName = '';
    public $detailMembers = [];

    public function showDetail($teamId)
    {
        $team = Team::with(['owner', 'members.user'])->findOrFail($teamId);

        $this->detailTeamName = $team->name;
        $this->detailMembers = $team->members->map(fn($m) => [
            'name' => $m->user?->name ?? '—',
            'email' => $m->user?->email ?? '—',
            'role' => $m->role,
        ]);
        $this->detailModal = true;
    }

    public function render()
    {
        $userId = auth()->id();

        $myTeams = Team::with('owner')
            ->where('owner_id', $userId)
            ->orWhereHas('members', fn($q) => $q->where('user_id', $userId))
            ->orderBy('name')
            ->get();

        return view('livewire.pm.my-teams', [
            'myTeams' => $myTeams,
        ]);
    }
}
