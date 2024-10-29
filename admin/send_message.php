<?php
include('../includes/config.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $parent_id = mysqli_real_escape_string($db_conn, $_POST['parent_id']);
    $message = mysqli_real_escape_string($db_conn, $_POST['message']);
    
    // Check if the parent_id exists in the parents table
    $checkQuery = "SELECT * FROM parents WHERE id = '$parent_id'";
    $checkResult = mysqli_query($db_conn, $checkQuery);

    if (mysqli_num_rows($checkResult) > 0) {
        // Parent ID exists, proceed to insert the message
        $query = "INSERT INTO admin_messages (parent_id, message) VALUES ('$parent_id', '$message')";
        
        if (mysqli_query($db_conn, $query)) {
            echo "Message sent successfully!";
        } else {
            echo "Error: " . mysqli_error($db_conn);
        }
    } else {
        echo "Error: Parent ID does not exist.";
    }
}
?>
