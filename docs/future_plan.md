# Future Plan — TaskFlow

Rencana pengembangan TaskFlow pasca MVP dan Next Update (NU). Dokumen ini sebagai acuan prioritas fitur, perbaikan, dan arah produk ke depan.

## 1. Status Saat Ini

- MVP: ✅ Selesai
- Next Update (NU-01 s.d. NU-10): ✅ Semua selesai
- Total test: 190+ passing (166 + 24 pre-existing failure di API/Auth yang perlu diperbaiki)

## 2. Prioritas Segera (Short-term)

### 2.1 Perbaikan Test yang Gagal

| Kode | Masalah | Rencana |
|------|---------|---------|
| FIX-01 | CategoryTest — `User::categories()` method tidak ada | Tambah relasi hasMany di User model |
| FIX-02 | TaskAssignmentTest — `Task::assignees()` tidak ada | Tambah relasi belongsToMany assignees |
| FIX-03 | SubtaskTest — `progress` field null di response | Tambah `->withCount` atau accessor di Task model |
| FIX-04 | Auth/RegistrationTest — `/register` 404 | Route register mungkin dihapus; sinkronkan test |
| FIX-05 | RoleLoginTest — `Sign In` teks tidak ditemukan | Sinkronkan test dengan tampilan login terbaru |

### 2.2 Polish & QA

| Kode | Item | Rencana |
|------|------|---------|
| POL-01 | Loading state di semua Livewire | Tambah wire:loading + skeleton |
| POL-02 | Error handling form validasi | Konsistenkan pesan error bahasa Indonesia |
| POL-03 | Responsive mobile dashboard | Uji layout grid di layar kecil |
| POL-04 | Empty state konsisten | Semua halaman punya ilustrasi "belum ada data" |
| POL-05 | Dark mode | Filament sudah support, Livewire belum |
| POL-06 | Performance query dashboard | Evaluasi N+1 dan lazy load chart |

### 2.3 Dokumentasi

| Kode | Item | Rencana |
|------|------|---------|
| DOC-01 | API Documentation | Swagger/OpenAPI spec |
| DOC-02 | User Manual | Panduan penggunaan per role |
| DOC-03 | Deployment Guide | Step-by-step deploy ke production |

## 3. Fitur Baru (Medium-term)

### 3.1 Fitur Produk

| Kode | Fitur | Prioritas | Estimasi |
|------|-------|-----------|----------|
| FP-01 | Subtask/checklist di dalam task | High | 3 hari |
| FP-02 | Drag & drop task board (Kanban) | High | 5 hari |
| FP-03 | Kalender task + deadline view | Medium | 3 hari |
| FP-04 | Gantt chart project | Medium | 5 hari |
| FP-05 | Import task dari Excel/CSV | Medium | 2 hari |
| FP-06 | Export laporan Excel (.xlsx) | Medium | 2 hari |
| FP-07 | Bulk action task (approve, assign, delete) | Low | 2 hari |
| FP-08 | Template task untuk project serupa | Low | 3 hari |
| FP-09 | Role custom (bukan hardcode super_admin/pm/member) | Low | 5 hari |
| FP-10 | Soft delete + trash untuk task & project | Low | 2 hari |

### 3.2 Notifikasi & Komunikasi

| Kode | Fitur | Prioritas | Estimasi |
|------|-------|-----------|----------|
| NK-01 | Chat internal antar role | Medium | 7 hari |
| NK-02 | Komentar & diskusi per task | High | 4 hari |
| NK-03 | Notifikasi push browser | Medium | 2 hari |
| NK-04 | Template notifikasi custom per event | Low | 3 hari |
| NK-05 | Integrasi Telegram | Low | 2 hari |

### 3.3 API & Integrasi

| Kode | Fitur | Prioritas | Estimasi |
|------|-------|-----------|----------|
| API-01 | Stabilkan REST API (test coverage) | High | 3 hari |
| API-02 | API rate limiting + throttling | High | 1 hari |
| API-03 | API key untuk third-party | Medium | 2 hari |
| API-04 | Webhook untuk event task | Medium | 3 hari |
| API-05 | Integrasi GitHub/GitLab commit | Low | 5 hari |
| API-06 | Integrasi Google Calendar | Low | 3 hari |
| API-07 | SSO (Google, GitHub) | Low | 3 hari |

## 4. Aplikasi Mobile (Long-term)

### 4.1 Mobile App

| Kode | Fitur | Platform | Estimasi |
|------|-------|----------|----------|
| MB-01 | Task list + filter | Android & iOS | 2 minggu |
| MB-02 | Upload lampiran | Android & iOS | 1 minggu |
| MB-03 | Notifikasi push FCM | Android & iOS | 1 minggu |
| MB-04 | QR scan untuk task | Android | 3 hari |
| MB-05 | Biometric login | Android & iOS | 2 hari |

Stack: Flutter (cross-platform) atau React Native.

### 4.2 PWA (Progressive Web App)

Alternatif ringan sebelum native app:

| Fitur | Estimasi |
|-------|----------|
| Service worker + offline cache | 2 hari |
| Push notification | 1 hari |
| Install prompt | 1 hari |

## 5. Infrastructure & DevOps

| Kode | Item | Prioritas |
|------|------|-----------|
| INF-01 | CI/CD pipeline (GitHub Actions) | High |
| INF-02 | Auto-test di setiap PR | High |
| INF-03 | Staging environment | High |
| INF-04 | Monitoring (Laravel Horizon, Telescope) | Medium |
| INF-05 | Automated backup database | Medium |
| INF-06 | Load balancing + horizontal scaling | Low |

## 6. Risiko & Mitigasi

| Risiko | Dampak | Mitigasi |
|--------|--------|----------|
| Skalabilitas query dashboard | Lambat saat data besar | Pagination, caching (Redis), materialized view |
| Keamanan API | Data bocor | Rate limit, audit log, test coverage RBAC |
| Adopsi user rendah | Produk tidak terpakai | User training, UX improvement, feedback loop |
| Ketergantungan library usang | Security patch lambat | Dependabot / Renovate, upgrade terjadwal |

## 7. Roadmap Timeline

```text
Q3 2026 (Jul-Sep)     Q4 2026 (Oct-Dec)      Q1 2027 (Jan-Mar)
├── Fix failing tests  ├── Subtask/checklist  ├── Mobile app (PWA)
├── Polish UI/UX       ├── Chat internal      ├── API stabilization
├── Kanban board       ├── Calendar view      ├── Webhook integrasi
├── Komentar task      ├── Export Excel       ├── SSO
└── CI/CD setup        └── Gantt chart       └── Multi-tenant
```

## 8. Cara Update Dokumen

- Tambah baris baru saat fitur mulai dikerjakan.
- Ubah status menjadi `In Progress`, `Done`, atau `Cancelled`.
- Jika fitur mengubah alur bisnis, update juga `BRD.md`, `PRD.md`, dan `implementation_plan.md`.
- Setiap akhir sprint, review prioritas dan sesuaikan.

---

*Dokumen ini bersifat living document — diperbarui secara berkala sesuai kebutuhan proyek.*
