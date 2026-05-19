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
        $all = self::forProjectGrouped($projectId);
        return $all[$status] ?? [];
    }

    /** One query for the whole board (faster than 3 separate status queries). */
    public static function forProjectGrouped(int $projectId): array {
        $columns = array_fill_keys(self::STATUSES, []);

        $stmt = db()->prepare(
            'SELECT t.*, u.name AS assignee_name
               FROM tasks t
          LEFT JOIN users u ON u.id = t.assigned_to
              WHERE t.project_id = :pid
           ORDER BY t.created_at ASC, t.id ASC'
        );
        $stmt->execute([':pid' => $projectId]);

        while ($row = $stmt->fetch()) {
            $status = $row['status'];
            if (!isset($columns[$status])) {
                continue;
            }
            $columns[$status][] = self::normalizeAssignee($row);
        }

        return $columns;
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
        $stmt->bindValue(':project_id', $data['project_id'], PDO::PARAM_INT);
        $stmt->bindValue(':title', $data['title'], PDO::PARAM_STR);
        $stmt->bindValue(':description', $data['description'], $data['description'] === null ? PDO::PARAM_NULL : PDO::PARAM_STR);
        $stmt->bindValue(':assigned_to', $data['assigned_to'], $data['assigned_to'] === null ? PDO::PARAM_NULL : PDO::PARAM_INT);
        $stmt->bindValue(':priority', $data['priority'], PDO::PARAM_STR);
        $stmt->bindValue(':due_date', $data['due_date'], PDO::PARAM_STR);
        $stmt->bindValue(':status', $data['status'], PDO::PARAM_STR);
        $stmt->execute();
        return (int)db()->lastInsertId();
    }

    public static function updateStatus(int $id, string $status): bool {
        $stmt = db()->prepare('UPDATE tasks SET status = :status WHERE id = :id');
        return $stmt->execute([':status' => $status, ':id' => $id]);
    }
}
