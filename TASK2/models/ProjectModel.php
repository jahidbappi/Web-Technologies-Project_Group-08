<?php

require_once(__DIR__ . "/../config/database.php");

class ProjectModel
{

    public function createProject(
        $workspace_id,
        $project_name,
        $description,
        $deadline,
        $color_label
    )
    {
        global $conn;

        $sql = "INSERT INTO projects(
                    workspace_id,
                    name,
                    description,
                    deadline,
                    color_label
                )
                VALUES(?, ?, ?, ?, ?)";

        $stmt = $conn->prepare($sql);

        $stmt->bind_param(
            "issss",
            $workspace_id,
            $project_name,
            $description,
            $deadline,
            $color_label
        );

        $stmt->execute();

        return $conn->insert_id;
    }



    public function addProjectMember($project_id, $user_id)
    {
        global $conn;

        $sql = "INSERT INTO project_members(
                    project_id,
                    user_id
                )
                VALUES(?, ?)";

        $stmt = $conn->prepare($sql);

        $stmt->bind_param(
            "ii",
            $project_id,
            $user_id
        );

        return $stmt->execute();
    }



    public function getWorkspaceMembers($workspace_id)
    {
        global $conn;

        $sql = "SELECT users.id,
                       users.name

                FROM workspace_members

                INNER JOIN users
                ON workspace_members.user_id = users.id

                WHERE workspace_members.workspace_id = ?";

        $stmt = $conn->prepare($sql);

        $stmt->bind_param(
            "i",
            $workspace_id
        );

        $stmt->execute();

        return $stmt->get_result();
    }



    public function getAllProjects(?int $workspace_id = null)
    {
        global $conn;

        $sql = "SELECT projects.*,

                (
                    SELECT COUNT(*)
                    FROM tasks
                    WHERE tasks.project_id = projects.id
                ) AS total_tasks,

                (
                    SELECT COUNT(*)
                    FROM tasks
                    WHERE tasks.project_id = projects.id
                    AND status = 'done'
                ) AS done_tasks

                FROM projects

                WHERE is_archived = 0";

        if ($workspace_id !== null) {
            $sql .= " AND projects.workspace_id = " . (int)$workspace_id;
        }

        return $conn->query($sql);
    }



    public function getArchivedProjects()
    {
        global $conn;

        $sql = "SELECT *
                FROM projects
                WHERE is_archived = 1";

        return $conn->query($sql);
    }



    public function archiveProject($project_id)
    {
        global $conn;

        $sql = "UPDATE projects
                SET is_archived = 1
                WHERE id = ?";

        $stmt = $conn->prepare($sql);

        $stmt->bind_param(
            "i",
            $project_id
        );

        return $stmt->execute();
    }



    public function deleteProject($project_id)
    {
        global $conn;

        $sql = "DELETE FROM projects
                WHERE id = ?";

        $stmt = $conn->prepare($sql);

        $stmt->bind_param(
            "i",
            $project_id
        );

        return $stmt->execute();
    }



    public function getProjectMembers($project_id)
    {
        global $conn;

        $sql = "SELECT users.id,
                       users.name

                FROM project_members

                INNER JOIN users
                ON project_members.user_id = users.id

                WHERE project_members.project_id = ?";

        $stmt = $conn->prepare($sql);

        $stmt->bind_param(
            "i",
            $project_id
        );

        $stmt->execute();

        return $stmt->get_result();
    }



    public function getProjectById($project_id)
    {
        global $conn;

        $sql = "SELECT *
                FROM projects
                WHERE id = ?";

        $stmt = $conn->prepare($sql);

        $stmt->bind_param(
            "i",
            $project_id
        );

        $stmt->execute();

        return $stmt->get_result();
    }



    public function getMemberTaskCounts($project_id)
    {
        global $conn;

        $sql = "SELECT users.id,
                       users.name,

                       COUNT(tasks.id) AS total_tasks

                FROM project_members

                INNER JOIN users
                ON project_members.user_id = users.id

                LEFT JOIN tasks
                ON tasks.assigned_to = users.id
                AND tasks.project_id = project_members.project_id

                WHERE project_members.project_id = ?

                GROUP BY users.id";

        $stmt = $conn->prepare($sql);

        $stmt->bind_param(
            "i",
            $project_id
        );

        $stmt->execute();

        return $stmt->get_result();
    }
public function updateProject(
    $project_id,
    $project_name,
    $description,
    $deadline,
    $color_label
)
{
    global $conn;

    $sql = "UPDATE projects

            SET
            name = ?,
            description = ?,
            deadline = ?,
            color_label = ?

            WHERE id = ?";

    $stmt = $conn->prepare($sql);

    $stmt->bind_param(
        "ssssi",
        $project_name,
        $description,
        $deadline,
        $color_label,
        $project_id
    );

    return $stmt->execute();
}



public function removeAllMembers($project_id)
{
    global $conn;

    $sql = "DELETE FROM project_members
            WHERE project_id = ?";

    $stmt = $conn->prepare($sql);

    $stmt->bind_param("i", $project_id);

    return $stmt->execute();
}
}

?>