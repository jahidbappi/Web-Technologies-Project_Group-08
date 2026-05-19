<?php

if (session_status() === PHP_SESSION_NONE) session_start();

$errors  = $_SESSION['errors'] ?? [];
$old     = $_SESSION['old']    ?? [];
unset($_SESSION['errors'], $_SESSION['old']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Login — TeamFlow</title>
  <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
<div class="auth-page">

  <div class="auth-brand">
    <div class="brand-logo">Team<span>Flow</span></div>
    <div class="brand-tagline">Build things<br>together, <em>faster.</em></div>
    <p class="brand-sub">Manage projects, track tasks, and collaborate with your team — all in one place.</p>
  </div>

  <div class="auth-form-side">
    <div class="auth-card">
      <h2>Welcome back</h2>
      <p class="auth-subtitle">Sign in to your TeamFlow account</p>

      <?php if (!empty($errors['general'])): ?>
        <div class="alert alert-error"><?= htmlspecialchars($errors['general']) ?></div>
      <?php endif; ?>

      <form method="post" action="index.php?page=do_login">
        <?php if (!empty($_GET['return'])): ?>
          <input type="hidden" name="return" value="<?= htmlspecialchars($_GET['return']) ?>">
        <?php endif; ?>

        <div class="form-group">
          <label for="email">Email address</label>
          <input
            type="email" id="email" name="email"
            value="<?= htmlspecialchars($old['email'] ?? '') ?>"
            placeholder="you@example.com" required autofocus
          >
          <?php if (!empty($errors['email'])): ?>
            <span class="field-error"><?= htmlspecialchars($errors['email']) ?></span>
          <?php endif; ?>
        </div>

        <div class="form-group">
          <label for="password">Password</label>
          <input type="password" id="password" name="password" placeholder="••••••••" required>
          <?php if (!empty($errors['password'])): ?>
            <span class="field-error"><?= htmlspecialchars($errors['password']) ?></span>
          <?php endif; ?>
        </div>

        <button type="submit" class="btn btn-primary btn-full" style="margin-top:8px;">
          Sign In
        </button>
      </form>

      <p style="text-align:center;margin-top:20px;font-size:13px;color:var(--navy-300);">
        Don't have an account? <a href="index.php?page=register">Register here</a>
      </p>
    </div>
  </div>

</div>
</body>
</html>
