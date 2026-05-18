<?php
session_start();
header('Content-Type: application/json');

require_once '../config/db.php';
require_once '../config/helpers.php';
require_once '../models/comment.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode([
        'error' => 'Unauthorized access'
    ]);
    exit;
}

$user_id = $_SESSION['user_id'];
$author_name = $_SESSION['name'];

$commentModel = new Comment($conn);


if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $task_id = (int) $_POST['task_id'];
    $body = trim($_POST['body']);

    if (empty($body)) 

        
        
        {
        echo json_encode([
            'error' => 'Comment cannot be empty'
        ]);
        exit;
    }

    if (strlen($body) > 500) {
        echo json_encode([
            'error' => 'Comment too long'
        ]);
        exit;
    }

    $task = $commentModel->getTaskDetails($task_id);

    if (!$task) {
        echo json_encode([
            'error' => 'Task not found'
        ]);
        exit;
    }

    
    $memberStmt = $conn->prepare(
        "SELECT * FROM project_members
         WHERE project_id = ?
         AND user_id = ?"
    );

    $memberStmt->bind_param(
        "ii",
        $task['project_id'],
        $user_id
    );

    $memberStmt->execute();

    if ($memberStmt->get_result()->num_rows === 0) {
        echo json_encode([
            'error' => 'Access denied'
        ]);
        exit;
    }

    $task_res = $commentModel->getTaskDetails($task_id);
    if (!$task_res) {
        echo json_encode(['error' => 'Task not found']);
        exit;
    }

    
    $memberStmt = $conn->prepare("SELECT * FROM project_members WHERE project_id = ? AND user_id = ?");
    $memberStmt->bind_param("ii", $task_res['project_id'], $user_id);
    $memberStmt->execute();
    
    if ($memberStmt->get_result()->num_rows === 0) {
        echo json_encode(['error' => 'Access denied. You are not a member of this project.']);
        exit;
    }

    $comment_id = $commentModel->addComment(
        $task_id,
        $user_id,
        htmlspecialchars($body)
    );

    if ($comment_id) {

        $action_text = "Commented on task '" . $task['title'] . "'";

        log_activity(
            $task['project_id'],
            $user_id,
            $action_text
        );

        echo json_encode([
            'id' => $comment_id,
            'author_name' => $author_name,
            'body' => htmlspecialchars($body),
            'created_at' => date('Y-m-d H:i:s')
        ]);

    } else {

        echo json_encode([
            'error' => 'Database error'
        ]);

    }

    exit;
}


if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {

    $comment_id = (int) $_GET['id'];

    $comment = $commentModel->getCommentDetails($comment_id);

    if (!$comment) {
        echo json_encode([
            'error' => 'Comment not found'
        ]);
        exit;
    }

    if ($comment['user_id'] != $user_id) {
        echo json_encode([
            'error' => 'Unauthorized delete action'
        ]);
        exit;
    }

    if ($commentModel->deleteComment($comment_id)) {

        $action_text =
            "Deleted a comment on task '" .
            $comment['title'] .
            "'";

        log_activity(
            $comment['project_id'],
            $user_id,
            $action_text
        );

        echo json_encode([
            'ok' => true
        ]);

    } else {

        echo json_encode([
            'error' => 'Delete failed'
        ]);

    }

    exit;
}