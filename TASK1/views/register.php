<?php

if (session_status() === PHP_SESSION_NONE) session_start();

$errors = $_SESSION['errors'] ?? [];
$old    = $_SESSION['old']    ?? [];
unset($_SESSION['errors'], $_SESSION['old']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Register — TeamFlow</title>
  <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
<div class="auth-page">

  <div class="auth-brand">
    <div class="brand-logo">Team<span>Flow</span></div>
    <div class="brand-tagline">Your team,<br><em>one workspace.</em></div>
    <p class="brand-sub">Create an account and invite your teammates in seconds.</p>
  </div>

  <div class="auth-form-side">
    <div class="auth-card">
      <h2>Create your account</h2>
      <p class="auth-subtitle">Get started — it only takes a minute</p>

      <?php if (!empty($errors['general'])): ?>
        <div class="alert alert-error"><?= htmlspecialchars($errors['general']) ?></div>
      <?php endif; ?>

      <form method="post" action="index.php?page=do_register">

        <div class="form-group">
          <label for="name">Full name</label>
          <input
            type="text" id="name" name="name"
            value="<?= htmlspecialchars($old['name'] ?? '') ?>"
            placeholder="Rahim Uddin" required autofocus
          >
          <?php if (!empty($errors['name'])): ?>
            <span class="field-error"><?= htmlspecialchars($errors['name']) ?></span>
          <?php endif; ?>
        </div>

        <div class="form-group">
          <label for="email">Email address</label>
          <input
            type="email" id="email" name="email"
            value="<?= htmlspecialchars($old['email'] ?? '') ?>"
            placeholder="you@example.com" required
          >
          <?php if (!empty($errors['email'])): ?>
            <span class="field-error"><?= htmlspecialchars($errors['email']) ?></span>
          <?php endif; ?>
        </div>

        <div class="form-group">
          <label for="password">Password</label>
          <input type="password" id="password" name="password" placeholder="At least 8 characters" required>
          <span style="font-size:11px;color:var(--navy-200);margin-top:3px;display:block;">Minimum 8 characters</span>
          <?php if (!empty($errors['password'])): ?>
            <span class="field-error"><?= htmlspecialchars($errors['password']) ?></span>
          <?php endif; ?>
        </div>

        <button type="submit" class="btn btn-primary btn-full" style="margin-top:8px;">
          Create Account
        </button>
      </form>

      <p style="text-align:center;margin-top:20px;font-size:13px;color:var(--navy-300);">
        Already have an account? <a href="index.php?page=login">Sign in</a>
      </p>
    </div>
  </div>

</div>
</body>
</html>
