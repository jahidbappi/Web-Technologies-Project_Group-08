<?php
require_once __DIR__ . '/../config/auth.php';
require_once __DIR__ . '/../models/Project.php';

class ProjectController {
    public function index(): void {
        require_login();
        $projects = Project::listForUser(current_user_id(), current_workspace_id());
        $title = 'Projects';
        require __DIR__ . '/../views/projects/index.php';
    }
}
