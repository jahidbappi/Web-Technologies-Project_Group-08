<?php
require_once __DIR__ . '/../includes/init.php';

$project_id = isset($_GET['project_id']) ? (int)$_GET['project_id'] : 0;

if ($project_id <= 0) {
    die('Invalid project ID');
}

$memberCheck = $conn->prepare(
    'SELECT 1 FROM projects p
     INNER JOIN workspace_members wm ON wm.workspace_id = p.workspace_id
     WHERE p.id = ? AND wm.user_id = ?
     LIMIT 1'
);
$memberCheck->bind_param('ii', $project_id, $_SESSION['user_id']);
$memberCheck->execute();
if (!$memberCheck->get_result()->fetch_assoc()) {
    die('Access denied');
}

$stmt = $conn->prepare(
    'SELECT al.*, u.name AS user_name
     FROM activity_logs al
     LEFT JOIN users u ON u.id = al.user_id
     WHERE al.project_id = ?
     ORDER BY al.created_at DESC'
);
$stmt->bind_param('i', $project_id);
$stmt->execute();
$activities = $stmt->get_result();

$projectStmt = $conn->prepare('SELECT name FROM projects WHERE id = ?');
$projectStmt->bind_param('i', $project_id);
$projectStmt->execute();
$project = $projectStmt->get_result()->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Activity Feed</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
<div class="page-container">
    <p>
        <a href="<?= htmlspecialchars(app_url('TASK1/index.php?page=dashboard')) ?>">Dashboard</a>
        &nbsp;|&nbsp;
        <a href="<?= htmlspecialchars(task3_route('board', ['project_id' => $project_id])) ?>">Board</a>
    </p>
    <h1>Activity — <?= htmlspecialchars($project['name'] ?? 'Project') ?></h1>
    <ul class="activity-list">
        <?php while ($row = $activities->fetch_assoc()): ?>
            <li>
                <strong><?= htmlspecialchars($row['user_name'] ?? 'User') ?></strong>:
                <?= htmlspecialchars($row['action_text']) ?>
                <small><?= htmlspecialchars($row['created_at']) ?></small>
            </li>
        <?php endwhile; ?>
    </ul>
</div>
</body>
</html>
