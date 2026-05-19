<?php

require_once dirname(__DIR__) . '/config/app.php';
app_session_start();

$method = strtoupper($_SERVER['REQUEST_METHOD'] ?? 'GET');
if ($method === 'POST' && !empty($_POST['_method'])) {
    $method = strtoupper($_POST['_method']);
}

$route = isset($_GET['route']) ? trim((string)$_GET['route']) : '';
if ($route === '') {
    $route = 'projects';
}

$needsAuth = ($route !== 'logout');

if ($needsAuth && empty($_SESSION['user_id'])) {
    require_once __DIR__ . '/config/helpers.php';
    $return = $_SERVER['REQUEST_URI'] ?? route('projects');
    header('Location: ' . app_url('TASK1/index.php?page=login&return=' . rawurlencode($return)));
    exit;
}

require_once __DIR__ . '/config/helpers.php';
require_once __DIR__ . '/config/auth.php';

function dispatch(string $controllerClass, string $action, array $params = []): void {
    require_once __DIR__ . '/controllers/' . $controllerClass . '.php';
    $instance = new $controllerClass();
    call_user_func_array([$instance, $action], $params);
}

try {
    switch ($route) {
        case 'projects':
            if ($method === 'GET') {
                dispatch('ProjectController', 'index');
                exit;
            }
            break;

        case 'board':
            if ($method === 'GET') {
                $pid = (int)($_GET['project_id'] ?? 0);
                if ($pid < 1) {
                    break;
                }
                dispatch('TaskController', 'board', [$pid]);
                exit;
            }
            break;

        case 'task_create':
            if ($method === 'POST') {
                dispatch('TaskController', 'create');
                exit;
            }
            break;

        case 'logout':
            logout();
            exit;

        case 'api_task_status':
            if ($method === 'PUT') {
                $tid = (int)($_GET['task_id'] ?? 0);
                if ($tid < 1) {
                    json_response(['ok' => false, 'error' => 'Missing task_id'], 422);
                }
                dispatch('TaskApiController', 'updateStatus', [$tid]);
                exit;
            }
            break;

        default:
            break;
    }

    http_response_code(404);
    header('Content-Type: text/html; charset=utf-8');
    echo '<!doctype html><meta charset="utf-8"><title>404</title>';
    echo '<p>No handler for <code>' . e($method . ' route=' . $route) . '</code>.</p>';
    echo '<p><a href="' . e(route('projects')) . '">Back to projects</a></p>';
} catch (Throwable $e) {
    http_response_code(500);
    error_log((string)$e);
    echo 'Internal server error.';
}
