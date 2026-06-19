# Fitur Lanjutan & Status Implementasi

## Status Per Fase

### ✅ V2.0 — SELESAI (Categories, Subtasks, Comments)
Semua fitur: backend + frontend React + Filament admin.

### ✅ V2.5 — SELESAI
| Fitur | Backend | Frontend | Admin | Notes |
|-------|---------|----------|-------|-------|
| **Attachments** | ✅ API + Storage | ✅ Upload/download/hapus | ❌ | Minio/S3 bisa jadi enhancement |
| **Notifications** | ✅ API (all endpoints) | ✅ Bell icon + dropdown | ❌ | Auto-fetch tiap 30 detik |
| **Notif Scheduler** | ✅ Command + cron | — | — | `reminders:deadline` hourly |

### ✅ V3.0 — SELESAI
| Fitur | Backend | Frontend | Admin | Notes |
|-------|---------|----------|-------|-------|
| **Teams** | ✅ Full CRUD + members | ✅ Pages | ✅ TeamResource (super_admin) | — |
| **Task Assignment** | ✅ API (assign/unassign) | ✅ Assign/unassign di TaskDetail | ❌ | Search anggota tim |
| **Reports** | ✅ API (summary/team/export) | ✅ Halaman Reports + export CSV | ❌ | Stat cards + aktivitas |

### ✅ V3.5 — SELESAI
| Fitur | Backend | Frontend | Notes |
|-------|---------|----------|-------|
| **User Profile** | ✅ PUT /api/user | ✅ Halaman edit nama/email/phone | Update di store |
| **Pagination** | ✅ Paginate tasks | ✅ Infinite scroll (IntersectionObserver) | 50 per page |
| **Bulk Actions** | ✅ Bulk delete + status | ✅ Select mode + toolbar + batch | — |
| **PWA** | — | ✅ Manifest.json + Service Worker + SW registrasi | Icons inline SVG |

### ❌ Belum dimulai
- Email notifications di frontend (tapi SMTP sudah aktif)
- Frontend tests (unit + e2e)
- Drag & drop task kanban

## Catatan
- Semua fitur core sudah diimplementasi frontend + backend
- Notifikasi via WhatsApp: butuh nomor hp user diisi di profile
- Untuk development, jalankan `php artisan reminders:deadline` manual
- Build: `npm run build` di `src/`
