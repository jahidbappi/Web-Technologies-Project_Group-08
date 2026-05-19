<?php
require_once __DIR__ . '/../../includes/init.php';
require_once __DIR__ . '/../../models/ProjectModel.php';

$model = new ProjectModel();

$projects = $model->getAllProjects(current_workspace_id());

?>

<!DOCTYPE html>
<html>

<head>

    <title>Project List</title>

    <link rel="stylesheet"
    href="../../assets/css/style.css">

</head>

<body>

<div class="page-container">

    <div style="width:100%; max-width:1300px;">

        <p style="text-align:center;margin-bottom:12px;">
            <a href="<?= htmlspecialchars(app_url('TASK1/index.php?page=dashboard')) ?>">← Dashboard</a>
        </p>

        <h1 style="text-align:center;">
            All Projects
        </h1>

        <div style="text-align:center; margin-bottom:40px;">

            <a
            href="archived_projects.php"

            style="
            background:white;
            color:#4b1d95;
            padding:12px 20px;
            border-radius:10px;
            display:inline-block;
            font-weight:bold;
            ">

                View Archived Projects

            </a>

        </div>

        <div class="project-container">

<?php

while($row = $projects->fetch_assoc())
{

?>

            <div class="project-card">

<?php

if(!empty($row['image']))
{
?>

                <img
                src="../../assets/images/<?php echo $row['image']; ?>"

                class="project-image">

<?php
}
?>

                <div
                style="
                border-left:10px solid <?php echo $row['color_label']; ?>;
                padding:20px;
                ">

                    <h2>
                        <?php echo $row['name']; ?>
                    </h2>

                    <p>
                        <?php echo $row['description']; ?>
                    </p>

                    <p>

                        <strong>Members:</strong>

<?php

$members = $model->getProjectMembers($row['id']);

while($member = $members->fetch_assoc())
{
    $initial =
    strtoupper(substr($member['name'], 0, 1));

    echo "
    <span class='badge done'>
        $initial
    </span>";
}

?>

                    </p>

                    <p>

                        <strong>Deadline:</strong>

<?php

if($row['deadline'] < date("Y-m-d"))
{
?>

    <span style="
    color:red;
    font-weight:bold;
    ">

        <?php echo $row['deadline']; ?>

    </span>

<?php
}
else
{
?>

    <?php echo $row['deadline']; ?>

<?php
}
?>

                    </p>

<?php

if($row['total_tasks'] == 0)
{
?>

                    <p>
                        No tasks yet
                    </p>

<?php
}
else
{
    $progress =
    ($row['done_tasks'] / $row['total_tasks']) * 100;
?>

                    <p>

                        <strong>Progress:</strong>

                        <?php echo round($progress); ?>%

                    </p>

                    <div class="progress-bar">

                        <div
                        class="progress-fill"

                        style="
                        width:<?php echo $progress; ?>%;">

                        </div>

                    </div>

<?php
}
?>

 <br><br>

  <a
      href="<?= htmlspecialchars(task3_route('board', ['project_id' => (int)$row['id']])) ?>"
      style="
     background:#10b981;
       color:white;
     padding:10px 16px;
        border-radius:10px;
         display:inline-block;
            margin-right:10px;
                    ">
                        Open Board
                    </a>

  <a
      href="project_detail.php?id=<?php echo $row['id']; ?>"

      style="
     background:#7b2ff7;
       color:white;
     padding:10px 16px;
        border-radius:10px;
         display:inline-block;
            margin-right:10px;
                    ">

                        View Details

                    </a>

                    <a
                    href="edit_project.php?id=<?php echo $row['id']; ?>"

                    style="
                    background:#007bff;
                    color:white;
                    padding:10px 16px;
                    border-radius:10px;
                    display:inline-block;
                    margin-right:10px;
                    ">

                   Edit Project
                    </a>

                    <a
                    href="../../controllers/ProjectController.php?archive=<?php echo $row['id']; ?>"

                    style="
                    background:red;
                    color:white;
                    padding:10px 16px;
                    border-radius:10px;
                    display:inline-block;
                    ">

                        Archive Project

                    </a>

                </div>

            </div>

<?php

}

?>

        </div>

    </div>

</div>

</body>

</html>