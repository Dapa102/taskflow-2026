# TaskFlow — Panduan Dokumentasi

Repositori ini berisi dokumentasi lengkap sistem **TaskFlow**, aplikasi manajemen tugas kolaboratif multi-level.

---

## Daftar Dokumen

| File | Isi |
|------|-----|
| [`README.md`](../README.md) | README utama proyek — role, fitur, cara install, struktur |
| [`BRD.md`](BRD.md) | Business Requirements Document — kebutuhan bisnis & fungsional |
| [`PRD.md`](PRD.md) | Product Requirements Document — spesifikasi produk & teknis |
| [`implementation_plan.md`](implementation_plan.md) | Rencana implementasi MVP & milestone |
| [`nex_update.md`](nex_update.md) | Track record fitur lanjutan setelah MVP (NU-01 s.d. NU-10) |

---

## Gambaran Cepat

| Item | Detail |
|------|--------|
| **Stack** | Laravel 12, Livewire 3, Tailwind CSS, Alpine.js, MariaDB/MySQL |
| **Role** | Super Admin, Project Manager, Anggota |
| **Workflow** | 9 status: draft → assigned_pm → assigned_member → pending_pm → revision (max 3×) → pending_admin → done (dengan arbitrase & eskalasi) |
| **Notifikasi** | Inbox database, Email SMTP, WhatsApp Fonnte |
| **Fitur Lanjutan** | Approval SA, arbitrase, eskalasi, deputy PM, deadline reminder, laporan + export PDF, audit log |
| **Testing** | Pest PHP — 20+ test files, 62+ test cases |

---

## Cara Memulai

```bash
cd src
cp .env.example .env
composer install
npm install
php artisan key:generate
php artisan migrate --seed
php artisan storage:link
npm run build
# atau: php artisan project:init
```

Akses `/login`. Demo user ada di README utama.

---

## Untuk Kontributor

1. Baca `BRD.md` & `PRD.md` untuk memahami konteks bisnis
2. Lihat `implementation_plan.md` untuk arsitektur & milestone
3. Cek `nex_update.md` untuk track record fitur yang sudah/belum dikerjakan
4. Jalankan `php artisan test` sebelum pull request

---

## Struktur Direktori Dokumentasi

```
docs/
├── BRD.md                  # Business requirements
├── PRD.md                  # Product requirements & spesifikasi
├── implementation_plan.md  # Rencana implementasi
├── nex_update.md           # Track record fitur lanjutan
└── README.md               # Dokumen ini
```
