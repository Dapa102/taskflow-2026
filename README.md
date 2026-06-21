# TaskFlow — Collaborative Task Management System

## Deskripsi
Sistem manajemen tugas kolaboratif 4-level hierarki: **Atasan → Super Admin → Project Manager → Anggota**. Berbasis Laravel 11 + Livewire 3 + Tailwind CSS + Alpine.js.

Setiap role punya sidebar navigasi sendiri, tugas mengalir dari Atasan turun ke anggota dengan review di setiap level.

---

## Role & Alur

| Role | Tanggung Jawab |
|------|---------------|
| **Atasan** | Buat tugas → kirim ke Super Admin. Pantau status tugas (sudah/belum diberikan). |
| **Super Admin** | Terima tugas dari Atasan (Global Tasks). Assign PM. Final approve tugas selesai. |
| **Project Manager** | Kelola tim. Assign tugas ke anggota. Approve/reject hasil kerja. |
| **Anggota** | Kerjakan tugas. Upload file. Terima revisi. |

Alur:
```
Atasan → Super Admin (Global Tasks) → PM (Daftar Tugas) → Anggota
```

---

## Tech Stack

- **Backend:** PHP 8.2+, Laravel 11
- **Frontend:** Blade, Tailwind CSS 3.4, Alpine.js 3.0, Livewire 3
- **Database:** MariaDB 10.6 (InnoDB)
- **Auth:** Laravel Breeze (session-based)
- **Build:** Vite

---

## User Demo

| Role | Email | Password |
|------|-------|----------|
| Atasan | atasan@test.com | password |
| Super Admin | admin@admin.com | password |
| PM | pm1@test.com | password |
| Anggota | member1@test.com | password |
| Anggota | member2@test.com | password |

Login di `/login`.

---

## Struktur Direktori (Key)

```
app/
├── Livewire/
│   ├── Admin/          # Super Admin components
│   ├── Atasan/         # Atasan components
│   ├── Pm/             # PM components
│   ├── Member/         # Member components
│   └── AllTasks.php    # Read-only all tasks
├── Models/
│   ├── User.php
│   ├── Task.php
│   ├── Workspace.php
│   ├── Team.php
│   ├── TeamMember.php
│   └── Attachment.php
├── Http/Middleware/
│   ├── CheckRole.php   # Filter by role
│   └── CheckActive.php # Block inactive users
resources/views/
├── layouts/
│   ├── admin.blade.php
│   ├── atasan.blade.php
│   ├── pm.blade.php
│   └── member.blade.php
├── livewire/
│   ├── admin/
│   ├── atasan/
│   ├── pm/
│   └── member/
└── auth/               # Breeze auth views
```

---

## Routes

| Prefix | Role | Halaman |
|--------|------|---------|
| `/atasan` | atasan | Dashboard, Buat Tugas, Tugas Saya |
| `/admin` | admin | Dashboard, Daftar Tugas, Global Tasks, PM Performance, Hubungi Team |
| `/pm` | pm | Dashboard |
| `/member` | member | Dashboard |
| `/tasks` | all | Read-only all tasks |

---

## Status Task

### Workflow Status
```
todo → on_progress → pending_pm → pending_admin → done
                  ↘ revision ↗
```

| Status | Arti |
|--------|------|
| todo | Menunggu |
| on_progress | Dikerjakan |
| pending_pm | Review PM |
| pending_admin | Review Admin |
| revision | Revisi |
| done | Selesai |

### Global Tasks Status (Admin)
| Status | Arti |
|--------|------|
| Belum Diberikan | Tugas dari Atasan, belum di-assign ke PM |
| Sudah Diberikan | Tugas dari Atasan, sudah di-assign ke PM |

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

Seeder bawaan: 1 atasan, 1 admin, 2 PM (Budi, Siti), 2 member (Ahmad, Dewi), 3 workspace, 10+ tasks.
