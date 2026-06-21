# PRODUCT REQUIREMENTS DOCUMENT (PRD)
## Collaborative Task Management System — Review Workflow Edition

---

**Versi:** 2.0
**Tanggal:** 21 Juni 2026
**Status:** Final untuk Tim Engineering

---

## 1. ARSITEKTUR SISTEM

### 1.1. Arsitektur Umum
Laravel Fullstack (Blade + Livewire) + MariaDB. Sidebar layout per role.

### 1.2. Alur Data
```
[Browser] → [Router (web.php)] → [Auth + Role Middleware] → [Livewire Component]
    → [Eloquent ORM] → [MariaDB] → [Blade View + Sidebar Layout] → [Browser]
```

### 1.3. Layer Layout
| Role | Layout | Sidebar |
|------|--------|---------|
| admin | `layouts.admin` | Daftar Tugas + Links Admin |
| pm | `layouts.pm` | Daftar Tugas + Links PM |
| member | `layouts.member` | Tugas Saya + Links Member |

### 1.4. Tech Stack
- Laravel ^11.0, Livewire ^3.0, Tailwind ^3.4, Alpine.js ^3.0, MariaDB ^10.6

---

## 2. DATABASE DESIGN

### 2.1. Tabel `users`
| Kolom | Tipe | Ket |
|-------|------|-----|
| id | BIGINT UNSIGNED PK | - |
| name | VARCHAR(255) | - |
| email | VARCHAR(255) | Unique |
| password | VARCHAR(255) | Hash bcrypt |
| phone | VARCHAR(20) | Nullable |
| role | ENUM('admin','pm','member') | Default: 'member' |
| is_active | BOOLEAN | Default: true |

### 2.2. Tabel `workspaces`
| Kolom | Tipe | Ket |
|-------|------|-----|
| id | BIGINT UNSIGNED PK | - |
| pm_id | BIGINT UNSIGNED | FK → users.id |
| name | VARCHAR(100) | - |
| description | TEXT | Nullable |

### 2.3. Tabel `workspace_members`
| Kolom | Tipe | Ket |
|-------|------|-----|
| id | BIGINT UNSIGNED PK | - |
| workspace_id | BIGINT UNSIGNED | FK → workspaces.id |
| user_id | BIGINT UNSIGNED | FK → users.id |

### 2.4. Tabel `teams`
| Kolom | Tipe | Ket |
|-------|------|-----|
| id | BIGINT UNSIGNED PK | - |
| owner_id | BIGINT UNSIGNED | FK → users.id (PM) |
| name | VARCHAR(100) | - |
| invite_code | VARCHAR(20) | Nullable |

### 2.5. Tabel `team_members`
| Kolom | Tipe | Ket |
|-------|------|-----|
| id | BIGINT UNSIGNED PK | - |
| team_id | BIGINT UNSIGNED | FK → teams.id |
| user_id | BIGINT UNSIGNED | FK → users.id |
| role | ENUM('admin','member') | Team role |
| joined_at | TIMESTAMP | - |

### 2.6. Tabel `tasks`
| Kolom | Tipe | Ket |
|-------|------|-----|
| id | BIGINT UNSIGNED PK | - |
| workspace_id | BIGINT UNSIGNED | FK → workspaces.id |
| created_by | BIGINT UNSIGNED | FK → users.id |
| assigned_to | BIGINT UNSIGNED | FK → users.id |
| reviewed_by | BIGINT UNSIGNED | Nullable, FK → users.id |
| team_id | BIGINT UNSIGNED | Nullable, index |
| title | VARCHAR(255) | - |
| description | TEXT | Nullable |
| review_note | TEXT | Nullable, catatan revisi |
| status | ENUM('todo','on_progress','pending_pm','pending_admin','revision','done') | Default: 'todo' |
| priority | ENUM('low','medium','high') | Default: 'medium' |
| deadline | DATE | Nullable |

### 2.7. Tabel `attachments`
| Kolom | Tipe | Ket |
|-------|------|-----|
| id | BIGINT UNSIGNED PK | - |
| task_id | BIGINT UNSIGNED | FK → tasks.id |
| user_id | BIGINT UNSIGNED | FK → users.id |
| filename | VARCHAR(255) | - |
| file_path | VARCHAR(255) | - |
| file_size | BIGINT | - |
| mime_type | VARCHAR(100) | - |

---

## 3. ROUTING & MIDDLEWARE

### 3.1. Middleware
- `CheckRole`: Memfilter akses berdasarkan role.
- `CheckActive`: Tolak akses jika akun nonaktif.

### 3.2. Routes
```php
// Auth + check.active
Route::get('/dashboard', [role redirect])->name('dashboard');
Route::get('/tasks', AllTasks::class)->name('tasks.all'); // Semua role

// PM
Route::prefix('pm')->name('pm.')->middleware('role:pm')->group(function () {
    Route::get('/dashboard', PmDashboard::class)->name('dashboard');
    Route::get('/compose-email', ComposeEmail::class)->name('compose.email');
});

// Member
Route::prefix('member')->name('member.')->middleware('role:member')->group(function () {
    Route::get('/dashboard', MemberDashboard::class)->name('dashboard');
});

// Admin
Route::prefix('admin')->name('admin.')->middleware('role:admin')->group(function () {
    Route::get('/dashboard', AdminDashboard::class)->name('dashboard');
    Route::get('/tasks', TaskList::class)->name('tasks.list');
    Route::get('/tasks/oversight/{taskId?}', TaskOversight::class)->name('tasks.oversight');
    Route::get('/assign-task', AssignTask::class)->name('assign.task');
    Route::get('/pm-performance', PmPerformance::class)->name('pm.performance');
    Route::get('/compose-email', ComposeEmail::class)->name('compose.email');
});
```

---

## 4. LIVEWIRE COMPONENTS

### 4.1. Admin\TaskList (`layouts.admin`)
- Buat tugas baru → pilih PM → tampil tim yang dipimpin.
- Filter status (Semua/Pending/Menunggu Admin/Selesai/Terlambat).
- Tombol "Selesai" untuk tugas `pending_admin` (final approve).
- Tombol "Hapus".

### 4.2. Admin\AdminDashboard (`layouts.admin`)
- Statistik global + breakdown status.
- Manajemen user (aktif/suspend) + label tim.
- Tabel workspaces + label PM + tim.
- Tabel teams dengan label (Project Manager) / (Anggota).

### 4.3. Pm\PmDashboard (`layouts.pm`)
- Statistik: Total, Done, Menunggu Review, Revisi.
- Team Members list dengan label (Project Manager) / (Anggota) + nama tim.
- Daftar Tugas Team: Assign ke anggota, Approve, Revisi + catatan.
- **Tidak ada** tombol "Buat Tugas Baru".

### 4.4. Member\MemberDashboard (`layouts.member`)
- Info PM dengan label (Project Manager).
- Team Saya card (nama tim + PM).
- Daftar Tugas Saya: upload file + submit (→ pending_pm).
- Status revision tampilkan catatan revisi.

### 4.5. AllTasks (`layouts.app`)
- Read-only daftar semua tugas untuk semua role.

---

## 5. ALUR STATUS TUGAS

```
SuperAdmin buat → todo
  ↓
PM assign → on_progress
  ↓
Anggota selesai + upload → pending_pm
  ↓
PM approve → pending_admin  |  PM reject + note → revision
  ↓                              ↓
SuperAdmin final → done     Anggota perbaiki + upload ulang → pending_pm
```

---

## 6. SIDEBAR LAYOUTS

### 6.1. `layouts.admin`
- Logo + Nav (Dashboard, Daftar Tugas, Assign Task, Global Tasks, PM Performance, Compose Email).
- Sidebar task list (50 tugas terbaru).
- User footer (nama, "Super Admin", tim).

### 6.2. `layouts.pm`
- Logo + Nav (Dashboard, Daftar Tugas).
- Sidebar task list (tugas workspace PM).
- User footer (nama, "Project Manager", tim).

### 6.3. `layouts.member`
- Logo + Nav (My Tasks, Daftar Tugas).
- Sidebar task list (tugas di-assign ke member).
- User footer (nama, "Anggota", tim).

View Composer di `AppServiceProvider` inject `$sidebarTasks` ke tiap layout.

---

## 7. SEEDER DATA

| Seeder | Data |
|--------|------|
| RoleSeeder | Roles default |
| UserSeeder | 1 admin, 2 PM (Budi/Siti), 4 member (Ahmad/Dewi/Rudi/Fitri), user@admin.com |
| TeamSeeder | Tim Developer (PM Budi), Tim Desain (PM Siti), masing-masing 3 anggota |
| DatabaseSeeder | 3 workspaces, 7 tasks |
| TaskSeeder | 6 personal tasks for user@admin.com |

---

## 8. KEAMANAN

- Policy `TaskPolicy`: Admin final approve only. PM manage workspace tasks. Member only assigned tasks.
- Middleware `CheckRole` dan `CheckActive` di setiap group route.
- Session auth, Blade escaping, CSRF aktif.

---

## 9. FILE UPLOAD

- Anggota upload file (pdf, doc, docx, zip, xlsx, jpg, png, max 10MB).
- File disimpan di `storage/app/public/task-submissions/`.
- Attachment tercatat di tabel `attachments`.
- Storage link: `php artisan storage:link`.
