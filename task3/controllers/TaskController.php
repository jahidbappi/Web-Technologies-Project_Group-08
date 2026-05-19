<?php
require_once __DIR__ . '/../config/auth.php';
require_once __DIR__ . '/../config/helpers.php';
require_once __DIR__ . '/../models/Project.php';
require_once __DIR__ . '/../models/Task.php';

class TaskController {
    public function board(int $projectId): void {
        require_login();

        $project = Project::find($projectId);
        if (!$project) {
            http_response_code(404);
            echo 'Project not found.';
            return;
        }
        if (!Project::userCanAccess($projectId, current_user_id(), $project)) {
            $_SESSION['flash_error'] = 'You are not a member of this project\'s workspace.';
            header('Location: ' . route('projects'));
            exit;
        }

        $columns = Task::forProjectGrouped($projectId);
        $members = Project::members($projectId, $project);

        $errors    = $_SESSION['task_errors']    ?? [];
        $old       = $_SESSION['task_old']       ?? [];
        $flash_ok  = $_SESSION['flash_ok']       ?? null;
        unset($_SESSION['task_errors'], $_SESSION['task_old'], $_SESSION['flash_ok']);

        $title = $project['name'] . ' — Board';
        require __DIR__ . '/../views/board/index.php';
    }

    public function create(): void {
        require_login();

        $projectId   = (int)($_POST['project_id'] ?? 0);
        $title       = trim((string)($_POST['title'] ?? ''));
        $description = trim((string)($_POST['description'] ?? ''));
        $assignedTo  = $_POST['assigned_to'] ?? '';
        $priority    = (string)($_POST['priority'] ?? '');
        $dueDate     = trim((string)($_POST['due_date'] ?? ''));

        $project = Project::find($projectId);
        if (!$project || !Project::userCanAccess($projectId, current_user_id(), $project)) {
            $_SESSION['flash_error'] = 'You are not a member of this project\'s workspace.';
            header('Location: ' . route('board', ['project_id' => $projectId]));
            exit;
        }

        $errors = [];
        if ($title === '' || mb_strlen($title) > 100) {
            $errors['title'] = 'Title is required (1–100 characters).';
        }
        if (!in_array($priority, Task::PRIORITIES, true)) {
            $errors['priority'] = 'Select a priority.';
        }
        if ($dueDate === '' || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $dueDate)) {
            $errors['due_date'] = 'Pick a valid due date.';
        }

        $assignedToId = null;
        if ($assignedTo !== '' && $assignedTo !== null) {
            $assignedToId = (int)$assignedTo;
            $memberIds = array_map(fn($m) => (int)$m['id'], Project::members($projectId));
            if (!in_array($assignedToId, $memberIds, true)) {
                $errors['assigned_to'] = 'Assigned member must belong to this project.';
            }
        }

        if ($errors) {
            $_SESSION['task_errors'] = $errors;
            $_SESSION['task_old']    = [
                'title'       => $title,
                'description' => $description,
                'assigned_to' => $assignedToId,
                'priority'    => $priority,
                'due_date'    => $dueDate,
                'open_modal'  => true,
            ];
            header('Location: ' . route('board', ['project_id' => $projectId]));
            exit;
        }

        $newId = Task::create([
            'project_id'  => $projectId,
            'title'       => $title,
            'description' => $description !== '' ? $description : null,
            'assigned_to' => $assignedToId,
            'priority'    => $priority,
            'due_date'    => $dueDate,
            'status'      => 'todo',
        ]);

        try {
            log_activity(
                $projectId,
                current_user_id(),
                "Task '{$title}' created"
            );
        } catch (Throwable $e) {
            error_log('activity log failed: ' . $e->getMessage());
        }

        $_SESSION['flash_ok'] = "Task #{$newId} created.";
        header('Location: ' . route('board', ['project_id' => $projectId]));
        exit;
    }
}
