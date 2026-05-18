<?php
require_once __DIR__ . '/../config/helpers.php';
require_once __DIR__ . '/../models/WorkspaceModel.php';

//create

function handleCreate(): void {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        header('Location: index.php?page=create_workspace'); exit();
    }

    $name        = trim($_POST['name']        ?? '');
    $description = trim($_POST['description'] ?? '');
    $userId      = (int)$_SESSION['user_id'];

    $errors = [];
    if (!$name) $errors['name'] = 'Workspace name is required.';

    if ($errors) {
        $_SESSION['errors'] = $errors;
        $_SESSION['old']    = compact('name', 'description');
        header('Location: index.php?page=create_workspace'); exit();
    }

    $wsModel = new WorkspaceModel();
    do { $code = generateInviteCode(); } while ($wsModel->inviteCodeExists($code));

    $workspaceId = $wsModel->create($name, $description, $userId, $code);

    $_SESSION['workspace_id'] = $workspaceId;
    header('Location: index.php?page=dashboard'); exit();
}

//join ws

function handleJoin(): void {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        header('Location: index.php?page=join_workspace'); exit();
    }

    $code   = strtoupper(trim($_POST['invite_code'] ?? ''));
    $userId = (int)$_SESSION['user_id'];

    $errors = [];
    if (!$code) $errors['invite_code'] = 'Please enter the invite code.';

    if (!$errors) {
        $wsModel   = new WorkspaceModel();
        $workspace = $wsModel->findByInviteCode($code);

        if (!$workspace) {
            $errors['invite_code'] = 'No workspace found with that code.';
        } else {
            if (!$wsModel->isMember((int)$workspace['id'], $userId)) {
                $wsModel->addMember((int)$workspace['id'], $userId);
            }
            $_SESSION['workspace_id'] = (int)$workspace['id'];
            header('Location: index.php?page=dashboard'); exit();
        }
    }

    $_SESSION['errors'] = $errors;
    header('Location: index.php?page=join_workspace'); exit();
}

//switch ws

function handleSwitch(): void {
    $workspaceId = (int)($_GET['id'] ?? 0);
    $userId      = (int)$_SESSION['user_id'];
    $wsModel     = new WorkspaceModel();

    if ($workspaceId && $wsModel->isMember($workspaceId, $userId)) {
        $_SESSION['workspace_id'] = $workspaceId;
    }

    header('Location: index.php?page=dashboard'); exit();
}
