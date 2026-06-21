# TaskFlow Architecture

## Overview
Laravel 11 Fullstack application utilizing Livewire 3 for reactivity and Blade for templating.

## Tech Stack
- **Backend:** PHP 8.2+, Laravel 11.
- **Frontend:** Blade, Tailwind CSS 3.4, Alpine.js 3.0, Livewire 3.
- **Database:** MariaDB 10.6.
- **Auth:** Laravel Breeze (Session-based).

## System Components

### 1. Routing & Middleware
- `web.php` handles all routes.
- Middleware:
  - `auth`: Native Laravel session auth.
  - `check.active`: Custom. Logs out inactive users.
  - `role:{role}`: Custom. Restricts access based on user role (`pm`, `member`, `admin`).

### 2. Livewire Components
Handles business logic and UI state.
- **PM:** `PMDashboard` (manages workspace, members, tasks).
- **Member:** `MemberDashboard` (views assigned tasks, updates status).
- **Admin:** `AdminDashboard` (stats, user management), `TaskOversight` (global task view), `PmPerformance` (KPI aggregation).

### 3. Eloquent Models & Relationships
- **User:** Has One Workspace (if PM), Belongs To Many Workspaces (if member), Has Many Tasks (assigned_to).
- **Workspace:** Belongs To User (PM), Belongs To Many Users (members), Has Many Tasks.
- **Task:** Belongs To Workspace, Belongs To User (created_by), Belongs To User (assigned_to).

### 4. Authorization (Policies)
- `TaskPolicy` is the core security mechanism preventing unauthorized access/edits.
  - `view`: Admin (all), PM (workspace tasks), Member (assigned tasks).
  - `update/delete`: PM (workspace tasks only). Admin strictly denied.
  - `changeStatus`: Member (assigned tasks only).

### 5. Database Schema
Defined in migrations. Key tables: `users`, `workspaces`, `workspace_members`, `tasks`.
Critical constraints: `tasks.assigned_to` cannot be null.

## Data Flow Example (Member Updates Status)
1. Member clicks status dropdown in `MemberDashboard` Livewire component.
2. Component calls `updateStatus($taskId, $newStatus)`.
3. `TaskPolicy@changeStatus` checks if member owns the task assignment.
4. If authorized, task status updated in DB.
5. Livewire re-renders the task card.
