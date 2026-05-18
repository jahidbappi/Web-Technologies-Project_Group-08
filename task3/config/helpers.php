<?php
require_once __DIR__ . '/db.php';

function log_activity(int $project_id, int $user_id, string $action_text): void {
    $stmt = db()->prepare(
        'INSERT INTO activity_logs (project_id, user_id, action_text, created_at)
         VALUES (:project_id, :user_id, :action_text, NOW())'
    );
    $stmt->execute([
        ':project_id'  => $project_id,
        ':user_id'     => $user_id,
        ':action_text' => $action_text,
    ]);
}

function e($v): string {
    return htmlspecialchars((string)($v ?? ''), ENT_QUOTES, 'UTF-8');
}

function initials(?string $name): string {
    $name = trim((string)$name);
    if ($name === '') return '?';
    $parts = preg_split('/\s+/', $name);
    $first = mb_substr($parts[0], 0, 1);
    $last  = count($parts) > 1 ? mb_substr(end($parts), 0, 1) : '';
    return mb_strtoupper($first . $last);
}

function status_label(string $status): string {
    return [
        'todo'        => 'To Do',
        'in-progress' => 'In Progress',
        'done'        => 'Done',
    ][$status] ?? $status;
}

function json_response(array $data, int $code = 200): void {
    http_response_code($code);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($data);
    exit;
}

/**
 * Front-controller URL — always hits index.php so the app works when opened as
 * http://localhost/WebTechProject/task3/index.php
 * without Apache rewrite rules.
 */
function route(string $name, array $params = []): string {
    $params['route'] = $name;
    return 'index.php?' . http_build_query($params);
}

/** Static asset path relative to index.php (CSS / JS). */
function asset(string $path): string {
    return ltrim(str_replace('\\', '/', $path), '/');
}
