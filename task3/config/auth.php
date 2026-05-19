<?php

require_once dirname(__DIR__, 2) . '/config/app.php';

function require_login(): void {
    if (empty($_SESSION['user_id'])) {
        $return = $_SERVER['REQUEST_URI'] ?? task3_route('projects');
        header('Location: ' . app_url('TASK1/index.php?page=login&return=' . rawurlencode($return)));
        exit;
    }
    if (empty($_SESSION['workspace_id'])) {
        task3_ensure_workspace();
    }
}

function require_login_api(): void {
    if (empty($_SESSION['user_id'])) {
        require_once __DIR__ . '/helpers.php';
        json_response(['ok' => false, 'error' => 'Unauthorized'], 401);
    }
    if (empty($_SESSION['workspace_id'])) {
        task3_ensure_workspace();
    }
}

/** Use workspace from Task 1 session; avoid extra redirects when only user_id is set. */
function task3_ensure_workspace(): void {
    if (!empty($_SESSION['workspace_id'])) {
        return;
    }
    require_once __DIR__ . '/db.php';
    $uid = (int)$_SESSION['user_id'];
    $stmt = db()->prepare(
        'SELECT workspace_id FROM workspace_members WHERE user_id = :uid ORDER BY id ASC LIMIT 1'
    );
    $stmt->execute([':uid' => $uid]);
    $row = $stmt->fetch();
    if ($row) {
        $_SESSION['workspace_id'] = (int)$row['workspace_id'];
    }
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
