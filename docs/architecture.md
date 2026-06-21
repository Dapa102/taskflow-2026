# TaskFlow Architecture

## Overview
Laravel 11 Fullstack + Livewire 3 + Sidebar Layout per Role. MariaDB 10.6.

## Tech Stack
- **Backend:** PHP 8.2+, Laravel 11.
- **Frontend:** Blade, Tailwind CSS 3.4, Alpine.js 3.0, Livewire 3.
- **Database:** MariaDB 10.6 (InnoDB).
- **Auth:** Laravel Breeze (Session-based).

## Directory Structure (Key)
```
app/
├── Livewire/
│   ├── Admin/
│   │   ├── Dashboard.php         # Super admin stats + user management
│   │   ├── TaskList.php          # Tasks assigned to PMs + final approve
│   │   ├── TaskOversight.php     # Tasks from Atasan → assign to PM
│   │   ├── PmPerformance.php     # PM KPI metrics
│   │   ├── AssignTask.php        # Assign task to PM
│   │   └── HubungiTeam.php       # Contact team
│   ├── Atasan/
│   │   ├── AtasanDashboard.php   # Stats overview for Atasan
│   │   ├── CreateTask.php        # Create task → Super Admin
│   │   └── AtasanTaskList.php    # View created tasks status
│   ├── Pm/
│   │   ├── PmDashboard.php       # Manage team tasks + review
│   │   └── ComposeEmail.php      # Email PM → Member
│   ├── Member/
│   │   └── MemberDashboard.php   # View tasks + upload + submit
│   └── AllTasks.php              # Read-only task list all roles
├── Models/
│   ├── User.php
│   ├── Task.php                  # BelongsTo: workspace, creator, assignee, reviewedBy
│   ├── Workspace.php
│   ├── Team.php
│   ├── TeamMember.php
│   └── Attachment.php
├── Providers/
│   └── AppServiceProvider.php    # View Composers for sidebar layouts
resources/views/
├── layouts/
│   ├── admin.blade.php           # Sidebar admin
│   ├── atasan.blade.php          # Sidebar atasan
│   ├── pm.blade.php              # Sidebar PM
│   ├── member.blade.php          # Sidebar member
│   └── app.blade.php             # Default top-nav (for AllTasks)
├── livewire/
│   ├── admin/
│   │   ├── admin-dashboard.blade.php
│   │   ├── task-list.blade.php
│   │   └── task-oversight.blade.php
│   ├── atasan/
│   │   ├── atasan-dashboard.blade.php
│   │   ├── create-task.blade.php
│   │   └── atasan-task-list.blade.php
│   ├── pm/
│   │   └── pm-dashboard.blade.php
│   ├── member/
│   │   └── member-dashboard.blade.php
│   └── all-tasks.blade.php
```

## Models & Relationships
- **User (role: admin/pm/member/atasan):** HasOne Workspace (if PM), HasMany Tasks (assigned_to), HasMany Teams (owner), HasMany createdTasks.
- **Workspace:** BelongsTo User (PM), BelongsToMany Users (members), HasMany Tasks.
- **Team:** BelongsTo User (owner), HasMany TeamMembers.
- **Task:** BelongsTo Workspace, BelongsTo User (created_by), BelongsTo User (assigned_to), BelongsTo User (reviewed_by), HasMany Attachments.
- **TeamMember:** BelongsTo Team, BelongsTo User.

## Layout System
Each role gets a dedicated sidebar layout:
- `layouts.admin`: Nav (Dashboard, Global Tasks, Daftar Tugas, PM Performance, Hubungi Team).
- `layouts.atasan`: Nav (Dashboard, Buat Tugas, Tugas Saya).
- `layouts.pm`: Nav (Dashboard).
- `layouts.member`: Nav (My Tasks).

## Task Flow (3-level hierarchy)
```
Atasan (buat tugas) → Super Admin (terima di Global Tasks) → PM (assign ke anggota) → Member
```

## Task Status Flow
```
todo → on_progress (PM assigns)
on_progress → pending_pm (Member submits + upload)
pending_pm → pending_admin (PM approves)
pending_pm → revision (PM rejects + note)
revision → pending_pm (Member re-submits)
pending_admin → done (Super Admin final approve)
```

## Global Tasks Status
- **Belum Diberikan** — tugas dari Atasan, belum di-assign ke PM.
- **Sudah Diberikan** — tugas dari Atasan, sudah di-assign ke PM.

## Authorization
TaskPolicy:
- Admin: view all, final approve only (no edit/delete).
- PM: manage workspace tasks (assign, approve, reject).
- Member: only assigned tasks (submit + upload).
- Atasan: create tasks (created_by).
