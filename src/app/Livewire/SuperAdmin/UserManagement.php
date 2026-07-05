<?php

namespace App\Livewire\SuperAdmin;

use Livewire\Component;
use App\Models\User;
use Livewire\Attributes\Layout;
use Illuminate\Support\Facades\Hash;

#[Layout('layouts.super-admin')]
class UserManagement extends Component
{
    public $showCreateForm = false;
    public $name;
    public $email;
    public $password;
    public $role = 'member';
    public $phone;

    public function toggleUserStatus($userId)
    {
        if (auth()->id() == $userId) {
            session()->flash('error', 'Cannot suspend yourself.');
            return;
        }

        $user = User::find($userId);
        if ($user && $user->role !== 'admin') {
            $user->update(['is_active' => !$user->is_active]);
            session()->flash('message', 'User status updated.');
        }
    }

    public function createUser()
    {
        $this->validate([
            'name' => 'required|string|max:100',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6',
            'role' => 'required|in:pm,member,admin',
            'phone' => 'nullable|string|max:20',
        ]);

        User::create([
            'name' => $this->name,
            'email' => $this->email,
            'password' => Hash::make($this->password),
            'role' => $this->role,
            'phone' => $this->phone,
            'is_active' => true,
        ]);

        session()->flash('message', 'User created successfully.');
        $this->reset(['name', 'email', 'password', 'role', 'phone', 'showCreateForm']);
    }

    public function render()
    {
        $users = User::latest()->get();

        return view('livewire.super-admin.user-management', [
            'users' => $users,
        ]);
    }
}
