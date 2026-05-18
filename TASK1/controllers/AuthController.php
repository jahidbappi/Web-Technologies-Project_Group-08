<?php
require_once __DIR__ . '/../config/helpers.php';
require_once __DIR__ . '/../models/UserModel.php';
require_once __DIR__ . '/../models/WorkspaceModel.php';

//reg

function handleRegister(): void {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        header('Location: index.php?page=register'); exit();
    }

    $name     = trim($_POST['name']     ?? '');
    $email    = trim($_POST['email']    ?? '');
    $password = $_POST['password']      ?? '';

    $errors = [];
    if (!$name)
        $errors['name']     = 'Full name is required.';
    if (!$email || !filter_var($email, FILTER_VALIDATE_EMAIL))
        $errors['email']    = 'A valid email is required.';
    if (strlen($password) < 6)
    $errors['password'] = 'Password must be at least 6 characters.';
elseif (!preg_match('/[!@#$%^&*()_+\-=\[\]{};\':"\\|,.<>\/?]/', $password))
    $errors['password'] = 'Password must contain at least one special character (e.g. @, #, !, $).';

    if (!$errors) {
        if ((new UserModel())->emailExists($email)) {
            $errors['email'] = 'This email is already registered.';
        }
    }

    if ($errors) {
        $_SESSION['errors'] = $errors;
        $_SESSION['old']    = compact('name', 'email');
        header('Location: index.php?page=register'); exit();
    }

    $userId = (new UserModel())->register($name, $email, $password);

    if (!$userId) {
        $_SESSION['errors'] = ['general' => 'Registration failed. Please try again.'];
        header('Location: index.php?page=register'); exit();
    }

    $_SESSION['user_id']      = $userId;
    $_SESSION['name']         = $name;
    $_SESSION['workspace_id'] = null;

    header('Location: index.php?page=choose_workspace'); exit();
}

//HELLO

function handleLogin(): void {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        header('Location: index.php?page=login'); exit();
    }

    $email    = trim($_POST['email']    ?? '');
    $password = $_POST['password']      ?? '';

    $errors = [];
    if (!$email)    $errors['email']    = 'Email is required.';
    if (!$password) $errors['password'] = 'Password is required.';

    if ($errors) {
        $_SESSION['errors'] = $errors;
        $_SESSION['old']    = ['email' => $email];
        header('Location: index.php?page=login'); exit();
    }

    $user = (new UserModel())->findByEmailAndPassword($email, $password);

    if (!$user) {
        $_SESSION['errors'] = ['general' => 'Invalid email or password.'];
        $_SESSION['old']    = ['email' => $email];
        header('Location: index.php?page=login'); exit();
    }

    $workspaces = (new WorkspaceModel())->getUserWorkspaces((int)$user['id']);

    $_SESSION['user_id'] = (int)$user['id'];
    $_SESSION['name'] = $user['name'];
    $_SESSION['workspace_id'] = !empty($workspaces) ? (int)$workspaces[0]['id'] : null;

    // Redirect: if user has a ws>>> dashboard, else..choose ws
    $next = empty($workspaces) ? 'choose_workspace' : 'dashboard';
    header("Location: index.php?page=$next"); exit();
}

//tata bye bye

function handleLogout(): void {
    session_destroy();
    header('Location: index.php?page=login'); exit();
}
