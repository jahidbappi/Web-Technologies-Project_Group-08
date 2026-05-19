<?php

require_once __DIR__ . '/../includes/init.php';
require_once __DIR__ . '/../models/ProjectModel.php';

$model = new ProjectModel();


if(isset($_POST['update_project']))
{
    $project_id = $_POST['edit_project_id'];

    $project_name = $_POST['project_name'];

    $description = $_POST['description'];

    $deadline = $_POST['deadline'];

    $color_label = $_POST['color_label'];

    $members = [];

    if(isset($_POST['members']))
    {
        $members = $_POST['members'];
    }

    $model->updateProject(
        $project_id,
        $project_name,
        $description,
        $deadline,
        $color_label
    );

    $model->removeAllMembers($project_id);

    foreach($members as $member_id)
    {
        $model->addProjectMember(
            $project_id,
            $member_id
        );
    }

    header(
    "Location: ../views/projects/project_list.php");

    exit();
}   

if(
    $_SERVER['REQUEST_METHOD'] == "POST"

    &&

    !isset($_POST['add_member'])

    &&

    !isset($_POST['update_project'])
)
{
    $workspace_id = current_workspace_id() ?? 1;

    $project_name = $_POST['project_name'];

    $description = $_POST['description'];

    $deadline = $_POST['deadline'];

    $color_label = $_POST['color_label'];

    $members = [];

    if(isset($_POST['members']))
    {
        $members = $_POST['members'];
    }

    $errors = [];

    if(empty($project_name))
    {
        $errors['project_name'] =
        "Project name required";
    }

    if(empty($description))
    {
        $errors['description'] =
        "Description required";
    }

    if(empty($deadline))
    {
        $errors['deadline'] =
        "Deadline required";
    }

    if(empty($color_label))
    {
        $errors['color_label'] =
        "Select a colour";
    }

    if(count($members) == 0)
    {
        $errors['members'] =
        "Select at least one member";
    }

    if(count($errors) > 0)
    {
        $_SESSION['errors'] = $errors;

        header(
        "Location: ../views/projects/create_project.php");

        exit();
    }

    $project_id = $model->createProject(
        $workspace_id,
        $project_name,
        $description,
        $deadline,
        $color_label
    );

    foreach($members as $member_id)
    {
        $model->addProjectMember(
            $project_id,
            $member_id
        );
    }

    header(
    "Location: ../views/projects/project_list.php");

    exit();
}


if(isset($_GET['archive']))
{
    $project_id = $_GET['archive'];

    $model->archiveProject($project_id);

    header(
    "Location: ../views/projects/project_list.php");

    exit();
}


if(isset($_GET['delete']))
{
    $project_id = $_GET['delete'];

    $model->deleteProject($project_id);

    header(
    "Location: ../views/projects/archived_projects.php");

    exit();
}



if(isset($_POST['add_member']))
{
    $project_id = $_POST['project_id'];

    $user_id = $_POST['user_id'];

    $model->addProjectMember(
        $project_id,
        $user_id
    );

    header(
    "Location: ../views/projects/project_detail.php?id=" . $project_id);

    exit();
}

if(isset($_GET['remove_member']))
{
    $user_id = $_GET['remove_member'];

    $project_id = $_GET['project_id'];

    $sql = "DELETE FROM project_members
            WHERE project_id = ?
            AND user_id = ?";

    $stmt = $conn->prepare($sql);

    $stmt->bind_param(
        "ii",
        $project_id,
        $user_id
    );

    $stmt->execute();

    header(
    "Location: ../views/projects/project_detail.php?id=" . $project_id);

    exit();
}

?>