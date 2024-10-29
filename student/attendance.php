<?php
session_start();
include('../includes/config.php'); // Include your database configuration

// Ensure the user is logged in
if (!isset($_SESSION['student_id'])) {
    header("Location: login.php"); // Redirect to login if not logged in
    exit;
}

$student_id = $_SESSION['student_id'];

// Fetch attendance records for the logged-in student
$attendance_sql = "SELECT attendance_date, status FROM attendance WHERE student_id = $student_id ORDER BY attendance_date DESC";
$attendance_query = mysqli_query($db_conn, $attendance_sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Attendance Records</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h1 class="mb-4">Your Attendance Records</h1>

        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php if (mysqli_num_rows($attendance_query) > 0): ?>
                    <?php while ($attendance = mysqli_fetch_assoc($attendance_query)): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($attendance['attendance_date']); ?></td>
                            <td><?php echo htmlspecialchars($attendance['status']); ?></td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="2">No attendance records found.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
        <a href="logout.php" class="btn btn-danger">Logout</a>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.0.7/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
