<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/User.php';

class Project {
    public static function listForUser(int $userId, ?int $workspaceId): array {
        $sql = 'SELECT p.*
                FROM projects p
                WHERE p.is_archived = 0';
        $params = [];
        if ($workspaceId !== null) {
            $sql .= ' AND p.workspace_id = :wid';
            $params[':wid'] = $workspaceId;
        }
        $sql .= ' ORDER BY p.created_at DESC';
        $stmt = db()->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public static function find(int $id): ?array {
        $stmt = db()->prepare('SELECT * FROM projects WHERE id = :id');
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    /**
     * Board access: any member of the project's workspace (P8 checklist).
     * project_members is used for the assignee dropdown, not for locking the board.
     */
    public static function userCanAccess(int $projectId, int $userId): bool {
        $project = self::find($projectId);
        if (!$project || $project['workspace_id'] === null) {
            return false;
        }

        $stmt = db()->prepare(
            'SELECT 1 FROM workspace_members
             WHERE workspace_id = :wid AND user_id = :uid
             LIMIT 1'
        );
        $stmt->execute([':wid' => $project['workspace_id'], ':uid' => $userId]);
        if ((bool)$stmt->fetch()) {
            return true;
        }

        return User::isFallbackUser($userId) && $project['workspace_id'] === 1;
    }

    public static function members(int $projectId): array {
        $stmt = db()->prepare(
            'SELECT u.id, u.name, u.email
               FROM project_members pm
               JOIN users u ON u.id = pm.user_id
              WHERE pm.project_id = :pid
              ORDER BY u.name ASC'
        );
        $stmt->execute([':pid' => $projectId]);
        $members = $stmt->fetchAll();

        $project = self::find($projectId);
        if ($project && $project['workspace_id'] === 1) {
            $existingIds = array_column($members, 'id');
            foreach (User::fallbackUsers() as $fallback) {
                if (!in_array($fallback['id'], $existingIds, true)) {
                    $members[] = $fallback;
                }
            }
        }

        if (!$members) {
            if ($project) {
                $stmt = db()->prepare(
                    'SELECT u.id, u.name, u.email
                       FROM workspace_members wm
                       JOIN users u ON u.id = wm.user_id
                      WHERE wm.workspace_id = :wid
                      ORDER BY u.name ASC'
                );
                $stmt->execute([':wid' => $project['workspace_id']]);
                $members = $stmt->fetchAll();
            }
        }

        return $members;
    }
}
