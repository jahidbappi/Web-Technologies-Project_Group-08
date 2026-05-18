<?php
session_start();

$_SESSION['user_id'] = 1;
$_SESSION['name'] = 'Mim';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit;
}

require_once '../config/db.php';

$project_id = isset($_GET['project_id'])
    ? (int)$_GET['project_id']
    : 0;

if ($project_id <= 0) {
    die("Invalid project ID");
}

// Security check
$memberCheck = $conn->prepare(
    "SELECT * FROM project_members
     WHERE project_id = ?
     AND user_id = ?"
);

$memberCheck->bind_param(
    "ii",
    $project_id,
    $_SESSION['user_id']
);

$memberCheck->execute();

if ($memberCheck->get_result()->num_rows === 0) {
    die("Access denied");
}

$stmt = $conn->prepare(
    "SELECT u.id, u.name
     FROM project_members pm
     JOIN users u ON pm.user_id = u.id
     WHERE pm.project_id = ?"
);

$stmt->bind_param("i", $project_id);
$stmt->execute();
$members = $stmt->get_result();
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
    
    <div style="text-align: center; margin: 20px 0;">
    <a href="task_details.php?task_id=1" style="background: #2563eb; color: white; padding: 10px 20px; text-decoration: none; border-radius: 8px; font-weight: bold;">⬅️ Back to Task Details</a>
</div>

<div class="activity-page">

    <h3>Project Activity Feed</h3>

    <input type="hidden"
           id="project_id"
           value="<?= $project_id ?>">

    <select id="member-filter" class="filter-dropdown">

        <option value="">
            All Members
        </option>

        <?php while($m = $members->fetch_assoc()): ?>

            <option value="<?= $m['id'] ?>">
                <?= htmlspecialchars($m['name']) ?>
            </option>

        <?php endwhile; ?>

    </select>

    <div id="activity-list"></div>

</div>

<script src="../Js/activity.js"></script>
</body>
</html>