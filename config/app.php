<?php

/**
 * Shared app config for TASK1 (auth/workspace), TASK2 (projects/comments), task3 (board).
 */

function app_base_path(): string {
    static $base = null;
    if ($base !== null) {
        return $base;
    }

    $script = str_replace('\\', '/', $_SERVER['SCRIPT_NAME'] ?? '');
    if (preg_match('#^(.+?)/(TASK1|TASK2|task3)(/|$)#', $script, $m)) {
        $base = $m[1];
    } else {
        $base = rtrim(dirname($script), '/');
    }

    if ($base === '' || $base === '.') {
        $base = '';
    }

    return $base;
}

function app_url(string $path = ''): string {
    $base = app_base_path();
    $path = ltrim(str_replace('\\', '/', $path), '/');
    if ($path === '') {
        return $base === '' ? '/' : $base . '/';
    }
    return ($base === '' ? '' : $base) . '/' . $path;
}

function app_session_start(): void {
    if (session_status() !== PHP_SESSION_NONE) {
        return;
    }

    $path = app_base_path();
    session_set_cookie_params([
        'lifetime' => 0,
        'path'     => ($path === '' ? '/' : $path . '/'),
        'secure'   => false,
        'httponly' => true,
        'samesite' => 'Lax',
    ]);
    session_start();
}

function require_app_auth(): void {
    app_session_start();
    if (empty($_SESSION['user_id'])) {
        header('Location: ' . app_url('TASK1/index.php?page=login'));
        exit;
    }
}

function current_workspace_id(): ?int {
    return isset($_SESSION['workspace_id']) ? (int)$_SESSION['workspace_id'] : null;
}

function task2_asset(string $rel): string {
    return app_url('TASK2/' . ltrim($rel, '/'));
}

function task3_route(string $name, array $params = []): string {
    $params['route'] = $name;
    return app_url('task3/index.php?' . http_build_query($params));
}
