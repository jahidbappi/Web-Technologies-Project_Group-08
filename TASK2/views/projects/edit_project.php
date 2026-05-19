<?php
require_once __DIR__ . '/../../includes/init.php';
require_once __DIR__ . '/../../models/ProjectModel.php';

$model = new ProjectModel();

$id = $_GET['id'];

$result = $model->getProjectById($id);

$project = $result->fetch_assoc();

$wsId = (int)($project['workspace_id'] ?? current_workspace_id() ?? 0);
if ($wsId < 1) {
    header('Location: ' . app_url('TASK1/index.php?page=choose_workspace'));
    exit();
}
$workspace_members = $model->getWorkspaceMembers($wsId);

$current_members =
$model->getProjectMembers($id);

$member_ids = [];

while($m = $current_members->fetch_assoc())
{
    $member_ids[] = $m['id'];
}

?>

<!DOCTYPE html>
<html>

<head>

    <title>Edit Project</title>

    <link rel="stylesheet"
    href="../../assets/css/style.css">

</head>

<body>

<div class="page-container">

    <div class="form-container">

        <h2>Edit Project</h2>

        <form
        action="../../controllers/ProjectController.php"
        method="POST">

            <input
            type="hidden"
            name="edit_project_id"
            value="<?php echo $project['id']; ?>">

            <label>Project Name</label>

            <input
            type="text"
            name="project_name"
            value="<?php echo $project['name']; ?>">

            <label>Description</label>

            <textarea
            name="description"><?php echo $project['description']; ?></textarea>

            <label>Deadline</label>

            <input
            type="date"
            name="deadline"
            value="<?php echo $project['deadline']; ?>">

            <label>Colour</label>

            <input
            type="text"
            name="color_label"
            value="<?php echo $project['color_label']; ?>">

            <br><br>

            <h3>Select Members</h3>

<?php

while($user = $workspace_members->fetch_assoc())
{

    $checked = "";

    if(in_array($user['id'], $member_ids))
    {
        $checked = "checked";
    }

?>

            <div class="member-option">

                <input
                type="checkbox"
                name="members[]"
                value="<?php echo $user['id']; ?>"

                <?php echo $checked; ?>>

                <?php echo $user['name']; ?>

            </div>

<?php

}

?>

            <button type="submit"
            name="update_project">

                Update Project

            </button>

        </form>

    </div>

</div>

</body>

</html>