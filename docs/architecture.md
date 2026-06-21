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
│   │   ├── TaskList.php          # CRUD tasks + final approve
│   │   ├── TaskOversight.php     # Global task detail
│   │   ├── PmPerformance.php     # PM KPI metrics
│   │   ├── AssignTask.php        # Assign task to PM
│   │   └── ComposeEmail.php      # Email SA → PM
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
│   ├── pm.blade.php              # Sidebar PM
│   ├── member.blade.php          # Sidebar member
│   └── app.blade.php             # Default top-nav (for AllTasks)
├── livewire/
│   ├── admin/
│   │   ├── admin-dashboard.blade.php
│   │   └── task-list.blade.php
│   ├── pm/
│   │   └── pm-dashboard.blade.php
│   ├── member/
│   │   └── member-dashboard.blade.php
│   └── all-tasks.blade.php
```

## Models & Relationships
- **User:** HasOne Workspace (if PM), HasMany Tasks (assigned_to), HasMany Teams (owner).
- **Workspace:** BelongsTo User (PM), BelongsToMany Users (members), HasMany Tasks.
- **Team:** BelongsTo User (owner), HasMany TeamMembers.
- **Task:** BelongsTo Workspace, BelongsTo User (created_by), BelongsTo User (assigned_to), BelongsTo User (reviewed_by), HasMany Attachments.
- **TeamMember:** BelongsTo Team, BelongsTo User.

## Layout System
Each role gets a dedicated sidebar layout injected with `$sidebarTasks` via View Composer:
- `layouts.admin`: `Task::latest()->take(50)` — all tasks.
- `layouts.pm`: `Task::where('workspace_id', $workspace->id)` — workspace tasks.
- `layouts.member`: `Task::where('assigned_to', auth()->id())` — assigned tasks.

## Task Status Flow
```
todo → on_progress (PM assigns)
on_progress → pending_pm (Member submits + upload)
pending_pm → pending_admin (PM approves)
pending_pm → revision (PM rejects + note)
revision → pending_pm (Member re-submits)
pending_admin → done (Super Admin final approve)
```

## Authorization
TaskPolicy:
- Admin: view all, final approve only (no edit/delete).
- PM: manage workspace tasks (assign, approve, reject).
- Member: only assigned tasks (submit + upload).
