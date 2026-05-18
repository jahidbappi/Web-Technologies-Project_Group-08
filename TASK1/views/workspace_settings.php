<?php

if (session_status() === PHP_SESSION_NONE) session_start();
require_once 'config/helpers.php';
require_once 'models/WorkspaceModel.php';
requireAuth();

$userId  = (int)$_SESSION['user_id'];
$wsModel = new WorkspaceModel();

$workspace = $wsModel->findById((int)($_SESSION['workspace_id'] ?? 0));

if (!$workspace || (int)$workspace['owner_id'] !== $userId) {
    header('Location: index.php?page=dashboard'); exit();
}

$members       = $wsModel->getMembers((int)$workspace['id']);
$allWorkspaces = $wsModel->getUserWorkspaces($userId);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Workspace Settings — TeamFlow</title>
  <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

<nav class="navbar">
  <div class="navbar-brand">Team<span>Flow</span></div>

  <div class="navbar-ws-switcher">
    <span style="color:var(--navy-200);font-size:12px;">Workspace:</span>
    <select class="ws-select" onchange="switchWorkspace(this.value)">
      <?php foreach ($allWorkspaces as $ws): ?>
        <option value="<?= $ws['id'] ?>" <?= ($ws['id'] == $workspace['id']) ? 'selected' : '' ?>>
          <?= htmlspecialchars($ws['name']) ?>
        </option>
      <?php endforeach; ?>
    </select>
  </div>

  <div class="navbar-actions">
    <a href="index.php?page=create_workspace" class="navbar-action-btn">+ Create Workspace</a>
    <a href="index.php?page=join_workspace"   class="navbar-action-btn">+ Join Workspace</a>
    <a href="index.php?page=workspace_settings" class="navbar-action-btn navbar-action-btn--ghost navbar-action-btn--active">⚙️ Settings</a>
  </div>

  <div class="navbar-spacer"></div>

  <div class="navbar-user">
    <div class="navbar-avatar"><?= htmlspecialchars(getInitials($_SESSION['name'])) ?></div>
    <span><?= htmlspecialchars($_SESSION['name']) ?></span>
    <a href="index.php?page=do_logout" class="navbar-logout">Sign out</a>
  </div>
</nav>

<div class="page-content">
  <a href="index.php?page=dashboard" class="back-link">← Back to Dashboard</a>

  <div class="page-header">
    <h1>⚙️ Workspace Settings</h1>
    <p><?= htmlspecialchars($workspace['name']) ?> · Manage members</p>
  </div>

  <div class="card">
    <div class="card-title">
      👥 Members
      <span class="badge badge-blue" id="member-count"><?= count($members) ?></span>
    </div>

    <table class="members-table">
      <thead>
        <tr>
          <th></th>
          <th>Name</th>
          <th>Email</th>
          <th>Joined</th>
          <th>Action</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($members as $m): ?>
          <tr data-member-id="<?= $m['member_id'] ?>">
            <td>
              <div class="avatar"><?= htmlspecialchars(getInitials($m['name'])) ?></div>
            </td>
            <td>
              <div>
                <div class="avatar-name"><?= htmlspecialchars($m['name']) ?></div>
                <?php if ((int)$m['user_id'] === (int)$workspace['owner_id']): ?>
                  <span class="badge badge-amber" style="margin-top:3px;">Owner</span>
                <?php endif; ?>
              </div>
            </td>
            <td style="color:var(--navy-300);"><?= htmlspecialchars($m['email']) ?></td>
            <td style="color:var(--navy-300);font-size:13px;">
              <?= date('d M Y', strtotime($m['joined_at'])) ?>
            </td>
            <td>
              <?php if ((int)$m['user_id'] !== (int)$workspace['owner_id']): ?>
                <button class="btn-danger" onclick="removeMember(<?= $m['member_id'] ?>)">
                  Remove
                </button>
              <?php else: ?>
                <span style="color:var(--navy-100);font-size:12px;">—</span>
              <?php endif; ?>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>

  <div class="card">
    <div class="card-title">🔗 Invite Code</div>
    <p style="font-size:14px;color:var(--navy-300);margin-bottom:16px;">
      Share this with teammates so they can join via "+ Join Workspace" in the navbar.
    </p>
    <div class="invite-box" style="margin-bottom:0;">
      <div class="invite-label">Current invite code:</div>
      <div class="invite-code" id="inviteCode"><?= htmlspecialchars($workspace['invite_code']) ?></div>
      <button class="btn btn-outline" onclick="copyInviteCode()" id="copyBtn">📋 Copy</button>
    </div>
  </div>
</div>

<script src="assets/js/remove_member.js"></script>
<script>
function switchWorkspace(id) {
  if (id) window.location.href = 'index.php?page=do_switch_ws&id=' + id;
}
function copyInviteCode() {
  const code = document.getElementById('inviteCode').textContent.trim();
  navigator.clipboard.writeText(code).then(() => {
    const btn = document.getElementById('copyBtn');
    btn.textContent = '✓ Copied!';
    btn.style.color = 'var(--green)';
    setTimeout(() => { btn.textContent = '📋 Copy'; btn.style.color = ''; }, 2000);
  });
}
</script>
</body>
</html>
