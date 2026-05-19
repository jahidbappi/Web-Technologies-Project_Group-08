<?php

function log_activity(int $project_id, int $user_id, string $action_text): void {
    require_once __DIR__ . '/db.php';
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
    if ($name === '') {
        return '?';
    }
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

function route(string $name, array $params = []): string {
    if (!function_exists('task3_route')) {
        require_once dirname(__DIR__, 2) . '/config/app.php';
    }
    return task3_route($name, $params);
}

function asset(string $path): string {
    $path = ltrim(str_replace('\\', '/', $path), '/');
    $filePath = __DIR__ . '/../' . $path;
    if (file_exists($filePath)) {
        return $path . '?v=' . filemtime($filePath);
    }
    return $path;
}
