<?php

namespace App\Livewire\Admin;

use App\Mail\ComposeMessage;
use Livewire\Component;
use App\Models\User;
use App\Models\Task;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Livewire\Attributes\Layout;

#[Layout('layouts.admin')]
class PmPerformance extends Component
{
    public ?int $contactPmId = null;
    public string $contactSendType = 'email';
    public string $contactSubject = '';
    public string $contactMessage = '';

    public function sendContactMessage(): void
    {
        $this->validate([
            'contactPmId' => 'required|exists:users,id',
            'contactMessage' => 'required|string|max:5000',
        ]);

        if ($this->contactSendType === 'email') {
            $this->validate(['contactSubject' => 'required|string|max:255']);
            $this->sendContactEmail();
        } else {
            $this->sendContactWhatsApp();
        }

        $this->reset(['contactPmId', 'contactSubject', 'contactMessage']);
        session()->flash('message', 'Pesan berhasil dikirim.');
    }

    private function sendContactEmail(): void
    {
        $pm = User::find($this->contactPmId);
        $sender = auth()->user();

        Mail::to($pm->email)
            ->send(new ComposeMessage($this->contactSubject, $this->contactMessage, $sender->name));
    }

    private function sendContactWhatsApp(): void
    {
        $pm = User::find($this->contactPmId);
        $sender = auth()->user();

        if (!$pm->phone) {
            session()->flash('error', $pm->name . ' belum memiliki nomor telepon.');
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
            'target' => $pm->phone,
            'message' => $message,
            'countryCode' => '62',
        ]);

        if (!$response->successful()) {
            session()->flash('error', 'Gagal mengirim WhatsApp: ' . $response->body());
        }
    }

    public function render()
    {
        // Cache for 5 minutes as requested in PRD
        $pms = Cache::remember('admin_pm_performance', 300, function () {
            $pmsList = User::where('role', 'pm')->with('workspace')->get();
            
            foreach ($pmsList as $pm) {
                if (!$pm->workspace) {
                    $pm->total_tasks = 0;
                    $pm->done_tasks = 0;
                    $pm->overdue_tasks = 0;
                    $pm->on_time_rate = 0;
                    continue;
                }

                $tasks = Task::where('workspace_id', $pm->workspace->id)->get();
                
                $pm->total_tasks = $tasks->count();
                $pm->done_tasks = $tasks->where('status', 'done')->count();
                $pm->overdue_tasks = $tasks->filter(function($t) {
                    return $t->deadline && $t->deadline < now() && $t->status != 'done';
                })->count();
                
                $pm->on_time_rate = $pm->total_tasks > 0 
                    ? round(($pm->done_tasks / $pm->total_tasks) * 100, 2) 
                    : 0;
            }
            
            return $pmsList;
        });

        return view('livewire.admin.pm-performance', [
            'pms' => $pms
        ]);
    }
}
