# TaskFlow — Collaborative Task Management System

Sistem manajemen tugas kolaboratif multi-level: **Super Admin → Project Manager → Anggota**. Berbasis Laravel 12 + Filament 3 + Livewire 3 + Tailwind CSS + Alpine.js. Docker-ready.

---

## Role & Alur

| Role | Tanggung Jawab |
|------|---------------|
| **Super Admin** | Buat & assign tugas ke PM, final approve, arbitrase, eskalasi, user management, PM performance, hubungi team |
| **Project Manager** | Kelola workspace & tim, assign tugas ke anggota, review hasil, reject/approve |
| **Anggota** | Kerjakan tugas, upload file, terima & kirim ulang revisi |

Alur lengkap (9 status):
```
Super Admin → draft
               ↓ assigned_pm
PM → assign → assigned_member
                ↓
Anggota → submit → pending_pm
                     ↓ (approve/reject)
PM → approve → pending_admin   PM → reject → revision (counter +1)
       ↓                           ↓ (max 3× → pending_arbitration)
Super Admin → approve → done     Anggota → re-upload → pending_pm (loop)
```

---

## Tech Stack

- **Backend:** PHP 8.2+, Laravel 12
- **Admin Panel:** Filament 3 (dengan Shield, Logger, Progressbar, Slim Scrollbar, dll.)
- **Frontend:** Blade, Tailwind CSS 3.4, Alpine.js 3.0, Livewire 3
- **Database:** MariaDB 10.6 (InnoDB)
- **Auth:** Laravel Breeze (session-based) + Spatie Permission
- **Notifications:** Database (inbox), Mail (SMTP), WhatsApp (Fonnte API)
- **Build:** Vite

---

## User Demo

| Role | Email | Password |
|------|-------|----------|
| Super Admin | admin@admin.com | password |
| PM | pm1@test.com | password |
| Member | member1@test.com | password |
| Member | member2@test.com | password |

Login di `/login`.

---

## Struktur Direktori (Key)

```
app/
├── Livewire/
│   ├── Admin/                # Admin/super_admin: dashboard, tasks, assign, users, PM perf, arbitration
│   ├── SuperAdmin/           # Super Admin: dashboard, create task, task list
│   ├── Pm/                   # PM: dashboard, compose email
│   ├── Member/               # Member: dashboard
│   └── NotificationBell.php  # Livewire: inbox bell component
├── Console/Commands/
│   ├── SendDeadlineReminders.php
│   ├── CheckPmEscalation.php
│   ├── ProjectInitialize.php
│   ├── ProjectUpdate.php
│   ├── DevIde.php
│   └── Recache.php
├── Models/
│   ├── User.php, Task.php, Workspace.php
│   ├── Team.php, TeamMember.php
│   ├── Category.php, Comment.php, Subtask.php
│   ├── Attachment.php, InboxNotification.php
│   └── TaskStatusHistory.php
├── Services/
│   ├── TaskStatusHistoryService.php  # Status transition + auto-notification
│   └── FonnteService.php             # WhatsApp API client
├── Notifications/
│   ├── TaskAssignedNotification.php
│   ├── TaskCommentNotification.php
│   ├── DeadlineReminderNotification.php
│   └── Channels/FonnteChannel.php
├── Http/Middleware/
│   ├── CheckRole.php          # Filter by role column
│   └── CheckActive.php        # Block inactive users
resources/views/
├── layouts/
│   ├── super-admin.blade.php, pm.blade.php, member.blade.php
│   └── navigation.blade.php   # Includes notification bell
└── livewire/
    ├── admin/, super-admin/, pm/, member/
    └── notification-bell.blade.php
```

---

## Routes

| Prefix | Middleware | Halaman |
|--------|-----------|---------|
| `/super-admin` | super_admin | Dashboard, Buat Tugas, Daftar Tugas |
| `/admin` | admin,super_admin | Dashboard, Tasks, Oversight, Assign, Users, PM Performance, Arbitration Recap, Hubungi Team, Compose Email |
| `/pm` | pm | Dashboard, Compose Email |
| `/member` | member | Dashboard |
| `/tasks` | all | Read-only all tasks |

---

## Status Task (9 status)

| Status | Arti |
|--------|------|
| `draft` | Draft — baru dibuat Super Admin |
| `assigned_pm` | Dikirim ke PM — menunggu ditugaskan ke anggota |
| `assigned_member` | Dikerjakan Anggota — PM sudah assign |
| `pending_pm` | Menunggu Review PM — anggota sudah submit |
| `revision` | Revisi — ditolak PM, anggota perbaiki |
| `pending_admin` | Menunggu Approval Admin — disetujui PM |
| `pending_arbitration` | Arbitrase — revisi ≥ 3×, Super Admin putuskan |
| `done` | Selesai — disetujui Super Admin |
| `cancelled` | Dibatalkan |

---

## Notifikasi

Tiap transisi status otomatis kirim InboxNotification ke penerima sesuai mapping:

| Transisi | Penerima |
|----------|----------|
| draft → assigned_pm | PM |
| assigned_pm → assigned_member | Anggota |
| assigned_member → pending_pm | PM |
| pending_pm → revision | Anggota |
| pending_pm → pending_admin | Creator |
| pending_admin → done | Creator + PM |
| → pending_arbitration | Creator |
| → cancelled | Creator + PM + Anggota |

Channel: inbox (database) selalu aktif. Email & WhatsApp via Laravel Notification.

---

## Cara Install

```bash
git clone <repo-url>
cd src
cp .env.example .env
composer install
npm install
php artisan key:generate
php artisan migrate --seed
php artisan storage:link
npm run build
```

Atau sekali jalan:

```bash
php artisan project:init
```

Pastikan DB MariaDB running, sesuaikan `.env`.

---

## Migrasi & Seeder

```bash
php artisan migrate:fresh --seed
```

Seeder bawaan: 1 super_admin, 1 PM (Budi), 2 member (Ahmad, Dewi), 1 workspace, 10+ tasks.

---

## Perintah Artisan

```bash
# Init project: migrate fresh + seed + shield generate + optimize
php artisan project:init

# Update project: migrate + shield + optimize
php artisan project:update

# Deadline reminders (terjadwal: setiap jam)
php artisan reminders:deadline

# Eskalasi PM (terjadwal: setiap 6 jam)
php artisan tasks:check-pm-escalation
```
