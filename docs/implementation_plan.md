# TaskFlow Implementation Plan

## Phase 1: Foundation & Setup
**Goal:** Setup environment, database schema, and authentication basics.

1. **Project Initialization:**
   - Install Laravel 11.
   - Configure `.env` for MariaDB.
   - Install Laravel Breeze (Blade stack).
   - Install Livewire 3.

2. **Database Migrations:**
   - Update `users` table (`role`, `is_active`).
   - Create `workspaces` table.
   - Create `workspace_members` table.
   - Create `tasks` table (`workspace_id`, `created_by`, `assigned_to`, `status`, `priority`, `deadline`).

3. **Models & Relationships:**
   - Define Eloquent models (`User`, `Workspace`, `Task`).
   - Setup relationships (HasOne, HasMany, BelongsToMany, BelongsTo).

4. **Middleware:**
   - Create `CheckRole` middleware.
   - Create `CheckActive` middleware.
   - Register in `bootstrap/app.php` or kernel.

## Phase 2: Core Features (PM & Member)
**Goal:** Enable team creation, task assignment, and progress tracking.

1. **Project Manager (PM) Features:**
   - Build `PMDashboard` Livewire component.
   - Implement workspace creation logic.
   - Implement member invitation/removal logic.
   - Implement task CRUD (Create, Read, Update, Delete) for PM.
   - Setup `TaskPolicy` (PM permissions).

2. **Team Member Features:**
   - Build `MemberDashboard` Livewire component.
   - Display tasks assigned to member.
   - Implement status update logic (To-Do -> On-Progress -> Done).
   - Update `TaskPolicy` (Member permissions).

3. **Routing & Views:**
   - Setup `pm/*` and `member/*` routes in `web.php`.
   - Build UI for dashboards using Tailwind CSS.

## Phase 3: Admin Oversight & Analytics
**Goal:** Implement Super Admin monitoring capabilities.

1. **Admin Dashboard:**
   - Build `AdminDashboard` component (global stats).
   - Implement user management (activate/suspend).

2. **Global Task Oversight:**
   - Build `TaskOversight` Livewire component.
   - Implement global task list with filters (status, overdue).
   - Build read-only task detail modal.
   - Enforce `TaskPolicy` (Admin read-only rules).

3. **PM Performance Metrics:**
   - Build `PmPerformance` Livewire component.
   - Implement aggregation logic (total tasks, done, overdue, completion rate).
   - Build performance table UI.

4. **Routing & Views:**
   - Setup `admin/*` routes in `web.php`.
   - Build UI for admin panels.

## Phase 4: Refinement & Testing
**Goal:** Polish UI, fix bugs, and prepare for deployment.

1. **UI/UX Polish:**
   - Ensure responsive design across all dashboards.
   - Add loading states and feedback notifications (Livewire/Alpine).

2. **Security & Validation:**
   - Verify all form requests and Livewire validation rules.
   - Test middleware and policy enforcement (e.g., attempt unauthorized actions).

3. **Seeding & Demo Data:**
   - Create seeders for Admin, sample PMs, Members, and Tasks to facilitate testing.

4. **Final Review:**
   - Code cleanup.
   - Verify alignment with BRD/PRD.
