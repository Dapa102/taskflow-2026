# IMPLEMENTATION PLAN - TaskFlow MVP
## Daily Task Management System

**Tanggal:** 19 Juni 2026
**Status:** Draft - Menunggu Persetujuan
**Referensi:** BRD v3.0 & PRD v1.0

---

## 0. ANALISIS KESESUAIAN DOKUMEN vs KODE AKTUAL

### 0.1 Ringkasan Kesesuaian BRD & PRD

BRD dan PRD **saling konsisten dan selaras** dalam hal:
- Fitur MVP (Auth, CRUD Task, Filter, Search, Sorting, Dashboard Summary)
- Database schema (2 tabel: `users` dan `tasks`)
- 9 API endpoint yang didefinisikan
- Scope exclusion (fitur yang ditunda ke v2)
- Timeline 4 minggu

### 0.2 Discrepancy dengan Kode Aktual Project

| Aspek | BRD/PRD Menyebutkan | Kondisi Aktual di Repo | Dampak | Rekomendasi |
|-------|---------------------|------------------------|--------|-------------|
| **Laravel Version** | Laravel 11 | **Laravel 12** (`^12.0`) | Rendah | Update dokumen ke Laravel 12. Backward compatible, tidak ada perubahan API signifikan. |
| **Arsitektur Frontend** | Vue 3 SPA terpisah (monorepo `backend/` + `frontend/`) | **Filament 3** (Livewire-based monolith) | **Tinggi** | **Gunakan Filament** sebagai UI layer. Tidak perlu setup Vue SPA terpisah. |
| **State Management** | Pinia | N/A (Filament punya state sendiri) | Tinggi | Tidak relevan dengan Filament. Gunakan Filament reactive properties. |
| **Routing Frontend** | Vue Router v4 | N/A (Filament punya navigation) | Tinggi | Gunakan Filament Pages & Resources untuk navigasi. |
| **HTTP Client** | Axios + Interceptor | N/A (Filament server-rendered) | Tinggi | Tidak diperlukan. Filament menggunakan Livewire untuk reaktifitas. |
| **CSS Framework** | Tailwind CSS | **Tailwind CSS** (sudah ada) | Tidak ada | Sudah sesuai. Filament menggunakan Tailwind. |
| **Auth (API)** | Laravel Sanctum (token-based) | **Belum terinstall** | Sedang | Install Sanctum jika API eksternal dibutuhkan. Untuk Filament, auth sudah built-in (session-based). |
| **Roles/Permissions** | Single user sederhana | **Filament Shield** (Spatie Permission) | Sedang | Pertahankan Shield untuk RBAC. MVP bisa gunakan single role saja. |
| **User Model** | 4 kolom dasar (id, name, email, password) | Ada tambahan `avatar_url`, `HasRoles`, `FilamentUser` | Rendah | Kolom tambahan tidak mengganggu. Gunakan apa yang ada. |
| **MariaDB** | >= 10.6 | **10.11** (Docker) | Tidak ada | Sudah sesuai. |
| **Docker** | Tidak disebutkan | **Docker Compose** (PHP, Nginx, MariaDB) | Rendah | Infrastruktur sudah siap. |

### 0.3 Keputusan Arsitektur

> **REKOMENDASI: Gunakan arsitektur Filament-based (yang sudah ada), BUKAN Vue SPA terpisah.**

**Alasan:**
1. **Filament sudah terinstall dan terkonfigurasi** — menghemat ~1 minggu setup frontend.
2. **Filament menyediakan semua komponen UI** yang dibutuhkan: form builder, table builder, widgets, pages, auth.
3. **Tidak perlu setup Vue, Pinia, Vue Router, Axios** — mengurangi kompleksitas dan potensi bug.
4. **Filament Shield** sudah handle RBAC — bisa langsung dipakai untuk proteksi route.
5. **Filament Table Builder** sudah built-in support filter, search, sorting — sesuai kebutuhan P1.
6. **Tailwind CSS** sudah terintegrasi dengan Filament.

**Implikasi terhadap BRD/PRD:**
- Endpoint API (`/api/tasks`, dll) **tetap dibuat** sebagai RESTful API (untuk kebutuhan mobile/future).
- UI Dashboard dibangun menggunakan **Filament Resources & Pages** (bukan Vue components).
- Auth menggunakan **Filament built-in auth** (session-based) untuk UI, **Sanctum** untuk API.
- Route guard diganti dengan **Filament middleware** (`auth` + Shield policy).

---

## 1. LANGKAH IMPLEMENTASI

### Minggu 1: Backend Foundation (Model, Migration, API, Policy)

#### Step 1.1 — Install Laravel Sanctum
```
Tujuan: Menyediakan autentikasi API token-based
File yang dibuat/diubah:
  - composer.json (tambah laravel/sanctum)
  - config/sanctum.php
  - database/migrations/xxxx_create_personal_access_tokens_table.php
  - app/Models/User.php (tambah HasApiTokens trait)
```

**Detail:**
- Jalankan `composer require laravel/sanctum`
- Publish config dan migration Sanctum
- Tambahkan `HasApiTokens` trait ke model `User`
- Daftarkan Sanctum middleware di `bootstrap/app.php`

#### Step 1.2 — Buat Model, Migration & Factory untuk `Task`
```
Tujuan: Mendefinisikan struktur data tugas sesuai BRD section 6
File yang dibuat:
  - app/Models/Task.php
  - database/migrations/xxxx_create_tasks_table.php
  - database/factories/TaskFactory.php
```

**Migration `create_tasks_table`:**
```php
Schema::create('tasks', function (Blueprint $table) {
    $table->id();
    $table->foreignId('user_id')->constrained()->onDelete('cascade');
    $table->string('title', 255);
    $table->text('description')->nullable();
    $table->enum('status', ['todo', 'on_progress', 'done'])->default('todo');
    $table->enum('priority', ['low', 'medium', 'high'])->default('medium');
    $table->date('deadline')->nullable();
    $table->timestamps();
});
```

**Model `Task`:**
- `$fillable`: title, description, status, priority, deadline, user_id
- Relasi `belongsTo` ke `User`
- Cast `deadline` sebagai `date`
- Scope: `scopeByStatus()`, `scopeSearch()`

#### Step 1.3 — Buat Form Request untuk Validasi
```
Tujuan: Validasi input sesuai PRD section 4.1
File yang dibuat:
  - app/Http/Requests/StoreTaskRequest.php
  - app/Http/Requests/UpdateTaskRequest.php
```

**Validasi StoreTaskRequest:**
- `title`: required, string, max:255
- `description`: nullable, string
- `status`: nullable, in:todo,on_progress,done
- `priority`: nullable, in:low,medium,high
- `deadline`: nullable, date

#### Step 1.4 — Buat TaskPolicy
```
Tujuan: Authorization — user hanya bisa akses task miliknya (PRD US-07)
File yang dibuat:
  - app/Policies/TaskPolicy.php
```

**Method:**
- `view(User $user, Task $task)`: return `$user->id === $task->user_id`
- `update(User $user, Task $task)`: return `$user->id === $task->user_id`
- `delete(User $user, Task $task)`: return `$user->id === $task->user_id`

#### Step 1.5 — Buat API Controllers
```
Tujuan: Implementasi 9 endpoint sesuai BRD section 7
File yang dibuat:
  - app/Http/Controllers/Api/AuthController.php
  - app/Http/Controllers/Api/TaskController.php
```

**AuthController:**
- `register()`: validasi input, hash password, create user, generate token, return 201
- `login()`: validasi kredensial, generate token, return 200
- `logout()`: revoke current token, return 200

**TaskController (Resource Controller):**
- `index()`: list tasks milik user + filter (status, search) + sorting (deadline ASC, nulls last)
- `store()`: buat task baru, assign user_id dari auth user
- `show()`: detail 1 task (dengan authorization)
- `update()`: update task (dengan authorization)
- `destroy()`: hapus task (dengan authorization)

#### Step 1.6 — Daftarkan API Routes
```
Tujuan: Mendaftarkan semua endpoint di routes/api.php
File yang diubah:
  - routes/api.php
```

**Routes:**
```php
// Public
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Protected (auth:sanctum)
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
    Route::apiResource('tasks', TaskController::class);
});
```

#### Step 1.7 — Buat Seeder untuk Testing
```
Tujuan: Data dummy untuk testing manual
File yang diubah/dibuat:
  - database/seeders/TaskSeeder.php (baru)
  - database/seeders/DatabaseSeeder.php (update)
```

---

### Minggu 2: Filament UI — Auth & Task Resource

#### Step 2.1 — Buat Filament Task Resource
```
Tujuan: CRUD interface untuk tasks menggunakan Filament
File yang dibuat:
  - app/Filament/Resources/TaskResource.php
  - app/Filament/Resources/TaskResource/Pages/ListTasks.php
  - app/Filament/Resources/TaskResource/Pages/CreateTask.php
  - app/Filament/Resources/TaskResource/Pages/EditTask.php
```

**Filament Form Schema (Create/Edit):**
- `title` — TextInput, required, maxLength(255)
- `description` — Textarea/MarkdownEditor, nullable
- `status` — Select, options: todo/on_progress/done, default: todo
- `priority` — Select, options: low/medium/high, default: medium
- `deadline` — DatePicker, nullable

**Filament Table Columns (List):**
- `title` — TextColumn, searchable, sortable
- `status` — BadgeColumn dengan warna sesuai PRD section 6 (biru/kuning/hijau)
- `priority` — BadgeColumn dengan warna sesuai PRD section 6 (abu/orange/merah)
- `deadline` — TextColumn, date format, sortable, icon peringatan jika overdue
- `created_at` — TextColumn, since format

**Table Filters:**
- Filter by status (SelectFilter)
- Filter by priority (SelectFilter)

**Table Actions:**
- EditAction, DeleteAction (dengan konfirmasi)
- Custom Action: Quick status change (dropdown)

**Default Sort:** `deadline` ascending (nulls last) — sesuai TF-09

#### Step 2.2 — Konfigurasi TaskResource untuk Multi-Tenancy
```
Tujuan: Memastikan user hanya melihat/edit task miliknya
File yang diubah:
  - app/Filament/Resources/TaskResource.php
```

**Implementasi:**
- Override `getEloquentQuery()` untuk filter `where('user_id', auth()->id())`
- Override `canAccess()` atau gunakan TaskPolicy
- Default value `user_id` saat create = `auth()->id()`

#### Step 2.3 — Custom Filament Login/Register Page (Opsional)
```
Tujuan: Menyesuaikan UI login/register jika perlu
File yang dibuat (jika diperlukan):
  - app/Filament/Pages/Auth/Login.php
  - app/Filament/Pages/Auth/Register.php
```

> **Catatan:** Filament sudah menyediakan halaman login/register bawaan. Step ini hanya jika perlu kustomisasi.

---

### Minggu 3: Dashboard & Widgets

#### Step 3.1 — Buat Dashboard Page dengan Summary Widgets
```
Tujuan: Menampilkan ringkasan jumlah tugas per status (DF-01, US-08)
File yang dibuat:
  - app/Filament/Widgets/TaskSummaryWidget.php
  - app/Filament/Widgets/RecentTasksWidget.php (opsional)
```

**TaskSummaryWidget:**
- Query: hitung task per status milik user yang login
- Tampilkan 4 kartu: To-Do, On-Progress, Done, Total
- Gunakan Filament `StatsOverviewWidget`
- Warna kartu sesuai PRD section 6

**Implementasi:**
```php
protected function getStats(): array
{
    $userId = auth()->id();
    return [
        Stat::make('To-Do', Task::where('user_id', $userId)->where('status', 'todo')->count())
            ->icon('heroicon-o-clipboard-list')
            ->color('info'),
        Stat::make('On Progress', Task::where('user_id', $userId)->where('status', 'on_progress')->count())
            ->icon('heroicon-o-arrow-path')
            ->color('warning'),
        Stat::make('Done', Task::where('user_id', $userId)->where('status', 'done')->count())
            ->icon('heroicon-o-check-circle')
            ->color('success'),
        Stat::make('Total', Task::where('user_id', $userId)->count())
            ->icon('heroicon-o-document-text')
            ->color('gray'),
    ];
}
```

#### Step 3.2 — Daftarkan Widgets di AdminPanelProvider
```
Tujuan: Menampilkan widget di halaman dashboard Filament
File yang diubah:
  - app/Providers/Filament/AdminPanelProvider.php
```

#### Step 3.3 — Konfigurasi Dashboard sebagai Landing Page
```
Tujuan: Setelah login, user langsung melihat dashboard (bukan Filament default)
File yang diubah:
  - app/Providers/Filament/AdminPanelProvider.php
```

- Set `->defaultPaginationPageOption(10)`
- Set dashboard sebagai default landing page setelah login

---

### Minggu 4: Polish, Testing & Deployment

#### Step 4.1 — Loading State & Visual Feedback (UX-01)
```
Tujuan: Menampilkan indikator loading saat proses
Implementasi:
  - Filament sudah built-in loading wire:loading
  - Tambahkan wire:loading pada tombol submit (simpan)
  - Tombol disabled setelah klik pertama (PRD edge case: double submit)
```

#### Step 4.2 — Form Validation Messages (UX-02)
```
Tujuan: Pesan error yang jelas dari backend
Implementasi:
  - Filament sudah handle validasi otomatis dari Form Request
  - Custom message di StoreTaskRequest/UpdateTaskRequest jika perlu
  - Pesan error dalam bahasa Indonesia
```

#### Step 4.3 — Overdue Visual Warning (Edge Case)
```
Tujuan: Highlight deadline yang sudah lewat
Implementasi di TaskResource Table:
  - Deadline column: text merah jika < today
  - Icon peringatan (heroicon-o-exclamation-triangle) di samping deadline
```

```php
TextColumn::make('deadline')
    ->date('d M Y')
    ->sortable()
    ->color(fn (Task $record): string => $record->deadline && $record->deadline->isPast() ? 'danger' : 'gray')
    ->icon(fn (Task $record): ?string => $record->deadline && $record->deadline->isPast() ? 'heroicon-o-exclamation-triangle' : null)
```

#### Step 4.4 — Backend Unit & Feature Tests
```
Tujuan: Mencapai test coverage ≥ 70% (PRD section 7)
File yang dibuat:
  - tests/Feature/Api/AuthTest.php
  - tests/Feature/Api/TaskCrudTest.php
  - tests/Unit/TaskPolicyTest.php
  - tests/Unit/TaskModelTest.php
```

**Test Scenarios (sesuai PRD Given-When-Then):**
- US-01: Register (positif, email duplikat, validasi kosong)
- US-02: Login (positif, kredensial salah), Logout
- US-03: Create task (minimal judul saja, lengkap, judul kosong)
- US-04: List tasks, detail task
- US-05: Filter status, search, kombinasi
- US-06: Update status, update lengkap
- US-07: Delete task sendiri, delete task orang lain (403)
- US-08: Dashboard summary counts

#### Step 4.5 — Database Seeder untuk Demo
```
Tujuan: Data demo untuk presentasi dan testing manual
File yang diubah:
  - database/seeders/UserSeeder.php (update)
  - database/seeders/TaskSeeder.php (update)
```

- Buat 1 user demo: `demo@taskflow.test` / `password`
- Buat 10-15 task dengan variasi status, prioritas, dan deadline

#### Step 4.6 — Deploy Preparation
```
Tujuan: Persiapan deployment ke staging
Checklist:
  - [ ] .env production (APP_DEBUG=false, APP_ENV=production)
  - [ ] Database migration di server
  - [ ] SSL certificate (Nginx sudah dikonfigurasi)
  - [ ] php artisan config:cache, route:cache, view:cache
  - [ ] Nginx konfigurasi untuk Filament
```

---

## 2. FILE INVENTORY (SEMUA FILE YANG DIBUAT/DIUBAH)

### File Baru (Dibuat)

| No | Path | Deskripsi |
|----|------|-----------|
| 1 | `app/Models/Task.php` | Model Task dengan relasi dan scope |
| 2 | `database/migrations/xxxx_create_tasks_table.php` | Migration tabel tasks |
| 3 | `database/factories/TaskFactory.php` | Factory untuk testing |
| 4 | `database/seeders/TaskSeeder.php` | Seeder data dummy tasks |
| 5 | `app/Http/Requests/StoreTaskRequest.php` | Validasi create task |
| 6 | `app/Http/Requests/UpdateTaskRequest.php` | Validasi update task |
| 7 | `app/Policies/TaskPolicy.php` | Authorization policy untuk Task |
| 8 | `app/Http/Controllers/Api/AuthController.php` | API controller auth (register/login/logout) |
| 9 | `app/Http/Controllers/Api/TaskController.php` | API controller CRUD tasks |
| 10 | `app/Filament/Resources/TaskResource.php` | Filament resource untuk Task |
| 11 | `app/Filament/Resources/TaskResource/Pages/ListTasks.php` | Halaman list tasks |
| 12 | `app/Filament/Resources/TaskResource/Pages/CreateTask.php` | Halaman create task |
| 13 | `app/Filament/Resources/TaskResource/Pages/EditTask.php` | Halaman edit task |
| 14 | `app/Filament/Widgets/TaskSummaryWidget.php` | Widget ringkasan dashboard |
| 15 | `tests/Feature/Api/AuthTest.php` | Feature test auth API |
| 16 | `tests/Feature/Api/TaskCrudTest.php` | Feature test CRUD API |
| 17 | `tests/Unit/TaskPolicyTest.php` | Unit test policy |
| 18 | `tests/Unit/TaskModelTest.php` | Unit test model |
| 19 | `database/migrations/xxxx_create_personal_access_tokens_table.php` | Migration Sanctum |

### File yang Diubah

| No | Path | Perubahan |
|----|------|-----------|
| 1 | `app/Models/User.php` | Tambah `HasApiTokens` trait + relasi `tasks()` |
| 2 | `routes/api.php` | Daftarkan semua API routes |
| 3 | `bootstrap/app.php` | Daftarkan Sanctum middleware |
| 4 | `database/seeders/DatabaseSeeder.php` | Panggil TaskSeeder |
| 5 | `app/Providers/Filament/AdminPanelProvider.php` | Tambah widget, set landing page |
| 6 | `composer.json` | Tambah `laravel/sanctum` |

---

## 3. MAPPING FITUR KE IMPLEMENTASI

| ID Fitur (BRD) | Fitur | Implementasi Via | Status |
|----------------|-------|-----------------|--------|
| UF-01 | Register | Filament Auth + API `POST /api/register` | Minggu 2/1 |
| UF-02 | Login | Filament Auth + API `POST /api/login` | Minggu 2/1 |
| UF-03 | Logout | Filament Auth + API `POST /api/logout` | Minggu 2/1 |
| UF-04 | Route Guard | Filament middleware `auth` + Shield | Minggu 2 |
| TF-01 | Buat Tugas | TaskResource CreateTask page + API `POST /api/tasks` | Minggu 2/1 |
| TF-02 | Lihat Daftar Tugas | TaskResource ListTasks page (table) + API `GET /api/tasks` | Minggu 2/1 |
| TF-03 | Edit Tugas | TaskResource EditTask page + API `PUT /api/tasks/{id}` | Minggu 2/1 |
| TF-04 | Hapus Tugas | TaskResource DeleteAction + API `DELETE /api/tasks/{id}` | Minggu 2/1 |
| TF-05 | Ubah Status | Quick status change action + dropdown di EditTask | Minggu 2 |
| TF-06 | Deadline | DatePicker di form + date column di table | Minggu 2 |
| TF-07 | Prioritas | Select di form + badge column di table | Minggu 2 |
| TF-08 | Filter Status | SelectFilter di TaskResource table | Minggu 2 |
| TF-09 | Sorting Otomatis | Default sort `deadline ASC` di table query | Minggu 2 |
| TF-10 | Pencarian | `searchable()` pada kolom title di table | Minggu 2 |
| DF-01 | Ringkasan Jumlah | TaskSummaryWidget (StatsOverview) | Minggu 3 |
| UX-01 | Loading State | Filament built-in wire:loading | Minggu 4 |
| UX-02 | Validasi Form | Form Request + Filament validation | Minggu 4 |

---

## 4. PRIORITAS & DEPENDENCY

```
Minggu 1 (Backend)
├── Step 1.1: Install Sanctum        (tidak ada dependency)
├── Step 1.2: Model & Migration      (tidak ada dependency)
├── Step 1.3: Form Request           (dependency: Step 1.2)
├── Step 1.4: TaskPolicy             (dependency: Step 1.2)
├── Step 1.5: API Controllers        (dependency: Step 1.2, 1.3, 1.4)
├── Step 1.6: API Routes             (dependency: Step 1.5)
└── Step 1.7: Seeder                 (dependency: Step 1.2)

Minggu 2 (Filament UI)
├── Step 2.1: Task Resource          (dependency: Step 1.2)
├── Step 2.2: Multi-Tenancy Filter   (dependency: Step 2.1)
└── Step 2.3: Custom Auth Pages      (opsional, tidak blocking)

Minggu 3 (Dashboard)
├── Step 3.1: Summary Widget         (dependency: Step 1.2)
├── Step 3.2: Register Widgets       (dependency: Step 3.1)
└── Step 3.3: Landing Page Config    (dependency: Step 3.2)

Minggu 4 (Polish & Test)
├── Step 4.1: Loading State          (dependency: Step 2.1)
├── Step 4.2: Validation Messages    (dependency: Step 1.3)
├── Step 4.3: Overdue Warning        (dependency: Step 2.1)
├── Step 4.4: Tests                  (dependency: semua step Minggu 1-3)
├── Step 4.5: Demo Seeder            (dependency: Step 1.7)
└── Step 4.6: Deploy Prep            (dependency: semua step)
```

---

## 5. RISIKO & MITIGASI

| Risiko | Probabilitas | Dampak | Mitigasi |
|--------|-------------|--------|----------|
| BRD/PRD mengharuskan Vue SPA, bukan Filament | Tinggi (stakeholder) | Tinggi | Presentasi keuntungan Filament: faster delivery, less maintenance, sudah terinstall. Tawarkan demo cepat sebagai proof. |
| Filament Shield overcomplicates single-user system | Sedang | Rendah | Gunakan Shield dengan 1 role default. Bisa di-disable jika terlalu kompleks. |
| Sanctum conflict dengan Filament session auth | Rendah | Sedang | Sanctum hanya untuk API (`/api/*`). Filament menggunakan session auth terpisah. Kedua mekanisme bisa coexist. |
| Deadline overdue logic timezone issue | Sedang | Rendah | Set `APP_TIMEZONE=Asia/Jakarta` (sudah dikonfigurasi). Gunakan Carbon comparison. |
| Nginx config tidak support Filament assets | Rendah | Sedang | Test di Docker lokal dulu. Filament assets served via Laravel, tidak perlu config Nginx khusus. |

---

## 6. CATATAN KHUSUS

### 6.1 Dual Auth Strategy
- **Filament UI**: Menggunakan session-based authentication (bawaan Laravel + Filament). Tidak perlu setup tambahan.
- **API**: Menggunakan Sanctum token-based authentication. Diperlukan jika di masa depan ada mobile app atau integrasi pihak ketiga.

### 6.2 Filament Shield Configuration
- Saat ini Shield sudah terinstall dengan Spatie Permission.
- Untuk MVP, buat 1 permission group: `task-management` dengan permissions: `view_task`, `create_task`, `update_task`, `delete_task`.
- Assign semua permission ke role default `user`.

### 6.3 Database Migration Order
Pastikan migration dijalankan dalam urutan:
1. `0001_01_01_000000_create_users_table.php` (sudah ada)
2. `2025_04_12_082932_create_permission_tables.php` (sudah ada - Shield)
3. `xxxx_create_personal_access_tokens_table.php` (baru - Sanctum)
4. `xxxx_create_tasks_table.php` (baru - Task)

### 6.4 API Response Format
Semua API response mengikuti format yang didefinisikan di PRD:
```json
{
    "status": "success|error",
    "data": {},
    "message": "optional message"
}
```

---

## 7. DEFINISI DONE (PER FITUR)

Sebuah fitur dianggap **selesai** jika:
- [ ] Kode diimplementasikan sesuai spesifikasi
- [ ] Manual test berhasil (sesuai skenario Given-When-Then di PRD)
- [ ] Automated test ditulis (minimal happy path)
- [ ] Tidak ada error 500 di endpoint terkait
- [ ] UI Filament berfungsi responsif (mobile & desktop)
- [ ] Code review dilakukan (jika tim > 1 orang)

---

## 8. PERSETUJUAN

| Peran | Nama | Status | Tanggal |
|-------|------|--------|---------|
| Product Owner | | ⏳ Menunggu Review | |
| Tech Lead | | ⏳ Menunggu Review | |

> **Catatan:** Plan ini memerlukan persetujuan terkait keputusan arsitektur (Filament vs Vue SPA) sebelum development dimulai.
