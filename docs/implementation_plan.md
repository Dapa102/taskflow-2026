# TaskFlow Implementation Plan

## Phase 1: Foundation
1. Laravel 11 + Breeze + Livewire install.
2. DB migrations: users (+role/+is_active), workspaces, workspace_members, teams, team_members, tasks, attachments.
3. Models: User, Workspace, Team, TeamMember, Task, Attachment.
4. Middleware: CheckRole, CheckActive.
5. Seeders: RoleSeeder, UserSeeder, TeamSeeder, DatabaseSeeder, TaskSeeder.

## Phase 2: Super Admin
1. `layouts.admin` sidebar: nav links, task list, user footer.
2. `AdminDashboard`: stats, user management, workspace table, teams table.
3. `TaskList`: create task (PM selector → show teams), table, filter, final approve, delete.
4. View Composer inject `$sidebarTasks` into `layouts.admin`.

## Phase 3: Project Manager
1. `layouts.pm` sidebar: nav links, workspace task list, user footer.
2. `PmDashboard`: stats, members list (with labels), task list, assign/approve/reject.
3. View Composer for `layouts.pm`.

## Phase 4: Member
1. `layouts.member` sidebar: nav links, assigned task list, user footer.
2. `MemberDashboard`: PM info, team badges, task list with upload + submit.
3. View Composer for `layouts.member`.

## Phase 5: Review Workflow
1. Migration: `review_note`, `reviewed_by`, new status enum.
2. `approveTask` / `rejectTask` in PmDashboard.
3. `finalApproveTask` in TaskList.
4. Member upload logic + file validation.

## Phase 6: Polish
1. Role + team labels across all views.
2. Remove redundant dashboard sections (task list, activity log).
3. Clear view cache, verify all routes.
4. Seeder refresh with complete demo data.
