# TaskFlow Design Document

## UI/UX Principles
- Sidebar-first navigation per role.
- Minimalist, mobile-responsive via Tailwind.
- Status badges with distinct colors.

## Layouts

### Atasan Sidebar (`layouts.atasan`)
- Fixed left sidebar 72rem width.
- Nav: Dashboard, Buat Tugas, Tugas Saya.
- User footer with name, "Atasan" label.

### Admin Sidebar (`layouts.admin`)
- Fixed left sidebar 72rem width.
- Nav: Dashboard, Global Tasks (tugas dari Atasan), Daftar Tugas (tugas ke PM), PM Performance, Hubungi Team.
- User footer with avatar, name, "Super Admin" label + team badge.

### PM Sidebar (`layouts.pm`)
- Nav: Dashboard.
- User footer with team badge.

### Member Sidebar (`layouts.member`)
- Nav: My Tasks.
- User footer with team badges.

## Pages

### 1. Atasan Dashboard (`/atasan/dashboard`)
- Stats: Total Tugas, Belum Diberikan, Sudah Diberikan, Selesai.
- Quick actions: Buat Tugas Baru, Lihat Tugas Saya.

### 2. Atasan Create Task (`/atasan/create-task`)
- Form: title, description, priority, deadline, workspace.
- No PM selector — tugas langsung ke Super Admin.

### 3. Atasan Task List (`/atasan/tasks`)
- Table with status filter (Semua, Belum Diberikan, Sudah Diberikan, Selesai).
- Columns: Tugas, Workspace, Assignee, Status, Priority, Deadline.

### 4. Admin Global Tasks (`/admin/tasks/oversight`)
- Tasks from Atasan (created_by user role = atasan).
- Status: Belum Diberikan / Sudah Diberikan / Selesai.
- Detail modal with assign-to-PM functionality.

### 5. Admin Task List (`/admin/tasks`)
- Tasks assigned to PMs (assigned_to is not null).
- No "Tambah Tugas" button or create form.
- Final approve for pending_admin tasks.
- Detail modal for all tasks.

### 6. PM Dashboard (`/pm/dashboard`)
- Stats cards.
- Assign task to member, approve pending_pm, reject with note.

### 7. Member Dashboard (`/member/dashboard`)
- Task list with file upload + submit button.

## Status Badge Colors

### Task Workflow Status
| Status | Color |
|--------|-------|
| todo / Menunggu | Gray |
| on_progress / Dikerjakan | Blue |
| pending_pm / Review PM | Yellow |
| pending_admin / Review Admin | Purple |
| revision / Revisi | Orange |
| done / Selesai | Green |

### Global Tasks Status
| Status | Color |
|--------|-------|
| Belum Diberikan | Yellow |
| Sudah Diberikan | Blue |
| Selesai | Green |

## Role Labels
| User Role | Label |
|-----------|-------|
| atasan | Atasan |
| admin | Super Admin |
| pm | Project Manager |
| member | Anggota |
