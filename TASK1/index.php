<?php

require_once __DIR__ . '/../config/app.php';
app_session_start();

$page = $_GET['page'] ?? 'login';

$publicPages = ['login', 'register', 'do_login', 'do_register'];

if (!in_array($page, $publicPages) && empty($_SESSION['user_id'])) {
    header('Location: index.php?page=login');
    exit();
}

if (in_array($page, ['login', 'register']) && !empty($_SESSION['user_id'])) {
    header('Location: index.php?page=dashboard');
    exit();
}

switch ($page) {

    case 'login':              require 'views/login.php';              break;
    case 'register':           require 'views/register.php';           break;
    case 'dashboard':          require 'views/dashboard.php';          break;
    case 'choose_workspace':   require 'views/choose_workspace.php';   break;
    case 'create_workspace':   require 'views/create_workspace.php';   break;
    case 'join_workspace':     require 'views/join_workspace.php';     break;
    case 'workspace_settings': require 'views/workspace_settings.php'; break;

    case 'do_register':
        require 'controllers/AuthController.php';
        $_GET['action'] = 'register';
        handleRegister();
        break;

    case 'do_login':
        require 'controllers/AuthController.php';
        $_GET['action'] = 'login';
        handleLogin();
        break;

    case 'do_logout':
        require 'controllers/AuthController.php';
        handleLogout();
        break;

    case 'do_create_ws':
        require 'controllers/WorkspaceController.php';
        $_GET['action'] = 'create';
        handleCreate();
        break;

    case 'do_join_ws':
        require 'controllers/WorkspaceController.php';
        $_GET['action'] = 'join';
        handleJoin();
        break;

    case 'do_switch_ws':
        require 'controllers/WorkspaceController.php';
        $_GET['action'] = 'switch';
        handleSwitch();
        break;

    // ajax delete ws manush
    case 'api_delete_member':
        require 'controllers/ApiController.php';
        break;

    default:
        http_response_code(404);
        echo '<p style="font-family:sans-serif;padding:40px;">404 — Page not found. <a href="index.php">Go home</a></p>';
}
