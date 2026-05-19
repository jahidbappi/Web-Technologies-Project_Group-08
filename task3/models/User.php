<?php
require_once __DIR__ . '/../config/db.php';

class User {
    public static function all(): array {
        $stmt = db()->query('SELECT id, name, email FROM users ORDER BY id ASC');
        return $stmt->fetchAll();
    }

    public static function find(int $id): ?array {
        $stmt = db()->prepare('SELECT id, name, email FROM users WHERE id = :id');
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public static function firstWorkspaceId(int $userId): ?int {
        $stmt = db()->prepare(
            'SELECT workspace_id FROM workspace_members WHERE user_id = :uid ORDER BY id ASC LIMIT 1'
        );
        $stmt->execute([':uid' => $userId]);
        $row = $stmt->fetch();
        return $row ? (int)$row['workspace_id'] : null;
    }
}
