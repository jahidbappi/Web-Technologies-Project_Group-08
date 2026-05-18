<?php


require_once __DIR__ . '/../config/Database.php';

class WorkspaceModel {

    private PDO $db;

    public function __construct() {
        $this->db = Database::connect();
    }

    public function create(string $name, string $description, int $ownerId, string $inviteCode): int {
        $stmt = $this->db->prepare(
            "INSERT INTO workspaces (name, description, owner_id, invite_code)
             VALUES (?, ?, ?, ?)"
        );
        $stmt->execute([$name, $description, $ownerId, $inviteCode]);
        $workspaceId = (int)$this->db->lastInsertId();

        $this->addMember($workspaceId, $ownerId);

        return $workspaceId;
    }

    public function findById(int $id): ?array {
        $stmt = $this->db->prepare("SELECT * FROM workspaces WHERE id = ? LIMIT 1");
        $stmt->execute([$id]);
        return $stmt->fetch() ?: null;
    }

    public function findByInviteCode(string $code): ?array {
        $stmt = $this->db->prepare("SELECT * FROM workspaces WHERE invite_code = ? LIMIT 1");
        $stmt->execute([strtoupper($code)]);
        return $stmt->fetch() ?: null;
    }

    public function inviteCodeExists(string $code): bool {
        $stmt = $this->db->prepare("SELECT id FROM workspaces WHERE invite_code = ?");
        $stmt->execute([$code]);
        return (bool)$stmt->fetch();
    }

    public function getUserWorkspaces(int $userId): array {
        $stmt = $this->db->prepare(
            "SELECT w.*
             FROM workspaces w
             JOIN workspace_members wm ON wm.workspace_id = w.id
             WHERE wm.user_id = ?
             ORDER BY w.created_at ASC"
        );
        $stmt->execute([$userId]);
        return $stmt->fetchAll();
    }

    public function isMember(int $workspaceId, int $userId): bool {
        $stmt = $this->db->prepare(
            "SELECT id FROM workspace_members
             WHERE workspace_id = ? AND user_id = ? LIMIT 1"
        );
        $stmt->execute([$workspaceId, $userId]);
        return (bool)$stmt->fetch();
    }

    public function addMember(int $workspaceId, int $userId): bool {
        if ($this->isMember($workspaceId, $userId)) return true;

        $stmt = $this->db->prepare(
            "INSERT INTO workspace_members (workspace_id, user_id) VALUES (?, ?)"
        );
        return $stmt->execute([$workspaceId, $userId]);
    }

    public function getMembers(int $workspaceId): array {
        $stmt = $this->db->prepare(
            "SELECT wm.id AS member_id, u.id AS user_id, u.name, u.email, wm.joined_at
             FROM workspace_members wm
             JOIN users u ON u.id = wm.user_id
             WHERE wm.workspace_id = ?
             ORDER BY wm.joined_at ASC"
        );
        $stmt->execute([$workspaceId]);
        return $stmt->fetchAll();
    }

    public function removeMember(int $memberId, int $workspaceOwnerId): true|string {
        $stmt = $this->db->prepare(
            "SELECT user_id FROM workspace_members WHERE id = ? LIMIT 1"
        );
        $stmt->execute([$memberId]);
        $row = $stmt->fetch();

        if (!$row)                                         return 'Member not found.';
        if ((int)$row['user_id'] === $workspaceOwnerId)   return 'Cannot remove the workspace owner.';

        $stmt2 = $this->db->prepare("DELETE FROM workspace_members WHERE id = ?");
        $stmt2->execute([$memberId]);
        return true;
    }
}
