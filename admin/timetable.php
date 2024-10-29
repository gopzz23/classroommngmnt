<?php
// Database connection
$host = 'localhost';
$user = 'root';
$pass = '';
$db = 'sms_project';

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch classes and sections for the dropdowns
$classes = $conn->query("SELECT id, class_name FROM classes");
$sections = $conn->query("SELECT id, title FROM sections");

// Fetch subjects from the courses table
$subjectQuery = $conn->query("SELECT course_name FROM courses"); // Use the correct column name
if (!$subjectQuery) {
    die("Error fetching subjects: " . $conn->error); // Display error if query fails
}
$subjects = $subjectQuery->fetch_all(MYSQLI_ASSOC); // Fetch subjects if the query is successful

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Process the timetable form submission
    $class_id = $_POST['class_id'];
    $section_id = $_POST['section_id'];
    $timetable = $_POST['timetable']; // Contains a multidimensional array for each period per day

    foreach ($timetable as $day => $periods) {
        foreach ($periods as $hour => $subject) {
            if (!empty($subject)) {
                $stmt = $conn->prepare("INSERT INTO timetable (class_id, section_id, day_of_week, hour, subject) VALUES (?, ?, ?, ?, ?)");
                $stmt->bind_param("iisis", $class_id, $section_id, $day, $hour, $subject);
                $stmt->execute();
            }
        }
    }
    echo "<div style='color: green;'>Timetable posted successfully!</div>";
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Post Timetable</title>
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
        form {
            background: #fff;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            max-width: 600px;
            margin: auto;
        }
        label {
            display: block;
            margin: 10px 0 5px;
        }
        select, input {
            width: calc(100% - 22px);
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 5px;
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
        td select {
            width: 100%;
            padding: 5px;
        }
        button {
            background-color: #28a745;
            color: white;
            border: none;
            padding: 10px 15px;
            border-radius: 5px;
            cursor: pointer;
            width: 100%;
        }
        button:hover {
            background-color: #218838;
        }
        .success-message {
            color: green;
            margin-top: 10px;
            text-align: center;
        }
    </style>
</head>
<body>
    <h1>Post Timetable</h1>
    <form method="POST">
        <label for="class">Select Class:</label>
        <select name="class_id" id="class" required>
            <option value="">Select Class</option>
            <?php while ($class = $classes->fetch_assoc()): ?>
                <option value="<?= $class['id'] ?>"><?= $class['class_name'] ?></option>
            <?php endwhile; ?>
        </select>

        <label for="section">Select Section:</label>
        <select name="section_id" id="section" required>
            <?php while ($section = $sections->fetch_assoc()): ?>
                <option value="<?= $section['id'] ?>"><?= $section['title'] ?></option>
            <?php endwhile; ?>
        </select>

        <div id="timetable-grid" style="display: none;">
            <h3>Timetable</h3>
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
                                <select name="timetable[<?= $day ?>][<?= $period ?>]">
                                    <option value="">Select Subject</option>
                                    <?php foreach ($subjects as $subject): ?>
                                        <option value="<?= $subject['course_name'] ?>"><?= $subject['course_name'] ?></option> <!-- Use the correct column name -->
                                    <?php endforeach; ?>
                                </select>
                            </td>
                        <?php endfor; ?>
                    </tr>
                <?php endforeach; ?>
            </table>
        </div>

        <button type="submit" style="display: none;" id="submit-button">Post Timetable</button>
    </form>

    <script>
        $(document).ready(function() {
            // Show timetable grid after selecting a class
            $('#class').change(function() {
                const classId = $(this).val();
                if (classId) {
                    $('#timetable-grid').show();
                    $('#submit-button').show();
                } else {
                    $('#timetable-grid').hide();
                    $('#submit-button').hide();
                }
            });
        });
    </script>
</body>
</html>

<?php
// Close the database connection
$conn->close();
?>
