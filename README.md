# TaskFlow — Daily Task Management System

Full-stack task management app: Laravel API + React SPA + Filament admin.

## Tech Stack

| Layer | Tech |
|-------|------|
| **Backend** | Laravel 12, MariaDB, Sanctum (token auth) |
| **Frontend** | React 19, Vite 6, React Router, Zustand, Tailwind CSS |
| **Admin** | Filament 3 + Shield (RBAC) |
| **Infra** | Docker (nginx + php + mariadb) |

## Features

### ✅ Built
- **Auth** — Register, Login, Logout (split-screen UI, remember me)
- **Tasks** — CRUD with optimistic updates, status toggle, priority, deadline
- **Filter/Search** — By status, category, priority, keyword
- **Task Detail** — Full page with subtasks, comments, attachments
- **Subtasks** — Add, toggle, progress bar, delete
- **Comments** — Post, delete, user avatars, relative timestamps
- **Attachments** — Upload (png/jpg/pdf/docx/xlsx), download, delete, icon by type
- **Categories** — CRUD with color picker, filter on dashboard
- **Teams** — Create, join by invite code, add/remove members (by email search)
- **Team Detail** — Member list with roles, invite code copy, team tasks
- **Super Admin** — Filament `/admin` with Shield roles & permissions
- **User Search API** — `GET /api/users/search?email=...`

### 🚧 Backend exists, frontend pending
- **Notifications** — DB notifications API ready, UI pending
- **Task Assignment** — Assign/unassign users to team tasks API ready
- **Reports** — Summary & team stats API ready (`/api/reports/*`)

### 📋 Planned
- Email notifications (deadline reminders)
- User profile page (edit name/email/avatar)
- PWA support
- Frontend tests

## Quick Start

```bash
docker compose up -d
docker exec taskflow_php php artisan migrate --seed
docker exec taskflow_php php artisan shield:generate --panel=admin --all
npm --prefix src run build
```

### Credentials (Seeder)

| Role | Email | Password |
|------|-------|----------|
| Super Admin | `super@admin.com` | `password` |
| User | `user@admin.com` | `password` |
| Team Member | `member@team.com` | `password` |

### Invite Codes
- Tim Developer: `DEV2026`
- Tim Desain: `DESIGN2026`

## Project Structure

```
src/
├── app/
│   ├── Filament/Admin/Resources/   # Filament resources (Task, User, Team, etc.)
│   ├── Http/Controllers/Api/       # REST API controllers
│   ├── Models/                      # Eloquent models
│   ├── Policies/                    # Authorization policies
│   └── Providers/                   # Service providers
├── database/
│   ├── migrations/
│   └── seeders/                     # DatabaseSeeder, TeamSeeder, etc.
├── resources/
│   └── js/
│       ├── api/client.js            # Axios instance
│       ├── stores/                  # Zustand stores
│       ├── pages/                   # React pages
│       ├── components/              # React components
│       └── main.jsx                 # Entry point
├── routes/
│   └── api.php                      # API routes
├── tests/
├── docs/                            # Documentation
├── docker-compose.yml
└── vite.config.js
```

## API Endpoints

### Auth
| Method | Endpoint | Description |
|--------|----------|-------------|
| POST | `/api/register` | Register |
| POST | `/api/login` | Login |
| POST | `/api/logout` | Logout (auth) |

### Tasks
| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/api/tasks` | List (supports `?include=`, `?status=`, `?search=`, `?category_id=`) |
| POST | `/api/tasks` | Create |
| GET | `/api/tasks/{id}` | Detail (supports `?include=subtasks,comments.user,attachments`) |
| PUT | `/api/tasks/{id}` | Update |
| DELETE | `/api/tasks/{id}` | Delete |
| GET | `/api/tasks/assigned` | Tasks assigned to me |

### Subtasks
| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/api/tasks/{id}/subtasks` | List |
| POST | `/api/tasks/{id}/subtasks` | Create |
| PUT | `/api/subtasks/{id}` | Update title |
| PATCH | `/api/subtasks/{id}/toggle` | Toggle completion |
| DELETE | `/api/subtasks/{id}` | Delete |

### Comments
| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/api/tasks/{id}/comments` | List (with user) |
| POST | `/api/tasks/{id}/comments` | Create |
| DELETE | `/api/comments/{id}` | Delete |

### Attachments
| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/api/tasks/{id}/attachments` | List |
| POST | `/api/tasks/{id}/attachments` | Upload (multipart, max 5MB) |
| DELETE | `/api/attachments/{id}` | Delete |

### Categories
| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/api/categories` | List |
| POST | `/api/categories` | Create |
| PUT | `/api/categories/{id}` | Update |
| DELETE | `/api/categories/{id}` | Delete |

### Teams
| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/api/teams` | List my teams (with members_count) |
| POST | `/api/teams` | Create |
| GET | `/api/teams/{id}` | Detail (with owner, members, tasks) |
| PUT | `/api/teams/{id}` | Update name |
| DELETE | `/api/teams/{id}` | Delete |
| POST | `/api/teams/join` | Join by invite_code |
| GET | `/api/teams/{id}/members` | List members |
| POST | `/api/teams/{id}/members` | Add member (by user_id) |
| DELETE | `/api/teams/{id}/members/{memberId}` | Remove member |

### Notifications
| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/api/notifications` | List |
| POST | `/api/notifications/{id}/read` | Mark as read |
| POST | `/api/notifications/read-all` | Mark all as read |
| DELETE | `/api/notifications/{id}` | Delete |

### Assignments
| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/api/tasks/{id}/assignees` | List assignees |
| POST | `/api/tasks/{id}/assign` | Assign user |
| DELETE | `/api/tasks/{id}/assign/{userId}` | Unassign |

### Reports
| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/api/reports/summary` | User task summary |
| GET | `/api/reports/team/{id}` | Team stats |
| GET | `/api/reports/export` | Export |

### Users
| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/api/user` | Current user profile |
| GET | `/api/users/search?email=` | Search user by email |

## Frontend Pages

| Route | Page | Description |
|-------|------|-------------|
| `/login` | Login.jsx | Split-screen with gradient bg |
| `/register` | Register.jsx | Same layout |
| `/dashboard` | Dashboard.jsx | Task list, filters, FAB |
| `/tasks/:id` | TaskDetail.jsx | Subtasks, comments, attachments |
| `/categories` | Categories.jsx | CRUD + task per category |
| `/teams` | Teams.jsx | List, create/join modals |
| `/teams/:id` | TeamDetail.jsx | Members, invite, tasks |

## Admin Panel

URL: `/admin`  
Super admin manages: users, roles, permissions, tasks, categories, teams.
