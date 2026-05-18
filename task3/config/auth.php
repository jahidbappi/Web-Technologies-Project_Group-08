<?php
require_once __DIR__ . '/helpers.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function require_login(): void {
    if (empty($_SESSION['user_id'])) {
        http_response_code(401);
        echo '<h1>Unauthorized</h1><p>Please sign in via the main application before accessing this board.</p>';
        exit;
    }
}

function require_login_api(): void {
    if (empty($_SESSION['user_id'])) {
        json_response(['ok' => false, 'error' => 'Unauthorized'], 401);
    }
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
