# Implementation Plan — TaskFlow (BRD + PRD Alignment)

Selaras dengan BRD (3 peran, 9 status, 13 alur bisnis) dan PRD (12 product backlog, 5 iterasi).

---

## Phase 0: Database Schema & Status Alignment

### Step 0.1 — `tasks` table: tambah field
- `recommended_pm_id` (FK users, nullable) — rekomendasi PM opsional (F-02, BR-03)
- `revision_counter` (integer, default 0)
- `max_revision_limit` (integer, default 3)
- `submitted_at` (timestamp, nullable) — untuk hitung eskalasi 2x24 jam (BR-02)
- `escalated_at` (timestamp, nullable)
- `cancellation_note` (text, nullable)

### Step 0.2 — Status enum: dari legacy ke 9 status BRD
- Lama: `todo, on_progress, pending_pm, pending_admin, revision, done`
- Baru: `draft, assigned_pm, assigned_member, pending_pm, revision, pending_arbitration, pending_admin, done, cancelled`
- Data migration: `todo` → `draft`, `on_progress` → `assigned_member`

### Step 0.3 — `task_status_histories` table (BRD 6.2, F-05)
- `id, task_id (FK), from_status, to_status, changed_by (FK users), notes (nullable), created_at`
- Setiap transisi status wajib tulis baris

### Step 0.4 — `notifications` table (BRD 11, BR-06)
- `id, user_id (FK), task_id (FK, nullable), type (whatsapp/email/inbox), subject, message, status (pending/sent/failed/read), sent_at, created_at`

### Step 0.5 — `users` table: validasi field
- `nomor_whatsapp` (string, nullable)
- `is_active` (boolean, default true)
- `role` enum: `super_admin`, `pm`, `member`

### Step 0.6 — `workspaces` & `workspace_members` (BRD 9)
- `workspaces`: `id, pm_id (FK), name, description, deputy_pm_id (FK, nullable)` — delegasi pengganti PM (F-14, BR-04)
- `workspace_members`: `id, workspace_id (FK), user_id (FK), joined_at`

---

## Phase 1: Super Admin — Manajemen Tugas & PM (Iterasi 1 PRD = PB-01 + PB-02)

**PRD Iterasi 1 — Fokus: fungsi dasar Super Admin. Backlog: PB-01 Manajemen Tugas, PB-02 Manajemen Project Manager**

### Step 1.1 — Create task → status `draft` (F-01, US-SA-01)
- Form: judul, deskripsi, prioritas (low/medium/high), deadline
- Super Admin membuat → status = `draft`, `assigned_pm` = null
- Simpan riwayat via `task_status_histories`

### Step 1.2 — PM recommendation pada form (F-02, BR-03)
- Dropdown opsional: pilih PM rekomendasi
- Simpan di `recommended_pm_id`
- Tampilkan rekomendasi di daftar tugas

### Step 1.3 — Super Admin Task List (F-04, F-07, US-SA-02)
- Tabel: semua tugas dengan filter status, search, pagination
- Lihat detail tugas (F-04)
- Badge revision counter pada tugas status `revision` (F-06)
- Tombol "Riwayat" → modal `task_status_histories` (F-05)

### Step 1.4 — Assign PM ke tugas (F-08, F-09, US-SA-03, US-SA-04, BR-05)
- Super Admin wajib lihat beban kerja PM sebelum assign (modal workload chart)
- Pilih PM dari daftar → status `assigned_pm`
- Tulis history + notifikasi ke PM

### Step 1.5 — Reassign PM / transfer tugas (F-13, US-SA-07)
- Super Admin pindahkan tugas dari PM A ke PM B
- Reset `assigned_member` jika sudah diassign ke anggota
- Revision counter tetap berlanjut (BR-07)

### Step 1.6 — Deputy PM (F-14, BR-04)
- Set `deputy_pm_id` di `workspaces`
- Deputi mewarisi akses PM (pengecekan role di middleware)

### Step 1.7 — Cancel task (F-08, BR-08)
- Super Admin batalkan tugas kapan saja sebelum `done`
- Konfirmasi + cancellation note → status `cancelled`
- Tulis history + notifikasi

---

## Phase 2: Project Manager — Penugasan Anggota & Anggota — Pelaksanaan Tugas (Iterasi 2 PRD = PB-07 + PB-11)

**PRD Iterasi 2 — Fokus: penugasan & pelaksanaan tugas. Backlog: PB-07 Penugasan Anggota, PB-11 Pelaksanaan Tugas**

### Step 2.1 — PM: lihat tugas masuk (F-17, US-PM-01)
- Dashboard PM → daftar tugas dengan status `assigned_pm`
- Filter, search, detail tugas

### Step 2.2 — PM: assign anggota (F-18, US-PM-02, US-PM-03)
- Lihat anggota workspace + beban kerja tiap anggota (F-19)
- Pilih anggota → status `assigned_member`
- Tulis history + notifikasi ke anggota

### Step 2.3 — Member: lihat "Tugas Saya" (F-25, US-AG-01, US-AG-02)
- Hanya tugas dengan `assigned_member` = current user
- Lihat detail: judul, deskripsi, prioritas, deadline, file referensi

### Step 2.4 — Member: upload hasil kerja (F-26, F-27, US-AG-03)
- Upload file: max 10MB, format: pdf, doc, docx, zip, xlsx, xls, jpg, jpeg, png
- Submit → status `pending_pm`, rekam `submitted_at`
- Tulis history + notifikasi ke PM

### Step 2.5 — Member: lihat status tugas (F-30, US-AG-06)
- Tampilkan status terkini + revision counter + sisa revisi

---

## Phase 3: PM — Peninjauan Hasil & Revisi (Iterasi 3 PRD = PB-08 + PB-09)

**PRD Iterasi 3 — Fokus: peninjauan & revisi. Backlog: PB-08 Peninjauan Hasil Kerja, PB-09 Revisi Tugas**

### Step 3.1 — PM: review hasil kerja (F-20, US-PM-04)
- Lihat file yang diupload anggota
- Lihat revision counter + riwayat revisi

### Step 3.2 — PM: approve (F-21, US-PM-06)
- Setuju → status `pending_admin`
- Tulis history + notifikasi ke Super Admin

### Step 3.3 — PM: reject + revision note (F-22, US-PM-05)
- Tolak → wajib isi catatan revisi → status `revision`
- `revision_counter` +1
- Jika `revision_counter >= max_revision_limit` (3):
  - Status = `pending_arbitration`
  - PM tidak bisa tolak lagi (lock)
  - Notifikasi ke Super Admin (BR-01)
- Tulis history + notifikasi ke anggota

### Step 3.4 — PM: monitor revision counter (F-23, US-PM-07)
- Tampilkan peringatan dini jika revisi mendekati batas

### Step 3.5 — PM: monitor perkembangan tim (F-24)
- Dashboard PM: lihat semua tugas tim + status + progress

### Step 3.6 — Member: baca catatan revisi (F-28, US-AG-04)
- Lihat catatan dari PM di detail tugas

### Step 3.7 — Member: re-upload hasil perbaikan (F-29, US-AG-05)
- Upload file baru → status kembali ke `pending_pm`
- Tulis history + notifikasi ke PM

---

## Phase 4: Super Admin — Persetujuan Akhir & Arbitrase (Iterasi 4 PRD = PB-03 + PB-04)

**PRD Iterasi 4 — Fokus: penyelesaian tugas. Backlog: PB-03 Persetujuan Tugas, PB-04 Arbitrase**

### Step 4.1 — Super Admin: final approval (F-10, US-SA-05)
- Daftar tugas status `pending_admin`
- Cek file, detail, riwayat revisi
- Setujui → status `done` (F-10)
- Tulis history + notifikasi ke creator + PM

### Step 4.2 — Super Admin: access arbitration list (F-11, US-SA-06)
- Menu khusus: "Arbitrase"
- Daftar tugas status `pending_arbitration`
- Tampilkan riwayat revisi + file lampiran

### Step 4.3 — Super Admin: arbitration decision (F-12)
- **Setujui** → status `pending_admin` (ke antrean final approval, bukan langsung `done`)
- **Kembalikan ke Revisi** → status `revision` + catatan dari Super Admin langsung ke Anggota
- Revision counter tetap berlanjut (BR-07), tidak direset
- Tulis history + notifikasi

---

## Phase 5: Eskalasi PM (BR-02)

### Step 5.1 — `pending_pm` timeout detection
- Artisan command: `tasks:check-pm-escalation`
- Cari: status `pending_pm` AND `submitted_at < now() - 48 hours`
- Trigger: notifikasi ke Super Admin + flag `escalated_at`

### Step 5.2 — Super Admin: resolve escalation
- Lihat daftar tugas tereskalasi
- Tindakan: transfer ke PM lain (reassign) atau approve/reject langsung

---

## Phase 6: Notifikasi (BR-06)

### Step 6.1 — Kirim notifikasi tiap transisi status
- Channel: inbox (database), email (SMTP), WhatsApp (Fonnte API)
- Mapping notifikasi per transisi:

| Transisi | Penerima |
|---|---|
| `draft` → `assigned_pm` | PM |
| `assigned_pm` → `assigned_member` | Anggota |
| `assigned_member` → `pending_pm` | PM |
| `pending_pm` → `revision` | Anggota (+ review_note) |
| `pending_pm` → `pending_admin` | Super Admin |
| `pending_admin` → `done` | Creator + PM |
| `→ pending_arbitration` | Super Admin |
| `→ cancelled` | Creator + PM + Anggota |

### Step 6.2 — Inbox UI (BR-06)
- Bell icon di navbar semua role
- Dropdown: unread count + 5 notifikasi terbaru
- Klik → mark as read → redirect ke task detail

---

## Phase 7: Manajemen Pengguna & Monitoring (Iterasi 5 PRD = PB-05 + PB-06 + PB-10 + PB-12)

**PRD Iterasi 5 — Fokus: pengelolaan sistem & monitoring. Backlog: PB-05, PB-06, PB-10, PB-12**

### Step 7.1 — User management (F-15, US-SA-09)
- CRUD user, role assignment
- Aktivasi/suspend akun (`is_active` toggle)
- Hanya Super Admin yang bisa akses

### Step 7.2 — PM performance report (F-16, F-32, US-SA-10)
- Metrik per PM: total tugas, selesai, terlambat, tingkat penyelesaian
- Tampilkan di menu Laporan Super Admin

### Step 7.3 — PM workload report (F-33)
- Grafik beban kerja PM: tugas aktif, menunggu review, terlambat
- Akses wajib sebelum assign PM (BR-05)

### Step 7.4 — Arbitration recap report (F-34)
- Laporan: semua tugas yang sempat masuk `pending_arbitration`
- Detail: keputusan, waktu, aktor

### Step 7.5 — Timeline / status history report (F-31)
- Per-tugas: timeline lengkap perubahan status

### Step 7.6 — PM: monitor team workload (F-19, US-PM-07)
- Dashboard PM: lihat beban kerja tiap anggota
- Jumlah tugas aktif per anggota

### Step 7.7 — Member: monitor status tugas sendiri (US-AG-06)
- Tampilkan progress bar / status badge di "Tugas Saya"

---

## Phase 8: Edge Cases & Polish

### Step 8.1 — Revision continuity on mutation (BR-07)
- Transfer PM / deputy take over → `revision_counter` tidak direset

### Step 8.2 — Deadline overdue indicator (BRD 14 No.14)
- Badge merah untuk tugas lewat deadline di semua dashboard
- Notifikasi otomatis saat deadline lewat

### Step 8.3 — Upload validation
- Format: pdf, doc, docx, zip, xlsx, xls, jpg, jpeg, png
- Max size: 10MB
- Validasi client + server

### Step 8.4 — Inbox as fallback (BR-06)
- Semua notifikasi tetap disimpan ke inbox meskipun WA/email gagal

---

## Implementation Order

```
Phase 0   →   Phase 1   →   Phase 2   →   Phase 3   →   Phase 4
(db/schema)    (SA: tugas + PM)   (PM:assign + member:exec)   (review + revisi)   (approval + arbitrase)

  →   Phase 5   →   Phase 6   →   Phase 7   →   Phase 8
  (eskalasi)       (notif)       (users + reports)    (polish)
```

**PRD Iterasi Mapping:**
- Iterasi 1 = Phase 1
- Iterasi 2 = Phase 2
- Iterasi 3 = Phase 3
- Iterasi 4 = Phase 4
- Iterasi 5 = Phase 7
- Eskalasi (Phase 5) & Notifikasi (Phase 6) berjalan paralel mulai Iterasi 2-4
- Database (Phase 0) & Polish (Phase 8) = setup + penyempurnaan akhir

## Rollback Plan

- Setiap migration: test `down()` sebelum `up()`
- Data migration status: backup tabel `tasks` dulu
- Feature flags untuk arbitration/escalation baru
