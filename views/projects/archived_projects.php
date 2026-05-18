<?php

require_once("../../models/ProjectModel.php");

$model = new ProjectModel();

$projects = $model->getArchivedProjects();

?>

<!DOCTYPE html>
<html>

<head>

    <title>Archived Projects</title>

    <link rel="stylesheet"
    href="http://localhost/MANAGEMENT_PROJECT/assets/css/style.css">

</head>

<body>

<div class="page-container">

    <div style="width:100%; max-width:1300px;">

        <h1 style="text-align:center;">
            Archived Projects
        </h1>

        <div style="text-align:center; margin-bottom:40px;">

            <a
            href="project_list.php"

            style="
            background:white;
            color:#4b1d95;
            padding:12px 20px;
            border-radius:10px;
            display:inline-block;
            font-weight:bold;
            ">

                ← Back To Active Projects

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

                style="
                width:100%;
                height:200px;
                object-fit:cover;
                border-top-left-radius:20px;
                border-top-right-radius:20px;
                ">

<?php
}
?>

                <div
                style="
                padding:25px;
                border-left:10px solid <?php echo $row['color_label']; ?>;
                ">

                    <h2>
                        <?php echo $row['name']; ?>
                    </h2>

                    <p>
                        <?php echo $row['description']; ?>
                    </p>

                    <p>

                        <strong>Deadline:</strong>

                        <?php echo $row['deadline']; ?>

                    </p>

                    <br>

                    <a
                    href="../../controllers/ProjectController.php?delete=<?php echo $row['id']; ?>"

                    onclick="return confirm('Are you sure you want to delete this project?')"

                    style="
                    background:red;
                    color:white;
                    padding:10px 16px;
                    border-radius:10px;
                    display:inline-block;
                    font-size:14px;
                    font-weight:bold;
                    ">

                        Delete Project

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