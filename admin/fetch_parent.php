<?php
include('../includes/config.php');

if (isset($_GET['class_id'])) {
    $class_id = mysqli_real_escape_string($db_conn, $_GET['class_id']);
    
    // Fetch students based on class ID
    $query = "SELECT id, name FROM students WHERE class_id = '$class_id'"; // Assuming class_id exists in students table
    
    $result = mysqli_query($db_conn, $query);
    $students = [];

    if ($result) {
        while ($student = mysqli_fetch_assoc($result)) {
            $students[] = $student;
        }
        echo json_encode($students);
    } else {
        echo json_encode([]);
    }
} else {
    echo json_encode([]);
}
?>
