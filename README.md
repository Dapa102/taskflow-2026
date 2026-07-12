# TaskFlow — Collaborative Task Management System

Sistem manajemen tugas kolaboratif multi-level: **Super Admin → Project Manager → Anggota**. Berbasis Laravel 12 + Livewire 3 + Tailwind CSS + Alpine.js.

Fitur lengkap: notifikasi multi-channel (inbox, email SMTP, WhatsApp Fonnte), approval bertingkat, arbitrase otomatis, revisi counter, deputy PM, deadline reminder, eskalasi review, dashboard performa, export PDF, audit log.

---

## Role & Alur

| Role | Tanggung Jawab |
|------|---------------|
| **Super Admin** | Buat & assign tugas ke PM, final approve, arbitrase, eskalasi, user & workspace management, pantau performa PM/Member, audit log, hubungi tim (email/WA) |
| **Project Manager** | Kelola workspace & tim, assign tugas ke anggota, review hasil, reject/approve, deputy PM saat berhalangan |
| **Anggota** | Kerjakan tugas, upload file, terima & kirim ulang revisi |

Alur 9 status:

```
Super Admin → draft
               ↓ assigned_pm
PM → assign → assigned_member
                ↓ submit
Anggota → pending_pm
            ↓ (approve/reject)
PM → approve → pending_admin       PM → reject → revision (counter +1)
      ↓                                ↓ (max 3× → pending_arbitration)
Super Admin → approve → done        Anggota → re-upload → pending_pm (loop)
```

Eskalasi: jika PM tidak review >48 jam, otomatis naik ke Super Admin.

---

## Tech Stack

- **Backend:** PHP 8.2+, Laravel 12
- **Frontend:** Blade, Tailwind CSS 3.4, Alpine.js 3.0, Livewire 3
- **Database:** MariaDB 10.6 / MySQL (InnoDB)
- **Auth:** Laravel Breeze (session-based)
- **Notifications:** Database (inbox), Mail SMTP, WhatsApp Fonnte
- **PDF Export:** DomPDF (barryvdh/laravel-dompdf)
- **Build:** Vite

---

## User Demo

| Role | Email | Password |
|------|-------|----------|
| Super Admin | admin@admin.com | password |
| PM | pm1@test.com | password |
| PM (cadangan) | pm2@test.com | password |
| Member | member1@test.com | password |
| Member | member2@test.com | password |

Login di `/login`.

---

## Fitur Lengkap

### Manajemen Tugas
- CRUD tugas dengan 9 status workflow
- Upload attachment (gambar, dokumen, PDF)
- Riwayat status (TaskStatusHistory) tiap transisi
- Filter tugas oleh status, PM, member, tenggat waktu

### Approval Bertingkat
- Approval Super Admin final setelah PM approve
- Arbitrase otomatis jika revisi ≥ 3×
- Eskalasi review PM jika deadline review >48 jam
- Deputy PM menggantikan PM utama jika berhalangan

### Notifikasi Multi-Channel
- **Inbox Database:** notifikasi internal selalu aktif
- **Email SMTP:** fallback otomatis setelah inbox
- **WhatsApp Fonnte:** terintegrasi via Laravel Notification channel
- **Deadline Reminder:** perintah `reminders:deadline` terjadwal tiap jam

### Laporan & Export
- **Performa PM:** total tugas, selesai, terlambat, tingkat penyelesaian
- **Performa Member:** detail tugas per status, tingkat penyelesaian
- **Tugas Terlambat:** daftar tugas overdue, filter workspace/tanggal
- **Export PDF:** semua laporan bisa diexport via DomPDF

### Audit Log
- Catat semua transisi status tugas
- Filter log berdasarkan aksi, pengguna, tipe entitas, rentang tanggal
- Halaman audit khusus Super Admin

---

## Struktur Direktori

```
src/
├── app/
│   ├── Livewire/
│   │   ├── SuperAdmin/           # Dashboard, users, workspaces, tasks, approval,
│   │   │                         # oversight, arbitration, PM/member performance,
│   │   │                         # late tasks, audit logs, compose email, hubungi team
│   │   ├── Pm/                   # Dashboard, tasks, review, members, workspace
│   │   ├── Member/               # Dashboard, tasks, history, teams
│   │   └── NotificationBell.php  # Inbox bell component
│   ├── Console/Commands/
│   │   ├── SendDeadlineReminders.php   # Deadline notif (hourly)
│   │   ├── CheckPmEscalation.php       # PM review escalation (6h)
│   │   ├── ProjectInitialize.php       # Init project
│   │   └── ProjectUpdate.php           # Update project
│   ├── Models/
│   │   ├── User.php, Task.php, Workspace.php
│   │   ├── Project.php, Category.php, Comment.php, Subtask.php
│   │   ├── Attachment.php, InboxNotification.php
│   │   ├── TaskStatusHistory.php, AuditLog.php
│   │   └── Team.php, TeamMember.php
│   ├── Services/
│   │   ├── TaskStatusHistoryService.php  # Status transition + notification
│   │   ├── AuditService.php             # Audit logging
│   │   └── FonnteService.php            # WhatsApp API
│   ├── Notifications/
│   │   ├── TaskMailNotification.php
│   │   ├── TaskAssignedNotification.php
│   │   ├── TaskCommentNotification.php
│   │   ├── DeadlineReminderNotification.php
│   │   └── Channels/FonnteChannel.php
│   ├── Enums/
│   │   └── TaskStatus.php      # Enum 9 status
│   └── Http/Middleware/
│       ├── CheckRole.php       # Filter by role
│       └── CheckActive.php     # Block inactive users
├── resources/views/
│   ├── layouts/
│   │   ├── super-admin.blade.php
│   │   ├── pm.blade.php
│   │   └── member.blade.php
│   ├── livewire/
│   │   ├── super-admin/
│   │   ├── pm/
│   │   └── member/
│   └── pdf/                   # PDF templates (DomPDF)
├── routes/
│   └── web.php
└── tests/
    ├── Feature/
    │   ├── Api/               # Notification, attachment, comment, category, report
    │   ├── Auth/              # Login, registration, password
    │   ├── PmPerformanceTest.php
    │   ├── MemberPerformanceTest.php
    │   ├── LateTasksTest.php
    │   ├── AuditLogTest.php
    │   ├── TaskCrudTest.php, TaskModelTest.php, TaskPolicyTest.php
    │   ├── TeamTest.php, ProfileTest.php
    │   └── FilamentTaskPageTest.php
    └── Unit/
```

---

## Routes

| Prefix | Middleware | Halaman Utama |
|--------|-----------|---------------|
| `/super-admin` | role:super_admin | Dashboard, Buat/Assign Tugas, Task List, Approval, Oversight, Workspaces, Users, Performa PM/Member, Tugas Terlambat, Laporan Arbitrase, Audit Log, Hubungi Team, Compose Email |
| `/pm` | role:pm | Dashboard, All Tasks, Review Tasks, Buat Tugas, Team Members, Workspace |
| `/member` | role:member | Dashboard, Tugas, Riwayat, Tim |
| `/tasks` | auth | Read-only all tasks (semua role) |

---

## Status Task (9 status)

| Status | Arti |
|--------|------|
| `draft` | Draft — baru dibuat Super Admin |
| `assigned_pm` | Dikirim ke PM — menunggu ditugaskan ke anggota |
| `assigned_member` | Dikerjakan Anggota — PM sudah assign |
| `pending_pm` | Menunggu Review PM — anggota submit |
| `revision` | Revisi — ditolak PM, anggota perbaiki (counter +1) |
| `pending_admin` | Menunggu Approval Admin — disetujui PM |
| `pending_arbitration` | Arbitrase — revisi ≥ 3×, Super Admin putuskan |
| `done` | Selesai — disetujui Super Admin |
| `cancelled` | Dibatalkan |

---

## Notifikasi — Mapping Transisi

| Transisi | Penerima |
|----------|----------|
| draft → assigned_pm | PM |
| assigned_pm → assigned_member | Anggota |
| assigned_member → pending_pm | PM |
| pending_pm → revision | Anggota |
| pending_pm → pending_admin | Creator + PM |
| pending_admin → done | Member + PM |
| → pending_arbitration | Creator |
| → cancelled | Creator + PM + Anggota |

Channel: **Inbox** selalu aktif. **Email** fallback otomatis. **WhatsApp** via Fonnte API.

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

---

## Perintah Artisan

```bash
# Init project: migrate fresh + seed + optimize
php artisan project:init

# Update project: migrate + optimize
php artisan project:update

# Deadline reminders (terjadwal: setiap jam)
php artisan reminders:deadline

# Eskalasi review PM (terjadwal: setiap 6 jam)
php artisan tasks:check-pm-escalation
```

---

## Testing

```bash
php artisan test
# 62+ test cases across 20+ test files
```

---

## 📄 Dokumen Terkait

- `docs/nex_update.md` — Track record fitur lanjutan setelah MVP
- `docs/implementation_plan.md` — Rencana implementasi
- `docs/BRD.md` — Business Requirements Document
- `docs/PRD.md` — Product Requirements Document
