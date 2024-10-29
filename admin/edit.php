<?php
include('../includes/config.php');

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);

    // Fetch the current course details
    $course_query = mysqli_query($db_conn, "SELECT * FROM `courses` WHERE `id` = $id");
    if (!$course_query) {
        die('DB error: ' . mysqli_error($db_conn));
    }

    $course = mysqli_fetch_object($course_query);
    if (!$course) {
        die('Course not found.');
    }
}

if (isset($_POST['update'])) {
    $course_name = $_POST['course_name'];
    $class_id = $_POST['class_id'];
    $description = $_POST['description'];

    // Validate input
    if (empty($course_name) || empty($class_id) || empty($description)) {
        die('All fields are required.');
    }

    // Update the course in the database
    $update_query = mysqli_query($db_conn, "UPDATE `courses` SET `course_name`='$course_name', `class_id`='$class_id', `description`='$description' WHERE `id` = $id");

    if (!$update_query) {
        die('DB error: ' . mysqli_error($db_conn));
    }

    header('Location: sections.php'); // Redirect back to the sections page
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Course</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .container {
            margin-top: 50px;
        }
        .form-control {
            margin-bottom: 15px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1 class="text-center">Edit Course</h1>
        <form action="" method="POST">
            <div class="form-group">
                <label for="course_name">Course Name</label>
                <input type="text" name="course_name" class="form-control" value="<?= htmlspecialchars($course->course_name) ?>" required>
            </div>
            <div class="form-group">
                <label for="class_id">Class ID</label>
                <input type="number" name="class_id" class="form-control" value="<?= htmlspecialchars($course->class_id) ?>" required>
            </div>
            <div class="form-group">
                <label for="description">Description</label>
                <textarea name="description" class="form-control" required><?= htmlspecialchars($course->description) ?></textarea>
            </div>
            <button name="update" class="btn btn-primary btn-block">Update</button>
        </form>
    </div>
</body>
</html>
