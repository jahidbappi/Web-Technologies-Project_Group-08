# Web Technologies Project — Group 08

Merged Task 1 (auth/workspaces), Task 2 (projects/comments), and Task 3 (Kanban board).

## Database (final)

Import **`project_management.sql`** at the project root into MySQL. Database name: `project_management`.

### Demo users

| Name   | Email              | Password  |
|--------|--------------------|-----------|
| Mim    | mim@gmail.com      | mim0@     |
| sinha  | sinha@gmail.com    | sinha0    |
| mohini | mohini@gmail.com   | mohini0   |

All three belong to workspace **Group 08 Workspace** (invite code: `GRP08A`).

Active projects (not archived): Aqua (12), Wastewise (13), SafeRoute (14), Meditrack (15). Sample tasks are included for the board.

## Setup (XAMPP)

1. Copy this folder to `htdocs/Web-Technologies-Project_Group-08`
2. Start **Apache** and **MySQL**
3. Import `project_management.sql` in phpMyAdmin, or visit  
   http://localhost/Web-Technologies-Project_Group-08/setup-database.php
4. Open http://localhost/Web-Technologies-Project_Group-08/

## App flow

1. Log in (Task 1)
2. Dashboard → projects (Task 2)
3. **Open Board** → Kanban (Task 3)

Sessions are shared across `TASK1/`, `TASK2/`, and `task3/`.
