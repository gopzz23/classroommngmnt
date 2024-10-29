<?php 
include('../includes/config.php'); // Include your database configuration

// Fetch students for the dropdown
$students_query = mysqli_query($db_conn, "SELECT id, name FROM students");
if (!$students_query) {
    die('DB error: ' . mysqli_error($db_conn));
}

// Handle form submission for adding a parent
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['submit'])) {
    $student_id = $_POST['student_id'];
    $father_name = $_POST['father_name'];
    $mother_name = $_POST['mother_name'];
    $email = $_POST['email'];
    $phone = $_POST['phone']; // Changed from mobile_number to phone

    // Validate input
    if (empty($student_id) || empty($father_name) || empty($mother_name) || empty($email) || empty($phone)) {
        die('All fields are required.');
    }

    // Insert new parent into the database
    $query = mysqli_query($db_conn, "INSERT INTO parents (student_id, father_name, mother_name, email, phone) VALUES ('$student_id', '$father_name', '$mother_name', '$email', '$phone')");

    if (!$query) {
        die('DB error: ' . mysqli_error($db_conn));
    }

    // Get the last inserted parent ID
    $parent_id = mysqli_insert_id($db_conn);
    echo "<div class='alert alert-success'>Parent added successfully! Parent ID: $parent_id</div>";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Parent</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h1 class="mb-4">Add Parent</h1>
        
        <form action="" method="POST">
            <div class="form-group">
                <label for="student_id">Select Student</label>
                <select name="student_id" id="student_id" class="form-control" required>
                    <option value="">--Select Student--</option>
                    <?php while ($student = mysqli_fetch_assoc($students_query)): ?>
                        <option value="<?php echo $student['id']; ?>">
                            <?php echo htmlspecialchars($student['name']); ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>
            <div class="form-group">
                <label for="father_name">Father's Name</label>
                <input type="text" name="father_name" placeholder="Father's Name" required class="form-control">
            </div>
            <div class="form-group">
                <label for="mother_name">Mother's Name</label>
                <input type="text" name="mother_name" placeholder="Mother's Name" required class="form-control">
            </div>
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" name="email" placeholder="Email" required class="form-control">
            </div>
            <div class="form-group">
                <label for="phone">Phone</label>
                <input type="text" name="phone" placeholder="Phone Number" required class="form-control">
            </div>
            <button type="submit" name="submit" class="btn btn-success">Add Parent</button>
        </form>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.0.7/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
