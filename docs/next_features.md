# Fitur Lanjutan & Status Implementasi

## Status Per Fase

### ✅ V2.0 — SELESAI (Categories, Subtasks, Comments)
Semua fitur: backend + frontend React + Filament admin.

### 🔶 V2.5 — Sebagian
| Fitur | Backend | Frontend | Admin | Notes |
|-------|---------|----------|-------|-------|
| **Attachments** | ✅ API + Storage | ✅ Upload/download/hapus | ❌ | Minio/S3 bisa jadi enhancement |
| **Notifications** | ✅ API (all endpoints) | **❌ Belum** | ❌ | Bell icon + dropdown di frontend |
| **Notif Scheduler** | **❌ Belum** (cron job) | — | — | Deadline reminder command |

### 🔶 V3.0 — Sebagian
| Fitur | Backend | Frontend | Admin | Notes |
|-------|---------|----------|-------|-------|
| **Teams** | ✅ Full CRUD + members | ✅ Pages | ✅ TeamResource (super_admin) | — |
| **Task Assignment** | ✅ API (assign/unassign) | **❌ Belum** | ❌ | UI assign di task detail |
| **Reports** | ✅ API (summary/team/export) | **❌ Belum** | ❌ | Dashboard chart atau export |

### ❌ Belum dimulai
- User profile page (edit nama/email/avatar/password)
- Email notifications (SMTP setup)
- PWA manifest + service worker
- Frontend tests (unit + e2e)
- Drag & drop task kanban
- Bulk actions (batch delete/status)
- Pagination/infinite scroll

## Prioritas Rekomendasi

1. **Notifications** (frontend bell icon) — backend sudah siap
2. **User profile** — backend endpoint `/api/user` sudah, tinggal form
3. **Task Assignment UI** — backend sudah, tinggal dropdown assign di TaskDetail
4. **Notif scheduler** — `php artisan make:command CheckDeadlines` + cron
5. **Reports frontend** — halaman simple dengan filter period
6. Email + PWA — enhancement non-kritis
