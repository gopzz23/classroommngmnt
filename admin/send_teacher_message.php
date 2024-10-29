<?php
include('../includes/config.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $teacher_id = intval($_POST['teacher_id']);
    $message_id = intval($_POST['message_id']);
    $message = mysqli_real_escape_string($db_conn, $_POST['message']);

    $insert_query = "INSERT INTO teacher_messages (teacher_id, message_id) VALUES ('$teacher_id', '$message_id')";
    if (mysqli_query($db_conn, $insert_query)) {
        header("Location: view_messages.php"); // Redirect back to the messages page
        exit();
    } else {
        echo "Error: " . mysqli_error($db_conn);
    }
}
?>
