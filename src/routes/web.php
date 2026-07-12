<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Livewire\Pm\PmDashboard;
use App\Livewire\SuperAdmin\SuperAdminDashboard;
use App\Livewire\SuperAdmin\PmPerformance;
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
        Route::get('/workspaces', \App\Livewire\Pm\Workspaces::class)->name('workspaces');
        Route::get('/projects', \App\Livewire\Pm\Projects::class)->name('projects');
        Route::get('/create-task', \App\Livewire\Pm\CreateTask::class)->name('create-task');
        Route::get('/review-tasks', \App\Livewire\Pm\ReviewTasks::class)->name('review-tasks');
        Route::get('/tasks/{task}', \App\Livewire\Pm\TaskDetail::class)->name('task-detail');
        Route::get('/all-tasks', \App\Livewire\Pm\PmAllTasks::class)->name('all-tasks');
        Route::get('/my-teams', \App\Livewire\Pm\MyTeams::class)->name('my-teams');
    });

    Route::middleware(['role:member'])->prefix('member')->name('member.')->group(function () {
        Route::get('/dashboard', \App\Livewire\Member\MemberDashboard::class)->name('dashboard');
        Route::get('/tasks', \App\Livewire\Member\Tasks::class)->name('tasks');
        Route::get('/history', \App\Livewire\Member\TaskHistory::class)->name('history');
        Route::get('/teams', \App\Livewire\Member\MemberTeams::class)->name('teams');
        Route::get('/workspaces', \App\Livewire\Member\MyWorkspaces::class)->name('my-workspaces');
    });

    Route::middleware(['role:super_admin'])->prefix('super-admin')->name('super-admin.')->group(function () {
        Route::get('/dashboard', SuperAdminDashboard::class)->name('dashboard');
        Route::get('/users', UserManagement::class)->name('users');
        Route::get('/performa-pm', PmPerformance::class)->name('performa-pm');
        Route::get('/member-performance', \App\Livewire\SuperAdmin\MemberPerformance::class)->name('member-performance');
        Route::get('/late-tasks', \App\Livewire\SuperAdmin\LateTasks::class)->name('late-tasks');
        Route::get('/audit-logs', \App\Livewire\SuperAdmin\AuditLogs::class)->name('audit-logs');
        Route::get('/arbitration-recap', \App\Livewire\SuperAdmin\ArbitrationRecap::class)->name('arbitration-recap');
        Route::get('/hubungi-team', \App\Livewire\SuperAdmin\HubungiTeam::class)->name('hubungi-team');
        Route::get('/compose-email', \App\Livewire\SuperAdmin\ComposeEmail::class)->name('compose-email');
        Route::get('/workspaces', \App\Livewire\SuperAdmin\ManageWorkspaces::class)->name('workspaces');
        Route::get('/tasks', \App\Livewire\SuperAdmin\SuperAdminTaskList::class)->name('tasks');
        Route::get('/task-oversight', \App\Livewire\SuperAdmin\TaskOversight::class)->name('task-oversight');
        Route::get('/task-approval', \App\Livewire\SuperAdmin\TaskApproval::class)->name('task-approval');
    });
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::prefix('export')->name('export.')->group(function () {
        Route::get('/pm-performance', [\App\Http\Controllers\ExportController::class, 'pmPerformance'])->name('pm-performance');
        Route::get('/member-performance', [\App\Http\Controllers\ExportController::class, 'memberPerformance'])->name('member-performance');
        Route::get('/late-tasks', [\App\Http\Controllers\ExportController::class, 'lateTasks'])->name('late-tasks');
    });
});

require __DIR__.'/auth.php';
