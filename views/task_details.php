<?php
session_start();


$_SESSION['user_id'] = 1;
$_SESSION['name'] = 'Mim';

require_once '../config/db.php';
require_once '../config/helpers.php';

$task_id = isset($_GET['task_id']) ? (int)$_GET['task_id'] : 1;


$taskStmt = $conn->prepare("SELECT * FROM tasks WHERE id = ?");
$taskStmt->bind_param("i", $task_id);
$taskStmt->execute();
$task = $taskStmt->get_result()->fetch_assoc();

if (!$task) {
    die("Task not found");
}


$stmt = $conn->prepare("SELECT c.*, u.name FROM comments c JOIN users u ON c.user_id = u.id WHERE c.task_id = ? ORDER BY c.created_at ASC");
$stmt->bind_param("i", $task_id);
$stmt->execute();
$comments = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Task Details</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>

<div class="task-details">
    
    <div style="background: #f8fafc; padding: 20px; border-radius: 12px; margin-bottom: 25px; border: 1px solid #e2e8f0;">
        <h2 style="color: #1e293b; margin-bottom: 10px; font-size: 22px;"><?= htmlspecialchars($task['title']) ?></h2>
        <p style="color: #475569; margin-bottom: 15px; line-height: 1.6; font-size: 15px;">
            <?= nl2br(htmlspecialchars($task['description'])) ?>
        </p>
        <div style="display: flex; gap: 10px; flex-wrap: wrap;">
            <span style="background: #dbeafe; color: #1e40af; padding: 6px 12px; border-radius: 20px; font-size: 12px; font-weight: bold;">
                Priority: <?= htmlspecialchars($task['priority']) ?>
            </span>
            <span style="background: #dbeafe; color: #1e40af; padding: 6px 12px; border-radius: 20px; font-size: 12px; font-weight: bold;">
                Status: <?= htmlspecialchars($task['status']) ?>
            </span>
        </div>
    </div>
    <h3>TASK COMMENTS</h3>
    
    <div id="comment-thread">
        <?php while($c = $comments->fetch_assoc()): ?>
            <div class="comment" id="comment-<?= $c['id'] ?>">
                <strong><?= htmlspecialchars($c['name']) ?></strong>: <?= htmlspecialchars($c['body']) ?> 
                <small class="time-text"><?= time_elapsed_string($c['created_at']) ?></small>
                
                <?php if(isset($_SESSION['user_id']) && $c['user_id'] == $_SESSION['user_id']): ?>
                    <a href="#" onclick="deleteComment(<?= $c['id'] ?>); return false;" class="delete-btn">Delete</a>
                <?php endif; ?>
            </div>
        <?php endwhile; ?>
    </div>
    
   <form id="comment-form">
        <input type="hidden" id="task_id" value="<?= $task_id ?>"> 
        
        <div id="error-message" style="color: red; margin-bottom: 10px; display: none; font-weight: bold;"></div>
        
        <textarea id="comment_body" placeholder="Write a comment..."></textarea>
        <button type="submit">Post Comment</button>
    </form>
</div>

<script src="../js/comments.js"></script>
</body>
</html>