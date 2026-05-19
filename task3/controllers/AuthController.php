<?php
require_once __DIR__ . '/../config/auth.php';
require_once __DIR__ . '/../models/User.php';

class AuthController {
    public function showLogin(): void {
        if (!empty($_SESSION['user_id'])) {
            header('Location: ' . route('projects'));
            exit;
        }

        $users = User::all();
        $title = 'Sign in';
        require __DIR__ . '/../views/auth/login.php';
    }

    public function login(): void {
        $userId = isset($_POST['user_id']) ? (int)$_POST['user_id'] : 0;
        if ($userId < 1 || !authenticate($userId)) {
            $_SESSION['flash_error'] = 'Please select a valid user.';
            header('Location: ' . route('login'));
            exit;
        }

        header('Location: ' . route('projects'));
        exit;
    }
}
