<?php

require_once __DIR__ . '/../config/helpers.php';
require_once __DIR__ . '/../models/WorkspaceModel.php';

header('Content-Type: application/json');

if (empty($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['ok' => false, 'error' => 'Unauthorized']);
    exit();
}

$method   = $_SERVER['REQUEST_METHOD'];
$memberId = (int)($_GET['id'] ?? 0);
$userId   = (int)$_SESSION['user_id'];

if ($method !== 'DELETE') {
    http_response_code(405);
    echo json_encode(['ok' => false, 'error' => 'Method not allowed']);
    exit();
}

if (!$memberId) {
    http_response_code(400);
    echo json_encode(['ok' => false, 'error' => 'Missing member id']);
    exit();
}

$wsModel     = new WorkspaceModel();
$workspaceId = (int)($_SESSION['workspace_id'] ?? 0);
$workspace   = $wsModel->findById($workspaceId);

if (!$workspace || (int)$workspace['owner_id'] !== $userId) {
    http_response_code(403);
    echo json_encode(['ok' => false, 'error' => 'Only the workspace owner can remove members.']);
    exit();
}

$result = $wsModel->removeMember($memberId, (int)$workspace['owner_id']);

if ($result === true) {
    echo json_encode(['ok' => true]);
} else {
    http_response_code(400);
    echo json_encode(['ok' => false, 'error' => $result]);
}
