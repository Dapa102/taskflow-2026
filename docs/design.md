# TaskFlow Design Document

## UI/UX Principles
- **Minimalist:** No clutter. Focus on tasks.
- **Role-Based Views:** UI changes based on role (PM, Member, Admin).
- **Responsive:** Mobile-first design.

## Pages & Layouts

### 1. Authentication
- **Login/Register:** Centered card. Clear role selection (PM vs Member) during registration.

### 2. Project Manager (PM) Dashboard
- **Header:** Workspace Name, PM Name.
- **Sidebar (optional):** Team Members list, Invite button.
- **Main Area:**
  - **KPI Cards:** Total Tasks, Done, Overdue.
  - **Task Board/List:** Filter by status. Create Task button.
- **Modals:**
  - Create/Edit Task.
  - Invite Member.

### 3. Team Member Dashboard
- **Header:** Workspace Name, Member Name.
- **Main Area:**
  - **Task List:** Only tasks assigned to them.
  - **Status Update:** Simple dropdown/toggle (To-Do, On-Progress, Done). No edit/delete buttons for task details.

### 4. Super Admin Dashboard
- **Header:** Admin Panel.
- **Main Area:**
  - **Overview:** Total Users, Workspaces, Tasks.
  - **User Management:** Table with active/suspend toggle.
  - **Task Oversight (Global):** Filterable list (Done, Pending, Overdue). Click opens read-only modal.
  - **PM Performance:** Table showing PM Name, Total Tasks, Done, Overdue, On-Time Rate %.

## Components (Tailwind + Livewire/Alpine)
- **Cards:** For tasks and KPI metrics.
- **Modals:** For forms (Create Task, Invite Member) and read-only details (Admin).
- **Badges:** Task status (Gray=To-Do, Blue=On-Progress, Green=Done, Red=Overdue).
- **Tables:** For Admin views (Users, Tasks, PM Performance).

## Color Palette (Proposed)
- **Primary:** Blue (Tailwind `blue-600`)
- **Success:** Green (`green-500`)
- **Warning:** Yellow (`yellow-500`)
- **Danger:** Red (`red-500`)
- **Background:** Light Gray (`gray-50`)
