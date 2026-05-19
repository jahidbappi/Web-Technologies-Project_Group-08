<?php
require_once __DIR__ . '/../../includes/init.php';
require_once __DIR__ . '/../../models/ProjectModel.php';

$model = new ProjectModel();

$wsId = current_workspace_id();
if (!$wsId) {
    header('Location: ' . app_url('TASK1/index.php?page=choose_workspace'));
    exit();
}
$members = $model->getWorkspaceMembers($wsId);

$errors = [];

if(isset($_SESSION['errors']))
{
    $errors = $_SESSION['errors'];

    unset($_SESSION['errors']);
}

?>

<!DOCTYPE html>
<html>

<head>

    <title>Create Project</title>

    <link rel="stylesheet"
    href="../../assets/css/style.css">

</head>

<body>


<div class="navbar">

    <div class="logo">
        TaskFlow
    </div>

    <div class="nav-links">

        <a href="create_project.php">
            Create Project
        </a>

        <a href="project_list.php">
            Projects
        </a>

        <a href="archived_projects.php">
            Archived
        </a>

    </div>

</div>


    


<div class="hero-section">

    <!-- LEFT SIDE -->

    <div class="hero-text">

        <h1>
            Manage Projects Smarter
        </h1>

        <p>
            Create projects, manage teams,
            track progress and organize tasks
            in one modern platform.
        </p>

        <img
        src="https://images.unsplash.com/photo-1522202176988-66273c2fd55f?q=80&w=1200&auto=format&fit=crop"

        class="hero-image">

    </div>



    <div class="form-container">

        <h2>Create Project</h2>

        <form
        action="../../controllers/ProjectController.php"
        method="POST"
        enctype="multipart/form-data">

            <!-- PROJECT NAME -->

            <label>Project Name</label>

            <input
            type="text"
            name="project_name">

<?php
if(isset($errors['project_name']))
{
?>
    <small style="color:#ffb3b3;">
        <?php echo $errors['project_name']; ?>
    </small>
<?php
}
?>

            <label>Description</label>

            <textarea
            name="description"></textarea>

<?php
if(isset($errors['description']))
{
?>
    <small style="color:#ffb3b3;">
        <?php echo $errors['description']; ?>
    </small>
<?php
}
?>

            <label>Deadline</label>

            <input
            type="date"
            name="deadline">

<?php
if(isset($errors['deadline']))
{
?>
    <small style="color:#ffb3b3;">
        <?php echo $errors['deadline']; ?>
    </small>
<?php
}
?>


            <label>Choose Colour</label>

            <div class="color-picker">

                <button
                type="button"
                class="color-btn"
                style="background:#ff4d4d;"
                onclick="selectColor('#ff4d4d', this)">
                </button>

                <button
                type="button"
                class="color-btn"
                style="background:#28a745;"
                onclick="selectColor('#28a745', this)">
                </button>

                <button
                type="button"
                class="color-btn"
                style="background:#007bff;"
                onclick="selectColor('#007bff', this)">
                </button>

                <button
                type="button"
                class="color-btn"
                style="background:#ffc107;"
                onclick="selectColor('#ffc107', this)">
                </button>

                <button
                type="button"
                class="color-btn"
                style="background:#ff00aa;"
                onclick="selectColor('#ff00aa', this)">
                </button>

            </div>

            <input
            type="hidden"
            name="color_label"
            id="color_label">

<?php
if(isset($errors['color_label']))
{
?>
    <small style="color:#ffb3b3;">
        <?php echo $errors['color_label']; ?>
    </small>
<?php
}
?>



            

            <label>Select Members</label>

<?php

while($member = $members->fetch_assoc())
{

?>

            <div class="member-option">

                <input
                type="checkbox"
                name="members[]"
                value="<?php echo $member['id']; ?>">

                <?php echo $member['name']; ?>

            </div>

<?php

}

?>

<?php
if(isset($errors['members']))
{
?>
    <small style="color:#ffb3b3;">
        <?php echo $errors['members']; ?>
    </small>
<?php
}
?>



           

            <button type="submit">
                Create Project
            </button>

        </form>

    </div>

</div>





<script>

function selectColor(color, button)
{
    document.getElementById("color_label").value = color;

    let buttons =
    document.querySelectorAll(".color-btn");

    buttons.forEach(btn =>
    {
        btn.classList.remove("selected");
    });

    button.classList.add("selected");
}

</script>
<footer class="footer">

    <p>
        © 2026 TaskFlow Project Management System
    </p>

</footer>
</body>

</html>