<?php
// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "sms_project";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Admin ID (Replace with actual logged-in admin ID)
$admin_id = 1;

// Sending a message to a parent
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['send_message'])) {
    $recipient_id = $_POST['recipient_id'];
    $message = $_POST['message'];

    $sql = "INSERT INTO messages (sender_id, recipient_id, message) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);
    
    if (!$stmt) {
        die("Error preparing statement: " . $conn->error);
    }
    
    $stmt->bind_param("iis", $admin_id, $recipient_id, $message);

    if ($stmt->execute()) {
        echo "Message sent successfully.";
    } else {
        echo "Error executing statement: " . $stmt->error;
    }

    $stmt->close();
}

// Fetching all parents from the accounts table where type is 'parent'
$parents_result = $conn->query("SELECT id, name FROM accounts WHERE type = 'parent'");

if (!$parents_result) {
    die("Error fetching parents: " . $conn->error);
}

// Fetching messages with selected parent
$selected_parent_id = isset($_GET['parent_id']) ? $_GET['parent_id'] : 0;
$start_date = isset($_GET['start_date']) ? $_GET['start_date'] : '';
$end_date = isset($_GET['end_date']) ? $_GET['end_date'] : '';

if ($selected_parent_id) {
    $sql = "SELECT m.*, 
                   IF(m.sender_id = ?, 'Admin', a.name) AS sender_name 
            FROM messages m
            JOIN accounts a ON (m.sender_id = a.id OR m.recipient_id = a.id)
            WHERE (m.sender_id = ? AND m.recipient_id = ?)
               OR (m.sender_id = ? AND m.recipient_id = ?)";
    
    // Adding date filter to SQL query if dates are provided
    if (!empty($start_date) && !empty($end_date)) {
        $sql .= " AND m.timestamp BETWEEN ? AND ?";
    }
    
    $sql .= " ORDER BY m.timestamp DESC";

    $stmt = $conn->prepare($sql);
    
    if (!$stmt) {
        die("Error preparing statement: " . $conn->error);
    }

    if (!empty($start_date) && !empty($end_date)) {
        $stmt->bind_param("iiiss", $admin_id, $admin_id, $selected_parent_id, $selected_parent_id, $admin_id, $start_date, $end_date);
    } else {
        $stmt->bind_param("iiiii", $admin_id, $admin_id, $selected_parent_id, $selected_parent_id, $admin_id);
    }

    $stmt->execute();
    $messages_result = $stmt->get_result();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Portal - Communication with Parents</title>
</head>
<body>
    <h2>Select Parent to Communicate</h2>
    <form method="GET" action="">
        <label for="parent_id">Select Parent:</label>
        <select name="parent_id" required onchange="this.form.submit()">
            <option value="">-- Select Parent --</option>
            <?php while ($parent = $parents_result->fetch_assoc()): ?>
                <option value="<?php echo $parent['id']; ?>" <?php echo ($selected_parent_id == $parent['id']) ? 'selected' : ''; ?>>
                    <?php echo $parent['name']; ?>
                </option>
            <?php endwhile; ?>
        </select>
    </form>

    <?php if ($selected_parent_id): ?>
        <h2>Chat with Parent</h2>
        <form method="GET" action="">
            <input type="hidden" name="parent_id" value="<?php echo $selected_parent_id; ?>">
            <label for="start_date">Start Date:</label>
            <input type="date" name="start_date" value="<?php echo $start_date; ?>">
            <label for="end_date">End Date:</label>
            <input type="date" name="end_date" value="<?php echo $end_date; ?>">
            <input type="submit" value="Filter Messages">
        </form>
        
        <div style="border: 1px solid #000; padding: 10px; max-height: 400px; overflow-y: scroll;">
            <?php while ($row = $messages_result->fetch_assoc()): ?>
                <p><strong><?php echo $row['sender_name']; ?>:</strong> <?php echo $row['message']; ?><br>
                <small><?php echo $row['timestamp']; ?></small></p>
                <hr>
            <?php endwhile; ?>
        </div>

        <h3>Send a Message</h3>
        <form method="POST" action="">
            <input type="hidden" name="recipient_id" value="<?php echo $selected_parent_id; ?>">
            <textarea name="message" rows="4" cols="50" placeholder="Type your message here..." required></textarea><br><br>
            <input type="submit" name="send_message" value="Send Message">
        </form>
    <?php else: ?>
        <p>Select a parent to start the conversation.</p>
    <?php endif; ?>

    <?php $conn->close(); ?>
</body>
</html>
