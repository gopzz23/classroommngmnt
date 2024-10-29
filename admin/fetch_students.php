<?php
include('../includes/config.php');

if (isset($_GET['class_id'])) {
    $class_id = intval($_GET['class_id']);
    $student_query = "SELECT id, name FROM students WHERE class_id = $class_id";
    $student_result = mysqli_query($db_conn, $student_query);

    while ($student_row = mysqli_fetch_assoc($student_result)) {
        echo '<option value="' . $student_row['id'] . '">' . htmlspecialchars($student_row['name']) . '</option>';
    }
}
?>
