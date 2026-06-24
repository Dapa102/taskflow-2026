# BUSINESS REQUIREMENT DOCUMENT (BRD)
## TaskFlow — Sistem Manajemen Tugas Kolaboratif 4-Level Hierarki

---

**Versi:** 1.0
**Tanggal:** 24 Juni 2026
**Status:** Final untuk Stakeholder

---

## 1. Ringkasan Eksekutif

Dokumen ini menguraikan kebutuhan bisnis untuk pengembangan **TaskFlow**, yaitu sistem manajemen tugas kolaboratif berbasis web yang dirancang untuk organisasi dengan struktur hierarki 4-level: **Atasan, Super Admin, Project Manager, dan Anggota**.

Sistem ini memungkinkan alur distribusi tugas yang terstruktur—mulai dari pembuatan tugas oleh Atasan, distribusi melalui Super Admin, penugasan oleh Project Manager, pengerjaan oleh Anggota, hingga review dan finalisasi. Setiap perubahan status tugas tercatat dengan audit trail yang jelas, memastikan akuntabilitas dan transparansi di setiap tahap.

Dengan TaskFlow, organisasi dapat mengelola tugas secara lebih teratur, memantau progres secara real-time, dan memastikan setiap tugas terselesaikan tepat waktu melalui mekanisme review dan revisi yang terdefinisi.

---

## 2. Latar Belakang dan Justifikasi Bisnis

### 2.1 Konteks
Dalam organisasi yang memiliki struktur hierarki, proses distribusi tugas sering kali melibatkan banyak pihak—dari pemberi arahan hingga pelaksana di lapangan. Namun, tanpa sistem yang terstruktur, tugas sering hilang, tidak jelas statusnya, atau tidak ada akuntabilitas yang jelas.

### 2.2 Permasalahan
- Tidak ada alur distribusi tugas yang baku—tugas sering berpindah secara informal via chat/email.
- Sulit melacak status tugas: apakah sudah dikerjakan, sedang direview, atau masih menunggu.
- Tidak ada catatan audit (siapa mengubah status, kapan, dan alasannya).
- Atasan tidak memiliki visibilitas penuh terhadap tugas yang sudah diberikan.
- Proses review dan revisi tidak terdokumentasi, sehingga terjadi pengulangan pekerjaan yang tidak perlu.

### 2.3 Solusi yang Diusulkan
Membangun sistem manajemen tugas berbasis web dengan fitur:
- Alur distribusi 4-level: **Atasan → Super Admin → PM → Anggota**.
- Status tugas yang jelas (8 status) dengan audit trail setiap perubahan.
- Mekanisme review dan revisi antara PM dan Anggota.
- Upload file hasil kerja oleh Anggota (max 10 MB).
- Dashboard per role dengan navigasi sidebar khusus.
- Notifikasi otomatis via WhatsApp dan Email.
- Manajemen akun pengguna oleh Super Admin.
- Dashboard kinerja PM untuk evaluasi oleh Super Admin.

---

## 3. Tujuan Bisnis

- Menyediakan alur distribusi tugas yang **terstruktur dan terdokumentasi**.
- Memastikan **akuntabilitas** setiap peran (siapa bertanggung jawab atas apa).
- Memberikan **visibilitas penuh** kepada Atasan terhadap status tugas di setiap tahap.
- Memudahkan proses **review dan revisi** antara PM dan Anggota.
- Menyediakan **audit trail** yang lengkap untuk setiap perubahan status tugas.
- Membantu **evaluasi kinerja PM** melalui metrik yang terukur.

---

## 4. Ruang Lingkup

### 4.1 Dashboard Atasan
- Membuat tugas dan mengirim ke Super Admin.
- Memantau status tugas (dari awal hingga selesai).
- Melihat riwayat tugas yang pernah dibuat.

### 4.2 Dashboard Super Admin
- Melihat **Global Tasks** (semua tugas dari Atasan).
- Menunjuk **Project Manager** untuk setiap tugas.
- Memberikan **persetujuan akhir** (final approval) untuk tugas yang sudah di-review PM.
- Mengelola akun pengguna (aktivasi/suspend).
- Memantau **kinerja PM** (total tugas, selesai, overdue, completion rate).

### 4.3 Dashboard Project Manager
- Menerima tugas dari Super Admin.
- Menugaskan tugas ke Anggota tim.
- **Mereview** hasil kerja anggota:
  - **Approve** → lanjut ke Super Admin untuk final approval.
  - **Reject** + catatan revisi → dikembalikan ke Anggota.
- Memantau progres tugas timnya.

### 4.4 Dashboard Anggota
- Melihat daftar tugas yang ditugaskan.
- Mengerjakan tugas dan **mengunggah file** hasil kerja (PDF, DOC, DOCX, ZIP, XLSX, JPG, PNG).
- Menerima catatan revisi dari PM dan mengunggah ulang hasil revisi.

### 4.5 Manajemen Status Tugas (Alur Status)
| Status | Deskripsi | Peran |
|--------|-----------|-------|
| `draft` | Tugas baru dibuat oleh Atasan | Atasan |
| `assigned_pm` | Super Admin menunjuk PM | Super Admin |
| `assigned_member` | PM menugaskan ke Anggota | PM |
| `pending_pm` | Anggota selesai & menunggu review PM | PM |
| `revision` | PM meminta revisi | PM → Anggota |
| `pending_admin` | PM approve → menunggu final Super Admin | Super Admin |
| `done` | Super Admin final approve — selesai | Super Admin |

### 4.6 Ruang Lingkup yang Tidak Termasuk (Out of Scope)
- Aplikasi mobile Android/iOS.
- Integrasi pembayaran online.
- Sistem notifikasi real-time (WebSocket).
- Manajemen proyek multi-workspace (1 PM ↔ 1 workspace di MVP).

---

## 5. Stakeholders dan Pengguna

| Stakeholder | Peran dan Tanggung Jawab |
|-------------|---------------------------|
| **Atasan** | Membuat tugas, memantau status tugas dari awal hingga selesai, dan memastikan tugas yang diberikan terselesaikan tepat waktu. |
| **Super Admin** | Mendistribusikan tugas dari Atasan ke PM, memberikan final approval, mengelola akun pengguna, dan memantau kinerja PM. |
| **Project Manager** | Menerima tugas dari Super Admin, menugaskan ke Anggota, melakukan review hasil kerja, dan menyetujui/menolak dengan catatan revisi. |
| **Anggota** | Mengerjakan tugas, mengunggah file hasil kerja, dan merespons catatan revisi dari PM. |

---

## 6. Persyaratan Fungsional

### 6.1 Manajemen Akun & Peran
| Kode | Kebutuhan | Prioritas |
|------|-----------|-----------|
| F-01 | Pengguna dapat mendaftar (role: Atasan, Super Admin, PM, Anggota). | Wajib |
| F-02 | Pengguna dapat login dengan email dan password. | Wajib |
| F-03 | Pengguna dapat logout. | Wajib |
| F-04 | Sistem memiliki middleware `CheckRole` untuk membatasi akses peran. | Wajib |
| F-05 | Sistem memiliki middleware `CheckActive` untuk menolak akses akun nonaktif. | Wajib |

### 6.2 Manajemen Tugas
| Kode | Kebutuhan | Prioritas |
|------|-----------|-----------|
| F-06 | **Atasan** dapat membuat tugas (judul, deskripsi, prioritas, deadline) dan mengirim ke Super Admin. | Wajib |
| F-07 | **Super Admin** dapat melihat Global Tasks dan menunjuk PM. | Wajib |
| F-08 | **Super Admin** dapat memberikan final approval (`done`). | Wajib |
| F-09 | **PM** dapat menugaskan tugas ke Anggota. | Wajib |
| F-10 | **PM** dapat mereview hasil kerja (approve → pending_admin / reject + catatan → revision). | Wajib |
| F-11 | **Anggota** dapat melihat tugas yang ditugaskan. | Wajib |
| F-12 | **Anggota** dapat mengunggah file hasil kerja dan submit. | Wajib |
| F-13 | **Anggota** dapat merespons revisi dan mengunggah ulang. | Wajib |

### 6.3 Sidebar Navigasi per Role
| Kode | Kebutuhan | Prioritas |
|------|-----------|-----------|
| F-14 | Setiap peran memiliki sidebar navigasi yang berbeda sesuai tanggung jawabnya. | Wajib |
| F-15 | Atasan: Dashboard, Buat Tugas, Riwayat Tugas. | Wajib |
| F-16 | Super Admin: Dashboard, Global Tasks, Kelola Pengguna, PM Performance. | Wajib |
| F-17 | PM: Dashboard, Tugas Tim, Review. | Wajib |
| F-18 | Anggota: Dashboard, Tugas Saya. | Wajib |

### 6.4 Dashboard Kinerja PM (Super Admin)
| Kode | Kebutuhan | Prioritas |
|------|-----------|-----------|
| F-19 | Super Admin dapat melihat metrik kinerja setiap PM. | Wajib |
| F-20 | Metrik mencakup: total tugas, selesai, overdue, completion rate (%). | Wajib |

### 6.5 Notifikasi
| Kode | Kebutuhan | Prioritas |
|------|-----------|-----------|
| F-21 | Sistem mengirim notifikasi WhatsApp otomatis saat status tugas berubah (asinkron). | Wajib |
| F-22 | Sistem mengirim notifikasi Email otomatis saat status tugas berubah. | Wajib |

### 6.6 Audit Trail
| Kode | Kebutuhan | Prioritas |
|------|-----------|-----------|
| F-23 | Setiap perubahan status tugas tercatat (siapa, kapan, dari status apa ke status apa). | Wajib |

### 6.7 Manajemen Pengguna (Super Admin)
| Kode | Kebutuhan | Prioritas |
|------|-----------|-----------|
| F-24 | Super Admin dapat melihat daftar semua pengguna. | Wajib |
| F-25 | Super Admin dapat mengaktifkan/menonaktifkan akun pengguna. | Wajib |

---

## 7. Persyaratan Non-Fungsional (Kualitatif)

| Kode | Kualitas | Ekspektasi Bisnis |
|------|----------|-------------------|
| N-01 | **Keamanan & Privasi** | Setiap peran hanya dapat mengakses data sesuai hak aksesnya (RBAC + Policy). |
| N-02 | **Reliabilitas** | Uptime minimal 99% selama jam operasional (08.00–22.00). |
| N-03 | **Kecepatan** | Halaman utama tampil < 3 detik di jaringan 4G. |
| N-04 | **Kemudahan Penggunaan** | Antarmuka intuitif dengan navigasi yang jelas per role. |
| N-05 | **Audit Trail** | Semua perubahan status tugas tercatat secara permanen. |
| N-06 | **Kompatibilitas** | Dapat diakses melalui browser modern (Chrome, Firefox, Edge, Safari) di desktop dan smartphone. |

---

## 8. Arsitektur Tingkat Tinggi

- **Back-end:** Laravel 11 (PHP 8.2+)
- **Panel/Admin UI:** Filament (untuk Super Admin)
- **Front-end:** Blade, Tailwind CSS 3.4, Alpine.js 3.0, Livewire 3
- **Database:** MariaDB 10.6 (InnoDB)
- **Autentikasi:** Laravel Breeze (session-based)
- **Build Tool:** Vite
- **Web Server:** Nginx
- **Containerization:** Docker Compose (PHP-FPM + Nginx + MariaDB)
- **Notifikasi:** WhatsApp via Fonnte API, Email via SMTP

---

## 9. Model Data (Ringkas)

- **users:** id, name, email, password, role, is_active, phone, created_at, updated_at
- **workspaces:** id, pm_id (user_id), name, description, created_at, updated_at
- **workspace_members:** id, workspace_id, user_id, joined_at
- **tasks:** id, created_by (user_id atasan), assigned_pm (user_id), assigned_member (user_id), workspace_id, title, description, priority (low/medium/high), deadline, status (draft/assigned_pm/assigned_member/pending_pm/revision/pending_admin/done), file_path, file_original_name, review_notes, created_at, updated_at
- **task_status_histories:** id, task_id, status, from_status, to_status, changed_by (user_id), notes, created_at

---

## 10. Alur Proses Bisnis (Ringkas)

1. **Atasan** membuat tugas → status `draft` → masuk ke Global Tasks Super Admin.
2. **Super Admin** membuka Global Tasks → melihat detail → menunjuk PM → status `assigned_pm`.
3. **PM** menerima tugas → menugaskan ke Anggota → status `assigned_member`.
4. **Anggota** melihat tugas → mengerjakan → mengunggah file → submit → status `pending_pm`.
5. **PM** mereview hasil:
   - **Approve** → status `pending_admin` → masuk ke Super Admin.
   - **Reject** + catatan revisi → status `revision` → kembali ke Anggota.
6. **Anggota** menerima revisi → memperbaiki → upload ulang → status kembali `pending_pm`.
7. **Super Admin** membuka tugas `pending_admin` → final approve → status `done`.

> Notifikasi otomatis dikirim setiap kali status berubah.

---

## 11. Teknologi

- **Back-end:** Laravel 11 (PHP 8.2+)
- **Panel/Admin UI:** Filament (Super Admin)
- **Front-end:** Blade, Tailwind CSS 3.4, Alpine.js 3.0, Livewire 3
- **Database:** MariaDB 10.6 (InnoDB)
- **Autentikasi:** Laravel Breeze (session-based)
- **Build Tool:** Vite
- **Web Server:** Nginx
- **Containerization:** Docker Compose
- **Version Control:** Git & GitHub
- **Notifikasi:** WhatsApp (Fonnte API), Email (SMTP)

---

## 12. Asumsi

- Setiap Atasan memiliki akses untuk membuat tugas.
- Super Admin bertindak sebagai pengelola utama platform.
- Setiap PM memimpin 1 workspace (tim) dan bertanggung jawab atas anggota timnya.
- Anggota hanya bertugas pada satu tim.
- File yang diunggah oleh Anggota memiliki format yang didukung.
- Proses review oleh PM dilakukan dalam waktu yang wajar setelah Anggota submit.
- Notifikasi WhatsApp dan Email dikirim secara asinkron (tidak menghambat alur utama).

---

## 13. Risiko & Mitigasi

| Risiko | Mitigasi |
|--------|----------|
| PM tidak melakukan review tepat waktu | Dashboard PM menampilkan indikator tugas yang menunggu review. Super Admin dapat memantau. |
| Anggota tidak mengunggah file sesuai format | Sistem membatasi ekstensi file yang diperbolehkan (pdf, doc, docx, zip, xlsx, jpg, png) dan maksimal 10 MB. |
| Tugas tidak selesai tepat waktu | Deadlines terlihat jelas di dashboard, dan sistem menandai overdue. |
| Kesalahan penunjukan PM oleh Super Admin | Super Admin dapat melihat daftar PM dan workspace-nya sebelum menunjuk. |
| Kehilangan data tugas | Database backup rutin. |
| Notifikasi gagal terkirim | Sistem mencatat log pengiriman; admin dapat melihat dan mengirim ulang secara manual. |

---

## 14. Kriteria Penerimaan

| No | Kriteria | Status |
|----|----------|--------|
| 1 | Atasan dapat membuat tugas dan mengirim ke Super Admin. | ✅ Wajib |
| 2 | Super Admin dapat menunjuk PM untuk tugas dari Atasan. | ✅ Wajib |
| 3 | PM dapat menugaskan tugas ke Anggota. | ✅ Wajib |
| 4 | Anggota dapat melihat tugas yang ditugaskan dan mengunggah file. | ✅ Wajib |
| 5 | PM dapat approve → pending_admin atau reject + catatan → revision. | ✅ Wajib |
| 6 | Super Admin dapat final approve → done. | ✅ Wajib |
| 7 | Setiap perubahan status tercatat dalam audit trail. | ✅ Wajib |
| 8 | Notifikasi WhatsApp dan Email terkirim otomatis saat status berubah. | ✅ Wajib |
| 9 | Super Admin dapat melihat daftar pengguna dan mengaktifkan/menonaktifkan akun. | ✅ Wajib |
| 10 | Super Admin dapat melihat dashboard kinerja PM. | ✅ Wajib |

---

## 15. Use Case Diagram

### 15.1 Daftar Aktor

| No | Aktor | Peran |
|----|-------|-------|
| 1 | **Atasan** | Membuat tugas dan memantau statusnya. |
| 2 | **Super Admin** | Mendistribusikan tugas, final approval, dan mengelola akun. |
| 3 | **Project Manager** | Menugaskan tugas, mereview hasil, approve/reject. |
| 4 | **Anggota** | Mengerjakan tugas, upload file, respons revisi. |

### 15.2 Daftar Use Case

#### A. Use Case untuk Atasan

| Kode | Nama Use Case | Deskripsi |
|------|---------------|-----------|
| UC-01 | Membuat Tugas Baru | Mengisi form tugas (judul, deskripsi, prioritas, deadline) dan mengirim ke Super Admin. |
| UC-02 | Memantau Status Tugas | Melihat daftar tugas yang dibuat beserta statusnya (dari awal hingga selesai). |

#### B. Use Case untuk Super Admin

| Kode | Nama Use Case | Deskripsi |
|------|---------------|-----------|
| UC-03 | Melihat Global Tasks | Melihat semua tugas yang masuk dari Atasan. |
| UC-04 | Menunjuk Project Manager | Memilih PM yang bertanggung jawab atas tugas. |
| UC-05 | Final Approval | Memberikan persetujuan akhir untuk tugas yang sudah di-review PM. |
| UC-06 | Mengelola Akun Pengguna | Mengaktifkan/menonaktifkan akun. |
| UC-07 | Memantau Kinerja PM | Melihat dashboard metrik kinerja PM. |

#### C. Use Case untuk Project Manager

| Kode | Nama Use Case | Deskripsi |
|------|---------------|-----------|
| UC-08 | Menugaskan Anggota | Memilih Anggota untuk mengerjakan tugas. |
| UC-09 | Mereview Hasil Kerja | Memeriksa file hasil kerja Anggota. |
| UC-10 | Menyetujui Hasil Kerja | Approve → status `pending_admin`. |
| UC-11 | Menolak Hasil Kerja | Reject + catatan → status `revision`. |

#### D. Use Case untuk Anggota

| Kode | Nama Use Case | Deskripsi |
|------|---------------|-----------|
| UC-12 | Melihat Tugas Saya | Melihat daftar tugas yang ditugaskan. |
| UC-13 | Mengerjakan & Mengunggah File | Upload file hasil kerja dan submit. |
| UC-14 | Merespons Revisi | Menerima catatan revisi dan mengunggah ulang. |

### 15.3 Relasi Include / Extend

| Use Case Utama | Relasi | Use Case Terkait | Alasan |
|----------------|--------|------------------|--------|
| UC-01 (Membuat Tugas) | **<<include>>** | Validasi Data | Judul wajib diisi. |
| UC-04 (Menunjuk PM) | **<<include>>** | Lihat Detail Tugas | Admin harus lihat detail sebelum menunjuk PM. |
| UC-08 (Menugaskan Anggota) | **<<include>>** | Lihat Anggota Tim | PM harus lihat daftar anggota sebelum assign. |
| UC-09 (Mereview Hasil) | **<<include>>** | Lihat File Upload | PM harus lihat file sebelum memutuskan. |
| UC-11 (Menolak Hasil) | **<<extend>>** | Kirim Catatan Revisi | Hanya jika reject (opsional). |

---

**Status BRD:** ✅ **SIAP UNTUK DIVALIDASI STAKEHOLDER DAN DITERUSKAN KE TIM ENGINEERING.**

---

Dengan BRD ini, seluruh alur bisnis TaskFlow—dari pembuatan tugas oleh Atasan hingga finalisasi oleh Super Admin—terdokumentasi dengan jelas. Setiap peran memiliki fungsi dan tanggung jawab yang terdefinisi, dan alur status tugas (8 status) memastikan transparansi di setiap tahap. 

Silakan gunakan dokumen ini sebagai acuan untuk pengembangan! 🚀