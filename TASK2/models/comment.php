<?php
class Comment {
    private $conn;

    
    public function __construct($db_conn) {
        $this->conn = $db_conn;
    }

    
    public function addComment($task_id, $user_id, $body) {
        $stmt = $this->conn->prepare("INSERT INTO comments (task_id, user_id, body) VALUES (?, ?, ?)");
        $stmt->bind_param("iis", $task_id, $user_id, $body);
        
        if ($stmt->execute()) {
            return $this->conn->insert_id; 
        }
        return false;
    }

    
    public function getTaskDetails($task_id) {
        $stmt = $this->conn->prepare("SELECT title, project_id FROM tasks WHERE id = ?");
        $stmt->bind_param("i", $task_id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    
    public function getCommentDetails($comment_id) {
        $stmt = $this->conn->prepare("SELECT c.user_id, c.task_id, t.title, t.project_id FROM comments c JOIN tasks t ON c.task_id = t.id WHERE c.id = ?");
        $stmt->bind_param("i", $comment_id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    
    public function deleteComment($comment_id) {
        $stmt = $this->conn->prepare("DELETE FROM comments WHERE id = ?");
        $stmt->bind_param("i", $comment_id);
        return $stmt->execute();
    }
}
?>