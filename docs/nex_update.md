# Next Update Track Record — TaskFlow

Dokumen ini menjadi track record fitur lanjutan setelah MVP TaskFlow selesai.

Nama file mengikuti permintaan: `nex_update.md`.

---

## 1. Tujuan Dokumen

- Mencatat fitur yang belum masuk MVP.
- Menjadi backlog lanjutan setelah MVP stabil.
- Menjaga agar implementasi MVP tetap fokus dan tidak melebar.
- Menyimpan catatan keputusan pengembangan berikutnya.

---

## 2. Status MVP Sebelum Next Update

Next update baru boleh dimulai setelah kondisi berikut terpenuhi:

- Super Admin dapat mengelola user dan workspace.
- Super Admin dapat menunjuk Project Manager.
- Project Manager dapat mengelola project dan anggota workspace.
- Project Manager dapat membuat dan menugaskan task.
- Member dapat melihat dan mengerjakan task.
- Member dapat upload lampiran hasil pekerjaan.
- Project Manager dapat review dan menyelesaikan task.
- Dashboard dasar semua role tersedia.
- Riwayat status task tercatat.
- Notifikasi internal dasar berjalan.
- RBAC sudah aman untuk semua role MVP.

---

## 3. Backlog Next Update

### NU-01 — Approval Final Super Admin

**Deskripsi**
Super Admin memberi persetujuan akhir untuk task yang sudah disetujui Project Manager.

**Alasan**
BRD menyebut Super Admin dapat memberikan persetujuan akhir untuk tugas yang telah disetujui Project Manager.

**Perubahan yang Dibutuhkan**
- Tambah status atau tahap approval setelah review PM.
- Tambah halaman daftar task menunggu approval Super Admin.
- Tambah tombol approve final.
- Tambah riwayat approval.
- Tambah notifikasi ke PM dan Member.

**Status**
Planned setelah MVP.

---

### NU-02 — Revision Counter dan Catatan Revisi Lanjutan

**Deskripsi**
Sistem menghitung jumlah revisi task dan menyimpan catatan revisi lebih detail.

**Alasan**
BRD menyebut pemantauan penghitung revisi saat task dalam proses perbaikan. PRD juga menyinggung catatan revisi dan unggah ulang hasil perbaikan.

**Perubahan yang Dibutuhkan**
- Tambah `revision_counter` di task.
- Tambah tabel atau kolom catatan revisi.
- Tampilkan jumlah revisi di detail task.
- Tampilkan riwayat revisi untuk PM, Member, dan Super Admin.

**Status**
Planned setelah MVP.

---

### NU-03 — Arbitrase Task

**Deskripsi**
Super Admin menjadi pihak penengah ketika task terlalu sering direvisi atau terjadi ketidaksesuaian antara PM dan Member.

**Alasan**
PRD menyebut proses arbitrase apabila diperlukan.

**Perubahan yang Dibutuhkan**
- Tambah status khusus arbitrase.
- Tambah menu arbitrase Super Admin.
- Tambah keputusan arbitrase: setujui, kembalikan revisi, batalkan.
- Simpan catatan keputusan.
- Kirim notifikasi ke pihak terkait.

**Status**
Planned setelah revision counter.

---

### NU-04 — Eskalasi Otomatis Review PM

**Deskripsi**
Sistem memberi peringatan ke Super Admin ketika task terlalu lama menunggu review Project Manager.

**Alasan**
BRD menyebut kebutuhan mekanisme agar alur penyelesaian tidak berhenti saat Project Manager berhalangan.

**Perubahan yang Dibutuhkan**
- Tambah timestamp submit review.
- Tambah scheduled command Laravel.
- Deteksi task yang menunggu review terlalu lama.
- Kirim notifikasi ke Super Admin.
- Tambah halaman daftar eskalasi.

**Status**
Planned setelah MVP stabil.

---

### NU-05 — Deputy Project Manager

**Deskripsi**
Workspace dapat memiliki Project Manager pengganti saat Project Manager utama berhalangan.

**Alasan**
BRD menyebut tidak adanya pengalihan otomatis saat Project Manager cuti atau sakit menjadi masalah bisnis.

**Perubahan yang Dibutuhkan**
- Tambah `deputy_pm_id` pada workspace.
- Tambah UI penunjukan Deputy PM.
- Tambah aturan akses Deputy PM.
- Tambah notifikasi saat Deputy PM aktif.

**Status**
Planned setelah eskalasi otomatis.

---

### NU-06 — Notifikasi Email SMTP

**Deskripsi**
Sistem mengirim notifikasi melalui email selain inbox internal.

**Alasan**
BRD dan PRD menyebut email sebagai media notifikasi.

**Perubahan yang Dibutuhkan**
- Setup SMTP config.
- Buat mail template.
- Tambah queue untuk pengiriman email.
- Simpan status pengiriman.
- Fallback ke inbox internal jika email gagal.

**Status**
Planned setelah notifikasi internal MVP stabil.

---

### NU-07 — Notifikasi WhatsApp Fonnte API

**Deskripsi**
Sistem mengirim notifikasi WhatsApp melalui Fonnte API.

**Alasan**
BRD dan PRD menyebut WhatsApp sebagai salah satu kanal notifikasi.

**Perubahan yang Dibutuhkan**
- Tambah field nomor WhatsApp user.
- Setup Fonnte API key.
- Buat service pengiriman WhatsApp.
- Tambah queue dan retry.
- Simpan status pengiriman.

**Status**
Planned setelah email notification.

---

### NU-08 — Report Lanjutan

**Deskripsi**
Laporan lebih detail untuk Super Admin dan Project Manager.

**Alasan**
BRD menyebut statistik penyelesaian task setiap Member dan statistik aktivitas Project Manager.

**Perubahan yang Dibutuhkan**
- Laporan kinerja Project Manager.
- Laporan penyelesaian task per Member.
- Laporan keterlambatan task.
- Export laporan ke PDF atau Excel.
- Filter tanggal dan workspace.

**Status**
Planned setelah dashboard MVP.

---

### NU-09 — Deadline Reminder dan Overdue Indicator

**Deskripsi**
Sistem memberi tanda task mendekati deadline atau sudah terlambat.

**Alasan**
BRD menyebut notifikasi deadline dan pemantauan tugas terlambat.

**Perubahan yang Dibutuhkan**
- Hitung deadline mendekat.
- Badge overdue di dashboard.
- Scheduled reminder.
- Notifikasi ke Member dan PM.

**Status**
Planned setelah notification system stabil.

---

### NU-10 — Audit Log Global

**Deskripsi**
Sistem menyimpan audit log aktivitas penting di luar status task.

**Alasan**
BRD menyebut aktivitas penting pengguna harus dicatat untuk audit dan monitoring.

**Perubahan yang Dibutuhkan**
- Tabel audit logs.
- Log aktivitas user, workspace, project, task, dan membership.
- Halaman audit untuk Super Admin.
- Filter berdasarkan aktor dan tanggal.

**Status**
Planned setelah MVP.

---

## 4. Backlog Future Enhancement Non-MVP

Fitur berikut tetap di luar MVP dan tidak dikerjakan sampai backlog next update utama selesai:

- Aplikasi mobile Android dan iOS.
- Chat antar pengguna.
- Panggilan suara atau video.
- Integrasi cloud storage pihak ketiga.
- Gantt Chart.
- Kalender project.
- Sprint dan backlog internal.
- Subtask atau task bertingkat.
- Single Sign-On (SSO).
- Integrasi GitHub, GitLab, atau Jira.
- Multi-tenant lintas organisasi.

---

## 5. Track Record Perubahan

| Tanggal | Kode | Perubahan | Status | Catatan |
|---|---|---|---|---|
| 2026-07-12 | INIT | Dokumen next update dibuat | Open | Menunggu MVP selesai |

---

## 6. Urutan Prioritas Setelah MVP

```text
NU-01 -> NU-02 -> NU-03 -> NU-04 -> NU-05
Approval   Revision   Arbitrase   Eskalasi   Deputy PM

NU-06 -> NU-07 -> NU-08 -> NU-09 -> NU-10
Email      WhatsApp   Report      Deadline   Audit Log
```

---

## 7. Aturan Update Dokumen

- Tambah baris di Track Record setiap fitur next update mulai dikerjakan.
- Ubah status backlog menjadi `In Progress`, `Done`, atau `Cancelled` sesuai kondisi.
- Jangan memasukkan fitur baru ke MVP tanpa memperbarui `docs/implementation_plan.md`.
- Jika fitur next update mengubah alur bisnis utama, update juga `docs/BRD.md` dan `docs/PRD.md`.
