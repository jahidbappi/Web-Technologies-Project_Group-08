<?php
/**
 * Embedded project list for TASK1 dashboard (TASK2 data + links to board & details).
 */
require_once dirname(__DIR__, 3) . '/config/app.php';
require_once dirname(__DIR__, 3) . '/TASK2/config/database.php';
require_once dirname(__DIR__, 3) . '/TASK2/models/ProjectModel.php';

$model = new ProjectModel();
$workspaceId = current_workspace_id();
$projects = $model->getAllProjects($workspaceId);
?>

<div class="dashboard-projects-header" style="display:flex;justify-content:space-between;align-items:center;margin-bottom:20px;flex-wrap:wrap;gap:12px;">
  <h2 style="margin:0;font-size:20px;color:var(--navy-600);">Projects</h2>
  <div style="display:flex;gap:10px;flex-wrap:wrap;">
    <a href="<?= htmlspecialchars(app_url('TASK2/views/projects/create_project.php')) ?>" class="btn btn-primary">+ New Project</a>
    <a href="<?= htmlspecialchars(app_url('task3/index.php?route=projects')) ?>" class="btn btn-outline">Task Board (Task 3)</a>
    <a href="<?= htmlspecialchars(app_url('TASK2/views/projects/archived_projects.php')) ?>" class="btn btn-outline">Archived</a>
  </div>
</div>

<?php if ($projects->num_rows === 0): ?>
  <div style="text-align:center;padding:48px 20px;color:var(--navy-200);">
    <p style="font-size:40px;margin:0;">📂</p>
    <p style="font-size:16px;margin-top:10px;font-weight:600;color:var(--navy-400);">No projects in this workspace</p>
    <p style="font-size:13px;margin-top:6px;">Create a project to get started.</p>
  </div>
<?php else: ?>
  <div class="project-grid-dashboard" style="display:grid;grid-template-columns:repeat(auto-fill,minmax(280px,1fr));gap:20px;">
    <?php while ($row = $projects->fetch_assoc()): ?>
      <?php
        $progress = ($row['total_tasks'] > 0)
            ? round(($row['done_tasks'] / $row['total_tasks']) * 100)
            : 0;
        $accent = $row['color_label'] ?: '#6366f1';
        $boardUrl = task3_route('board', ['project_id' => (int)$row['id']]);
        $detailUrl = app_url('TASK2/views/projects/project_detail.php?id=' . (int)$row['id']);
      ?>
      <article class="dash-project-card" style="background:#fff;border-radius:14px;border:1px solid var(--navy-100);overflow:hidden;box-shadow:0 2px 8px rgba(15,23,42,.06);">
        <div style="border-left:6px solid <?= htmlspecialchars($accent) ?>;padding:18px;">
          <h3 style="margin:0 0 8px;font-size:17px;color:var(--navy-700);"><?= htmlspecialchars($row['name']) ?></h3>
          <p style="margin:0 0 12px;font-size:13px;color:var(--navy-300);line-height:1.5;"><?= htmlspecialchars($row['description']) ?></p>
          <?php if ($row['deadline']): ?>
            <p style="font-size:12px;color:var(--navy-300);margin:0 0 10px;"><strong>Deadline:</strong> <?= htmlspecialchars($row['deadline']) ?></p>
          <?php endif; ?>
          <?php if ($row['total_tasks'] > 0): ?>
            <p style="font-size:12px;margin:0 0 6px;color:var(--navy-400);">Progress: <?= (int)$progress ?>%</p>
            <div style="height:6px;background:var(--navy-100);border-radius:99px;overflow:hidden;margin-bottom:14px;">
              <div style="height:100%;width:<?= (int)$progress ?>%;background:var(--green);"></div>
            </div>
          <?php else: ?>
            <p style="font-size:12px;color:var(--navy-200);margin:0 0 14px;">No tasks yet</p>
          <?php endif; ?>
          <div style="display:flex;flex-wrap:wrap;gap:8px;">
            <a href="<?= htmlspecialchars($boardUrl) ?>" style="font-size:12px;padding:8px 12px;background:#7b2ff7;color:#fff;border-radius:8px;text-decoration:none;font-weight:600;">Open Board</a>
            <a href="<?= htmlspecialchars($detailUrl) ?>" style="font-size:12px;padding:8px 12px;background:#0ea5e9;color:#fff;border-radius:8px;text-decoration:none;font-weight:600;">Details</a>
            <a href="<?= htmlspecialchars(app_url('TASK2/views/projects/edit_project.php?id=' . (int)$row['id'])) ?>" style="font-size:12px;padding:8px 12px;background:#64748b;color:#fff;border-radius:8px;text-decoration:none;">Edit</a>
          </div>
        </div>
      </article>
    <?php endwhile; ?>
  </div>
<?php endif; ?>
