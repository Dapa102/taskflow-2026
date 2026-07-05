<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Livewire\Pm\PmDashboard;
use App\Livewire\Member\MemberDashboard;
use App\Livewire\SuperAdmin\SuperAdminDashboard;
use App\Livewire\SuperAdmin\CreateTask;
use App\Livewire\SuperAdmin\SuperAdminTaskList;
use App\Livewire\SuperAdmin\TaskOversight;
use App\Livewire\SuperAdmin\PmPerformance;
use App\Livewire\SuperAdmin\AssignTask;
use App\Livewire\SuperAdmin\TaskList;
use App\Livewire\SuperAdmin\UserManagement;
use App\Livewire\AllTasks;

Route::get('/', function () {
    return auth()->check()
        ? redirect()->route('dashboard')
        : redirect()->route('login');
});

Route::middleware(['auth', 'check.active'])->group(function () {
    Route::get('/dashboard', function () {
        $role = auth()->user()->role;
        if ($role === 'pm') return redirect()->route('pm.dashboard');
        if ($role === 'member') return redirect()->route('member.dashboard');
        if ($role === 'super_admin') return redirect()->route('super-admin.dashboard');
        return view('dashboard');
    })->name('dashboard');

    Route::get('/tasks', AllTasks::class)->name('tasks.all');

    Route::middleware(['role:pm'])->prefix('pm')->name('pm.')->group(function () {
        Route::get('/dashboard', PmDashboard::class)->name('dashboard');
        Route::get('/compose-email', \App\Livewire\Pm\ComposeEmail::class)->name('compose.email');
        Route::get('/team-members', \App\Livewire\Pm\TeamMembers::class)->name('team.members');
        Route::get('/workspace', \App\Livewire\Pm\WorkspaceDetail::class)->name('workspace');
    });

    Route::middleware(['role:member'])->prefix('member')->name('member.')->group(function () {
        Route::get('/dashboard', MemberDashboard::class)->name('dashboard');
    });

    Route::middleware(['role:super_admin'])->prefix('super-admin')->name('super-admin.')->group(function () {
        Route::get('/dashboard', SuperAdminDashboard::class)->name('dashboard');
        Route::get('/create-task', CreateTask::class)->name('create.task');
        Route::get('/tasks', SuperAdminTaskList::class)->name('tasks');
        Route::get('/task-list', TaskList::class)->name('task-list');
        Route::get('/oversight/{taskId?}', TaskOversight::class)->name('oversight');
        Route::get('/assign-task', AssignTask::class)->name('assign-task');
        Route::get('/users', UserManagement::class)->name('users');
        Route::get('/pm-performance', PmPerformance::class)->name('pm-performance');
        Route::get('/arbitration-recap', \App\Livewire\SuperAdmin\ArbitrationRecap::class)->name('arbitration-recap');
        Route::get('/hubungi-team', \App\Livewire\SuperAdmin\HubungiTeam::class)->name('hubungi-team');
        Route::get('/compose-email', \App\Livewire\SuperAdmin\ComposeEmail::class)->name('compose-email');
        Route::get('/workspaces', \App\Livewire\SuperAdmin\ManageWorkspaces::class)->name('workspaces');
    });
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
