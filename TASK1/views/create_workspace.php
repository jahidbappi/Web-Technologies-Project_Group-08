<?php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once 'config/helpers.php';
require_once 'models/WorkspaceModel.php';
requireAuth();

$errors = $_SESSION['errors'] ?? [];
$old    = $_SESSION['old']    ?? [];
unset($_SESSION['errors'], $_SESSION['old']);

//back korle kothay jabe...dsb jodi ws thake
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
  <title>Create Workspace — TeamFlow</title>
  <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

<?php if ($hasWS):
    $currentWS    = $wsModel->findById((int)($_SESSION['workspace_id'] ?? 0));
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
    <a href="index.php?page=create_workspace" class="btn btn-primary" style="font-size:13px;padding:7px 14px;background:rgba(255,255,255,.15);border:1px solid rgba(255,255,255,.3);">+ Create Workspace</a>
    <a href="index.php?page=join_workspace"   class="btn btn-outline"  style="font-size:13px;padding:7px 14px;color:#fff;border-color:rgba(255,255,255,.3);">+ Join Workspace</a>
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
    <h1>🏢 Create a New Workspace</h1>
    <p>Give your workspace a name — you'll get a shareable invite code instantly.</p>
  </div>

  <div style="max-width:480px;">
    <div class="card">

      <?php if (!empty($errors['general'])): ?>
        <div class="alert alert-error"><?= htmlspecialchars($errors['general']) ?></div>
      <?php endif; ?>

      <form method="post" action="index.php?page=do_create_ws">

        <div class="form-group">
          <label for="name">Workspace Name <span style="color:var(--red);">*</span></label>
          <input
            type="text" id="name" name="name"
            value="<?= htmlspecialchars($old['name'] ?? '') ?>"
            placeholder="e.g. CSE Lab Group B" required autofocus
          >
          <?php if (!empty($errors['name'])): ?>
            <span class="field-error"><?= htmlspecialchars($errors['name']) ?></span>
          <?php endif; ?>
        </div>

        <div class="form-group">
          <label for="description">
            Description <span style="color:var(--navy-200);font-weight:400;">(optional)</span>
          </label>
          <textarea id="description" name="description"
            placeholder="What is this workspace for?"
          ><?= htmlspecialchars($old['description'] ?? '') ?></textarea>
        </div>

        <button type="submit" class="btn btn-primary btn-full">✓ Create Workspace</button>
      </form>
    </div>
  </div>
</div>
</body>
</html>
