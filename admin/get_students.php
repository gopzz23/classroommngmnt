<?php
include('../includes/config.php');

if (isset($_POST['class_id'])) {
    $class_id = $_POST['class_id'];
    $studentQuery = "SELECT id, name FROM students WHERE class_id = $class_id";
    $studentResult = mysqli_query($db_conn, $studentQuery);

    $students = array();
    while ($student = mysqli_fetch_assoc($studentResult)) {
        $students[] = $student;
    }

    // Return the data as JSON
    echo json_encode($students);
}
?>
