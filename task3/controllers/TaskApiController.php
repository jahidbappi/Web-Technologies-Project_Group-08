<?php
require_once __DIR__ . '/../config/auth.php';
require_once __DIR__ . '/../config/helpers.php';
require_once __DIR__ . '/../models/Project.php';
require_once __DIR__ . '/../models/Task.php';

class TaskApiController {
    public function updateStatus(int $taskId): void {
        require_login_api();

        $raw  = file_get_contents('php://input');
        $body = [];
        if ($raw) {
            $decoded = json_decode($raw, true);
            if (is_array($decoded)) $body = $decoded;
        }
        if (!$body) $body = $_POST;

        $newStatus = (string)($body['status'] ?? '');
        if (!in_array($newStatus, Task::STATUSES, true)) {
            json_response(['ok' => false, 'error' => 'Invalid status'], 422);
        }

        $task = Task::find($taskId);
        if (!$task) {
            json_response(['ok' => false, 'error' => 'Task not found'], 404);
        }

        if (!Project::userCanAccess((int)$task['project_id'], current_user_id())) {
            json_response(['ok' => false, 'error' => 'Forbidden'], 403);
        }

        $currentStatus = (string)$task['status'];
        if ($currentStatus === $newStatus) {
            json_response(['ok' => true, 'new_status' => $currentStatus]);
        }

        if (!Task::isValidTransition($currentStatus, $newStatus)) {
            json_response([
                'ok'    => false,
                'error' => "Invalid transition: {$currentStatus} → {$newStatus}",
            ], 422);
        }

        Task::updateStatus($taskId, $newStatus);

        $label = status_label($newStatus);
        log_activity(
            (int)$task['project_id'],
            current_user_id(),
            "Task '{$task['title']}' moved to {$label}"
        );

        json_response([
            'ok'         => true,
            'new_status' => $newStatus,
        ]);
    }
}
