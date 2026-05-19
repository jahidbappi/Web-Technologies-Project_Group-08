<?php
require_once __DIR__ . '/../../includes/init.php';
require_once __DIR__ . '/../../models/ProjectModel.php';

$model = new ProjectModel();

$id = $_GET['id'];

$result = $model->getProjectById($id);

$project = $result->fetch_assoc();

?>

<!DOCTYPE html>
<html>

<head>

    <title>Project Detail</title>

    <link rel="stylesheet"
    href="../../assets/css/style.css">

</head>

<body>

<div class="page-container">

    <p style="margin-bottom:16px;">
        <a href="<?= htmlspecialchars(app_url('TASK1/index.php?page=dashboard')) ?>">Dashboard</a>
        &nbsp;|&nbsp;
        <a href="<?= htmlspecialchars(task3_route('board', ['project_id' => (int)$id])) ?>">Task Board</a>
        &nbsp;|&nbsp;
        <a href="<?= htmlspecialchars(app_url('TASK2/views/activity_feed.php?project_id=' . (int)$id)) ?>">Activity Feed</a>
    </p>

    <h1>
        <?php echo htmlspecialchars($project['name']); ?>
    </h1>

    <div class="project-detail-card">

<?php

if(!empty($project['image']))
{
?>

        <img
        src="../../assets/images/<?php echo $project['image']; ?>"

        style="
        width:100%;
        height:220px;
        object-fit:cover;
        border-radius:15px;
        margin-bottom:20px;
        ">

<?php
}
?>

        <p>

            <strong>Description:</strong>

            <?php echo $project['description']; ?>

        </p>

        <p>

            <strong>Deadline:</strong>

            <?php echo $project['deadline']; ?>

        </p>

        <br>

        <span style="
            background:red;
            color:white;
            padding:10px;
            border-radius:8px;
        ">

            To Do: 0

        </span>

        <span style="
            background:orange;
            color:white;
            padding:10px;
            border-radius:8px;
            margin-left:10px;
        ">

            In Progress: 0

        </span>

        <span style="
            background:green;
            color:white;
            padding:10px;
            border-radius:8px;
            margin-left:10px;
        ">

            Done: 0

        </span>

        <br><br>

        <h3>Project Members</h3>

<?php

$members =
$model->getMemberTaskCounts($project['id']);

while($member = $members->fetch_assoc())
{
?>

        <div class="member-box">

            <strong>

                <?php echo $member['name']; ?>

            </strong>

            <br><br>

            Assigned Tasks:
            <?php echo $member['total_tasks']; ?>

            <br><br>

            <a
            href="../../controllers/ProjectController.php?remove_member=<?php echo $member['id']; ?>&project_id=<?php echo $project['id']; ?>"

            class="member-remove-btn">

                Remove Member

            </a>

        </div>

<?php
}
?>

        <br>

        <h3>Add Member</h3>

        <form
        action="../../controllers/ProjectController.php"
        method="POST">

            <input
            type="hidden"
            name="project_id"
            value="<?php echo $project['id']; ?>">

            <select
            name="user_id"

            style="
            width:100%;
            padding:12px;
            border-radius:10px;
            margin-bottom:20px;
            ">

<?php

$workspace_members =
$model->getWorkspaceMembers(1);

while($user = $workspace_members->fetch_assoc())
{
?>

                <option
                value="<?php echo $user['id']; ?>">

                    <?php echo $user['name']; ?>

                </option>

<?php
}
?>

            </select>

            <button
            type="submit"
            name="add_member"

            class="add-member-btn">

                Add Member

            </button>

        </form>

    </div>

</div>

</body>

</html>