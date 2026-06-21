# TaskFlow Implementation Plan

## Phase 1: Foundation
1. Laravel 11 + Breeze + Livewire install.
2. DB migrations: users (+role/+is_active), workspaces, workspace_members, teams, team_members, tasks, attachments.
3. Models: User, Workspace, Team, TeamMember, Task, Attachment.
4. Middleware: CheckRole, CheckActive.
5. Seeders: RoleSeeder, UserSeeder, TeamSeeder, DatabaseSeeder, TaskSeeder.

## Phase 2: Atasan Role
1. Migration: add 'atasan' to users.role (convert to VARCHAR).
2. `layouts.atasan` sidebar: nav links, user footer.
3. `AtasanDashboard`: stats overview, quick actions.
4. `CreateTask`: create task → assigned_to = null (goes to Super Admin).
5. `AtasanTaskList`: view created tasks with status filter.
6. Routes `/atasan/*`.

## Phase 3: Super Admin
1. `layouts.admin` sidebar: nav links, task list, user footer.
2. `AdminDashboard`: stats, user management, workspace table, teams table.
3. `TaskOversight` (Global Tasks): show tasks from atasan, assign to PM.
4. `TaskList` (Daftar Tugas): tasks assigned to PMs, final approve, no create form.
5. View Composer inject `$sidebarTasks` into `layouts.admin`.

## Phase 4: Project Manager
1. `layouts.pm` sidebar: nav links, workspace task list, user footer.
2. `PmDashboard`: stats, members list (with labels), task list, assign/approve/reject.
3. View Composer for `layouts.pm`.

## Phase 5: Member
1. `layouts.member` sidebar: nav links, assigned task list, user footer.
2. `MemberDashboard`: PM info, team badges, task list with upload + submit.
3. View Composer for `layouts.member`.

## Phase 6: Review Workflow
1. Migration: `review_note`, `reviewed_by`, new status enum.
2. `approveTask` / `rejectTask` in PmDashboard.
3. `finalApproveTask` in TaskList.
4. Member upload logic + file validation.

## Phase 7: Polish
1. Role + team labels across all views.
2. Remove redundant dashboard sections.
3. Clear view cache, verify all routes.
4. Seeder refresh with complete demo data.
