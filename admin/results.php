<?php 
include('../includes/config.php'); // Include your database configuration

// Handle form submission for result recording
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['results'])) {
    $class_id = $_POST['class_id'];
    $course_id = $_POST['course_id'];
    $exam_type = $_POST['exam_type']; // Get the exam type from the form

    // Loop through each student to save their result
    foreach ($_POST['results'] as $student_id => $result) {
        // Check if the result already exists for the student, class, course, and exam type
        $check_sql = "SELECT * FROM results WHERE student_id = $student_id AND class_id = $class_id AND course_id = $course_id AND exam_type = '$exam_type'";
        $check_query = mysqli_query($db_conn, $check_sql);

        if (!$check_query) {
            die("Query failed: " . mysqli_error($db_conn)); // Log query error
        }

        if (mysqli_num_rows($check_query) > 0) {
            // Update existing result record
            $update_sql = "UPDATE results SET result = '$result' WHERE student_id = $student_id AND class_id = $class_id AND course_id = $course_id AND exam_type = '$exam_type'";
            mysqli_query($db_conn, $update_sql);
        } else {
            // Insert new result record
            $insert_sql = "INSERT INTO results (student_id, class_id, course_id, exam_type, result) VALUES ($student_id, $class_id, $course_id, '$exam_type', '$result')";
            mysqli_query($db_conn, $insert_sql);
        }
    }

    echo "<div class='alert alert-success'>Results recorded successfully!</div>";
}

// Fetch classes
$classes_sql = "SELECT * FROM classes";
$classes_query = mysqli_query($db_conn, $classes_sql);

if (!$classes_query) {
    die("Query failed: " . mysqli_error($db_conn)); // Log query error
}

// Fetch courses
$courses_sql = "SELECT * FROM courses"; // Assuming there's a courses table
$courses_query = mysqli_query($db_conn, $courses_sql);

if (!$courses_query) {
    die("Query failed: " . mysqli_error($db_conn)); // Log query error
}

// Variables to store selected class and course
$class_id = $_POST['class_id'] ?? '';
$course_id = $_POST['course_id'] ?? '';
$exam_type = $_POST['exam_type'] ?? ''; // Store selected exam type
$students = [];

// Fetch students if class and course are selected
if ($_SERVER["REQUEST_METHOD"] == "POST" && !empty($class_id) && !empty($course_id) && !isset($_POST['results'])) {
    $students_sql = "SELECT * FROM students WHERE class_id = $class_id";
    $students_query = mysqli_query($db_conn, $students_sql);

    if (!$students_query) {
        die("Query failed: " . mysqli_error($db_conn)); // Log query error
    }
    $students = mysqli_fetch_all($students_query, MYSQLI_ASSOC);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Post Results</title>
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
            color: #343a40;
        }
        .form-control {
            border-radius: 5px;
        }
        .btn-primary {
            background-color: #007bff;
            border-color: #007bff;
        }
        .btn-success {
            background-color: #28a745;
            border-color: #28a745;
        }
        .alert {
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <div class="container mt-5">
        <h1 class="mb-4">Post Results</h1>
        
        <!-- Form to select class, course, and exam type -->
        <form action="" method="POST" class="mb-4">
            <div class="form-group">
                <label for="class_id">Select Class:</label>
                <select name="class_id" id="class_id" class="form-control" required>
                    <option value="">--Select Class--</option>
                    <?php while ($class = mysqli_fetch_assoc($classes_query)): ?>
                        <option value="<?php echo $class['id']; ?>" <?php echo $class['id'] == $class_id ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($class['class_name']); ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>
            <div class="form-group">
                <label for="course_id">Select Course:</label>
                <select name="course_id" id="course_id" class="form-control" required>
                    <option value="">--Select Course--</option>
                    <?php while ($course = mysqli_fetch_assoc($courses_query)): ?>
                        <option value="<?php echo $course['id']; ?>" <?php echo $course['id'] == $course_id ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($course['course_name']); ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>
            <div class="form-group">
                <label for="exam_type">Select Exam Type:</label>
                <select name="exam_type" id="exam_type" class="form-control" required>
                    <option value="">--Select Exam Type--</option>
                    <option value="FA1" <?php echo $exam_type == 'FA1' ? 'selected' : ''; ?>>FA1</option>
                    <option value="FA2" <?php echo $exam_type == 'FA2' ? 'selected' : ''; ?>>FA2</option>
                    <option value="FA3" <?php echo $exam_type == 'FA3' ? 'selected' : ''; ?>>FA3</option>
                    <option value="FA4" <?php echo $exam_type == 'FA4' ? 'selected' : ''; ?>>FA4</option>
                    <option value="SA1" <?php echo $exam_type == 'SA1' ? 'selected' : ''; ?>>SA1</option>
                    <option value="SA2" <?php echo $exam_type == 'SA2' ? 'selected' : ''; ?>>SA2</option>
                </select>
            </div>
            <button type="submit" class="btn btn-primary">View Students</button>
        </form>

        <?php if (!empty($students)): ?>
            <!-- Form to record results -->
            <form action="" method="POST">
                <input type="hidden" name="class_id" value="<?php echo $class_id; ?>">
                <input type="hidden" name="course_id" value="<?php echo $course_id; ?>">
                <input type="hidden" name="exam_type" value="<?php echo $exam_type; ?>">
                <table class="table table-bordered mt-4">
                    <thead>
                        <tr>
                            <th>Student Name</th>
                            <th>Result</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($students as $student): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($student['name']); ?></td>
                                <td>
                                    <input type="text" name="results[<?php echo $student['id']; ?>]" class="form-control" placeholder="Enter grade or score">
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <button type="submit" class="btn btn-success">Submit Results</button>
            </form>
        <?php endif; ?>
    </div>

    <!-- Bootstrap JS and dependencies -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.0.7/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
