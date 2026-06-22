<?php

namespace App\Livewire\Admin;

use App\Mail\ComposeMessage;
use Livewire\Component;
use App\Models\User;
use App\Models\Workspace;
use App\Models\Task;
use App\Models\Team;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Livewire\Attributes\Layout;

#[Layout('layouts.admin')]
class AdminDashboard extends Component
{
    public ?int $contactUserId = null;
    public string $contactSendType = 'email';
    public string $contactSubject = '';
    public string $contactMessage = '';

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

    public function sendContactMessage(): void
    {
        $this->validate([
            'contactUserId' => 'required|exists:users,id',
            'contactMessage' => 'required|string|max:5000',
        ]);

        if ($this->contactSendType === 'email') {
            $this->validate(['contactSubject' => 'required|string|max:255']);
            $this->sendContactEmail();
        } else {
            $this->sendContactWhatsApp();
        }

        $this->reset(['contactUserId', 'contactSubject', 'contactMessage']);
        session()->flash('message', 'Pesan berhasil dikirim.');
    }

    private function sendContactEmail(): void
    {
        $user = User::find($this->contactUserId);
        $sender = auth()->user();

        Mail::to($user->email)
            ->send(new ComposeMessage($this->contactSubject, $this->contactMessage, $sender->name));
    }

    private function sendContactWhatsApp(): void
    {
        $user = User::find($this->contactUserId);
        $sender = auth()->user();

        if (!$user->phone) {
            session()->flash('error', $user->name . ' belum memiliki nomor telepon.');
            return;
        }

        $token = config('fonnte.token');
        if (!$token) {
            session()->flash('error', 'Token Fonnte belum dikonfigurasi.');
            return;
        }

        $message = "*Pesan dari {$sender->name} (Super Admin)*\n\n{$this->contactMessage}";

        $response = Http::withHeaders([
            'Authorization' => $token,
        ])->post('https://api.fonnte.com/send', [
            'target' => $user->phone,
            'message' => $message,
            'countryCode' => '62',
        ]);

        if (!$response->successful()) {
            session()->flash('error', 'Gagal mengirim WhatsApp: ' . $response->body());
        }
    }

    public function render()
    {
        $taskStats = [
            'total' => Task::count(),
            'todo' => Task::where('status', 'todo')->count(),
            'on_progress' => Task::where('status', 'on_progress')->count(),
            'pending_pm' => Task::where('status', 'pending_pm')->count(),
            'pending_admin' => Task::where('status', 'pending_admin')->count(),
            'revision' => Task::where('status', 'revision')->count(),
            'done' => Task::where('status', 'done')->count(),
        ];

        $stats = [
            'users' => User::count(),
            'workspaces' => Workspace::count(),
            'tasks' => $taskStats,
        ];

        $users = User::latest()->get();
        $workspaces = Workspace::with('pm', 'tasks.assignee')->latest()->get();
        $teams = Team::with('owner', 'tasks.assignee', 'members.user')->latest()->get();
        $tasks = Task::with(['workspace', 'assignee', 'creator'])->latest()->get();

        return view('livewire.admin.admin-dashboard', [
            'stats' => $stats,
            'users' => $users,
            'workspaces' => $workspaces,
            'teams' => $teams,
            'tasks' => $tasks,
        ]);
    }
}
