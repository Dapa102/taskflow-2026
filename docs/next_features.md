# Next Features: WhatsApp & Email Notification System

## Overview

Integrasi notifikasi dua kanal: **WhatsApp** (via Fonnte API) dan **Email** (via SMTP). Memberi alert real-time ke PM dan Member saat task baru di-assign, serta komunikasi formal via email dari Super Admin ke PM dan PM ke Member.

---

## 1. WhatsApp Notifications (Fonnte API)

### 1.1. Flow Notifikasi

| Trigger | Pengirim | Penerima | Pesan |
|---------|----------|----------|-------|
| Super Admin assign task ke PM | Sistem (via SA action) | PM | "Task baru: {title} dari Super Admin. Deadline: {deadline}" |
| PM assign task ke Member | Sistem (via PM action) | Member | "Task baru: {title} dari {PM_name}. Deadline: {deadline}" |

### 1.2. Tech Stack

| Komponen | Teknologi |
|----------|-----------|
| Gateway | Fonnte API (https://api.fonnte.com/send) |
| HTTP Client | Laravel Http facade |
| Queue | Laravel Queue (database sync, default) |
| Config | `config/fonnte.php` |

### 1.3. Environment Variables

```
FONNTE_TOKEN=4HX8LnKM9gXCd4Erm9vN
```

**Catatan:** Token disimpan di `.env`, jangan di-hardcode.

### 1.4. Implementasi

#### 1.4.1. Service Class

```
app/Services/FonnteService.php
```

```php
<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class FonnteService
{
    protected string $token;
    protected string $endpoint = 'https://api.fonnte.com/send';

    public function __construct()
    {
        $this->token = config('fonnte.token');
    }

    public function send(string $phone, string $message): bool
    {
        $response = Http::withHeaders([
            'Authorization' => $this->token,
        ])->post($this->endpoint, [
            'target' => $phone,
            'message' => $message,
        ]);

        return $response->successful();
    }

    public function formatPhoneNumber(string $phone): string
    {
        // Hapus spasi, strip, dan karakter non-digit
        $phone = preg_replace('/[^0-9]/', '', $phone);

        // Jika diawali 0, ganti dengan 62 (Indonesia)
        if (str_starts_with($phone, '0')) {
            $phone = '62' . substr($phone, 1);
        }

        // Jika belum diawali 62, tambahkan 62
        if (!str_starts_with($phone, '62')) {
            $phone = '62' . $phone;
        }

        return $phone;
    }
}
```

#### 1.4.2. Notification Template

```
app/Notifications/TaskAssignedWhatsapp.php
```

```php
<?php

namespace App\Notifications;

use App\Models\Task;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class TaskAssignedWhatsapp extends Notification implements ShouldQueue
{
    use Queueable;

    protected Task $task;
    protected string $assignedBy;

    public function __construct(Task $task, string $assignedBy)
    {
        $this->task = $task;
        $this->assignedBy = $assignedBy;
    }

    public function via($notifiable): array
    {
        return $notifiable->phone ? ['fonnte'] : [];
    }

    public function toFonnte($notifiable): array
    {
        $message = "Task Baru!\n\n"
            . "Judul: {$this->task->title}\n"
            . "Dari: {$this->assignedBy}\n"
            . "Deadline: {$this->task->deadline?->format('d M Y') ?? 'Tidak ada deadline'}\n"
            . "Prioritas: " . ucfirst($this->task->priority) . "\n\n"
            . "Link: " . url('/login');

        return [
            'target' => app(FonnteService::class)->formatPhoneNumber($notifiable->phone),
            'message' => $message,
        ];
    }
}
```

#### 1.4.3. Custom Channel

```
app/Notifications/Channels/FonnteChannel.php
```

```php
<?php

namespace App\Notifications\Channels;

use App\Services\FonnteService;
use Illuminate\Notifications\Notification;

class FonnteChannel
{
    public function send($notifiable, Notification $notification): void
    {
        $data = $notification->toFonnte($notifiable);
        app(FonnteService::class)->send($data['target'], $data['message']);
    }
}
```

#### 1.4.4. Trigger di Task Creation

Di `app/Livewire/PM/PMDashboard.php` dan `app/Livewire/Admin/TaskOversight.php`:

```php
use App\Notifications\TaskAssignedWhatsapp;
use Illuminate\Support\Facades\Notification;

// Setelah task dibuat:
$assignee = User::find($data['assigned_to']);
if ($assignee->phone) {
    $assignee->notify(new TaskAssignedWhatsapp($task, auth()->user()->name));
}
```

### 1.5. Database: Tambah Kolom `phone` di Users

Migration:

```php
Schema::table('users', function (Blueprint $table) {
    $table->string('phone', 20)->nullable()->after('email');
});
```

---

## 2. Email Notifications (SMTP)

### 2.1. Flow Email

| Trigger | Pengirim | Penerima | Tujuan |
|---------|----------|----------|--------|
| PM kirim pesan ke Member | PM (via form) | Member tertentu | Komunikasi tugas, pengingat deadline |
| Super Admin kirim pesan ke PM | Super Admin (via form) | PM tertentu | Evaluasi kinerja, arahan, teguran |

### 2.2. Tech Stack

| Komponen | Teknologi |
|----------|-----------|
| Mailer | Laravel Mail (SMTP) |
| Queue | Laravel Queue (default sync) |
| View | Blade email templates |

### 2.3. Environment Variables

```
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your-email@gmail.com
MAIL_PASSWORD=your-app-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=your-email@gmail.com
MAIL_FROM_NAME="${APP_NAME}"
```

### 2.4. Implementasi

#### 2.4.1. Mailable

```
app/Mail/ComposeMessage.php
```

```php
<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ComposeMessage extends Mailable
{
    use Queueable, SerializesModels;

    public string $subject;
    public string $body;
    public string $senderName;

    public function __construct(string $subject, string $body, string $senderName)
    {
        $this->subject = $subject;
        $this->body = $body;
        $this->senderName = $senderName;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->subject,
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.compose',
        );
    }
}
```

#### 2.4.2. Blade View

```
resources/views/emails/compose.blade.php
```

```blade
<x-mail::message>
# {{ $subject }}

{{ $body }}

<x-mail::button :url="url('/login')">
Buka TaskFlow
</x-mail::button>

Terima kasih,<br>
{{ $senderName }}
</x-mail::message>
```

#### 2.4.3. Livewire Component: Compose Email

```
app/Livewire/Admin/ComposeEmail.php  (untuk SA → PM)
app/Livewire/PM/ComposeEmail.php     (untuk PM → Member)
```

```php
<?php

namespace App\Livewire\Admin;

use App\Mail\ComposeMessage;
use App\Models\User;
use Livewire\Component;
use Illuminate\Support\Facades\Mail;

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
        $recipients = User::where('role', 'pm')->get();
        return view('livewire.admin.compose-email', ['recipients' => $recipients]);
    }
}
```

#### 2.4.4. Routing

```php
// web.php — di dalam group admin
Route::get('/compose-email', ComposeEmail::class)->name('compose.email');

// web.php — di dalam group pm
Route::get('/compose-email', \App\Livewire\PM\ComposeEmail::class)->name('compose.email');
```

### 2.5. Blade View untuk Form

```
resources/views/livewire/admin/compose-email.blade.php
```

```blade
<div>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Kirim Email ke Project Manager
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                @if (session('message'))
                    <div class="mb-4 px-4 py-3 bg-green-100 text-green-700 rounded">
                        {{ session('message') }}
                    </div>
                @endif

                <form wire:submit="send" class="space-y-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Penerima (PM)</label>
                        <select wire:model="recipientId"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                            <option value="">— Pilih PM —</option>
                            @foreach ($recipients as $pm)
                                <option value="{{ $pm->id }}">{{ $pm->name }} ({{ $pm->email }})</option>
                            @endforeach
                        </select>
                        @error('recipientId') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Subjek</label>
                        <input type="text" wire:model="subject"
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                        @error('subject') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Pesan</label>
                        <textarea wire:model="body" rows="6"
                                  class="mt-1 block w-full rounded-md border-gray-300 shadow-sm"></textarea>
                        @error('body') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>

                    <div class="flex justify-end">
                        <button type="submit"
                                class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                            Kirim Email
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
```

---

## 3. Konfigurasi

### 3.1. File `config/fonnte.php`

```php
<?php

return [
    'token' => env('FONNTE_TOKEN'),
];
```

### 3.2. Register Channel di `config/app.php`

```php
'providers' => [
    // ...
    App\Providers\NotificationServiceProvider::class,
],
```

### 3.3. NotificationServiceProvider

```
app/Providers/NotificationServiceProvider.php
```

```php
<?php

namespace App\Providers;

use App\Notifications\Channels\FonnteChannel;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\ServiceProvider;

class NotificationServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Notification::extend('fonnte', fn ($app) => new FonnteChannel());
    }
}
```

---

## 4. Ringkasan Tabel

| Fitur | Kanal | Pengirim | Penerima | Trigger |
|-------|-------|----------|----------|---------|
| Notifikasi task baru ke PM | WhatsApp | Sistem | PM | SA assign task ke PM |
| Notifikasi task baru ke Member | WhatsApp | Sistem | Member | PM assign task ke Member |
| Komunikasi SA → PM | Email | Super Admin | PM | Manual via form compose |
| Komunikasi PM → Member | Email | PM | Member | Manual via form compose |

---

## 5. Catatan Keamanan

- Token Fonnte hanya di `.env`, tidak di-commit.
- Validasi `$request->user()->role` sebelum akses form compose email.
- Notifikasi WA dikirim via queue agar tidak blocking response.
- Gunakan `throttle` jika diperlukan untuk mencegah spam email/WA.
