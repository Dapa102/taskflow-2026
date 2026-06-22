<?php

namespace App\Livewire\Admin;

use App\Mail\ComposeMessage;
use App\Models\Team;
use App\Models\User;
use Livewire\Component;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Livewire\Attributes\Layout;

#[Layout('layouts.admin')]
class HubungiTeam extends Component
{
    public string $recipientId = '';
    public string $sendType = 'email';
    public string $subject = '';
    public string $body = '';

    public function mount(): void
    {
        if ($recipientId = request('recipient')) {
            $this->recipientId = $recipientId;
        }
        if ($sendType = request('sendType')) {
            $this->sendType = $sendType;
        }
    }

    protected function rules(): array
    {
        $rules = [
            'recipientId' => 'required|exists:users,id',
            'body' => 'required|string|max:5000',
        ];

        if ($this->sendType === 'email') {
            $rules['subject'] = 'required|string|max:255';
        }

        return $rules;
    }

    public function send(): void
    {
        $this->validate();

        $recipient = User::findOrFail($this->recipientId);
        $sender = auth()->user();

        if ($this->sendType === 'whatsapp') {
            $this->sendWhatsApp($recipient, $sender);
        } else {
            $this->sendEmail($recipient, $sender);
        }

        $this->reset(['recipientId', 'subject', 'body']);
    }

    private function sendEmail(User $recipient, User $sender): void
    {
        Mail::to($recipient->email)
            ->send(new ComposeMessage($this->subject, $this->body, $sender->name));

        session()->flash('message', 'Email berhasil dikirim ke ' . $recipient->name);
    }

    private function sendWhatsApp(User $recipient, User $sender): void
    {
        if (!$recipient->phone) {
            session()->flash('error', $recipient->name . ' belum memiliki nomor telepon.');
            return;
        }

        $token = config('fonnte.token');
        if (!$token) {
            session()->flash('error', 'Token Fonnte belum dikonfigurasi.');
            return;
        }

        $message = "*Pesan dari {$sender->name} (Super Admin)*\n\n{$this->body}";

        $response = Http::withHeaders([
            'Authorization' => $token,
        ])->post('https://api.fonnte.com/send', [
            'target' => $recipient->phone,
            'message' => $message,
            'countryCode' => '62',
        ]);

        if ($response->successful()) {
            session()->flash('message', 'WhatsApp berhasil dikirim ke ' . $recipient->name);
        } else {
            session()->flash('error', 'Gagal mengirim WhatsApp: ' . $response->body());
        }
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
