# Next Features: WhatsApp & Email Notification System

## Overview
Integrasi notifikasi dua kanal: WhatsApp (via Fonnte API) dan Email (via SMTP).

---

## 1. WhatsApp Notifications (Fonnte API)

### 1.1. Flow Notifikasi

| Trigger | Pengirim | Penerima | Pesan |
|---------|----------|----------|-------|
| Super Admin assign task ke PM | Sistem | PM | "Task baru: {title} dari Super Admin. Deadline: {deadline}" |
| PM assign task ke Member | Sistem | Member | "Task baru: {title} dari {PM_name}. Deadline: {deadline}" |

### 1.2. Tech Stack
- Gateway: Fonnte API (https://api.fonnte.com/send)
- HTTP Client: Laravel Http facade
- Queue: Laravel Queue
- Config: `config/fonnte.php`

### 1.3. Environment
```
FONNTE_TOKEN=your-token
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your-email@gmail.com
MAIL_PASSWORD=your-app-password
```

---

## 2. Implementasi

### 2.1. Service Class
`app/Services/FonnteService.php` — method `send(phone, message)`, `formatPhoneNumber(phone)`.

### 2.2. Notification
`app/Notifications/TaskAssignedWhatsapp.php` — channel 'fonnte', via `FonnteChannel`.

### 2.3. Channel
`app/Notifications/Channels/FonnteChannel.php` — calls FonnteService.

### 2.4. Trigger Points (update these file references)
- **Super Admin → PM:** `app/Livewire/Admin/TaskList.php` — setelah `createTask()`.
- **PM → Member:** `app/Livewire/Pm/PmDashboard.php` — setelah `assignToMember()`.

### 2.5. Database
Migration: `$table->string('phone', 20)->nullable()` di tabel `users` (sudah ada).

---

## 3. Email Notifications

### 3.1. Mailable
`app/Mail/ComposeMessage.php` — blade template `emails.compose`.

### 3.2. Livewire Components
- `app/Livewire/Admin/ComposeEmail.php` — SA → PM.
- `app/Livewire/Pm/ComposeEmail.php` — PM → Member.

### 3.3. Routes (already exist)
```php
Route::get('/compose-email', \App\Livewire\Admin\ComposeEmail::class)->name('compose.email');
Route::get('/compose-email', \App\Livewire\Pm\ComposeEmail::class)->name('compose.email');
```

---

## 4. Ringkasan

| Fitur | Kanal | Pengirim | Penerima | Trigger |
|-------|-------|----------|----------|---------|
| Notifikasi task ke PM | WhatsApp | Sistem | PM | SA assign task |
| Notifikasi task ke Member | WhatsApp | Sistem | Member | PM assign task |
| Komunikasi SA → PM | Email | SA | PM | Manual form |
| Komunikasi PM → Member | Email | PM | Member | Manual form |
