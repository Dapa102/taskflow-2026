# PRD ADDENDUM: FITUR LANJUTAN (POST-MVP)
## Daily Task Management System - Versi 2.0 & 3.0

**Versi Dokumen:** 2.0 (Roadmap Lanjutan)
**Tanggal:** 19 Juni 2026
**Target Rilis:** 3 - 6 Bulan setelah MVP (Tergantung prioritas)

---

## 1. STRATEGI PENGEMBANGAN (ROADMAP)

Kita akan mengembangkan fitur secara bertahap agar tim tidak kewalahan:

| Fase | Fokus Utama | Nilai Tambah |
|------|-------------|--------------|
| **V2.0 (Struktur)** | Kategori, Sub-tugas, & Catatan/Komentar | Membantu user mengorganisir tugas kompleks |
| **V2.5 (Produktivitas)** | Notifikasi (Email/Database) & Lampiran File | Mengingatkan deadline dan menyimpan bukti pekerjaan |
| **V3.0 (Kolaborasi)** | Workspace Tim, Assign Tugas, & Laporan Visual | Beralih dari Personal ke Team Tool |

---

# 🔵 FASE V2.0: ORGANISASI & STRUKTUR TUGAS

## Fitur 1: Manajemen Kategori (Categories)
**Deskripsi:** Pengguna dapat membuat "folder" atau label untuk mengelompokkan tugas (misal: *Pekerjaan*, *Kuliah*, *Rumah Tangga*).

### User Story
> Sebagai pengguna, saya ingin mengelompokkan tugas berdasarkan kategori, sehingga saya bisa fokus pada satu area pekerjaan dalam satu waktu.

### Database Changes (Migrasi Baru)
Buat tabel `categories` (relasi One-to-Many ke `tasks`):
```sql
CREATE TABLE categories (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED NOT NULL,
    name VARCHAR(100) NOT NULL,
    color VARCHAR(7) DEFAULT '#3B82F6', -- Kode HEX untuk badge warna
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);
-- Tambahkan kolom category_id ke tabel tasks (nullable)
ALTER TABLE tasks ADD COLUMN category_id BIGINT UNSIGNED NULL;
ALTER TABLE tasks ADD FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL;
```

### API Endpoints Baru (Prefix: `/api/categories`)
| Method | Endpoint | Fungsi | Auth |
|--------|----------|--------|------|
| GET | `/api/categories` | Ambil semua kategori milik user | ✅ |
| POST | `/api/categories` | Buat kategori baru | ✅ |
| PUT | `/api/categories/{id}` | Edit nama/warna kategori | ✅ |
| DELETE | `/api/categories/{id}` | Hapus kategori (tasks ter-set menjadi NULL) | ✅ |

### Acceptance Criteria (AC)
- [ ] Saat membuat/ mengedit tugas, ada dropdown pilihan kategori yang sudah dibuat.
- [ ] Di halaman Dashboard, ada sidebar/filter untuk memilih kategori tertentu.
- [ ] Jika kategori dihapus, tugas yang menggunakan kategori tersebut tidak ikut terhapus (hanya kehilangan label).

---

## Fitur 2: Sub-Tugas (Checklist / Sub-tasks)
**Deskripsi:** Tugas besar bisa dipecah menjadi langkah-langkah kecil (seperti checklist).

### User Story
> Sebagai pengguna, saya ingin memecah tugas besar (misal: "Buat Laporan") menjadi sub-tugas kecil ("Buat Grafik", "Tulis Kesimpulan"), sehingga saya bisa tracking progres lebih detail.

### Database Changes (Migrasi Baru)
Buat tabel `subtasks` (relasi Many-to-One ke `tasks`):
```sql
CREATE TABLE subtasks (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    task_id BIGINT UNSIGNED NOT NULL,
    title VARCHAR(255) NOT NULL,
    is_completed BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    FOREIGN KEY (task_id) REFERENCES tasks(id) ON DELETE CASCADE
);
```

### API Endpoints Baru (Prefix: `/api/tasks/{taskId}/subtasks`)
| Method | Endpoint | Fungsi | Auth |
|--------|----------|--------|------|
| GET | `/api/tasks/{taskId}/subtasks` | Ambil daftar sub-tugas | ✅ |
| POST | `/api/tasks/{taskId}/subtasks` | Tambah sub-tugas baru | ✅ |
| PUT | `/api/subtasks/{id}` | Edit judul sub-tugas | ✅ |
| PATCH | `/api/subtasks/{id}/toggle` | Centang / Uncentang (ubah is_completed) | ✅ |
| DELETE | `/api/subtasks/{id}` | Hapus sub-tugas | ✅ |

### Acceptance Criteria (AC)
- [ ] Di halaman Detail Tugas, ada area khusus untuk menambahkan sub-tugas.
- [ ] User bisa mencentang sub-tugas tanpa harus reload halaman (optimistic update di Vue).
- [ ] Progres utama tugas (persentase) otomatis terhitung dari `(subtasks completed / total subtasks) * 100%`.

---

## Fitur 3: Catatan / Komentar pada Tugas (Task Comments)
**Deskripsi:** Pengguna bisa menambahkan catatan tambahan atau pembaruan status secara kronologis (mirip activity log).

### User Story
> Sebagai pengguna, saya ingin menulis catatan harian pada tugas (misal: "Kendala: data belum masuk"), sehingga saya tidak lupa apa yang sudah saya kerjakan.

### Database Changes (Migrasi Baru)
Buat tabel `comments` (polymorphic atau langsung ke `tasks`):
```sql
CREATE TABLE comments (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    task_id BIGINT UNSIGNED NOT NULL,
    user_id BIGINT UNSIGNED NOT NULL,
    content TEXT NOT NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    FOREIGN KEY (task_id) REFERENCES tasks(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);
```

### API Endpoints Baru (Prefix: `/api/tasks/{taskId}/comments`)
| Method | Endpoint | Fungsi | Auth |
|--------|----------|--------|------|
| GET | `/api/tasks/{taskId}/comments` | Ambil semua komentar (urut ascending) | ✅ |
| POST | `/api/tasks/{taskId}/comments` | Tambah komentar baru | ✅ |
| DELETE | `/api/comments/{id}` | Hapus komentar milik sendiri | ✅ |

---

# 🟡 FASE V2.5: PRODUKTIVITAS & NOTIFIKASI

## Fitur 4: Notifikasi & Reminder (Database & Email)
**Deskripsi:** Sistem akan mengingatkan pengguna jika deadline tugas mepet (H-1) atau jika ada tugas baru (untuk versi kolaborasi nanti).

### User Story
> Sebagai pengguna, saya ingin mendapat notifikasi di dalam aplikasi dan email ketika deadline tugas saya tersisa 1 hari, agar saya tidak melewatkan batas waktu.

### Database Changes (Migrasi Baru) - Pakai Laravel Notifikasi Table bawaan
```bash
php artisan notifications:table
php artisan migrate
```
*(Tabel `notifications` akan otomatis terbuat untuk menyimpan notifikasi di database).*

### Logic / Scheduler (Backend)
- Buat **Command / Job** Laravel yang berjalan setiap jam (`schedule->hourly()`).
- Command ini akan mencari tugas dengan `deadline = tomorrow` dan `status != done`, lalu mengirim notifikasi ke database user (dan email).

### API Endpoints Baru (Prefix: `/api/notifications`)
| Method | Endpoint | Fungsi | Auth |
|--------|----------|--------|------|
| GET | `/api/notifications` | Ambil daftar notifikasi (unread dulu) | ✅ |
| POST | `/api/notifications/{id}/read` | Tandai notifikasi sudah dibaca | ✅ |
| DELETE | `/api/notifications/{id}` | Hapus notifikasi | ✅ |

### Acceptance Criteria (AC)
- [ ] Di pojok kanan atas navbar ada icon lonceng dengan badge angka (jumlah notifikasi unread).
- [ ] User menerima email otomatis (bisa pakai Mailtrap untuk testing) saat deadline H-1.

---

## Fitur 5: Lampiran File (File Attachments)
**Deskripsi:** Pengguna bisa mengunggah file (gambar, PDF, Word) sebagai pendukung tugas.

### User Story
> Sebagai pengguna, saya ingin melampirkan file referensi (misal: foto bukti atau template Excel) ke dalam tugas, sehingga semua data ada di satu tempat.

### Database Changes (Migrasi Baru)
Buat tabel `attachments`:
```sql
CREATE TABLE attachments (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    task_id BIGINT UNSIGNED NOT NULL,
    user_id BIGINT UNSIGNED NOT NULL,
    filename VARCHAR(255) NOT NULL,
    file_path VARCHAR(500) NOT NULL, -- Path di storage
    file_size INT UNSIGNED,          -- Dalam bytes
    mime_type VARCHAR(100),
    created_at TIMESTAMP NULL,
    FOREIGN KEY (task_id) REFERENCES tasks(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);
```

### API Endpoints Baru (Gunakan Multipart Form Data)
| Method | Endpoint | Fungsi | Auth |
|--------|----------|--------|------|
| POST | `/api/tasks/{taskId}/attachments` | Upload file (max 5MB, ekstensi: jpg,png,pdf,docx) | ✅ |
| GET | `/api/tasks/{taskId}/attachments` | Ambil daftar lampiran | ✅ |
| DELETE | `/api/attachments/{id}` | Hapus lampiran (file ikut terhapus dari storage) | ✅ |

---

# 🔴 FASE V3.0: KOLABORASI TIM (ENTERPRISE)

## Fitur 6: Workspace / Tim (Teams)
**Deskripsi:** Pengguna bisa membuat ruang kerja (Workspace) dan mengundang anggota lain.

### Database Changes (Migrasi Baru)
- Tabel `teams`: `id`, `name`, `owner_id` (user_id), `invite_code`.
- Tabel `team_members`: `id`, `team_id`, `user_id`, `role` (admin/member), `joined_at`.

> **Catatan:** Untuk menghindari komplikasi di awal V3, kita tidak ubah struktur `tasks` dulu. Kita tambahkan `team_id` pada tabel `tasks` (nullable). Jika `team_id` NULL, artinya tugas pribadi. Jika terisi, tugas milik tim.

---

## Fitur 7: Assign Tugas ke Anggota Tim (Task Assignment)
**Deskripsi:** Team Leader bisa menugaskan tugas ke anggota tertentu, dan anggota hanya melihat tugas yang di-assign kepadanya.

### User Story
> Sebagai Team Leader, saya ingin menugaskan "Mengerjakan Desain UI" kepada Budi, dan Budi langsung melihat tugas itu di dashboard-nya.

### Database Changes (Migrasi Baru) - Alternatif dari `task_assignees`
Alih-alih menambah kolom `assigned_to` di `tasks` (yang hanya bisa 1 orang), kita buat tabel pivot `task_assignees` agar 1 tugas bisa dikerjakan banyak orang (jika diperlukan).

```sql
CREATE TABLE task_assignees (
    task_id BIGINT UNSIGNED NOT NULL,
    user_id BIGINT UNSIGNED NOT NULL,
    assigned_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (task_id, user_id),
    FOREIGN KEY (task_id) REFERENCES tasks(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);
```

### API Endpoints Baru (untuk Assign)
| Method | Endpoint | Fungsi | Auth |
|--------|----------|--------|------|
| POST | `/api/tasks/{taskId}/assign` | Tambah assignee ke tugas | ✅ |
| DELETE | `/api/tasks/{taskId}/assign/{userId}` | Hapus assignee | ✅ |
| GET | `/api/teams/{teamId}/members` | Lihat anggota tim untuk di-assign | ✅ |

---

## Fitur 8: Laporan & Statistik Visual (Dashboard Admin)
**Deskripsi:** Menampilkan grafik (Chart.js) dan ekspor data ke Excel/PDF.

### User Story
> Sebagai Project Manager, saya ingin melihat grafik batang jumlah tugas yang selesai per minggu, dan mengekspor laporan kinerja tim ke Excel.

### Fitur Teknis:
- **Backend:** Gunakan package **Laravel Excel** (Maatwebsite) untuk export.
- **Frontend:** Gunakan **Chart.js** atau **ApexCharts** untuk visualisasi.
- **Endpoint Baru:**
  - `GET /api/reports/team/{teamId}?period=monthly` → Mengembalikan data statistik (total, selesai, on-progress).
  - `GET /api/reports/export/pdf` atau `/excel` → Download file.

---

## 2. REKOMENDASI STRATEGI IMPLEMENTASI (UNTUK TIM)

Agar tidak kewalahan, saya sarankan skema pengerjaan berikut:

| Sprint ke- | Fokus | Fitur yang Dikerjakan |
|------------|-------|-----------------------|
| **Sprint 5-6** | V2.0 | Categories, Subtasks, Comments (Struktur Database + API + Vue Components) |
| **Sprint 7** | V2.5 | Notifikasi (Scheduler + Email + Bell Icon), Upload File |
| **Sprint 8-9** | V3.0 | Teams & Members, Assign System, Reports & Charts |

---

## 3. DAMPAK TERHADAP ARSITEKTUR LAMA (BREAKING CHANGES)

1.  **Vue Router:** Akan bertambah halaman baru: `TeamDetail.vue`, `TaskDetail.vue` (yang sebelumnya cukup modal, sekarang butuh halaman khusus karena banyak komentar dan sub-tugas).
2.  **Pinia Store:** Akan bertambah `teamStore`, `categoryStore`, `notificationStore`.
3.  **Database Seeder:** Buat seeder untuk testing data dummy (misal: 1 user dengan 50 tugas dan 10 kategori).

---

## 4. PRIORITAS AKHIR UNTUK DITANYAKAN KE STAKEHOLDER

Sebelum mulai mengerjakan fitur lanjutan, tanyakan hal ini ke user/stakeholder:

1. **Apakah Anda lebih membutuhkan Kategori (V2.0) atau Kolaborasi Tim (V3.0)?** (Biasanya orang lebih butuh organisasi dulu sebelum kolaborasi).
2. **Berapa ukuran maksimal file lampiran yang diizinkan?** (Saya sarankan 5MB untuk menghemat storage).
3. **Apakah notifikasi via Email sangat krusial?** (Jika ya, siapkan SMTP seperti Mailgun/SendGrid).

