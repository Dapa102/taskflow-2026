# TaskFlow Design Document

## UI/UX Principles
- Sidebar-first navigation per role.
- Minimalist, mobile-responsive via Tailwind.
- Status badges with distinct colors.

## Layouts

### Admin Sidebar (`layouts.admin`)
- Fixed left sidebar 72rem width.
- Nav: Dashboard, Daftar Tugas, Assign Task, Global Tasks, PM Performance, Compose Email.
- Sidebar task list (latest 50) with color dots + deadline.
- User footer with avatar, name, "Super Admin" label + team badge.

### PM Sidebar (`layouts.pm`)
- Nav: Dashboard, Daftar Tugas.
- Sidebar task list (workspace tasks).
- User footer with team badge.

### Member Sidebar (`layouts.member`)
- Nav: My Tasks, Daftar Tugas.
- Sidebar task list (assigned tasks).
- User footer with team badges.

## Pages

### 1. Authentication
- Centered card login/register.

### 2. Admin Dashboard (`/admin/dashboard`)
- Stats: Users, Workspaces, Tasks.
- Task breakdown: Todo, On Progress, Review PM, Review Admin, Revisi, Done.
- User management table with role label + team badges.
- Workspaces table.
- Teams table: (Project Manager) label, on-progress tasks.

### 3. Admin Task List (`/admin/tasks`)
- Create Task form with PM selector → shows PM's teams.
- Task table with status filter + search.
- Final approve button for `pending_admin` tasks.
- Delete button.

### 4. PM Dashboard (`/pm/dashboard`)
- Stats cards: Total, Done, Menunggu Review, Revisi.
- Team Members list with (Project Manager) / (Anggota) labels + team names.
- Task list: assign to member, approve pending_pm, reject with note.

### 5. Member Dashboard (`/member/dashboard`)
- PM info card with (Project Manager) label.
- Team badges.
- Task list with file upload + submit button.
- Revision note display.

## Status Badge Colors
| Status | Color |
|--------|-------|
| todo / Menunggu | Gray |
| on_progress / Dikerjakan | Blue |
| pending_pm / Review PM | Yellow |
| pending_admin / Review Admin | Purple |
| revision / Revisi | Orange |
| done / Selesai | Green |

## Role Labels
| User Role | Label |
|-----------|-------|
| admin | Admin / Super Admin |
| pm | Project Manager |
| member | Anggota |

## Team Member Labels (in team_members)
| team_members.role | Label |
|-------------------|-------|
| admin | Project Manager (team admin) |
| member | Anggota |
