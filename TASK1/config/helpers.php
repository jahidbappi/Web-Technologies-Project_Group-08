<?php

require_once __DIR__ . '/Database.php';

function requireAuth(): void {
    if (!function_exists('app_session_start')) {
        require_once dirname(__DIR__, 2) . '/config/app.php';
    }
    app_session_start();
    if (empty($_SESSION['user_id'])) {
        header('Location: index.php?page=login');
        exit();
    }
}

function isWorkspaceOwner(): bool {
    if (empty($_SESSION['user_id']) || empty($_SESSION['workspace_id'])) return false;
    $db   = Database::connect();
    $stmt = $db->prepare("SELECT owner_id FROM workspaces WHERE id = ?");
    $stmt->execute([$_SESSION['workspace_id']]);
    $row  = $stmt->fetch();
    return $row && (int)$row['owner_id'] === (int)$_SESSION['user_id'];
}

// WORKSPACE HELPERS


//  unique 6-char invite code.

function generateInviteCode(int $length = 6): string {
    $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
    $code  = '';
    for ($i = 0; $i < $length; $i++) {
        $code .= $chars[random_int(0, strlen($chars) - 1)];
    }
    return $code;
}


function getInitials(string $name): string {
    $parts    = explode(' ', trim($name));
    $initials = '';
    foreach ($parts as $p) {
        if ($p !== '') $initials .= strtoupper($p[0]);
        if (strlen($initials) >= 2) break;
    }
    return $initials ?: '?';
}

function timeAgo(string $datetime): string {
    $diff = time() - strtotime($datetime);
    if ($diff < 60)   return 'just now';
    if ($diff < 3600) return floor($diff / 60) . ' min ago';
    if ($diff < 86400) return floor($diff / 3600) . ' hours ago';
    return floor($diff / 86400) . ' days ago';
}

// ACTIVITY LOG (use T3, T4)

function log_activity(int $project_id, int $user_id, string $action_text): void {
    $db   = Database::connect();
    $stmt = $db->prepare(
        "INSERT INTO activity_logs (project_id, user_id, action_text, created_at)
         VALUES (?, ?, ?, NOW())"
    );
    $stmt->execute([$project_id, $user_id, $action_text]);
}

// OUTPUT HELPERS

function e(string $str): string {
    return htmlspecialchars($str, ENT_QUOTES, 'UTF-8');
}

/** send JSON response and exit (for API endpoints) */
function jsonResponse(array $data, int $code = 200): void {
    http_response_code($code);
    header('Content-Type: application/json');
    echo json_encode($data);
    exit();
}
