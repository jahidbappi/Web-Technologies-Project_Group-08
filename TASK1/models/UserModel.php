<?php

require_once __DIR__ . '/../config/Database.php';

class UserModel {

    private PDO $db;

    public function __construct() {
        $this->db = Database::connect();
    }

    public function register(string $name, string $email, string $password): int|false {
        $stmt = $this->db->prepare(
            "INSERT INTO users (name, email, password_hash) VALUES (?, ?, ?)"
        );
        $ok = $stmt->execute([$name, $email, $password]);
        return $ok ? (int)$this->db->lastInsertId() : false;
    }

    public function findByEmailAndPassword(string $email, string $password): ?array {
        $stmt = $this->db->prepare(
            "SELECT * FROM users WHERE email = ? AND password_hash = ? LIMIT 1"
        );
        $stmt->execute([$email, $password]);
        return $stmt->fetch() ?: null;
    }

    public function findByEmail(string $email): ?array {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE email = ? LIMIT 1");
        $stmt->execute([$email]);
        return $stmt->fetch() ?: null;
    }

    public function findById(int $id): ?array {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE id = ? LIMIT 1");
        $stmt->execute([$id]);
        return $stmt->fetch() ?: null;
    }

    public function emailExists(string $email): bool {
        $stmt = $this->db->prepare("SELECT id FROM users WHERE email = ? LIMIT 1");
        $stmt->execute([$email]);
        return (bool)$stmt->fetch();
    }
}
