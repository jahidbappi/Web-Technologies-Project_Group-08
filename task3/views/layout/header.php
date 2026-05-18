<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= e($title ?? 'Task Board') ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:ital,opsz,wght@0,9..40,400;0,9..40,500;0,9..40,600;0,9..40,700;1,9..40,400&display=swap" rel="stylesheet">
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
                <a class="nav-pill" href="<?= e(route('projects')) ?>">Projects</a>
                <span class="topbar__user">
                    <span class="topbar__avatar"><?= e(initials($_SESSION['name'] ?? '?')) ?></span>
                    <span class="topbar__name"><?= e($_SESSION['name'] ?? '') ?></span>
                </span>
                <a class="btn btn--ghost btn--sm" href="<?= e(route('logout')) ?>">Sign out</a>
            </nav>
        <?php endif; ?>
    </div>
</header>
<main class="page">
