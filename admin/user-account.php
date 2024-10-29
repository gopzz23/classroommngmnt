<?php 
include('../includes/config.php'); // Include your database configuration

// Handle form submission for adding a student
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $student_name = $_POST['student_name'];
    $class_id = $_POST['class_id'];
    $parent_name = $_POST['parent_name'];
    $parent_phone = $_POST['parent_phone'];
    $parent_email = $_POST['parent_email'];

    // Insert the student into the database
    $insert_sql = "INSERT INTO students (name, class_id) VALUES ('$student_name', '$class_id')";
    if (mysqli_query($db_conn, $insert_sql)) {
        // Get the last inserted student ID
        $student_id = mysqli_insert_id($db_conn);
        
        // Insert parent details into a separate table (assuming you have a parents table)
        $insert_parent_sql = "INSERT INTO parents (student_id, name, phone, email) VALUES ($student_id, '$parent_name', '$parent_phone', '$parent_email')";
        mysqli_query($db_conn, $insert_parent_sql);
        
        $success_message = "Student added successfully! Student ID: $student_id";
    } else {
        $error_message = "Error: " . mysqli_error($db_conn);
    }
}

// Fetch classes to populate the dropdown
$classes_sql = "SELECT * FROM classes";
$classes_query = mysqli_query($db_conn, $classes_sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Student Profile</title>
    <!-- Bootstrap CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
            font-family: Arial, sans-serif;
        }
        .container {
            margin-top: 50px;
            background-color: #ffffff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        h1 {
            color: #007bff;
            border-bottom: 2px solid #007bff;
            padding-bottom: 10px;
        }
        .alert {
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <div class="container mt-5">
        <h1 class="mb-4">Add Student Profile</h1>
        
        <!-- Display success or error message -->
        <?php if (isset($success_message)): ?>
            <div class='alert alert-success'><?php echo $success_message; ?></div>
        <?php elseif (isset($error_message)): ?>
            <div class='alert alert-danger'><?php echo $error_message; ?></div>
        <?php endif; ?>

        <!-- Form to add a student -->
        <form action="" method="POST">
            <div class="form-group">
                <label for="student_name">Student Name:</label>
                <input type="text" name="student_name" id="student_name" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="class_id">Select Class:</label>
                <select name="class_id" id="class_id" class="form-control" required>
                    <option value="">--Select Class--</option>
                    <?php while ($class = mysqli_fetch_assoc($classes_query)): ?>
                        <option value="<?php echo $class['id']; ?>"><?php echo htmlspecialchars($class['class_name']); ?></option>
                    <?php endwhile; ?>
                </select>
            </div>

            <h4 class="mt-4">Parent Details</h4>
            <div class="form-group">
                <label for="parent_name">Parent Name:</label>
                <input type="text" name="parent_name" id="parent_name" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="parent_phone">Parent Phone:</label>
                <input type="text" name="parent_phone" id="parent_phone" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="parent_email">Parent Email:</label>
                <input type="email" name="parent_email" id="parent_email" class="form-control" required>
            </div>

            <button type="submit" class="btn btn-primary">Add Student</button>
        </form>
    </div>

    <!-- Bootstrap JS and dependencies -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.0.7/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
