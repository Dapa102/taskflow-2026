# TaskFlow — Collaborative Task Management System

Sistem manajemen tugas kolaboratif multi-level: **Atasan → Super Admin → Project Manager → Anggota**. Berbasis Laravel 11 + Livewire 3 + Tailwind CSS + Alpine.js. Docker-ready.

---

## Role & Alur

| Role | Tanggung Jawab |
|------|---------------|
| **Atasan** | Buat tugas → rekomendasi PM → kirim ke PM |
| **Super Admin** | Buat & assign tugas ke PM, final approve, arbitrase, eskalasi, user management |
| **Admin** | Dashboard monitoring, PM performance, user management, laporan arbitrase |
| **Project Manager** | Kelola workspace & tim, assign tugas ke anggota, review hasil, reject/approve |
| **Anggota** | Kerjakan tugas, upload file, terima & kirim ulang revisi |

Alur lengkap (9 status):
```
Atasan/Super Admin → draft
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

- **Backend:** PHP 8.2+, Laravel 11
- **Frontend:** Blade, Tailwind CSS 3.4, Alpine.js 3.0, Livewire 3
- **Database:** MariaDB 10.6 (InnoDB)
- **Auth:** Laravel Breeze (session-based)
- **Notifications:** Database (inbox), Mail (SMTP), WhatsApp (Fonnte API)
- **Build:** Vite

---

## User Demo

| Role | Email | Password |
|------|-------|----------|
| Atasan | atasan@test.com | password |
| Super Admin | admin@admin.com | password |
| PM | pm1@test.com | password |
| Member | member1@test.com | password |

Login di `/login`.

---

## Struktur Direktori (Key)

```
app/
├── Livewire/
│   ├── Admin/                # Admin dashboard, tasks, user management, PM perf, arbitration
│   ├── Atasan/               # Atasan/Super Admin: dashboard, create task, task list
│   ├── Pm/                   # PM: dashboard (workspace, assign, review)
│   ├── Member/               # Member: dashboard (my tasks, upload)
│   └── NotificationBell.php  # Livewire: inbox bell component
├── Console/Commands/
│   ├── SendDeadlineReminders.php
│   └── CheckPmEscalation.php
├── Models/
│   ├── User.php, Task.php, Workspace.php
│   ├── Team.php, TeamMember.php
│   ├── Attachment.php, InboxNotification.php
│   └── TaskStatusHistory.php
├── Services/
│   └── TaskStatusHistoryService.php  # Status transition + auto-notification
├── Notifications/
│   ├── TaskAssignedNotification.php
│   └── DeadlineReminderNotification.php
├── Http/Middleware/
│   ├── CheckRole.php          # Filter by role column
│   └── CheckActive.php        # Block inactive users
resources/views/
├── layouts/
│   ├── admin.blade.php, atasan.blade.php
│   ├── pm.blade.php, member.blade.php
│   └── navigation.blade.php   # Includes notification bell
└── livewire/
    ├── admin/, atasan/, pm/, member/
    └── notification-bell.blade.php
```

---

## Routes

| Prefix | Middleware | Halaman |
|--------|-----------|---------|
| `/atasan` | atasan | Dashboard, Buat Tugas, Daftar Tugas |
| `/super-admin` | super_admin | Dashboard, Buat Tugas, Daftar Tugas |
| `/admin` | admin | Dashboard, Tasks, Oversight, Assign, Users, PM Performance, Arbitration Recap, Hubungi Team |
| `/pm` | pm | Dashboard, Compose Email |
| `/member` | member | Dashboard |
| `/tasks` | all | Read-only all tasks |

---

## Status Task (9 status)

| Status | Arti |
|--------|------|
| `draft` | Draft — baru dibuat Atasan/Super Admin |
| `assigned_pm` | Dikirim ke PM — menunggu ditugaskan ke anggota |
| `assigned_member` | Dikerjakan Anggota — PM sudah assign |
| `pending_pm` | Menunggu Review PM — anggota sudah submit |
| `revision` | Revisi — ditolak PM, anggota perbaiki |
| `pending_admin` | Menunggu Approval Admin — disetujui PM |
| `pending_arbitration` | Arbitrase — revisi ≥ 3×, Super Admin putuskan |
| `done` | Selesai — disetujui Super Admin |
| `cancelled` | Dibatalkan |

---

## Fitur per Fase

| Fase | Fitur |
|------|-------|
| **0** | DB schema, 9 status enum, task_status_histories, inbox_notifications, role enum |
| **1** | Super Admin: create task, PM recommendation, task list + audit trail, cancel |
| **2** | PM: assign member. Member: lihat tugas, upload hasil, revision counter |
| **3** | PM: approve → pending_admin. Reject → revision (+counter, auto-arbitration at limit) |
| **4** | Super Admin: final approval (→ done). Arbitration decision (→ pending_admin / → revision) |
| **5** | PM escalation: artisan command detect pending_pm >48h. Super Admin resolve (approve/reject/reassign) |
| **6** | Notifikasi tiap transisi status (inbox). Bell icon navbar + unread badge + dropdown |
| **7** | User management (CRUD + suspend). PM performance (assigned_pm_id). Arbitration recap page |
| **8** | Upload validation, schedules (deadline reminders hourly, escalation every 6h), polish |

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

Pastikan DB MariaDB running, sesuaikan `.env`.

---

## Migrasi & Seeder

```bash
php artisan migrate:fresh --seed
```

Seeder bawaan: 1 atasan, 1 super_admin, 2 PM (Budi, Siti), 2 member (Ahmad, Dewi), 3 workspace, 10+ tasks.

---

## Notifikasi (Phase 6)

Tiap transisi status otomatis kirim InboxNotification ke penerima sesuai mapping (BR-06):

| Transisi | Penerima |
|----------|----------|
| draft → assigned_pm | PM |
| assigned_pm → assigned_member | Anggota |
| assigned_member → pending_pm | PM |
| pending_pm → revision | Anggota |
| pending_pm → pending_admin | Super Admin |
| pending_admin → done | Creator + PM |
| → pending_arbitration | Super Admin |
| → cancelled | Creator + PM + Anggota |

Channel: inbox (database) selalu aktif. Email & WhatsApp via Laravel Notification.

---

## Perintah Artisan

```bash
# Deadline reminders (terjadwal: setiap jam)
php artisan reminders:deadline

# Eskalasi PM (terjadwal: setiap 6 jam)
php artisan tasks:check-pm-escalation
```
