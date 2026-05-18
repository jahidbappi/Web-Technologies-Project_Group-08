<?php
/** http://127.0.0.1/WebTechProject/task3/index.php */
require_once __DIR__ . '/config/helpers.php';
require_once __DIR__ . '/config/auth.php';

$method = strtoupper($_SERVER['REQUEST_METHOD'] ?? 'GET');
if ($method === 'POST' && !empty($_POST['_method'])) {
    $method = strtoupper($_POST['_method']);
}

$route = isset($_GET['route']) ? trim((string)$_GET['route']) : '';

/* Bare index.php visit → send user to the project list. */
if ($route === '' && $method === 'GET') {
    header('Location: ' . route('projects'));
    exit;
}

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
    echo '<!doctype html><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">'
       . '<title>404</title>'
       . '<style>body{font-family:system-ui,sans-serif;padding:48px;background:#0f172a;color:#e2e8f0}'
       . 'a{color:#38bdf8}</style>'
       . '<h1>404 — Not found</h1>'
       . '<p>No handler for <code>' . e($method . ' route=' . ($route ?: '(empty)')) . '</code>.</p>'
       . '<p><a href="' . e(route('projects')) . '">Back to projects</a></p>';
} catch (Throwable $e) {
    http_response_code(500);
    if (filter_var(getenv('APP_DEBUG'), FILTER_VALIDATE_BOOLEAN)) {
        echo '<pre style="padding:24px;font-family:monospace;color:#fca5a5">' . e($e->getMessage()) . '</pre>';
    } else {
        echo 'Internal server error.';
    }
    error_log((string)$e);
}
