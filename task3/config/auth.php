<?php
require_once __DIR__ . '/helpers.php';
require_once __DIR__ . '/../models/User.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function require_login(): void {
    if (empty($_SESSION['user_id'])) {
        authenticate(1);
    }
}

function require_login_api(): void {
    if (empty($_SESSION['user_id'])) {
        authenticate(1);
    }
}

function authenticate(int $userId): bool {
    $user = User::find($userId);
    if (!$user) {
        return false;
    }

    $_SESSION['user_id'] = $userId;
    $_SESSION['name'] = $user['name'] ?? '';
    $_SESSION['email'] = $user['email'] ?? '';
    $_SESSION['workspace_id'] = User::firstWorkspaceId($userId);

    return true;
}

function current_user_id(): ?int {
    return isset($_SESSION['user_id']) ? (int)$_SESSION['user_id'] : null;
}

function current_workspace_id(): ?int {
    return isset($_SESSION['workspace_id']) ? (int)$_SESSION['workspace_id'] : null;
}

function logout(): void {
    $_SESSION = [];
    if (ini_get('session.use_cookies')) {
        $p = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000, $p['path'], $p['domain'], $p['secure'], $p['httponly']);
    }
    session_destroy();
    header('Location: ' . route('projects'));
    exit;
}
