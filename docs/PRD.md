# 📄 PRODUCT REQUIREMENTS DOCUMENT (PRD)
## Collaborative Task Management System — Pure Team Edition

---

**Versi:** 1.0 (System Focus)
**Tanggal:** 21 Juni 2026
**Status:** Final untuk Tim Engineering
**Arsitektur:** Laravel Fullstack (Blade + Livewire) + MariaDB

---

## 1. TUJUAN DOKUMEN

Dokumen ini adalah **spesifikasi teknis mutlak** yang wajib diimplementasikan oleh tim engineering. Berisi arsitektur sistem, struktur database (skema SQL), routing, middleware, komponen Livewire, policy/authorization, validasi, dan standar keamanan untuk aplikasi kolaborasi tim murni.

**Perbedaan Fundamental dengan PRD Sebelumnya:**

- **Tidak ada** konsep "tugas pribadi". Semua tugas wajib memiliki `workspace_id` dan `assigned_to`.
- **Admin** memiliki hak akses baca penuh ke semua data tugas (oversight), tetapi diblokir untuk aksi edit/delete.

---

## 2. ARSITEKTUR SISTEM

### 2.1. Arsitektur Umum
Aplikasi menggunakan pola **Server-Side Rendering (SSR)** dengan komponen reaktif berbasis **Livewire 3**. Semua logika bisnis, autentikasi (session-based), dan rendering dilakukan di sisi server. JavaScript (Alpine.js) hanya digunakan untuk interaksi UI ringan (animasi, toggle dropdown).

### 2.2. Alur Data
```text
[Browser] --(HTTP Request)--> [Router (web.php)]
                                   |
                    [Middleware: Auth, Role]
                                   |
         [Controller / Livewire Component (PM/Member/Admin)]
                                   |
                     [Eloquent ORM + Policy]
                                   |
                            [MariaDB]
                                   |
                          [Blade View]
                                   |
                         [Browser Render]
```

### 2.3. Tech Stack (Wajib)

| Komponen | Teknologi | Versi / Spesifikasi |
|----------|-----------|----------------------|
| Backend Framework | Laravel | ^11.0 (PHP 8.2+) |
| Komponen Reaktif | Livewire | ^3.0 |
| Templating | Blade | - |
| CSS | Tailwind CSS | ^3.4 |
| JS Interaksi | Alpine.js | ^3.0 |
| Database | MariaDB | ^10.6 (InnoDB) |
| Autentikasi | Laravel Breeze (Session) | - |

---

## 3. DATABASE DESIGN (SCHEMA & MIGRATION)

### 3.1. Tabel `users` (Default Laravel + Kolom Tambahan)
| Kolom | Tipe | Null | Keterangan |
|-------|------|------|------------|
| id | BIGINT UNSIGNED | NO | Primary Key, Auto Increment |
| name | VARCHAR(255) | NO | - |
| email | VARCHAR(255) | NO | Unique |
| password | VARCHAR(255) | NO | Hash bcrypt |
| **role** | **ENUM('pm', 'member', 'admin')** | **NO** | **Default: 'member'** (Penentu akses fitur) |
| **is_active** | **BOOLEAN** | **NO** | **Default: true** (Jika false, middleware tolak akses) |
| created_at | TIMESTAMP | YES | - |
| updated_at | TIMESTAMP | YES | - |

### 3.2. Tabel `workspaces` (Hanya untuk PM)
| Kolom | Tipe | Null | Keterangan |
|-------|------|------|------------|
| id | BIGINT UNSIGNED | NO | Primary Key, Auto Increment |
| pm_id | BIGINT UNSIGNED | NO | Foreign Key ke `users.id` (Pemilik/PM) |
| name | VARCHAR(100) | NO | Nama tim |
| description | TEXT | YES | Deskripsi tim |
| created_at | TIMESTAMP | YES | - |

*Relasi:* Satu PM hanya boleh memiliki 1 Workspace di MVP (dipaksakan di level aplikasi).

### 3.3. Tabel `workspace_members` (Relasi Many-to-Many)
| Kolom | Tipe | Null | Keterangan |
|-------|------|------|------------|
| id | BIGINT UNSIGNED | NO | Primary Key, Auto Increment |
| workspace_id | BIGINT UNSIGNED | NO | FK ke `workspaces.id` (ON DELETE CASCADE) |
| user_id | BIGINT UNSIGNED | NO | FK ke `users.id` (ON DELETE CASCADE) |
| joined_at | TIMESTAMP | YES | Default: CURRENT_TIMESTAMP |

### 3.4. Tabel `tasks` (Inti Aplikasi - WAJIB ADA ASSIGNEE & WORKSPACE)
| Kolom | Tipe | Null | Keterangan |
|-------|------|------|------------|
| id | BIGINT UNSIGNED | NO | Primary Key, Auto Increment |
| workspace_id | BIGINT UNSIGNED | NO | FK ke `workspaces.id` (ON DELETE CASCADE) |
| created_by | BIGINT UNSIGNED | NO | FK ke `users.id` (PM yang membuat) |
| **assigned_to** | **BIGINT UNSIGNED** | **NO** | **FK ke `users.id` (WAJIB diisi, tidak boleh NULL)** |
| title | VARCHAR(255) | NO | Judul tugas |
| description | TEXT | YES | Deskripsi detail |
| status | ENUM('todo', 'on_progress', 'done') | NO | Default: 'todo' |
| priority | ENUM('low', 'medium', 'high') | NO | Default: 'medium' |
| deadline | DATE | YES | Nullable |
| created_at | TIMESTAMP | YES | - |
| updated_at | TIMESTAMP | YES | - |
| *Index* | - | - | **Wajib** index pada `workspace_id`, `assigned_to`, `status` |

**Catatan Penting untuk Developer:**
- **Tidak ada** kolom `user_id` sebagai pemilik. Kepemilikan diatur melalui `workspace_id`.
- **Tidak boleh** ada tugas yang `assigned_to`-nya NULL. Validasi di backend harus memastikan ini.

---

## 4. ROUTING (web.php) & MIDDLEWARE

### 4.1. Konfigurasi Middleware Kustom
Buat middleware **`CheckRole`** dan **`CheckActive`** di `app/Http/Kernel.php`.

```php
// app/Http/Middleware/CheckRole.php
public function handle($request, Closure $next, ...$roles) {
    if (!auth()->check()) return redirect('login');
    if (!in_array(auth()->user()->role, $roles)) abort(403);
    return $next($request);
}

// app/Http/Middleware/CheckActive.php
public function handle($request, Closure $next) {
    if (auth()->check() && !auth()->user()->is_active) {
        auth()->logout();
        return redirect('login')->with('error', 'Akun Anda dinonaktifkan.');
    }
    return $next($request);
}
```

### 4.2. Daftar Route (web.php)
```php
<?php

use App\Livewire\Admin\AdminDashboard;
use App\Livewire\Admin\TaskOversight;
use App\Livewire\Admin\PmPerformance;
use App\Livewire\PM\PMDashboard;
use App\Livewire\Member\MemberDashboard;

Route::middleware(['auth', 'check.active'])->group(function () {

    // ===== ROUTE UNTUK PROJECT MANAGER =====
    Route::middleware(['role:pm'])->prefix('pm')->name('pm.')->group(function () {
        Route::get('/dashboard', PMDashboard::class)->name('dashboard');
        // Route lainnya untuk PM (kelola workspace, anggota, tugas) 
        // sudah tertampung di dalam komponen PMDashboard (Livewire).
    });

    // ===== ROUTE UNTUK TEAM MEMBER =====
    Route::middleware(['role:member'])->prefix('member')->name('member.')->group(function () {
        Route::get('/dashboard', MemberDashboard::class)->name('dashboard');
    });

    // ===== ROUTE UNTUK SUPER ADMIN =====
    Route::middleware(['role:admin'])->prefix('admin')->name('admin.')->group(function () {
        Route::get('/dashboard', AdminDashboard::class)->name('dashboard');
        Route::get('/tasks', TaskOversight::class)->name('tasks.oversight'); // Fitur UC-15,16,17
        Route::get('/pm-performance', PmPerformance::class)->name('pm.performance'); // Fitur UC-18
    });

});

// Route Auth (Login, Register, Logout) - Default dari Breeze
require __DIR__.'/auth.php';

// Route Register dengan tambahan pilihan role (PM/Member) di view.
```

---

## 5. LIVEWIRE COMPONENTS (STRUKTUR & LOGIKA WAJIB)

### 5.1. Komponen `PMDashboard` (app/Livewire/PM/PMDashboard.php)
- **Fungsi:** Mengelola Workspace, Anggota, dan Tugas Tim.
- **Method Wajib:**
  - `createWorkspace()`: Hanya bisa dijalankan jika user belum punya workspace.
  - `inviteMember($email)`: Mencari user dengan role 'member', menambah ke `workspace_members`.
  - `removeMember($userId)`: Menghapus dari `workspace_members`.
  - `createTask($data)`: Validasi `assigned_to` harus ada di `workspace_members`.
  - `updateTask($id, $data)`: Hanya bisa ubah tugas milik workspace-nya.
  - `deleteTask($id)`: Hanya bisa hapus tugas milik workspace-nya.
- **Render View:** `resources/views/livewire/pm/pm-dashboard.blade.php` (Menampilkan form + daftar tugas tim).

### 5.2. Komponen `MemberDashboard` (app/Livewire/Member/MemberDashboard.php)
- **Fungsi:** Melihat dan mengupdate status tugas yang di-assign.
- **Method Wajib:**
  - `mount()`: Ambil tugas `Task::where('assigned_to', auth()->id())->get()`.
  - `updateStatus($taskId, $newStatus)`: Validasi apakah tugas tersebut di-assign ke user ini. Jika ya, update status.
- **Render View:** Menampilkan daftar kartu tugas (tanpa tombol edit/delete).

### 5.3. Komponen `AdminDashboard` (app/Livewire/Admin/AdminDashboard.php)
- **Fungsi:** Statistik platform global (Total User, Total Workspace, Total Tugas).
- **Render View:** Halaman landing admin sederhana.

### 5.4. Komponen `TaskOversight` (app/Livewire/Admin/TaskOversight.php) - **FITUR UTAMA ADMIN**
- **Fungsi:** Melihat semua tugas dari semua workspace dengan filter.
- **Properti:** `$statusFilter` (null, 'done', 'overdue', 'pending'), `$search`.
- **Logic Query:**
  ```php
  $query = Task::with(['workspace', 'assignedTo', 'createdBy']);
  
  if ($this->statusFilter == 'overdue') {
      $query->where('deadline', '<', now())->where('status', '!=', 'done');
  } elseif ($this->statusFilter == 'done') {
      $query->where('status', 'done');
  } else { // pending
      $query->where('status', '!=', 'done');
  }
  // Di view, tombol "Detail" akan memunculkan modal read-only (tanpa form edit).
  ```
- **Policy:** `viewAny` untuk Admin selalu true. `update` dan `delete` secara tegas **ditolak** (tidak ada tombolnya di view, dan jika ada request manual via POST, Policy akan menolak).

### 5.5. Komponen `PmPerformance` (app/Livewire/Admin/PmPerformance.php) - **FITUR EVALUASI PM**
- **Fungsi:** Menampilkan metrik kinerja setiap Project Manager.
- **Logic Query (Aggregate):**
  ```php
  $pms = User::where('role', 'pm')->with('workspace')->get();
  foreach ($pms as $pm) {
      $tasks = Task::whereHas('workspace', function($q) use ($pm) {
          $q->where('pm_id', $pm->id);
      })->get();
      
      $pm->total_tasks = $tasks->count();
      $pm->done_tasks = $tasks->where('status', 'done')->count();
      $pm->overdue_tasks = $tasks->filter(function($t) {
          return $t->deadline < now() && $t->status != 'done';
      })->count();
      $pm->on_time_rate = $pm->total_tasks > 0 ? round(($pm->done_tasks / $pm->total_tasks) * 100, 2) : 0;
  }
  ```

---

## 6. AUTORIZATION (POLICIES)

Buat `TaskPolicy` untuk mengatur hak akses secara ketat.

```php
// app/Policies/TaskPolicy.php
class TaskPolicy {
    // Semua orang bisa melihat tugas sendiri (untuk member) atau tim (untuk PM)
    public function view(User $user, Task $task) {
        // Admin bisa melihat semua
        if ($user->role === 'admin') return true;
        // PM bisa melihat tugas di workspacenya
        if ($user->role === 'pm') {
            return $task->workspace->pm_id === $user->id;
        }
        // Member hanya bisa melihat tugas yang di-assign ke dirinya
        return $task->assigned_to === $user->id;
    }

    // CREATE: Hanya PM yang bisa membuat, dan hanya untuk workspace miliknya
    public function create(User $user) {
        return $user->role === 'pm' && $user->workspace; // Pastikan punya workspace
    }

    // UPDATE: PM bisa update tugas di workspacenya. ADMIN DIBLOKIR!
    public function update(User $user, Task $task) {
        if ($user->role === 'admin') return false; // EKSPLISIT DIBLOKIR
        if ($user->role === 'pm') {
            return $task->workspace->pm_id === $user->id;
        }
        return false; // Member tidak boleh update (hanya boleh ganti status via method terpisah)
    }

    // DELETE: Sama seperti update, Admin & Member diblokir
    public function delete(User $user, Task $task) {
        if ($user->role === 'admin') return false;
        if ($user->role === 'pm') {
            return $task->workspace->pm_id === $user->id;
        }
        return false;
    }
    
    // Khusus untuk Member mengubah status (bukan edit penuh)
    public function changeStatus(User $user, Task $task) {
        return $task->assigned_to === $user->id && $user->role === 'member';
    }
}
```

---

## 7. VALIDASI TEKNIS (FORM REQUEST / LIVEWIRE RULES)

### 7.1. Validasi Tugas (Create / Edit oleh PM)
| Field | Aturan | Pesan Error |
|-------|--------|-------------|
| `title` | Required, string, max:255 | "Judul wajib diisi" |
| `description` | Nullable, string | - |
| `assigned_to` | Required, exists:users,id, **dan harus menjadi anggota workspace** (validasi custom) | "Pilih anggota tim" |
| `priority` | In: low, medium, high | - |
| `deadline` | Nullable, date, after_or_equal:today | "Deadline tidak boleh kurang dari hari ini" |

### 7.2. Validasi Registrasi (Pilihan Role)
| Field | Aturan | Pesan Error |
|-------|--------|-------------|
| `role` | Required, in:pm,member | "Pilih peran" |
| (Email, Password tetap standar) | - | - |

---

## 8. KEAMANAN SISTEM (IMPLEMENTASI)

| Ancaman | Solusi Teknis (Laravel Fullstack) |
|---------|-----------------------------------|
| **SQL Injection** | Eloquent ORM / Query Builder (binding otomatis). |
| **XSS** | Blade `{{ }}` otomatis escape; Livewire juga melakukan sanitasi output. |
| **CSRF** | **Aktif** di semua form (`@csrf`). |
| **Mass Assignment** | Model `Task` set `$fillable = ['workspace_id', 'created_by', 'assigned_to', 'title', 'description', 'priority', 'deadline']`. |
| **Autorisasi (IDOR)** | **Wajib** gunakan `$this->authorize()` di setiap method Livewire sebelum aksi (update/delete/view). |
| **Brute Force Login** | Aktifkan `throttle:5,1` pada route login. |
| **Akses Admin ke Edit** | Selain Policy, di view Blade Admin untuk `TaskOversight`, **jangan render** tombol Edit/Delete sama sekali. |

---

## 9. PERFORMANCE & OPTIMASI

| Area | Optimasi |
|------|----------|
| **Query** | Gunakan `with()` (Eager Loading) untuk relasi `workspace`, `assignedTo`, `createdBy` pada halaman Admin dan PM untuk menghindari N+1. |
| **Index Database** | Wajib tambahkan index pada kolom `workspace_id`, `assigned_to`, `status` di tabel `tasks`. |
| **Livewire** | Gunakan `#[On]` untuk event antar komponen. Gunakan `wire:key` pada loop `@foreach` untuk efisiensi DOM diffing. |
| **Caching** | Untuk halaman `PmPerformance` (Admin), cache hasil query selama 5 menit untuk mengurangi beban agregasi jika data besar. |

---

## 10. DEPLOYMENT CHECKLIST

- [ ] Set `APP_ENV=production` dan `APP_DEBUG=false`.
- [ ] Jalankan `php artisan migrate --force`.
- [ ] Jalankan `php artisan config:cache`, `route:cache`, `view:cache`.
- [ ] Pastikan `storage/` dan `bootstrap/cache` memiliki permission yang benar.
- [ ] Buat akun Admin pertama via seeder (`php artisan db:seed --class=AdminSeeder`).
