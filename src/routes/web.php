<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Livewire\Pm\PmDashboard;
use App\Livewire\Member\MemberDashboard;
use App\Livewire\Admin\AdminDashboard;
use App\Livewire\Admin\TaskOversight;
use App\Livewire\Admin\PmPerformance;
use App\Livewire\Admin\AssignTask;
use App\Livewire\Admin\TaskList;
use App\Livewire\Admin\UserManagement;
use App\Livewire\Atasan\AtasanDashboard;
use App\Livewire\Atasan\CreateTask;
use App\Livewire\Atasan\AtasanTaskList;
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
        if ($role === 'admin') return redirect()->route('admin.dashboard');
        if ($role === 'atasan') return redirect()->route('atasan.dashboard');
        return view('dashboard');
    })->name('dashboard');

    Route::get('/tasks', AllTasks::class)->name('tasks.all');

    Route::middleware(['role:pm'])->prefix('pm')->name('pm.')->group(function () {
        Route::get('/dashboard', PmDashboard::class)->name('dashboard');
        Route::get('/compose-email', \App\Livewire\Pm\ComposeEmail::class)->name('compose.email');
    });

    Route::middleware(['role:member'])->prefix('member')->name('member.')->group(function () {
        Route::get('/dashboard', MemberDashboard::class)->name('dashboard');
    });

    Route::middleware(['role:atasan'])->prefix('atasan')->name('atasan.')->group(function () {
        Route::get('/dashboard', AtasanDashboard::class)->name('dashboard');
        Route::get('/create-task', CreateTask::class)->name('create.task');
        Route::get('/tasks', AtasanTaskList::class)->name('tasks');
    });

    Route::middleware(['role:super_admin'])->prefix('super-admin')->name('super-admin.')->group(function () {
        Route::get('/dashboard', AtasanDashboard::class)->name('dashboard');
        Route::get('/create-task', CreateTask::class)->name('create.task');
        Route::get('/tasks', AtasanTaskList::class)->name('tasks');
    });
    
    Route::middleware(['role:admin'])->prefix('admin')->name('admin.')->group(function () {
        Route::get('/dashboard', AdminDashboard::class)->name('dashboard');
        Route::get('/tasks', TaskList::class)->name('tasks.list');
        Route::get('/tasks/oversight/{taskId?}', TaskOversight::class)->name('tasks.oversight');
        Route::get('/assign-task', AssignTask::class)->name('assign.task');
        Route::get('/users', UserManagement::class)->name('users');
        Route::get('/pm-performance', PmPerformance::class)->name('pm.performance');
        Route::get('/hubungi-team', \App\Livewire\Admin\HubungiTeam::class)->name('hubungi.team');
    });
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
