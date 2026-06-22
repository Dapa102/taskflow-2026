# PRODUCT REQUIREMENTS DOCUMENT (PRD)
## Collaborative Task Management System — 3-Level Hierarchy

---

**Versi:** 3.0
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

### 1.3. Alur Hierarki Tugas
```
Atasan (buat tugas) → Super Admin (Global Tasks) → PM (Daftar Tugas) → Member
```

### 1.4. Layer Layout
| Role | Layout | Sidebar |
|------|--------|---------|
| atasan | `layouts.atasan` | Dashboard, Buat Tugas, Tugas Saya |
| admin | `layouts.admin` | Dashboard, Global Tasks, Daftar Tugas, PM Performance, Hubungi Team |
| pm | `layouts.pm` | Dashboard |
| member | `layouts.member` | My Tasks |

### 1.6. Profile & Pengaturan
- Semua role (atasan, admin, pm, member) bisa edit profil sendiri via `/profile`
- Field: name, email, phone (no. telepon untuk notifikasi WhatsApp)
- Sidebar footer tiap layout: icon profile → route `profile.edit`

### 1.5. Tech Stack
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
| role | VARCHAR(20) | 'admin','pm','member','atasan' |
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
| created_by | BIGINT UNSIGNED | FK → users.id (Atasan/Admin/PM) |
| assigned_to | BIGINT UNSIGNED | Nullable, FK → users.id (PM/Member) |
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
Route::get('/tasks', AllTasks::class)->name('tasks.all');

// Profile (semua role)
Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

// Atasan
Route::middleware(['role:atasan'])->prefix('atasan')->name('atasan.')->group(function () {
    Route::get('/dashboard', AtasanDashboard::class)->name('dashboard');
    Route::get('/create-task', CreateTask::class)->name('create.task');
    Route::get('/tasks', AtasanTaskList::class)->name('tasks');
});

// PM
Route::middleware(['role:pm'])->prefix('pm')->name('pm.')->group(function () {
    Route::get('/dashboard', PmDashboard::class)->name('dashboard');
    Route::get('/compose-email', ComposeEmail::class)->name('compose.email');
});

// Member
Route::middleware(['role:member'])->prefix('member')->name('member.')->group(function () {
    Route::get('/dashboard', MemberDashboard::class)->name('dashboard');
});

// Admin
Route::middleware(['role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', AdminDashboard::class)->name('dashboard');
    Route::get('/tasks', TaskList::class)->name('tasks.list');
    Route::get('/tasks/oversight/{taskId?}', TaskOversight::class)->name('tasks.oversight');
    Route::get('/assign-task', AssignTask::class)->name('assign.task');
    Route::get('/pm-performance', PmPerformance::class)->name('pm.performance');
    Route::get('/hubungi-team', HubungiTeam::class)->name('hubungi.team');
});
```

---

## 4. LIVEWIRE COMPONENTS

### 4.1. Atasan\AtasanDashboard (`layouts.atasan`)
- Stats cards: Total, Belum Diberikan, Sudah Diberikan, Selesai.
- Quick actions: Buat Tugas Baru, Lihat Tugas Saya.

### 4.2. Atasan\CreateTask (`layouts.atasan`)
- Form: title, description, priority, deadline, workspace.
- Tidak ada PM selector — tugas langsung ke Super Admin.
- `assigned_to` = null, `created_by` = atasan ID.

### 4.3. Atasan\AtasanTaskList (`layouts.atasan`)
- Filter: Semua, Belum Diberikan, Sudah Diberikan, Selesai.
- Table: Tugas, Workspace, Assignee, Status, Priority, Deadline.

### 4.4. Admin\AdminDashboard (`layouts.admin`)
- Statistik global + breakdown status.
- Manajemen user.

### 4.5. Admin\TaskOversight (`layouts.admin`)
- Global Tasks: tugas dari Atasan (`whereHas('creator', role=atasan)`).
- Status: Belum Diberikan / Sudah Diberikan / Selesai.
- Detail modal + assign ke PM.

### 4.6. Admin\TaskList (`layouts.admin`)
- Daftar Tugas: tugas yang sudah di-assign ke PM.
- Tidak ada tombol "Tambah Tugas" atau form create.
- Final approve untuk `pending_admin`.
- Delete + Detail modal.

### 4.7. Pm\PmDashboard (`layouts.pm`)
- Statistik: Total, Done, Menunggu Review, Revisi.
- Assign ke anggota, Approve, Revisi + catatan.

### 4.8. Member\MemberDashboard (`layouts.member`)
- Tugas Saya: upload file + submit (→ pending_pm).
- Status revision tampilkan catatan revisi.

### 4.9. Admin\PmPerformance (`layouts.admin`)
- Menampilkan tabel metrik kinerja PM (total tugas, done, overdue, completion rate).
- Data di-cache 5 menit.
- Per PM ada tombol "Hubungi" → modal popup (email / WhatsApp).

### 4.10. Admin\HubungiTeam (`layouts.admin`)
- Form compose email ke PM.
- Pilih PM dari dropdown → tampilkan tim yang dipimpin.
- Kirim email via SMTP.

### 4.11. AllTasks (`layouts.app`)
- Read-only daftar semua tugas untuk semua role.

---

## 5. ALUR STATUS TUGAS

```
Atasan buat → todo (assigned_to = null)
  ↓ (Super Admin assign ke PM)
todo → on_progress (PM assigns ke member)
  ↓
on_progress → pending_pm (Member submits + upload)
  ↓
pending_pm → pending_admin (PM approves)  |  pending_pm → revision (PM rejects)
  ↓                                            ↓
pending_admin → done (Super Admin final)   revision → pending_pm (Member re-submits)
```

---

## 6. SIDEBAR LAYOUTS

### 6.1. `layouts.atasan`
- Nav: Dashboard, Buat Tugas, Tugas Saya.
- User footer (nama, "Atasan", icon Profile → `/profile`, Logout).

### 6.2. `layouts.admin`
- Nav: Dashboard, Global Tasks, Daftar Tugas, PM Performance, Hubungi Team.
- Sidebar task list.
- User footer (nama, "Super Admin", tim, icon Profile → `/profile`, Logout).

### 6.3. `layouts.pm`
- Nav: Dashboard.
- Sidebar task list (tugas workspace PM).
- User footer (nama, "Project Manager", tim, icon Profile → `/profile`, Logout).

### 6.4. `layouts.member`
- Nav: My Tasks.
- Sidebar task list (tugas di-assign ke member).
- User footer (nama, "Anggota", tim, icon Profile → `/profile`, Logout).

---

## 7. SEEDER DATA

| Seeder | Data |
|--------|------|
| RoleSeeder | Roles default |
| UserSeeder | 1 atasan (Hendra), 1 admin, 2 PM (Budi/Siti), 2 member |
| TeamSeeder | Tim masing-masing PM |
| DatabaseSeeder | Tasks from atasan (assigned + unassigned) + PM internal tasks |

---

## 8. KEAMANAN

- Policy `TaskPolicy`: Admin final approve only. PM manage workspace tasks. Member only assigned tasks.
- Middleware `CheckRole` dan `CheckActive` di setiap group route.
- Session auth, Blade escaping, CSRF aktif.

---

## 9. FILE UPLOAD

- Anggota upload file (pdf, doc, docx, zip, xlsx, jpg, png, max 10MB).
- File disimpan di `storage/app/public/task-submissions/`.
- Storage link: `php artisan storage:link`.
