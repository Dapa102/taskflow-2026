## Daily Task Management System - Technical Design Document

**Versi:** 1.0 (MVP Ready)
**Tanggal:** 19 Juni 2026
**Audience:** Backend Developer, Frontend Developer, DevOps, QA

---

## 1. TUJUAN DESAIN

Dokumen ini menjelaskan arsitektur teknis, struktur kode, konfigurasi database, alur autentikasi, dan standar pengembangan untuk aplikasi **Daily Task Management System**. 

Tujuan utamanya adalah:
1. Menstandarisasi cara tim menulis kode (coding convention).
2. Memberikan blueprint struktur folder agar Backend (Laravel) dan Frontend (Vue) tidak bentrok saat dikerjakan paralel.
3. Memastikan sistem aman, cepat, dan mudah dikembangkan ke depannya (scalable).

---

## 2. ARSITEKTUR SISTEM (HIGH-LEVEL)

Kita menggunakan arsitektur **Decoupled SPA (Single Page Application)**. Backend dan Frontend berkomunikasi via RESTful API.

```mermaid
graph LR
    User[User Browser] -->|HTTPS| FE[Vue 3 SPA (Vite)]
    FE -->|Axios HTTP + Token| BE[Laravel 11 API]
    BE -->|Eloquent ORM| DB[MariaDB]
    BE -->|Queue/Job (Future)| Redis[Redis - Opsional]
    FE -->|Static Hosting| CDN[Vercel / Netlify / Nginx]
```

**Pemisahan Tanggung Jawab:**
- **Laravel (Backend):** Menangani logic bisnis, validasi, interaksi database, dan autentikasi. **TIDAK** menangani rendering HTML.
- **Vue (Frontend):** Menangani seluruh UI/UX, state management (Pinia), routing antar halaman, dan konsumsi API.

---

## 3. TEKNOLOGI STACK (DETAIL)

| Lapisan | Teknologi | Versi / Spesifikasi |
|---------|-----------|----------------------|
| **OS** | Linux (Ubuntu 22.04) atau MacOS untuk dev | - |
| **Backend** | PHP | ^8.2 (dengan extension: BCMath, Ctype, JSON, MBString, OpenSSL, PDO, Tokenizer, XML) |
| **Backend Framework** | Laravel | ^11.0 |
| **Database** | MariaDB | ^10.6 (InnoDB engine) |
| **Cache / Session** | File (dev) / Redis (prod opsional) | - |
| **Frontend Framework** | Vue 3 | ^3.4 (Composition API) |
| **Frontend Build** | Vite | ^5.0 |
| **State Management** | Pinia | ^2.1 |
| **Frontend Routing** | Vue Router | ^4.3 |
| **HTTP Client** | Axios | ^1.6 |
| **CSS** | Tailwind CSS | ^3.4 (PostCSS) |
| **Version Control** | Git | - |

---

## 4. STRUKTUR FOLDER PROYEK (MONOREPO)

Karena ini adalah proyek terpisah (API + SPA), kita gunakan struktur **Monorepo** agar mudah dikelola:

```text
project-root/
├── backend/                       # Laravel Project
│   ├── app/
│   │   ├── Http/
│   │   │   ├── Controllers/
│   │   │   │   └── Api/           # Semua Controller API (AuthController, TaskController)
│   │   │   ├── Requests/          # Form Request (StoreTaskRequest, UpdateTaskRequest)
│   │   │   └── Middleware/        # (Default Laravel + CORS)
│   │   ├── Models/                # User.php, Task.php
│   │   ├── Policies/              # TaskPolicy.php (Authorization)
│   │   └── Services/              # (Opsional) Untuk logic bisnis berat. Contoh: TaskService.php
│   ├── bootstrap/                 
│   ├── config/                    # cors.php, sanctum.php, database.php
│   ├── database/
│   │   ├── migrations/            # 2014_..._users, 2024_..._tasks
│   │   └── seeders/               # DatabaseSeeder, TaskSeeder
│   ├── routes/
│   │   └── api.php                # DAFTAR ENDPOINT UTAMA
│   ├── tests/                     # Unit & Feature Tests (TaskTest, AuthTest)
│   ├── .env                       # Environment (jangan commit)
│   └── artisan
│
├── frontend/                      # Vue 3 Project
│   ├── public/                    
│   ├── src/
│   │   ├── api/                   # Config Axios & endpoint calls
│   │   │   ├── axios.js           # Instance dengan interceptor token
│   │   │   └── taskApi.js         # function fetchTasks, createTask, dll.
│   │   ├── components/            # UI Components
│   │   │   ├── layout/            # Navbar.vue, Sidebar.vue
│   │   │   ├── tasks/             # TaskList.vue, TaskFormModal.vue, TaskCard.vue
│   │   │   └── common/            # FilterBar.vue, LoadingSpinner.vue
│   │   ├── composables/           # Vue Composables (useAuth, useTask)
│   │   ├── router/
│   │   │   └── index.js           # Route definitions + Route Guard (beforeEach)
│   │   ├── stores/                # Pinia Store
│   │   │   ├── authStore.js       # State: user, token. Actions: login, logout
│   │   │   └── taskStore.js       # State: tasks, filters. Actions: fetch, add, update, delete
│   │   ├── views/                 # Halaman (Pages)
│   │   │   ├── Login.vue
│   │   │   ├── Register.vue
│   │   │   └── Dashboard.vue      # Halaman utama (berisi TaskList & Summary)
│   │   ├── App.vue
│   │   └── main.js                # Entry point (setup Pinia, Router, Axios)
│   ├── tailwind.config.js
│   ├── vite.config.js
│   └── package.json
│
└── docker-compose.yml             # (Opsional) Untuk local development dengan MariaDB & Nginx
```

---

## 5. DESAIN DATABASE (SCHEMA & ERD)

### 5.1. Diagram Relasi (MVP)
Hanya 2 tabel utama. Relasi: **One-to-Many** (1 User punya banyak Tasks).

```sql
-- Tabel: users (Menggunakan migration default Laravel)
-- Kolom: id, name, email, email_verified_at, password, remember_token, created_at, updated_at

-- Tabel: tasks (Migrasi khusus)
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
    -- Index untuk mempercepat query filter
    INDEX tasks_user_status_index (user_id, status),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);
```

### 5.2. Catatan Penting untuk Developer Database:
- **Engine:** Gunakan `InnoDB` untuk mendukung foreign key dan transaksi.
- **Collation:** `utf8mb4_unicode_ci` untuk mendukung emoji dan karakter khusus.
- **Soft Deletes:** **TIDAK** digunakan di MVP untuk menjaga kesederhanaan, tapi siapkan kolom `deleted_at` (nullable) jika ingin menambahkan fitur "Trash" di V2.

---

## 6. DESAIN BACKEND (LARAVEL)

### 6.1. Standar Response API (JSON Wrapper)
Semua response API harus mengikuti format standar agar Frontend (Axios) mudah mengolahnya.

**Sukses (200, 201):**
```json
{
  "status": "success",
  "data": { ... }  // Object atau Array
}
```

**Error Validasi (422):**
```json
{
  "status": "error",
  "message": "The given data was invalid.",
  "errors": {
    "title": ["The title field is required."]
  }
}
```

**Error Server / Auth (401, 403, 500):**
```json
{
  "status": "error",
  "message": "Unauthenticated." // atau "Server Error"
}
```

### 6.2. Autentikasi (Laravel Sanctum)
Kita menggunakan **Sanctum** dengan **Token-based** (bukan session cookie) agar lebih fleksibel untuk SPA.

- **Instalasi:** `composer require laravel/sanctum`
- **Konfigurasi:** `config/sanctum.php` atur `stateful` = [] (kosong) karena kita tidak pakai cookie domain utama.
- **Middleware:** Tambahkan `auth:sanctum` ke group `api` di `app/Http/Kernel.php`.

**Alur Login:**
1. Client kirim `POST /api/login` dengan email & password.
2. Server cek kredensial. Jika benar, buat token: `$user->createToken('auth_token')->plainTextToken`.
3. Server kembalikan `{ user: ..., token: ... }`.
4. Client simpan token di **LocalStorage** (atau Cookie) dan set header `Authorization: Bearer {token}` untuk request selanjutnya.

### 6.3. Authorization (Policy)
Untuk memastikan User A tidak bisa mengakses/update Task milik User B, wajib membuat **TaskPolicy**.

```php
// app/Policies/TaskPolicy.php
public function update(User $user, Task $task) {
    return $user->id === $task->user_id;
}
```
Tambahkan di `AuthServiceProvider` dan gunakan di Controller: `$this->authorize('update', $task);`

### 6.4. Routing (api.php)
```php
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', [AuthController::class, 'user']);
    
    Route::apiResource('/tasks', TaskController::class); // Otomatis buat index, store, show, update, destroy
});
```

### 6.5. Validasi (Form Request)
Buat `StoreTaskRequest` untuk menangani validasi masuk.

```php
public function rules(): array {
    return [
        'title' => 'required|string|max:255',
        'description' => 'nullable|string',
        'status' => 'nullable|in:todo,on_progress,done',
        'priority' => 'nullable|in:low,medium,high',
        'deadline' => 'nullable|date|date_format:Y-m-d',
    ];
}
```

---

## 7. DESAIN FRONTEND (VUE 3)

### 7.1. Struktur State Management (Pinia)
Kita akan memiliki 2 Store utama.

**authStore.js:**
```javascript
export const useAuthStore = defineStore('auth', {
  state: () => ({
    user: null,
    token: localStorage.getItem('token') || null,
  }),
  actions: {
    async login(credentials) {
      const res = await axios.post('/api/login', credentials);
      this.user = res.data.user;
      this.token = res.data.token;
      localStorage.setItem('token', this.token);
    },
    logout() {
      this.user = null;
      this.token = null;
      localStorage.removeItem('token');
    }
  },
  getters: {
    isAuthenticated: (state) => !!state.token,
  }
});
```

**taskStore.js:**
```javascript
export const useTaskStore = defineStore('task', {
  state: () => ({
    tasks: [],
    filters: { status: null, search: '' },
  }),
  actions: {
    async fetchTasks() {
      const res = await axios.get('/api/tasks', { params: this.filters });
      this.tasks = res.data.data;
    },
    async addTask(taskData) { ... },
    async updateTask(id, data) { ... },
    async deleteTask(id) { ... }
  },
  getters: {
    todoTasks: (state) => state.tasks.filter(t => t.status === 'todo'),
    progressTasks: ...,
    doneTasks: ...,
    summary: (state) => ({
      total: state.tasks.length,
      todo: state.tasks.filter(t => t.status === 'todo').length,
      // ...
    })
  }
});
```

### 7.2. Interceptor Axios (Menangani Token & 401)
Buat di `src/api/axios.js`:

```javascript
const api = axios.create({ baseURL: import.meta.env.VITE_API_URL });

// Request Interceptor: Suntikkan token
api.interceptors.request.use((config) => {
  const token = localStorage.getItem('token');
  if (token) config.headers.Authorization = `Bearer ${token}`;
  return config;
});

// Response Interceptor: Jika 401, hapus token & redirect ke login
api.interceptors.response.use(
  (response) => response,
  (error) => {
    if (error.response?.status === 401) {
      const authStore = useAuthStore();
      authStore.logout();
      window.location.href = '/login';
    }
    return Promise.reject(error);
  }
);
```

### 7.3. Route Guard (Vue Router)
Di `router/index.js`, gunakan `beforeEach` untuk proteksi halaman.

```javascript
router.beforeEach((to, from, next) => {
  const authStore = useAuthStore();
  if (to.meta.requiresAuth && !authStore.isAuthenticated) {
    next('/login');
  } else if ((to.path === '/login' || to.path === '/register') && authStore.isAuthenticated) {
    next('/dashboard');
  } else {
    next();
  }
});
```

### 7.4. Komponen Reusable (Best Practice)
- **TaskCard.vue:** Menerima `prop: task`. Menampilkan judul, deadline, dan badge status. Emit event `@edit` dan `@delete`.
- **TaskFormModal.vue:** Menggunakan `v-model` untuk form. Bisa digunakan untuk "Tambah" dan "Edit" (bergantung pada prop `initialData`).
- **FilterBar.vue:** Mengandung tombol status (To-Do, On Progress, Done) dan input search. Mengirim event `@filter` ke parent (Dashboard).

---

## 8. KEAMANAN (SECURITY IMPLEMENTATION)

| Ancaman | Solusi / Konfigurasi |
|---------|-----------------------|
| **SQL Injection** | Gunakan Eloquent ORM dan Query Builder (otomatis menggunakan PDO binding). Hindari `DB::raw()` tanpa binding. |
| **XSS (Cross-Site Scripting)** | Di Vue, semua output menggunakan `{{ }}` yang otomatis di-escape. Di Blade tidak dipakai. |
| **CSRF** | Karena kita pakai Token (Sanctum) dan bukan Session Cookie, CSRF tidak relevan. Nonaktifkan VerifyCsrfToken untuk route API. |
| **CORS** | Atur di `config/cors.php`. Izinkan `allowed_origins` = `[env('FRONTEND_URL', 'http://localhost:5173')]`. |
| **Mass Assignment** | Di Model `Task.php`, atur `$fillable = ['title', 'description', 'status', 'priority', 'deadline']` agar tidak bisa menyetel `user_id` secara manual dari request. `user_id` diisi otomatis dari `auth()->id()`. |
| **Rate Limiting** | Gunakan throttle bawaan Laravel di `RouteServiceProvider` untuk mencegah brute force login (misal: 5 attempt per menit). |

---

## 9. DEPLOYMENT STRATEGY (CARA GO-LIVE)

Karena arsitektur terpisah, deployment dilakukan ke 2 target berbeda:

1.  **Backend (Laravel)**: Deploy ke VPS / Shared Hosting dengan Nginx.
    - Set `public/` sebagai root directory.
    - Jalankan `php artisan migrate --force`.
    - Jalankan `php artisan config:cache`.

2.  **Frontend (Vue)**: Bangun file statik.
    - Jalankan `npm run build`.
    - Hasil folder `dist/` di-deploy ke layanan Static Hosting seperti **Vercel**, **Netlify**, atau disimpan di subfolder `public/frontend` (jika digabung di satu server, tapi saya sarankan dipisah).
    - Set environment variable `VITE_API_URL` mengarah ke domain backend (misal: `https://api.tugas.app`).

**Konfigurasi Nginx untuk Backend (Contoh):**
```nginx
location /api {
    try_files $uri $uri/ /index.php?$query_string;
}
```

**Konfigurasi Vercel untuk Frontend (vercel.json):**
```json
{
  "rewrites": [{ "source": "/(.*)", "destination": "/index.html" }]
}
```

---

## 10. PANDUAN TESTING

| Jenis Test | Tools | Target Coverage |
|------------|-------|-----------------|
| **Unit Test (Backend)** | PHPUnit (Laravel) | Test Models, Policies, Helpers |
| **Feature Test (Backend)** | PHPUnit | Test setiap endpoint API (success & error) minimal 80% |
| **E2E (Frontend)** | Cypress / Playwright | Test flow kritis: Login → Buat Tugas → Ubah Status → Logout |
| **Manual (QA)** | Postman Collection | Ekspor collection untuk regression test |

**Contoh Test API (Laravel Feature):**
```php
public function test_user_can_create_task() {
    $user = User::factory()->create();
    $this->actingAs($user, 'sanctum');
    
    $response = $this->postJson('/api/tasks', ['title' => 'Test Task']);
    $response->assertStatus(201);
    $this->assertDatabaseHas('tasks', ['title' => 'Test Task', 'user_id' => $user->id]);
}
```

---

## 11. RENCANA SCALABILITY (UNTUK FITUR V2.0)

Untuk mengantisipasi fitur lanjutan (Kategori, Sub-tugas, Tim), kita sudah menyiapkan desain yang fleksibel:

1. **Penambahan Kolom:** Di V2, kita cukup tambahkan `category_id` (FK) ke tabel `tasks`. Tidak perlu rewrite besar.
2. **Polymorphic Relations:** Untuk Comments, lebih baik pakai One-to-Many langsung ke `tasks` (spesifik) daripada Polymorphic agar query lebih cepat.
3. **Queue (Job):** Untuk fitur Notifikasi Email (V2.5), kita akan gunakan Laravel Queue dengan driver `database` (membuat tabel jobs). Jangan kirim email secara synchronous!

---

## 12. GLOSARIUM TEKNIS UNTUK TIM

- **DTO (Data Transfer Object):** Tidak wajib di Laravel, tapi disarankan menggunakan `array` atau Resource Collection (`TaskResource::collection`) untuk memformat output API.
- **N+1 Problem:** Waspada! Saat mengambil daftar tasks, gunakan `Task::with('user')` jika di masa depan butuh relasi.
- **Atomic Design:** Untuk komponen Vue, usahakan mengikuti prinsip Atom (Button) → Molecule (Form) → Organism (TaskList) → Template (Dashboard).

