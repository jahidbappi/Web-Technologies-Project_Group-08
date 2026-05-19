<?php
require_once dirname(__DIR__, 2) . '/config/app.php';
app_session_start();
require_once 'config/helpers.php';
require_once 'models/WorkspaceModel.php';
requireAuth();

$userId  = (int)$_SESSION['user_id'];
$wsModel = new WorkspaceModel();

if (empty($_SESSION['workspace_id'])) {
    $workspaces = $wsModel->getUserWorkspaces($userId);
    if (empty($workspaces)) {
        header('Location: index.php?page=choose_workspace'); exit();
    }
    $_SESSION['workspace_id'] = (int)$workspaces[0]['id'];
}

$workspace = $wsModel->findById((int)$_SESSION['workspace_id']);

if (!$workspace || !$wsModel->isMember((int)$workspace['id'], $userId)) {
    $_SESSION['workspace_id'] = null;
    header('Location: index.php?page=choose_workspace'); exit();
}

$allWorkspaces = $wsModel->getUserWorkspaces($userId);
$isOwner       = ((int)$workspace['owner_id'] === $userId);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= htmlspecialchars($workspace['name']) ?> — TeamFlow</title>
  <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

<nav class="navbar">

  <div class="navbar-brand">Team<span>Flow</span></div>

  <div class="navbar-ws-switcher">
    <span style="color:var(--navy-200);font-size:12px;">Workspace:</span>
    <select
      class="ws-select"
      id="workspaceSwitcher"
      onchange="switchWorkspace(this.value)"
    >
      <?php foreach ($allWorkspaces as $ws): ?>
        <option
          value="<?= $ws['id'] ?>"
          <?= ((int)$ws['id'] === (int)$workspace['id']) ? 'selected' : '' ?>
        >
          <?= htmlspecialchars($ws['name']) ?>
        </option>
      <?php endforeach; ?>
    </select>
  </div>

  <div class="navbar-actions">
    <a href="index.php?page=create_workspace" class="navbar-action-btn">
      + Create Workspace
    </a>
    <a href="index.php?page=join_workspace" class="navbar-action-btn">
      + Join Workspace
    </a>
    <a href="<?= htmlspecialchars(app_url('task3/index.php?route=projects')) ?>" class="navbar-action-btn">
      Task Board
    </a>
    <?php if ($isOwner): ?>
      <a href="index.php?page=workspace_settings" class="navbar-action-btn navbar-action-btn--ghost">
        ⚙️ Settings
      </a>
    <?php endif; ?>
  </div>

  <div class="navbar-spacer"></div>

  <div class="navbar-user">
    <div class="navbar-avatar"><?= htmlspecialchars(getInitials($_SESSION['name'])) ?></div>
    <span><?= htmlspecialchars($_SESSION['name']) ?></span>
    <a href="index.php?page=do_logout" class="navbar-logout">Sign out</a>
  </div>
</nav>

<div class="page-content">

  <div class="dashboard-workspace-banner">
    <div>
      <div class="workspace-banner-name">
        <?= htmlspecialchars($workspace['name']) ?>
      </div>
      <?php if ($workspace['description']): ?>
        <div class="workspace-banner-desc">
          <?= htmlspecialchars($workspace['description']) ?>
        </div>
      <?php endif; ?>
    </div>

    <?php if ($isOwner): ?>
      <div class="workspace-banner-actions">
        <a href="index.php?page=workspace_settings"
           class="btn btn-outline"
           style="color:#fff;border-color:rgba(255,255,255,.35);">
          ⚙️ Manage Members
        </a>
      </div>
    <?php endif; ?>
  </div>

  <div class="invite-box">
    <div class="invite-label">
      📨 <strong>Invite teammates</strong> — share this code:
    </div>
    <div class="invite-code" id="inviteCode">
      <?= htmlspecialchars($workspace['invite_code'] ?? '——') ?>
    </div>
    <button class="btn btn-outline" onclick="copyInviteCode()" id="copyBtn">
      📋 Copy
    </button>
  </div>

  <div id="projects-section">
    <?php
    if (file_exists('views/projects/list.php')) {
        include 'views/projects/list.php';
    } else {
        echo '
        <div style="text-align:center;padding:60px 20px;color:var(--navy-200);">
          <p style="font-size:48px;">📂</p>
          <p style="font-size:17px;margin-top:10px;font-weight:600;color:var(--navy-400);">No projects yet</p>
          <p style="font-size:13px;margin-top:6px;">(Task 2 will display projects here)</p>
        </div>';
    }
    ?>
  </div>

</div>

<script>

function switchWorkspace(id) {
  if (id) {
    window.location.href = 'index.php?page=do_switch_ws&id=' + id;
  }
}

function copyInviteCode() {
  const code = document.getElementById('inviteCode').textContent.trim();
  navigator.clipboard.writeText(code).then(() => {
    const btn = document.getElementById('copyBtn');
    btn.textContent = '✓ Copied!';
    btn.style.color = 'var(--green)';
    setTimeout(() => { btn.textContent = '📋 Copy'; btn.style.color = ''; }, 2000);
  }).catch(() => {
    alert('Invite code: ' + code);
  });
}
</script>
</body>
</html>
