<?php
include('../includes/config.php'); // Include the database configuration

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Restrict access to only logged-in users and parents
if (!isset($_SESSION['login']) || $_SESSION['user_type'] !== 'parent') {
    header("Location: ../login.php");
    exit();
}

// Fetch classes
$classes = mysqli_query($db_conn, "SELECT id, class_name FROM classes");

// Initialize timetable data
$timetableData = [];

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['class_id'])) {
    $class_id = $_POST['class_id'];
    
    // Fetch timetable for the selected class
    $result = mysqli_query($db_conn, "SELECT day_of_week, hour, subject FROM timetable WHERE class_id = '$class_id' ORDER BY day_of_week, hour");
    
    // Organize the timetable data
    while ($row = mysqli_fetch_assoc($result)) {
        $timetableData[$row['day_of_week']][$row['hour']] = $row['subject'];
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>View Timetable</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 20px;
        }
        h1 {
            color: #333;
            text-align: center;
        }
        .timetable-container {
            background: #fff;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            max-width: 800px;
            margin: auto;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            text-align: center;
            padding: 10px;
            background-color: #fafafa;
        }
        th {
            background-color: #007bff;
            color: white;
        }
        select {
            width: 100%;
            padding: 10px;
            margin-bottom: 20px;
        }
        .no-data {
            color: red;
            text-align: center;
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <h1>View Timetable</h1>
    <div class="timetable-container">
        <form method="POST" action="">
            <label for="class">Select Class:</label>
            <select name="class_id" id="class" required>
                <option value="">Select Class</option>
                <?php while ($class = mysqli_fetch_assoc($classes)): ?>
                    <option value="<?= $class['id'] ?>"><?= $class['class_name'] ?></option>
                <?php endwhile; ?>
            </select>
            <button type="submit">View Timetable</button>
        </form>

        <?php if (!empty($timetableData)): ?>
            <table>
                <tr>
                    <th>Day/Period</th>
                    <?php for ($period = 1; $period <= 8; $period++): ?>
                        <th>Period <?= $period ?></th>
                    <?php endfor; ?>
                </tr>
                <?php 
                $days = ["Monday", "Tuesday", "Wednesday", "Thursday", "Friday"];
                foreach ($days as $day): ?>
                    <tr>
                        <td><?= $day ?></td>
                        <?php for ($period = 1; $period <= 8; $period++): ?>
                            <td>
                                <?= isset($timetableData[$day][$period]) ? $timetableData[$day][$period] : '-' ?>
                            </td>
                        <?php endfor; ?>
                    </tr>
                <?php endforeach; ?>
            </table>
        <?php else: ?>
            <div class="no-data">No data found for the selected class.</div>
        <?php endif; ?>
    </div>
</body>
</html>

<?php
// Close the database connection
mysqli_close($db_conn);
?>
