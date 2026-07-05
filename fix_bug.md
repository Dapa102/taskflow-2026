# Fix Bug — Role-Based Logic Errors

23 bugs ditemukan (6 critical, 10 high, 4 medium, 3 low).
Perbaikan pertahap, tiap fase deploy + test dulu.

---

## Fase 1 — SA Route + Task List Visibility (CRITICAL)

**Masalah**: PM approve task → `pending_admin` → SA gak bisa liat karena:
- Route SA `super-admin.task-list` / `super-admin.tasks` gak ada
- `SuperAdminTaskList::render()` filter `where('created_by', auth()->id())` — task dibuat PM, `created_by = PM`, SA gak liat

**Fix**:
1. Tambah route `super-admin.tasks` → `SuperAdmin\SuperAdminTaskList`
2. Tambah link sidebar SA
3. Ubah query `SuperAdminTaskList::render()` — hapus filter `created_by`, ganti tampilkan task butuh aksi SA (`pending_admin`, `pending_arbitration`, `pending_pm` escalated)
4. Update action methods (`approveTask`, `cancelTask`, dll) — jangan filter `created_by`

---

## Fase 2 — Status Fixes (CRITICAL)

**Masalah**: Beberapa component pake status legacy (`todo`, `in_progress`, `pending_review`) yang invalid di enum BRD.

### 2a. `SuperAdmin\AssignTask` — set `todo` → `assigned_pm`
### 2b. `Member\Tasks` — `updateStatus()` set `in_progress` / `pending_review`, hapus method
### 2c. `Pm\ReviewTasks` — rewrite total, ganti `pending_review` → `pending_pm`, pake `transition()`
### 2d. `SuperAdmin\TaskOversight` — set `todo` → `assigned_pm`, `assigned_to` → `assigned_pm_id`
### 2e. Blade files — update status references (task-list.blade.php, all-tasks.blade.php, review-tasks.blade.php)

---

## Fase 3 — Notification Ordering (HIGH)

**Masalah**: `assignToMember()` dan `reassignPm()` update `assigned_member_id` / `assigned_pm_id` AFTER `transition()`. Notifikasi dikirim ke user lama/null.

**Fix**:
1. `PmDashboard::assignToMember()` — update `assigned_member_id` SEBELUM `transition()`
2. `SuperAdminTaskList::reassignPm()` — update `assigned_pm_id` SEBELUM `transition()`

---

## Fase 4 — Role admin→super_admin (HIGH)

**Masalah**: `TaskPolicy`, blade, `UserManagement` masih cek role `admin` (legacy).

**Fix**:
1. `TaskPolicy` — ganti `$user->role === 'admin'` → `'super_admin'`
2. `UserManagement::toggleUserStatus()` — ganti `'admin'` → `'super_admin'`
3. Blade `user-management.blade.php` — ganti `<option value="admin">` → `"super_admin"`
4. `TaskPolicy::create()` — izinkan `super_admin` juga

---

## Fase 5 — Missing Notifications + Blade Display (MEDIUM)

**Masalah**: Notifikasi gak dikirim untuk transisi tertentu. Blade pake field name salah.

**Fix**:
1. `TaskStatusHistoryService::notifyTransition()` — tambah `assigned_member→pending_pm` notif ke PM
2. `pending_arbitration` — tambah notif ke `assigned_member_id`
3. Blade `task-detail.blade.php` — ganti `$h->note` → `$h->notes`
4. Blade `pm-dashboard.blade.php` — tombol Assign cuma untuk `assigned_pm`
5. `isOverdue()` — tambah `pending_arbitration` ke pengecualian

---

## Fase 6 — Dead Code Cleanup (LOW)

**Masalah**: Event listener gak dipake, dispatch tanpa listener.

**Fix**:
1. Hapus `#[On('showDetail')]` dari `MemberDashboard` (method dipanggil via `wire:click`)
2. Hapus `dispatch('comment-added')` dari `TaskDetail` (gak ada listener) atau ganti `$refresh`
3. `AllTasks` — tambah filter per role biar gak data leak

---

## Cara Test Per Fase

Setelah tiap fase di-deploy:
1. Login sebagai role terkait
2. Cek error console (F12) — harus bersih
3. Ikuti flow: create → assign → kerja → submit → review → approve
4. Cek notifikasi in-app (inbox)
5. Cek history status di task detail
