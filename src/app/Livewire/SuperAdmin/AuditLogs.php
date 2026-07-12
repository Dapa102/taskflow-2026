<?php

namespace App\Livewire\SuperAdmin;

use Livewire\Component;
use App\Models\AuditLog;
use App\Models\User;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;

#[Layout('layouts.super-admin')]
class AuditLogs extends Component
{
    use WithPagination;

    public $actionFilter = '';
    public $userIdFilter = '';
    public $entityTypeFilter = '';
    public $startDate = '';
    public $endDate = '';

    public function render()
    {
        $query = AuditLog::with('user')->latest();

        if ($this->actionFilter) {
            $query->where('action', $this->actionFilter);
        }
        if ($this->userIdFilter) {
            $query->where('user_id', $this->userIdFilter);
        }
        if ($this->entityTypeFilter) {
            $query->where('entity_type', $this->entityTypeFilter);
        }
        if ($this->startDate) {
            $query->whereDate('created_at', '>=', $this->startDate);
        }
        if ($this->endDate) {
            $query->whereDate('created_at', '<=', $this->endDate);
        }

        return view('livewire.super-admin.audit-logs', [
            'logs' => $query->paginate(20),
            'users' => User::whereIn('role', ['super_admin', 'pm', 'member'])->orderBy('name')->get(),
            'actions' => AuditLog::select('action')->distinct()->pluck('action'),
            'entityTypes' => AuditLog::select('entity_type')->distinct()->whereNotNull('entity_type')->pluck('entity_type'),
        ]);
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }
}
