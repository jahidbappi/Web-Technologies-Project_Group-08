<?php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once 'config/helpers.php';
require_once 'models/WorkspaceModel.php';
requireAuth();

$userId         = (int)$_SESSION['user_id'];
$wsModel        = new WorkspaceModel();
$userWorkspaces = $wsModel->getUserWorkspaces($userId);
$hasWorkspace   = !empty($userWorkspaces);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= $hasWorkspace ? 'Add Workspace' : 'Get Started' ?> — TeamFlow</title>
  <link rel="stylesheet" href="assets/css/style.css">
</head>
<body style="background:var(--surface);">

<?php if ($hasWorkspace): ?>
  <nav class="navbar">
    <div class="navbar-brand">Team<span>Flow</span></div>

    <div class="navbar-ws-switcher">
      <span style="color:var(--navy-200);font-size:12px;">Workspace:</span>
      <select class="ws-select"
        onchange="if(this.value) window.location.href='index.php?page=do_switch_ws&id='+this.value">
        <?php foreach ($userWorkspaces as $ws): ?>
          <option value="<?= $ws['id'] ?>"
            <?= ((int)$ws['id'] === (int)($_SESSION['workspace_id'] ?? 0)) ? 'selected' : '' ?>>
            <?= htmlspecialchars($ws['name']) ?>
          </option>
        <?php endforeach; ?>
      </select>
    </div>

    <div class="navbar-actions">
      <a href="index.php?page=create_workspace" class="navbar-action-btn">+ Create Workspace</a>
      <a href="index.php?page=join_workspace"   class="navbar-action-btn">+ Join Workspace</a>
    </div>

    <div class="navbar-spacer"></div>
    <div class="navbar-user">
      <div class="navbar-avatar"><?= htmlspecialchars(getInitials($_SESSION['name'])) ?></div>
      <span><?= htmlspecialchars($_SESSION['name']) ?></span>
      <a href="index.php?page=do_logout" class="navbar-logout">Sign out</a>
    </div>
  </nav>

<?php else: ?>
  <div style="background:var(--navy);height:56px;display:flex;align-items:center;padding:0 28px;gap:12px;">
    <span class="navbar-brand">Team<span>Flow</span></span>
    <span style="flex:1;"></span>
    <span style="color:var(--navy-200);font-size:14px;">
      Hi, <?= htmlspecialchars($_SESSION['name']) ?> 👋
    </span>
    <a href="index.php?page=do_logout" class="navbar-logout">Sign out</a>
  </div>
<?php endif; ?>

<div class="page-content">

  <?php if ($hasWorkspace): ?>
    <a href="index.php?page=dashboard" class="back-link">← Back to Dashboard</a>
  <?php endif; ?>

  <div class="page-header">
    <h1><?= $hasWorkspace ? '➕ Add Another Workspace' : '👋 Welcome to TeamFlow!' ?></h1>
    <p>
      <?= $hasWorkspace
        ? 'Create a brand-new workspace or join another one with an invite code.'
        : "You're not part of any workspace yet. Create a new one or join your team's existing workspace."
      ?>
    </p>
  </div>

  <div class="ws-choice-grid">

    <div class="ws-choice-card">
      <div class="ws-choice-icon">🏢</div>
      <h3>Create a Workspace</h3>
      <p>Start fresh — you'll get an invite code to share with teammates.</p>
      <a href="index.php?page=create_workspace" class="btn btn-primary">Create Workspace</a>
    </div>

    <div class="ws-choice-card">
      <div class="ws-choice-icon">🔗</div>
      <h3>Join a Workspace</h3>
      <p>Enter the 6-character invite code your team gave you.</p>
      <a href="index.php?page=join_workspace" class="btn btn-outline">Join with Code</a>
    </div>

  </div>
</div>
</body>
</html>
