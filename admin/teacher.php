<?php 
include('../includes/config.php'); // Include your database configuration

// Handle form submission for adding a teacher
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $teacher_id = uniqid('teacher_'); // Generate a unique ID for the teacher
    $name = $_POST['name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT); // Hash the password
    $class_ids = $_POST['class_ids']; // Array of selected class IDs

    // Insert the teacher into the database
    $insert_sql = "INSERT INTO teachers (teacher_id, name, email, phone, password) VALUES ('$teacher_id', '$name', '$email', '$phone', '$password')";
    if (mysqli_query($db_conn, $insert_sql)) {
        // Insert the relationship between teacher and classes
        foreach ($class_ids as $class_id) {
            $class_insert_sql = "INSERT INTO teacher_classes (teacher_id, class_id) VALUES ('$teacher_id', '$class_id')";
            mysqli_query($db_conn, $class_insert_sql);
        }
        echo "<div class='alert alert-success'>Teacher added successfully!</div>";
    } else {
        echo "<div class='alert alert-danger'>Error: " . mysqli_error($db_conn) . "</div>";
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
    <title>Add Teacher Profile</title>
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
        <h1 class="mb-4">Add Teacher Profile</h1>
        
        <!-- Form to add a teacher -->
        <form action="" method="POST">
            <div class="form-group">
                <label for="name">Teacher Name:</label>
                <input type="text" name="name" id="name" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" name="email" id="email" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="phone">Phone Number:</label>
                <input type="text" name="phone" id="phone" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="password">Password:</label>
                <input type="password" name="password" id="password" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="class_ids">Select Classes:</label>
                <select name="class_ids[]" id="class_ids" class="form-control" multiple required>
                    <?php while ($class = mysqli_fetch_assoc($classes_query)): ?>
                        <option value="<?php echo $class['id']; ?>"><?php echo htmlspecialchars($class['class_name']); ?></option>
                    <?php endwhile; ?>
                </select>
                <small>Select multiple classes using Ctrl or Command key.</small>
            </div>
            <button type="submit" class="btn btn-primary">Add Teacher</button>
        </form>
    </div>

    <!-- Bootstrap JS and dependencies -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.0.7/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
