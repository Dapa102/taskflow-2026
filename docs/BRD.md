# 📄 BUSINESS REQUIREMENT DOCUMENT (BRD) - REVISI FINAL
## Collaborative Task Management System (Pure Team Edition)

---

**Versi:** 6.0 (Super Admin dengan Fungsi Oversight Penuh)
**Tanggal:** 20 Juni 2026
**Status:** Final untuk Stakeholder

---

## 1. Ringkasan Eksekutif

Dokumen ini menguraikan persyaratan untuk pengembangan aplikasi kolaborasi tim yang dirancang khusus untuk membantu tim kecil (3-10 orang) mengelola tugas sehari-hari. Sistem ini **sepenuhnya berbasis tim**; setiap tugas wajib terikat pada Workspace dan ditugaskan (assign) ke anggota tim tertentu. 

Aplikasi ini memiliki tiga pilar utama: 
1. **Project Manager** sebagai koordinator yang membuat tugas dan memantau timnya.
2. **Team Member** sebagai pelaksana yang hanya fokus pada tugasnya.
3. **Super Admin** sebagai pengawas platform yang tidak hanya mengelola akun, tetapi juga memantau kinerja seluruh Project Manager dan timnya (melihat tugas yang selesai, pending, atau terlambat) untuk memastikan produktivitas dan kepatuhan di seluruh ekosistem.

---

## 2. Latar Belakang dan Justifikasi Bisnis

- **Konteks:** Dalam tim kecil, pemimpin tim (PM) sering kali sibuk mengkoordinasi hingga lupa memonitor secara objektif. Sementara itu, pemilik platform membutuhkan visibilitas penuh untuk memastikan semua tim berjalan produktif dan tidak ada penyalahgunaan.
- **Permasalahan:** Tools kolaborasi yang ada biasanya hanya memberikan visibilitas kepada PM tim masing-masing. Pemilik platform (Super Admin) tidak memiliki alat untuk melihat "gambaran besar" kinerja seluruh tim secara agregat.
- **Solusi yang Diusulkan:** Membangun platform di mana Super Admin memiliki **hak melihat (read-only oversight)** ke semua tugas di semua tim, lengkap dengan filter status (belum selesai, selesai, terlambat), serta dasbor khusus untuk menilai kinerja setiap Project Manager.

---

## 3. Tujuan Bisnis

- Menyediakan platform kolaborasi yang terjangkau bagi tim kecil.
- Memberdayakan Project Manager dalam mendelegasikan dan memonitor tugas.
- Memberikan **Super Admin** kendali penuh atas keamanan **dan** visibilitas operasional (produktivitas tim).
- Membantu Super Admin mengidentifikasi tim atau PM yang bermasalah (banyak tugas terlambat) lebih awal.
- Menjadi alat bagi Super Admin untuk melakukan audit tugas secara cepat.

---

## 4. Ruang Lingkup

**A. Modul Manajemen Workspace (PM):**
- PM membuat workspace, mengundang member, dan mengelola anggota.

**B. Modul Manajemen Tugas Tim (PM & Member):**
- PM membuat & assign tugas; Member hanya mengupdate status.
- **Tidak ada tugas pribadi.** Semua tugas bersifat tim.

**C. Modul Pengawasan & Oversight (Super Admin - Diperluas):**
- Admin dapat melihat **daftar seluruh tugas dari semua workspace** dengan filter status (Pending / Selesai / Terlambat).
- Admin dapat melihat **detail** sebuah tugas (judul, deskripsi, assignee, deadline, status) tanpa bisa mengeditnya.
- Admin dapat melihat **dasbor kinerja per Project Manager** (berapa total tugas di timnya, berapa yang selesai tepat waktu, berapa yang terlambat).
- Admin tetap memiliki fungsi manajemen akun (aktivasi/suspend pengguna).

---

## 5. Stakeholders dan Pengguna

| Stakeholder | Peran dan Tanggung Jawab |
|-------------|---------------------------|
| **Project Manager (Pimpinan Tim)** | Membuat workspace, mengundang anggota, membuat & assign tugas, memantau progres timnya sendiri. |
| **Team Member (Anggota Tim)** | Menerima tugas dari PM, mengupdate status tugas yang menjadi tanggung jawabnya. |
| **Super Admin** | Mengelola akun pengguna, **memantau seluruh aktivitas tugas di semua tim**, mengevaluasi kinerja PM, dan memastikan kepatuhan terhadap aturan platform. |
| **Pemilik Produk** | Menentukan prioritas fitur berdasarkan umpan balik pasar. |

---

## 6. Persyaratan Fungsional

### 6.1 Manajemen Akun & Peran
| Kode | Kebutuhan | Prioritas |
|------|-----------|-----------|
| F-01 | Pengguna mendaftar dengan memilih peran: **Project Manager** atau **Team Member**. | Wajib |
| F-02 | Semua pengguna dapat login dan logout. | Wajib |
| F-03 | Sistem membatasi akses fitur berdasarkan peran (middleware). | Wajib |

### 6.2 Manajemen Workspace (Khusus Project Manager)
| Kode | Kebutuhan | Prioritas |
|------|-----------|-----------|
| F-04 | PM dapat membuat 1 Workspace (nama, deskripsi). | Wajib |
| F-05 | PM dapat mengundang anggota (max 10 orang di MVP). | Wajib |
| F-06 | PM dapat menghapus anggota dari workspace. | Wajib |

### 6.3 Manajemen Tugas (PM & Member)
| Kode | Kebutuhan | Prioritas |
|------|-----------|-----------|
| F-07 | PM dapat membuat tugas (judul, deskripsi, deadline, prioritas) dan **menentukan assignee**. | Wajib |
| F-08 | PM dapat mengedit dan menghapus tugas. | Wajib |
| F-09 | PM dapat melihat dashboard progres timnya sendiri. | Wajib |
| F-10 | Member dapat melihat daftar tugas yang di-assign kepadanya. | Wajib |
| F-11 | Member dapat mengubah status tugas yang di-assign (To-Do → On-Progress → Done). | Wajib |
| F-12 | Member **tidak dapat** mengedit/menghapus tugas (hanya PM yang bisa). | Wajib |

### 6.4 Fungsi Oversight & Pengawasan (Super Admin - DIPERLUAS)
| Kode | Kebutuhan | Prioritas |
|------|-----------|-----------|
| F-13 | Admin dapat melihat **daftar seluruh pengguna** (PM dan Member) dengan status akun. | Wajib |
| F-14 | Admin dapat **mengaktifkan/menonaktifkan** akun pengguna. | Wajib |
| F-15 | Admin dapat melihat **statistik platform dasar** (total user, total workspace, total tugas). | Wajib |
| **F-16** | **Admin dapat melihat daftar seluruh tugas** dari semua workspace yang ada di platform, dengan fitur **filter** berdasarkan: status (Belum Selesai / Selesai / Terlambat), workspace, atau assignee. | **Wajib** |
| **F-17** | **Admin dapat melihat detail** sebuah tugas (judul, deskripsi, deadline, status, assignee, workspace) dalam mode read-only. Admin **tidak memiliki hak** untuk mengedit atau menghapus tugas tersebut. | **Wajib** |
| **F-18** | **Admin dapat melihat dasbor kinerja per Project Manager**, yang menampilkan metrik: Total tugas di timnya, tugas yang sudah selesai, tugas yang terlambat, dan tingkat penyelesaian tepat waktu (%). | **Wajib** |

---

## 7. Persyaratan Non-Fungsional (Kualitatif)

| Kode | Kualitas | Ekspektasi Bisnis |
|------|----------|-------------------|
| **N-01** | **Keamanan & Privasi** | Admin hanya memiliki hak **baca (read-only)** untuk tugas. Admin tidak boleh mengubah atau menghapus tugas. |
| **N-02** | **Reliabilitas** | Uptime minimal 99%. |
| **N-03** | **Kecepatan** | Halaman utama dan halaman admin (dengan data agregat) tampil < 3 detik. |
| **N-04** | **Kemudahan** | Admin harus bisa melihat tugas terlambat di seluruh platform dalam < 2 kali klik. |

---

## 8. Arsitektur Tingkat Tinggi

- **Pola Arsitektur:** Aplikasi Web Terintegrasi (Laravel Fullstack + Livewire).
- **Database:** Satu basis data dengan tabel `users` (role: `pm`, `member`, `admin`), `workspaces`, `workspace_members`, dan `tasks`.
- **Keamanan:** Middleware peran. Admin tidak memiliki akses ke route edit/delete tugas.

---

## 9. Model Data (Ringkas)

- **users:** id, name, email, password, role (pm, member, admin), is_active.
- **workspaces:** id, pm_id (user_id), name, description.
- **workspace_members:** id, workspace_id, user_id.
- **tasks:** id, workspace_id, created_by (user_id), assigned_to (user_id), title, description, status (todo, on_progress, done), priority (low, medium, high), deadline, created_at.

> **Catatan:** Semua tugas wajib memiliki `assigned_to` dan `workspace_id`. Tidak ada tugas tanpa assignee.

---

## 10. Alur Proses Bisnis

1. **Registrasi:** Pengguna mendaftar dan memilih peran (PM atau Member).
2. **Aktivasi:** Admin mengaktifkan akun.
3. **Project Manager:** Membuat workspace → Mengundang member → Membuat & Assign tugas → Memantau timnya.
4. **Team Member:** Menerima undangan → Melihat tugas yang di-assign → Update status.
5. **Super Admin:**
   - Mengelola akun.
   - **Membuka halaman "Pantau Tugas"** → Melihat semua tugas di semua tim → Menyaring berdasarkan status "Terlambat" untuk menemukan tim yang bermasalah.
   - **Membuka halaman "Kinerja PM"** → Melihat daftar PM dan metrik kinerja mereka.

---

## 11. Teknologi

- **Framework:** Laravel Fullstack (Blade + Livewire).
- **Database:** MySQL / MariaDB.
- **Frontend:** Tailwind CSS, Alpine.js.
- **Autentikasi:** Session-based (Laravel Breeze).

---

## 12. Asumsi

- Setiap PM hanya memiliki 1 Workspace.
- Admin adalah pengguna internal yang dibuat melalui seeder.
- Admin tidak akan menyalahgunakan hak baca untuk kepentingan di luar pekerjaan (dijaga oleh kebijakan internal).

---

## 13. Risiko & Mitigasi

| Risiko | Mitigasi |
|--------|----------|
| Admin menyalahgunakan akses baca untuk mengintip data sensitif | Sistem mencatat log akses Admin (siapa, kapan, melihat tugas apa) untuk audit trail di V2. |
| PM merasa diawasi berlebihan | Tampilan Admin tidak menampilkan percakapan internal atau data pribadi, hanya metrik tugas. |

---

## 14. Kriteria Penerimaan (Acceptance Criteria)

| No | Kriteria | Status |
|----|----------|--------|
| 1 | PM dapat membuat workspace, mengundang member, dan membuat tugas. | ✅ Wajib |
| 2 | Member hanya melihat tugas yang di-assign kepadanya dan dapat update status. | ✅ Wajib |
| 3 | PM dapat melihat dashboard progres timnya sendiri. | ✅ Wajib |
| 4 | **Admin dapat melihat daftar semua tugas di semua workspace.** | ✅ Wajib |
| 5 | **Admin dapat memfilter tugas berdasarkan status (Belum Selesai / Selesai / Terlambat).** | ✅ Wajib |
| 6 | **Admin dapat melihat detail tugas (read-only).** | ✅ Wajib |
| 7 | **Admin dapat melihat dasbor kinerja per PM (total tugas, selesai, terlambat).** | ✅ Wajib |
| 8 | Admin dapat mengaktifkan/menonaktifkan akun. | ✅ Wajib |
| 9 | Admin tidak bisa mengedit atau menghapus tugas. | ✅ Wajib |

---

## 15. Use Case (Penjelasan untuk Digambar Manual)

### 15.1 Daftar Aktor (FINAL)

| No | Aktor | Peran |
|----|-------|-------|
| 1 | **Project Manager (Pimpinan Tim)** | Memimpin tim: membuat workspace, mengundang anggota, membuat & assign tugas, memantau progres timnya sendiri. |
| 2 | **Team Member (Anggota Tim)** | Menerima tugas dari PM dan mengupdate status tugas yang ditugaskan kepadanya. |
| 3 | **Super Admin** | Mengelola akun pengguna (aktivasi/suspend), **serta memantau seluruh tugas di platform dan mengevaluasi kinerja setiap PM**. |

---

### 15.2 Daftar Use Case & Narasi Skenario

#### A. Use Case untuk SEMUA Pengguna (PM & Member)

| Kode | Nama Use Case | Aktor | Deskripsi | Skenario Normal |
|------|---------------|-------|-----------|-----------------|
| UC-01 | Mendaftar Akun | PM & Member | Pengguna mengisi form dan memilih peran. | Isi data → pilih peran → daftar. |
| UC-02 | Login | PM & Member | Masuk ke akun. | Isi email & password → masuk. |
| UC-03 | Logout | PM & Member | Keluar dari akun. | Klik logout. |

#### B. Use Case untuk Project Manager (Khusus)

| Kode | Nama Use Case | Aktor | Deskripsi | Skenario Normal | Include/Extend |
|------|---------------|-------|-----------|-----------------|----------------|
| UC-04 | Membuat Workspace | PM | Membuat tim baru. | Isi nama & deskripsi → simpan. | - |
| UC-05 | Mengundang Anggota | PM | Mengundang user lain ke tim. | Masukkan email → kirim undangan. | - |
| UC-06 | Menghapus Anggota | PM | Mengeluarkan anggota dari tim. | Klik keluarkan → konfirmasi. | **<<include>>** Konfirmasi |
| UC-07 | Membuat Tugas | PM | Membuat tugas untuk anggota. | Isi judul, pilih assignee → simpan. | **<<include>>** Validasi |
| UC-08 | Mengedit Tugas | PM | Mengubah tugas. | Klik edit → ubah data → simpan. | - |
| UC-09 | Menghapus Tugas | PM | Menghapus tugas. | Klik hapus → konfirmasi. | **<<include>>** Konfirmasi |
| UC-10 | Melihat Progres Tim | PM | Melihat dashboard timnya. | Buka halaman utama → lihat statistik tim. | - |

#### C. Use Case untuk Team Member (Khusus)

| Kode | Nama Use Case | Aktor | Deskripsi | Skenario Normal |
|------|---------------|-------|-----------|-----------------|
| UC-11 | Melihat Tugas Saya | Member | Melihat daftar tugas yang di-assign. | Buka dashboard → lihat daftar tugas. |
| UC-12 | Mengubah Status Tugas | Member | Update progres tugas. | Pilih status baru (To-Do/Progress/Done). |

#### D. Use Case untuk Super Admin (Khusus - Diperluas)

| Kode | Nama Use Case | Aktor | Deskripsi | Skenario Normal |
|------|---------------|-------|-----------|-----------------|
| UC-13 | Login Admin | Admin | Masuk ke panel admin. | Login dengan kredensial admin. |
| UC-14 | Mengelola Akun Pengguna | Admin | Mengaktifkan/menonaktifkan akun. | Lihat daftar user → klik suspend/aktifkan. |
| **UC-15** | **Memantau Seluruh Tugas Platform** | **Admin** | **Admin melihat daftar semua tugas dari semua workspace.** | Buka halaman "Semua Tugas" → lihat daftar tugas global. |
| **UC-16** | **Menyaring Tugas Global** | **Admin** | **Admin memfilter tugas berdasarkan status (Belum Selesai / Selesai / Terlambat).** | Pilih filter "Terlambat" → sistem tampilkan hanya tugas yang melewati deadline. |
| **UC-17** | **Melihat Detail Tugas (Read-Only)** | **Admin** | **Admin mengklik tugas untuk melihat detail lengkap, tanpa bisa mengedit.** | Klik judul tugas → muncul modal/detail read-only. |
| **UC-18** | **Memantau Kinerja Project Manager** | **Admin** | **Admin melihat dasbor metrik kinerja setiap PM.** | Buka halaman "Kinerja PM" → lihat tabel berisi: Nama PM, Total Tugas, Selesai, Terlambat, dan % Tepat Waktu. |

---

### 15.3 Daftar Relasi (Include / Extend) untuk Diagram

| Use Case Utama | Relasi | Use Case Terkait | Alasan |
|----------------|--------|------------------|--------|
| UC-07 (Membuat Tugas) | **<<include>>** | Validasi Data | Judul wajib diisi. |
| UC-06 (Hapus Anggota) | **<<include>>** | Konfirmasi | Tindakan destruktif. |
| UC-09 (Hapus Tugas) | **<<include>>** | Konfirmasi | Tindakan destruktif. |
| *UC-16 (Menyaring Tugas)* | *Tidak ada* | - | Fitur mandiri. |

