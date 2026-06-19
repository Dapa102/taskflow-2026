# PRODUCT REQUIREMENTS DOCUMENT (PRD) - MVP
## Daily Task Management System (Laravel 11 API + Vue 3 SPA + MariaDB)

**Versi Dokumen:** 1.0 (Engineering Ready)
**Tanggal Efektif:** 19 Juni 2026
**Audience:** Backend Dev, Frontend Dev, QA Engineer

---

## 1. TUJUAN DOKUMEN

Dokumen ini berisi spesifikasi teknis fungsional yang menjadi acuan tunggal (*Single Source of Truth*) bagi tim pengembang dalam membangun fitur MVP. Setiap fitur dijelaskan dalam bentuk **Skenario Uji (Acceptance Criteria)** dan **Kontrak API** agar pengembangan Backend (Laravel) dan Frontend (Vue) dapat berjalan secara paralel.

---

## 2. RUANG LINGKUP TEKNIS (IN-SCOPE)

| Komponen | Spesifikasi |
|----------|-------------|
| **Backend** | RESTful API dengan Laravel 11, proteksi menggunakan Laravel Sanctum (Token-based). |
| **Frontend** | SPA dengan Vue 3 (Composition API + `<script setup>`), Pinia, Vue Router, Axios. |
| **Database** | MariaDB 10.6+ dengan 2 tabel: `users` dan `tasks`. |
| **Fitur** | Hanya mencakup: Auth (Regis/Login/Logout), CRUD Task, Filter Status, Search, Sorting Deadline, Dashboard Ringkasan. |

---

## 3. USER STORIES & ACCEPTANCE CRITERIA (FUNGSIONAL)

Berikut adalah daftar lengkap *User Stories* yang harus diimplementasikan. Setiap story memiliki skenario **Given-When-Then** yang wajib lulus uji QA.

---

### US-01: Registrasi Akun
**Sebagai** pengguna baru, **Saya ingin** mendaftar menggunakan Nama, Email, dan Password, **Sehingga** saya memiliki akses ke sistem.

| Skenario | Given | When | Then |
|----------|-------|------|------|
| **Positif (Berhasil)** | Halaman Register terbuka | User mengisi semua field valid dan klik Submit | Sistem menyimpan data, mengembalikan token, dan redirect ke Dashboard. |
| **Negatif (Email duplikat)** | Email sudah terdaftar di database | User mendaftar dengan email yang sama | Sistem menolak dengan status 422 dan pesan "Email already taken". |
| **Negatif (Validasi)** | Form kosong | User klik Submit tanpa mengisi apapun | Sistem menampilkan error "Name required", "Email required", "Password min 8 characters". |

**Kontrak API (Endpoint):**
- **Method:** `POST`
- **URL:** `/api/register`
- **Request Body (JSON):**
  ```json
  {
    "name": "Andi Pratama",
    "email": "andi@email.com",
    "password": "rahasia123",
    "password_confirmation": "rahasia123"
  }
  ```
- **Success Response (201 Created):**
  ```json
  {
    "status": "success",
    "user": { "id": 1, "name": "Andi Pratama", "email": "andi@email.com" },
    "token": "1|djhfgsdjkfghsdkjf..."
  }
  ```

---

### US-02: Login & Logout
**Sebagai** pengguna terdaftar, **Saya ingin** login dan logout, **Sehingga** data tugas saya aman secara pribadi.

| Skenario | Given | When | Then |
|----------|-------|------|------|
| **Positif (Login)** | User sudah terdaftar | User memasukkan kredensial benar | Sistem mengembalikan token dan data user. |
| **Negatif (Login)** | Kredensial salah | User memasukkan password salah | Sistem menolak dengan status 401 dan pesan "Invalid credentials". |
| **Positif (Logout)** | User dalam keadaan login | User klik tombol Logout | Sistem menghapus token di database dan Frontend menghapus state lokal. |

**Kontrak API (Login):**
- **Method:** `POST`
- **URL:** `/api/login`
- **Request Body:** `{ "email": "andi@email.com", "password": "rahasia123" }`
- **Success Response (200 OK):** Sama seperti Register (mengembalikan token & user).

**Kontrak API (Logout):**
- **Method:** `POST`
- **URL:** `/api/logout`
- **Header:** `Authorization: Bearer {token}`
- **Success Response (200 OK):** `{ "status": "success", "message": "Logged out" }`

---

### US-03: Membuat Tugas Baru (Create)
**Sebagai** pengguna, **Saya ingin** membuat tugas baru, **Sehingga** saya bisa mencatat apa yang harus saya kerjakan.

| Skenario | Given | When | Then |
|----------|-------|------|------|
| **Positif (Minimal)** | User login, halaman Dashboard terbuka | User mengisi **Judul** saja (wajib), lalu submit | Tugas baru muncul di daftar dengan status default `todo` dan prioritas `medium`. |
| **Positif (Lengkap)** | User login | User mengisi Judul, Deskripsi, Deadline, dan Prioritas | Tugas tersimpan sesuai data yang diisi. |
| **Negatif (Validasi)** | Form modal terbuka | User tidak mengisi Judul (kosong) lalu submit | Sistem menolak, menampilkan pesan error "The title field is required" di dekat field. |

**Kontrak API (Create):**
- **Method:** `POST`
- **URL:** `/api/tasks`
- **Request Body (JSON):**
  ```json
  {
    "title": "Mengerjakan Laporan Keuangan",
    "description": "Rekap data penjualan Q2",
    "status": "todo",         // enum: todo, on_progress, done
    "priority": "high",       // enum: low, medium, high
    "deadline": "2026-07-01"  // format YYYY-MM-DD (nullable)
  }
  ```
- **Success Response (201 Created):** Mengembalikan object Task lengkap dengan ID dan timestamps.

---

### US-04: Melihat Daftar & Detail Tugas (Read)
**Sebagai** pengguna, **Saya ingin** melihat daftar tugas saya yang diurutkan berdasarkan deadline terdekat, **Sehingga** saya tahu prioritas yang harus dikerjakan duluan.

| Skenario | Given | When | Then |
|----------|-------|------|------|
| **Positif (List)** | User memiliki 5 tugas | User membuka halaman Dashboard | Tampil semua tugas, diurutkan ascending berdasarkan `deadline` (null di paling bawah). |
| **Positif (Detail)** | User melihat daftar | User mengklik salah satu tugas | Muncul Modal/Page detail berisi semua field tugas. |

**Kontrak API (Get All Tasks):**
- **Method:** `GET`
- **URL:** `/api/tasks`
- **Query Params (Opsional):** `?status=todo` atau `?search=laporan`
- **Success Response (200 OK):**
  ```json
  {
    "status": "success",
    "data": [
      {
        "id": 1,
        "title": "Mengerjakan Laporan",
        "description": "...",
        "status": "todo",
        "priority": "high",
        "deadline": "2026-07-01",
        "created_at": "2026-06-19T10:00:00Z",
        "updated_at": "2026-06-19T10:00:00Z"
      }
    ]
  }
  ```

---

### US-05: Filter, Sorting, dan Pencarian (Search)
**Sebagai** pengguna dengan banyak tugas, **Saya ingin** memfilter dan mencari tugas, **Sehingga** saya tidak kewalahan melihat daftar yang panjang.

| Skenario | Given | When | Then |
|----------|-------|------|------|
| **Filter Status** | Daftar ada 10 tugas (3 todo, 4 progress, 3 done) | User mengklik tab/filter "On Progress" | Hanya 4 tugas berstatus `on_progress` yang tampil. |
| **Pencarian (Search)** | Ada tugas berjudul "Beli Alat Tulis" | User mengetik "Alat" di kolom search | Hanya tugas yang mengandung kata "Alat" yang tampil. |
| **Kombinasi** | User sedang filter "todo" | User mengetik kata kunci di search | Data yang tampil adalah irisan antara `status=todo` DAN `search=kata`. |

> **Catatan untuk Backend:** Query Builder harus menangani `where('user_id', auth()->id())->when($request->status, ...)->when($request->search, ...)->orderBy('deadline')`.

---

### US-06: Mengubah Status (Update)
**Sebagai** pengguna, **Saya ingin** mengubah status tugas (misal dari "To-Do" menjadi "Done"), **Sehingga** saya bisa melacak progres saya.

| Skenario | Given | When | Then |
|----------|-------|------|------|
| **Positif (Dropdown)** | Tugas berstatus `todo` | User memilih opsi `done` pada dropdown status | Status berubah menjadi `done`, data terupdate di database dan tampilan. |
| **Positif (Edit Lengkap)** | Halaman Edit terbuka | User mengubah Judul, Deadline, dan Prioritas lalu simpan | Semua field berubah sesuai input. |

**Kontrak API (Update):**
- **Method:** `PUT` atau `PATCH`
- **URL:** `/api/tasks/{id}`
- **Request Body:** Mengandung field yang ingin diubah (semua field opsional saat update, kecuali title jika dikirim harus diisi).
- **Success Response (200 OK):** Mengembalikan object Task yang sudah di-update.

---

### US-07: Menghapus Tugas (Delete)
**Sebagai** pengguna, **Saya ingin** menghapus tugas yang sudah tidak relevan, **Sehingga** daftar saya tetap bersih.

| Skenario | Given | When | Then |
|----------|-------|------|------|
| **Positif** | Tugas dengan ID 5 ada | User klik tombol "Hapus" dan konfirmasi "Ya" | Tugas hilang dari database dan daftar (tidak bisa dikembalikan). |
| **Negatif (OTP)** | User mencoba menghapus tugas milik orang lain | User menyisipkan ID tugas orang lain di URL | Sistem mengembalikan status 403 Forbidden (karena ada policy/authorization). |

**Kontrak API (Delete):**
- **Method:** `DELETE`
- **URL:** `/api/tasks/{id}`
- **Success Response (200 OK):** `{ "status": "success", "message": "Task deleted" }`

---

### US-08: Dashboard Ringkasan (Summary)
**Sebagai** pengguna, **Saya ingin** melihat angka-angka ringkasan di halaman utama, **Sehingga** saya langsung tahu beban kerja saya hari ini.

| Skenario | Given | When | Then |
|----------|-------|------|------|
| **Positif** | User memiliki 5 To-Do, 3 On-Progress, 2 Done | User membuka Dashboard | Di bagian atas tampil kartu: `To-Do: 5`, `On-Progress: 3`, `Done: 2`, `Total: 10`. |

> **Catatan Teknis:** Ringkasan ini bisa didapat dari 1 endpoint `GET /api/tasks` lalu Frontend menghitung `filter()` di Pinia, ATAU buat endpoint terpisah `GET /api/tasks/summary`. Untuk MVP, saya sarankan hitung di Frontend (Pinia getter) agar tidak perlu buat endpoint baru.

---

## 4. SPESIFIKASI TEKNIS WAJIB (HARD REQUIREMENTS)

### 4.1 Backend (Laravel)
- **Model & Migration:** Buat migration untuk `tasks` dengan foreign key `user_id` yang *constrained* (`onDelete('cascade')`).
- **Authorization:** Gunakan **Policy** Laravel (`TaskPolicy`) untuk memastikan user hanya bisa mengakses `tasks` miliknya sendiri (method `view`, `update`, `delete`).
- **Validation:** Gunakan Form Request Laravel (misal: `StoreTaskRequest`) untuk validasi. Pesan error harus dalam format JSON.

### 4.2 Frontend (Vue 3)
- **State Management (Pinia):**
  - `authStore`: menyimpan `user`, `token`, dan method `login/register/logout`.
  - `taskStore`: menyimpan `tasks[]`, method `fetchTasks`, `addTask`, `updateTask`, `deleteTask`.
- **Axios Interceptor:** Wajib ada `request.use()` untuk menyisipkan `Authorization: Bearer {token}` ke setiap request. Wajib ada `response.use()` untuk menangani error 401 (auto logout jika token expired).
- **Reaktifitas:** Gunakan `computed` untuk menghitung ringkasan status (To-Do, On-Progress, Done) agar otomatis berubah saat data berubah.

### 4.3 Database Schema (Final ERD)
```sql
-- Tabel users (menggunakan migration default Laravel + tambahan)
-- Tidak perlu tambahan kolom, biarkan name, email, password.

-- Tabel tasks
CREATE TABLE tasks (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED NOT NULL,
    title VARCHAR(255) NOT NULL,
    description TEXT NULL,
    status ENUM('todo', 'on_progress', 'done') DEFAULT 'todo',
    priority ENUM('low', 'medium', 'high') DEFAULT 'medium',
    deadline DATE NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);
```

---

## 5. EDGE CASES & PENANGANAN ERROR (QA FOKUS DI SINI)

Tim QA wajib menguji skenario berikut agar aplikasi tidak crash:

| Skenario Edge | Ekspektasi Sistem |
|---------------|-------------------|
| **Token Expired / Invalid** | Backend mengembalikan 401 Unauthorized. Frontend harus langsung hapus token di Pinia dan redirect ke halaman Login. |
| **Deadline di masa lalu** | Boleh disimpan (tidak ada validasi minimal), tapi Frontend wajib memberi highlight/peringatan visual (misal teks merah "Overdue"). |
| **Judul tugas sangat panjang (>255)** | Validasi backend harus menolak dengan pesan "Title may not be greater than 255 characters". |
| **Mengirimkan status yang tidak ada (misal: "canceled")** | Backend harus menolak dengan 422 Unprocessable Entity karena enum tidak mengizinkan. |
| **Klik Tombol Simpan 2x berturut-turut** | Tombol harus *disabled* setelah diklik pertama (loading state) untuk mencegah duplikasi data. |
| **Koneksi Internet mati saat submit** | Axios akan timeout/catch error. Frontend harus menampilkan notifikasi "Gagal terhubung ke server" tanpa menghilangkan data yang sudah diinput user. |

---

## 6. UI/UX PEGANGAN UNTUK FRONTEND (DESIGN TOKEN)

Untuk menjaga konsistensi, gunakan standar berikut (implementasikan dengan Tailwind CSS):

| Elemen | Warna / Style |
|--------|---------------|
| **Status To-Do** | 🔵 Biru (Blue-500) / Badge: `bg-blue-100 text-blue-800` |
| **Status On-Progress** | 🟡 Kuning (Yellow-500) / Badge: `bg-yellow-100 text-yellow-800` |
| **Status Done** | 🟢 Hijau (Green-500) / Badge: `bg-green-100 text-green-800` |
| **Prioritas Rendah** | ⚪ Abu-abu (Gray-400) |
| **Prioritas Sedang** | 🔶 Oren (Orange-400) |
| **Prioritas Tinggi** | 🔴 Merah (Red-600) |
| **Task Overdue** | Tanda seru merah di samping deadline |

---

## 7. METRIK KEBERHASILAN TEKNIS (UNTUK TIM)

| Metrik | Target |
|--------|--------|
| **Test Coverage (Backend)** | Minimal 70% (Unit Test untuk Model, Controller, dan Policy) |
| **API Response Time** | < 200ms untuk `GET /api/tasks` dengan 100 data dummy |
| **Lighthouse Score (Frontend)** | Performance ≥ 80, Accessibility ≥ 90, SEO ≥ 80 |
| **Zero Critical Bugs** | Tidak ada error 500 saat mengikuti semua skenario positif di atas |

---

## 8. DAFTAR ENDPOINT LENGKAP (CHEAT SHEET UNTUK TIM)

| Method | Endpoint | Fungsi | Auth |
|--------|----------|--------|------|
| POST | `/api/register` | Register | ❌ |
| POST | `/api/login` | Login | ❌ |
| POST | `/api/logout` | Logout | ✅ |
| GET | `/api/user` | Ambil profil | ✅ |
| GET | `/api/tasks` | List tugas + filter/search | ✅ |
| POST | `/api/tasks` | Buat tugas | ✅ |
| GET | `/api/tasks/{id}` | Detail tugas | ✅ |
| PUT | `/api/tasks/{id}` | Update tugas | ✅ |
| DELETE | `/api/tasks/{id}` | Hapus tugas | ✅ |

---

## 9. GLOSARIUM

- **MVP:** Minimum Viable Product (Produk dengan fitur paling dasar namun sudah layak pakai).
- **SPA:** Single Page Application (Aplikasi halaman tunggal tanpa reload).
- **Pinia:** Library State Management untuk Vue 3 (pengganti Vuex).
- **Sanctum:** Package Laravel untuk autentikasi API ringan (token-based).

