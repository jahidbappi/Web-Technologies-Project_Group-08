<?php
if (!function_exists('app_url')) {
    require_once dirname(__DIR__, 3) . '/config/app.php';
}
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= e($title ?? 'Task Board') ?></title>
    <link rel="stylesheet" href="<?= e(asset('public/css/style.css')) ?>">
</head>
<body class="app-body">
<div class="mesh-bg" aria-hidden="true"></div>
<header class="topbar">
    <div class="topbar__inner">
        <a class="brand" href="<?= e(route('projects')) ?>">
            <span class="brand__mark" aria-hidden="true"></span>
            <span class="brand__text">Kanban<span class="brand__muted"> · Task 3</span></span>
        </a>
        <?php if (!empty($_SESSION['user_id'])): ?>
            <nav class="topbar__nav">
                <a class="nav-pill" href="<?= e(app_url('TASK1/index.php?page=dashboard')) ?>">Dashboard</a>
                <a class="nav-pill" href="<?= e(route('projects')) ?>">Projects</a>
            </nav>
        <?php endif; ?>
    </div>
</header>
<main class="page">
