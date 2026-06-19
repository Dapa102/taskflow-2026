# BUSINESS REQUIREMENTS DOCUMENT (BRD) - MVP
## Daily Task Management System (Personal Edition)

**Versi:** 3.0 (Final MVP Scope)
**Tanggal:** 19 Juni 2026
**Status:** Final untuk Pengembangan Awal

---

## 1. PENDAHULUAN

### 1.1 Tujuan Dokumen
Dokumen ini adalah acuan resmi untuk membangun **versi perdana (MVP)** dari aplikasi manajemen pekerjaan sehari-hari. Fokus utama adalah menyediakan alat yang **stabil, cepat, dan fungsional** bagi pengguna individu untuk mencatat, melacak, dan menyelesaikan tugas pribadi.

### 1.2 Ruang Lingkup MVP
Sistem dibangun sebagai **Single Page Application (SPA)** dengan backend API yang terpisah. Aplikasi hanya melayani pengguna tunggal (tidak ada fitur tim) dan berfokus pada siklus hidup dasar sebuah tugas: **Buat → Baca → Perbarui Status → Hapus**.

### 1.3 Target Pengguna (MVP)
| No | Jenis Pengguna | Deskripsi |
|----|----------------|-----------|
| 1 | **Individual User** | Pengguna tunggal yang ingin mengatur rutinitas, pekerjaan freelance, atau tugas kuliah/kantor sehari-hari. |

---

## 2. TUJUAN BISNIS (MVP)

1. **Bukti Konsep (Proof of Concept)** - Meluncurkan aplikasi dalam waktu ≤ 4 minggu.
2. **Validasi Pasar** - Mendapatkan pengguna awal untuk menguji apakah aplikasi ini membantu produktivitas.
3. **Landasan Teknis** - Membangun arsitektur (Laravel API + Vue) yang siap dikembangkan ke fitur yang lebih kompleks di masa depan.

---

## 3. KEBUTUHAN FUNGSIONAL (FITUR MVP)

### 🔴 LEVEL P0 (WAJIB HADIR - "Kill Zone")

| ID | Modul | Fitur | Deskripsi |
|----|-------|-------|-----------|
| UF-01 | **Auth** | Register | Pengguna mendaftar dengan **Nama**, **Email**, dan **Password** |
| UF-02 | **Auth** | Login | Pengguna masuk menggunakan Email dan Password |
| UF-03 | **Auth** | Logout | Pengguna keluar dari sistem |
| UF-04 | **Auth** | Route Guard | Halaman dashboard tidak bisa diakses jika belum login (otomatis redirect ke login) |
| TF-01 | **Task** | Buat Tugas | Pengguna dapat membuat tugas baru dengan **Judul** (*wajib diisi*) dan **Deskripsi** (*opsional*) |
| TF-02 | **Task** | Lihat Daftar Tugas | Menampilkan seluruh tugas dalam bentuk daftar/table |
| TF-03 | **Task** | Edit Tugas | Pengguna dapat mengubah **Judul**, **Deskripsi**, **Deadline**, **Prioritas**, dan **Status** |
| TF-04 | **Task** | Hapus Tugas | Pengguna dapat menghapus tugas yang tidak diperlukan |
| TF-05 | **Task** | Ubah Status | Tugas memiliki 3 status wajib: **To-Do** → **On-Progress** → **Done** |
| TF-06 | **Task** | Deadline | Pengguna dapat menambahkan tanggal batas akhir (Deadline) pada tugas |
| TF-07 | **Task** | Prioritas | Tugas memiliki 3 level prioritas: **Rendah**, **Sedang**, **Tinggi** |

### 🟡 LEVEL P1 (SANGAT DISARANKAN - "User Experience Basic")

| ID | Modul | Fitur | Deskripsi |
|----|-------|-------|-----------|
| TF-08 | **Task** | Filter Status | Pengguna dapat memfilter daftar tugas berdasarkan status (misal: klik "To-Do" hanya menampilkan tugas To-Do) |
| TF-09 | **Task** | Sorting Otomatis | Daftar tugas diurutkan secara default berdasarkan **Deadline terdekat** (paling mendesak di atas) |
| TF-10 | **Task** | Pencarian (Search) | Pengguna dapat mencari tugas berdasarkan **Judul** |
| DF-01 | **Dashboard** | Ringkasan Jumlah | Dashboard menampilkan angka total tugas per status (Contoh: To-Do: 5, On-Progress: 3, Done: 10) |
| UX-01 | **UI/UX** | Loading State | Menampilkan animasi/indikator "Memuat..." saat aplikasi sedang mengambil data dari server |
| UX-02 | **UI/UX** | Validasi Form | Form tidak bisa dikirim jika judul tugas kosong; menampilkan pesan error yang jelas dari backend |

---

## 4. KEBUTUHAN NON-FUNGSIONAL (MVP)

| ID | Kategori | Deskripsi |
|----|----------|-----------|
| NF-01 | **Keamanan** | Autentikasi menggunakan **Laravel Sanctum** dengan token yang disimpan di HTTP-Only Cookie atau LocalStorage (sesuai konfigurasi). |
| NF-02 | **Keamanan** | Semua endpoint API (kecuali login/register) harus dilindungi oleh middleware `auth:sanctum`. |
| NF-03 | **Kinerja** | Waktu muat halaman (First Contentful Paint) < 3 detik. |
| NF-04 | **Database** | Hanya menggunakan 2 tabel utama: `users` dan `tasks` (tidak ada tabel kategori, assignee, dll.). |
| NF-05 | **Ketersediaan** | Aplikasi harus berjalan di browser modern (Chrome, Firefox, Edge, Safari versi terbaru). |

---

## 5. SPESIFIKASI TEKNIS (ARSITEKTUR)

| Komponen | Teknologi | Keterangan |
|----------|-----------|------------|
| **Backend API** | Laravel 11 (PHP 8.2+) | Menyediakan endpoint RESTful |
| **Frontend SPA** | Vue 3 (Composition API) | Menggunakan `<script setup>` |
| **State Management** | Pinia | Menyimpan data user dan daftar tugas secara global |
| **Routing Frontend** | Vue Router v4 | Navigasi antar halaman (Login, Register, Dashboard) |
| **HTTP Client** | Axios | Menghubungkan Vue ke Laravel API + Interceptor Token |
| **Database** | MariaDB (>= 10.6) | Relational database |
| **Autentikasi API** | Laravel Sanctum | Manajemen token berbasis cookie/token |
| **CSS Framework** | Tailwind CSS | Styling cepat dan responsif |

### 5.1 Struktur Folder (Monorepo Sederhana)
```
project-mvp/
├── backend/               (Laravel API)
│   ├── app/Models/
│   ├── routes/api.php
│   └── .env
└── frontend/              (Vue 3 SPA)
    ├── src/
    │   ├── views/         (Login.vue, Register.vue, Dashboard.vue)
    │   ├── components/    (TaskList.vue, TaskForm.vue, FilterButton.vue)
    │   ├── stores/        (authStore.js, taskStore.js)
    │   ├── router/        (index.js - berisi Route Guard)
    │   └── api/           (axios instance & endpoint calls)
    ├── package.json
    └── vite.config.js
```

---

## 6. DATABASE DESIGN (MVP - HANYA 2 TABEL)

Berikut adalah struktur minimalis untuk memenuhi semua fitur di atas:

### Tabel `users`

| Kolom | Tipe Data | Keterangan |
|-------|-----------|------------|
| id | BIGINT (PK, AI) | Primary Key |
| name | VARCHAR(255) | Nama lengkap pengguna |
| email | VARCHAR(255) | Unique, untuk login |
| password | VARCHAR(255) | Hash bcrypt |
| created_at | TIMESTAMP | Otomatis |
| updated_at | TIMESTAMP | Otomatis |

### Tabel `tasks`

| Kolom | Tipe Data | Keterangan |
|-------|-----------|------------|
| id | BIGINT (PK, AI) | Primary Key |
| user_id | BIGINT (FK) | Foreign Key ke `users.id` (ON DELETE CASCADE) |
| title | VARCHAR(255) | **Wajib diisi** (Judul tugas) |
| description | TEXT | **Nullable** (Deskripsi opsional) |
| status | ENUM('todo','on_progress','done') | Default: 'todo' |
| priority | ENUM('low','medium','high') | Default: 'medium' |
| deadline | DATE | **Nullable** (Tanggal batas akhir) |
| created_at | TIMESTAMP | Otomatis |
| updated_at | TIMESTAMP | Otomatis |

> **Catatan:** Tidak ada tabel `categories`, `task_assignees`, atau `notifications` di MVP.

---

## 7. LIST API ENDPOINT (BACKEND LARAVEL)

Hanya 7 endpoint yang perlu dibuat di `routes/api.php`:

| Method | Endpoint | Fungsi | Proteksi |
|--------|----------|--------|----------|
| POST | `/api/register` | Registrasi akun baru | ❌ Public |
| POST | `/api/login` | Login dan mendapatkan token | ❌ Public |
| POST | `/api/logout` | Logout (revoke token) | ✅ Auth:sanctum |
| GET | `/api/user` | Ambil data profil (untuk cek login & tampil nama di header) | ✅ Auth:sanctum |
| GET | `/api/tasks` | Ambil daftar tugas (bisa pakai query: `?status=todo&search=belajar`) | ✅ Auth:sanctum |
| POST | `/api/tasks` | Buat tugas baru | ✅ Auth:sanctum |
| GET | `/api/tasks/{id}` | Ambil detail 1 tugas (untuk modal edit) | ✅ Auth:sanctum |
| PUT | `/api/tasks/{id}` | Update tugas | ✅ Auth:sanctum |
| DELETE | `/api/tasks/{id}` | Hapus tugas | ✅ Auth:sanctum |

---

## 8. LIST HALAMAN FRONTEND (VUE ROUTER)

| Path | Nama Halaman | Komponen | Deskripsi |
|------|--------------|----------|-----------|
| `/login` | Login | `Login.vue` | Form email & password |
| `/register` | Register | `Register.vue` | Form nama, email, password |
| `/dashboard` | Dashboard | `Dashboard.vue` | Menampilkan ringkasan jumlah tugas + daftar tugas (dengan filter & search) |
| *(Modal)* | Form Tugas | `TaskForm.vue` | Muncul sebagai modal/dialog untuk tambah atau edit tugas |

---

## 9. DI LUAR LINGKUP MVP (TIDAK DIKERJAKAN!)

Fitur-fitur berikut **secara tegas DITUNDA** ke rilis versi 2.0 agar tidak menghambat peluncuran:

- ❌ Kolaborasi Tim / Berbagi Tugas
- ❌ Kategori atau Label
- ❌ Notifikasi (Email atau Web Push)
- ❌ Sub-tugas (Nested Task)
- ❌ Komentar / Diskusi pada tugas
- ❌ Laporan Grafik atau Ekspor PDF/CSV
- ❌ Ganti Foto Profil
- ❌ Dark Mode
- ❌ Integrasi dengan Google Calendar

---

## 10. KRITERIA KEBERHASILAN MVP

| No | Metrik | Target Minimal |
|----|--------|----------------|
| 1 | Fungsionalitas | Semua fitur P0 dan P1 berjalan tanpa error kritis |
| 2 | Stabilitas API | Response time < 500ms untuk semua endpoint (dengan < 50 tugas) |
| 3 | Pengalaman Pengguna | Pengguna dapat membuat tugas pertama dalam waktu < 1 menit setelah registrasi |
| 4 | Deployment | Aplikasi berhasil di-deploy ke environment staging (atau production) |

---

## 11. JADWAL PENGEMBANGAN (4 MINGGU)

| Minggu | Fokus | Aktivitas Utama |
|--------|-------|-----------------|
| **Minggu 1** | **Backend (Laravel API)** | Setup Laravel + MariaDB, Buat Model & Migration `users` & `tasks`, Implementasi Sanctum Auth (Login/Register), Buat semua endpoint CRUD Tasks, Testing API dengan Postman. |
| **Minggu 2** | **Frontend (Vue 3) - Bagian 1** | Setup Vue + Vite + Pinia + Router, Buat halaman Login & Register, Buat interceptor Axios untuk token, Implementasi Route Guard. |
| **Minggu 3** | **Frontend (Vue 3) - Bagian 2** | Buat halaman Dashboard, Komponen Daftar Tugas, Modal Form (Tambah/Edit), Implementasi Fitur Filter, Search, dan Sorting. |
| **Minggu 4** | **Integrasi & Polish** | Hubungkan semua API ke Vue, Tambahkan Loading State & Validasi Form, Uji coba End-to-End (manual), Deploy ke Server. |

---

## 12. PERSETUJUAN

| Jabatan | Nama | Tanda Tangan | Tanggal |
|---------|------|--------------|---------|
| Product Owner | | | |
| Tech Lead | | | |
