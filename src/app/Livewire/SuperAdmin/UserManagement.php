<?php

namespace App\Livewire\SuperAdmin;

use Livewire\Component;
use App\Models\User;
use Livewire\Attributes\Layout;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;

#[Layout('layouts.super-admin')]
class UserManagement extends Component
{
    public $showCreateForm = false;
    public $name;
    public $email;
    public $password;
    public $role = 'member';
    public $phone;

    public $editUserId = null;
    public $editName;
    public $editEmail;
    public $editRole;
    public $editPhone;
    public $showEditModal = false;

    public $inviteEmail;
    public $inviteRole = 'member';
    public $showInviteForm = false;

    public function editUser($id)
    {
        $user = User::findOrFail($id);
        $this->editUserId = $user->id;
        $this->editName = $user->name;
        $this->editEmail = $user->email;
        $this->editRole = $user->role;
        $this->editPhone = $user->phone;
        $this->showEditModal = true;
    }

    public function updateUser()
    {
        $this->validate([
            'editName' => 'required|string|max:100',
            'editEmail' => 'required|email|unique:users,email,' . $this->editUserId,
            'editRole' => 'required|in:pm,member,super_admin',
            'editPhone' => 'nullable|string|max:20',
        ]);

        User::findOrFail($this->editUserId)->update([
            'name' => $this->editName,
            'email' => $this->editEmail,
            'role' => $this->editRole,
            'phone' => $this->editPhone,
        ]);

        session()->flash('message', 'User berhasil diperbarui.');
        $this->reset(['editUserId', 'editName', 'editEmail', 'editRole', 'editPhone', 'showEditModal']);
    }

    public function inviteUser()
    {
        $this->validate([
            'inviteEmail' => 'required|email|unique:users,email',
            'inviteRole' => 'required|in:pm,member',
        ]);

        $password = \Str::random(10);

        $user = User::create([
            'name' => explode('@', $this->inviteEmail)[0],
            'email' => $this->inviteEmail,
            'password' => \Illuminate\Support\Facades\Hash::make($password),
            'role' => $this->inviteRole,
            'is_active' => true,
        ]);

        try {
            \Illuminate\Support\Facades\Mail::send([], [], function ($message) use ($user, $password) {
                $message->to($user->email)
                    ->subject('Undangan Bergabung TaskFlow')
                    ->html("
                        <h2>Selamat Bergabung!</h2>
                        <p>Anda telah diundang bergabung ke TaskFlow.</p>
                        <p>Berikut kredensial login Anda:</p>
                        <table style='background:#f5f5f5;padding:12px;border-radius:6px;margin:12px 0'>
                            <tr><td style='padding:4px 8px'><strong>Email</strong></td><td>{$user->email}</td></tr>
                            <tr><td style='padding:4px 8px'><strong>Password</strong></td><td>{$password}</td></tr>
                        </table>
                        <p><a href='" . route('login') . "' style='background:#6366f1;color:white;padding:10px 20px;border-radius:6px;text-decoration:none;display:inline-block'>Login ke TaskFlow</a></p>
                        <p style='color:#888;font-size:12px'>Abaikan email ini jika Anda tidak merasa mendaftar.</p>
                    ");
            });
            session()->flash('message', "Undangan berhasil dikirim ke {$user->email}.");
        } catch (\Exception $e) {
            session()->flash('message', "User dibuat, tapi email gagal dikirim. Password: {$password}");
        }

        $this->reset(['inviteEmail', 'inviteRole', 'showInviteForm']);
    }

    public function toggleUserStatus($userId)
    {
        if (auth()->id() == $userId) {
            session()->flash('error', 'Cannot suspend yourself.');
            return;
        }

        $user = User::find($userId);
        if ($user && $user->role !== 'super_admin') {
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
            'role' => 'required|in:pm,member,super_admin',
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
