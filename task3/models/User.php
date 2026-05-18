<?php
require_once __DIR__ . '/../config/db.php';

class User {
    private const FALLBACK_USERS = [
        4 => ['id' => 4, 'name' => 'Jahid',  'email' => 'jahid@example.com'],
        5 => ['id' => 5, 'name' => 'Turjo',  'email' => 'turjo@example.com'],
        6 => ['id' => 6, 'name' => 'Rodela', 'email' => 'rodela@example.com'],
        7 => ['id' => 7, 'name' => 'Raisha', 'email' => 'raisha@example.com'],
    ];

    public static function all(): array {
        return array_values(self::FALLBACK_USERS);
    }

    public static function find(int $id): ?array {
        $stmt = db()->prepare('SELECT id, name, email FROM users WHERE id = :id');
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch();
        if ($row) {
            return $row;
        }

        return self::FALLBACK_USERS[$id] ?? null;
    }

    public static function firstWorkspaceId(int $userId): ?int {
        if (isset(self::FALLBACK_USERS[$userId])) {
            return 1;
        }

        $stmt = db()->prepare(
            'SELECT workspace_id FROM workspace_members WHERE user_id = :uid ORDER BY id ASC LIMIT 1'
        );
        $stmt->execute([':uid' => $userId]);
        $row = $stmt->fetch();
        return $row ? (int)$row['workspace_id'] : null;
    }

    public static function isFallbackUser(int $userId): bool {
        return isset(self::FALLBACK_USERS[$userId]);
    }

    public static function fallbackUsers(): array {
        return array_values(self::FALLBACK_USERS);
    }
}
