# Web Technologies Project — Group 08 (Merged)

This repository combines all three group tasks into one application:

| Module | Folder | Features |
|--------|--------|----------|
| **Task 1** | `TASK1/` | User registration/login, workspaces, invite codes, member management |
| **Task 2** | `TASK2/` | Project CRUD, archiving, comments API, activity feed |
| **Task 3** | `task3/` | Kanban board, drag-and-drop task status, task creation |

## Setup

1. Copy the project into your XAMPP `htdocs` folder (e.g. `htdocs/Web-Technologies-Project_Group-08`).
2. Start **Apache** and **MySQL** in XAMPP.
3. Import `project_management.sql` into phpMyAdmin (database name: `project_management`).
4. Open `http://localhost/Web-Technologies-Project_Group-08/` — you will be redirected to Task 1 login.

Default MySQL: user `root`, empty password. The app tries ports **3306** and **3307**.

## Flow

1. **Register / log in** (`TASK1/`) and select or create a workspace.
2. **Dashboard** shows projects for the active workspace (Task 2 data).
3. **Open Board** opens the Kanban view (Task 3) for that project.
4. **Task details** (from a card title on the board) opens comments (Task 2).
5. **Activity feed** is available from project detail pages.

Sessions are shared across all three modules when served from the same app folder path.

## Structure

```
├── TASK1/          # Auth & workspaces
├── TASK2/          # Projects, comments, activity
├── task3/          # Kanban board
├── config/app.php  # Shared session & URL helpers
├── index.php       # Entry redirect → TASK1
└── project_management.sql
```
