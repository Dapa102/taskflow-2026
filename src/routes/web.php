<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Livewire\Pm\PmDashboard;
use App\Livewire\Member\MemberDashboard;
use App\Livewire\Admin\AdminDashboard;
use App\Livewire\Admin\TaskOversight;
use App\Livewire\Admin\PmPerformance;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware(['auth', 'check.active'])->group(function () {
    Route::get('/dashboard', function () {
        $role = auth()->user()->role;
        if ($role === 'pm') return redirect()->route('pm.dashboard');
        if ($role === 'member') return redirect()->route('member.dashboard');
        if ($role === 'admin') return redirect()->route('admin.dashboard');
        return view('dashboard');
    })->name('dashboard');

    Route::middleware(['role:pm'])->prefix('pm')->name('pm.')->group(function () {
        Route::get('/dashboard', PmDashboard::class)->name('dashboard');
    });

    Route::middleware(['role:member'])->prefix('member')->name('member.')->group(function () {
        Route::get('/dashboard', MemberDashboard::class)->name('dashboard');
    });
    
    Route::middleware(['role:admin'])->prefix('admin')->name('admin.')->group(function () {
        Route::get('/dashboard', AdminDashboard::class)->name('dashboard');
        Route::get('/tasks', TaskOversight::class)->name('tasks.oversight');
        Route::get('/pm-performance', PmPerformance::class)->name('pm.performance');
    });
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
