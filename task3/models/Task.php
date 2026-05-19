<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/User.php';

class Task {
    public const STATUSES   = ['todo', 'in-progress', 'done'];
    public const PRIORITIES = ['low', 'medium', 'high'];

    public const TRANSITIONS = [
        'todo'        => ['in-progress'],
        'in-progress' => ['todo', 'done'],
        'done'        => ['in-progress'],
    ];

    public static function isValidTransition(string $from, string $to): bool {
        return in_array($to, self::TRANSITIONS[$from] ?? [], true);
    }

    public static function forProjectByStatus(int $projectId, string $status): array {
        $stmt = db()->prepare(
            'SELECT t.*, u.name AS assignee_name
               FROM tasks t
          LEFT JOIN users u ON u.id = t.assigned_to
              WHERE t.project_id = :pid AND t.status = :status
           ORDER BY t.created_at ASC, t.id ASC'
        );
        $stmt->execute([':pid' => $projectId, ':status' => $status]);
        $rows = $stmt->fetchAll();

        return array_map([self::class, 'normalizeAssignee'], $rows);
    }

    public static function find(int $id): ?array {
        $stmt = db()->prepare(
            'SELECT t.*, u.name AS assignee_name
               FROM tasks t
          LEFT JOIN users u ON u.id = t.assigned_to
              WHERE t.id = :id'
        );
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch();
        return $row ? self::normalizeAssignee($row) : null;
    }

    private static function normalizeAssignee(array $task): array {
        if (empty($task['assignee_name']) && !empty($task['assigned_to'])) {
            $user = User::find((int)$task['assigned_to']);
            if ($user) {
                $task['assignee_name'] = $user['name'];
            }
        }
        return $task;
    }

    public static function create(array $data): int {
        $stmt = db()->prepare(
            'INSERT INTO tasks
                (project_id, title, description, assigned_to, priority, due_date, status, created_at)
             VALUES
                (:project_id, :title, :description, :assigned_to, :priority, :due_date, :status, NOW())'
        );
        $stmt->execute([
            ':project_id'  => $data['project_id'],
            ':title'       => $data['title'],
            ':description' => $data['description'],
            ':assigned_to' => $data['assigned_to'],
            ':priority'    => $data['priority'],
            ':due_date'    => $data['due_date'],
            ':status'      => $data['status'],
        ]);
        return (int)db()->lastInsertId();
    }

    public static function updateStatus(int $id, string $status): bool {
        $stmt = db()->prepare('UPDATE tasks SET status = :status WHERE id = :id');
        return $stmt->execute([':status' => $status, ':id' => $id]);
    }
}
