<?php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once 'config/helpers.php';
require_once 'models/WorkspaceModel.php';
requireAuth();

$errors = $_SESSION['errors'] ?? [];
unset($_SESSION['errors']);

$userId     = (int)$_SESSION['user_id'];
$wsModel    = new WorkspaceModel();
$workspaces = $wsModel->getUserWorkspaces($userId);
$hasWS      = !empty($workspaces);
$backLink   = $hasWS ? 'index.php?page=dashboard' : 'index.php?page=choose_workspace';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Join Workspace — TeamFlow</title>
  <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

<?php if ($hasWS):
    $currentWS = $wsModel->findById((int)($_SESSION['workspace_id'] ?? 0));
?>
<nav class="navbar">
  <div class="navbar-brand">Team<span>Flow</span></div>

  <div class="navbar-ws-switcher">
    <span style="color:var(--navy-200);font-size:12px;">Workspace:</span>
    <select class="ws-select" onchange="if(this.value) window.location.href='index.php?page=do_switch_ws&id='+this.value">
      <?php foreach ($workspaces as $ws): ?>
        <option value="<?= $ws['id'] ?>" <?= ($ws['id'] == ($_SESSION['workspace_id'] ?? 0)) ? 'selected' : '' ?>>
          <?= htmlspecialchars($ws['name']) ?>
        </option>
      <?php endforeach; ?>
    </select>
  </div>

  <div style="display:flex;gap:8px;align-items:center;">
    <a href="index.php?page=create_workspace" class="btn btn-outline" style="font-size:13px;padding:7px 14px;color:#fff;border-color:rgba(255,255,255,.3);">+ Create Workspace</a>
    <a href="index.php?page=join_workspace"   class="btn btn-primary" style="font-size:13px;padding:7px 14px;background:rgba(255,255,255,.15);border:1px solid rgba(255,255,255,.3);">+ Join Workspace</a>
    <?php if ($currentWS && (int)$currentWS['owner_id'] === $userId): ?>
      <a href="index.php?page=workspace_settings" class="btn btn-outline" style="font-size:13px;padding:7px 14px;color:#fff;border-color:rgba(255,255,255,.3);">⚙️ Settings</a>
    <?php endif; ?>
  </div>

  <div class="navbar-spacer"></div>
  <div class="navbar-user">
    <div class="navbar-avatar"><?= htmlspecialchars(getInitials($_SESSION['name'])) ?></div>
    <span><?= htmlspecialchars($_SESSION['name']) ?></span>
    <a href="index.php?page=do_logout" class="navbar-logout">Sign out</a>
  </div>
</nav>
<?php else: ?>
<div style="background:var(--navy);height:56px;display:flex;align-items:center;padding:0 28px;">
  <span class="navbar-brand">Team<span>Flow</span></span>
  <span style="flex:1;"></span>
  <a href="index.php?page=do_logout" class="navbar-logout">Sign out</a>
</div>
<?php endif; ?>

<div class="page-content">
  <a href="<?= $backLink ?>" class="back-link">← Back<?= $hasWS ? ' to Dashboard' : '' ?></a>

  <div class="page-header">
    <h1>🔗 Join a Workspace</h1>
    <p>Ask your team for the 6-character invite code, then enter it below.</p>
  </div>

  <div style="max-width:400px;">
    <div class="card">

      <?php if (!empty($errors['invite_code'])): ?>
        <div class="alert alert-error"><?= htmlspecialchars($errors['invite_code']) ?></div>
      <?php endif; ?>
      <?php if (!empty($errors['general'])): ?>
        <div class="alert alert-error"><?= htmlspecialchars($errors['general']) ?></div>
      <?php endif; ?>

      <form method="post" action="index.php?page=do_join_ws">
        <div class="form-group">
          <label for="invite_code">Invite Code</label>
          <input
            type="text" id="invite_code" name="invite_code"
            class="invite-input"
            maxlength="6" placeholder="AB12CD"
            autocomplete="off" required autofocus
          >
          <span style="font-size:11px;color:var(--navy-200);margin-top:4px;display:block;">
            6 characters — letters and numbers
          </span>
        </div>
        <button type="submit" class="btn btn-primary btn-full">Join Workspace</button>
      </form>
    </div>
  </div>
</div>

<script>
document.getElementById('invite_code').addEventListener('input', function () {
  this.value = this.value.toUpperCase().replace(/[^A-Z0-9]/g, '');
});
</script>
</body>
</html>
