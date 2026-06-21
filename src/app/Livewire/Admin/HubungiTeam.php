<?php

namespace App\Livewire\Admin;

use App\Mail\ComposeMessage;
use App\Models\Team;
use App\Models\User;
use Livewire\Component;
use Illuminate\Support\Facades\Mail;
use Livewire\Attributes\Layout;

#[Layout('layouts.admin')]
class HubungiTeam extends Component
{
    public string $recipientId = '';
    public string $subject = '';
    public string $body = '';

    public function mount(): void
    {
        if ($recipientId = request('recipient')) {
            $this->recipientId = $recipientId;
        }
    }

    protected function rules(): array
    {
        return [
            'recipientId' => 'required|exists:users,id',
            'subject' => 'required|string|max:255',
            'body' => 'required|string|max:5000',
        ];
    }

    public function send(): void
    {
        $this->validate();

        $recipient = User::findOrFail($this->recipientId);
        $sender = auth()->user();

        Mail::to($recipient->email)
            ->send(new ComposeMessage($this->subject, $this->body, $sender->name));

        session()->flash('message', 'Email berhasil dikirim ke ' . $recipient->name);
        $this->reset(['recipientId', 'subject', 'body']);
    }

    public function render()
    {
        $recipients = User::where('role', 'pm')->get();
        $pmTeams = collect();
        if ($this->recipientId) {
            $pmTeams = Team::where('owner_id', $this->recipientId)
                ->with('members.user')
                ->get();
        }
        return view('livewire.admin.hubungi-team', [
            'recipients' => $recipients,
            'pmTeams' => $pmTeams,
        ]);
    }
}
