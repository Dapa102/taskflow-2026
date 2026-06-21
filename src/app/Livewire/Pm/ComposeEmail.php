<?php

namespace App\Livewire\Pm;

use App\Mail\ComposeMessage;
use App\Models\User;
use Livewire\Component;
use Illuminate\Support\Facades\Mail;
use Livewire\Attributes\Layout;

#[Layout('layouts.app')]
class ComposeEmail extends Component
{
    public string $recipientId = '';
    public string $subject = '';
    public string $body = '';

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
        $pm = auth()->user();
        $workspace = $pm->workspace;
        $recipients = $workspace ? $workspace->members : collect();

        return view('livewire.pm.compose-email', ['recipients' => $recipients]);
    }
}
