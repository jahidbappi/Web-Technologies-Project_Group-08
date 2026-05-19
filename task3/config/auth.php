<?php
require_once __DIR__ . '/helpers.php';
require_once dirname(__DIR__, 2) . '/config/app.php';
require_once __DIR__ . '/../models/User.php';

function require_login(): void {
    app_session_start();
    if (empty($_SESSION['user_id'])) {
        header('Location: ' . app_url('TASK1/index.php?page=login'));
        exit;
    }
}

function require_login_api(): void {
    app_session_start();
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
    header('Location: ' . app_url('TASK1/index.php?page=login'));
    exit;
}
