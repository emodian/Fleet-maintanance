<?php
// Database connection
$conn = new mysqli("localhost", "root", "", "fleet_management");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$success = "";
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $type = $_POST['type'];
    $title = $_POST['title'];
    $message = $_POST['message'];
    $user_id = $_POST['user_id'];
    $link = $_POST['link'] ?? null;
    $severity = $_POST['severity'];

    $stmt = $conn->prepare("INSERT INTO notifications (type, title, message, user_id, link, severity) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssiss", $type, $title, $message, $user_id, $link, $severity);

    if ($stmt->execute()) {
        header("Location: news_and_alerts.php?success=1");
        exit;
    } else {
        $success = "Error: " . $stmt->error;
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Add Notification</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f6f8fa; padding: 40px; }
        .container { max-width: 2000px; margin: auto; background: #fff; padding: 30px; border-radius: 12px; box-shadow: 0 4px 12px rgba(0,0,0,0.1); }
        input, textarea, select {
            width: 100%;
            padding: 10px;
            margin-bottom: 20px;
            border-radius: 6px;
            border: 1px solid #ccc;
        }
        button {
            background-color: #007bff;
            color: white;
            padding: 12px 20px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
        }
        .error {
            color: red;
            margin-bottom: 15px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Add Notification</h2>
        <?php if (!empty($success)): ?>
            <p class="error"><?php echo $success; ?></p>
        <?php endif; ?>

        <form method="POST">
            <label>Type:</label>
            <select name="type" required>
                <option value="News">News</option>
                <option value="Alert">Alert</option>
            </select>

            <label>Title:</label>
            <input type="text" name="title" required>

            <label>Message:</label>
            <textarea name="message" rows="5" required></textarea>

            <label>Select User:</label>
            <select name="user_id" required>
                <option value="">-- Select User --</option>
                <?php
                $user_result = $conn->query("SELECT user_id, username FROM users");
                while ($user = $user_result->fetch_assoc()):
                ?>
                    <option value="<?php echo $user['user_id']; ?>"><?php echo $user['username']; ?></option>
                <?php endwhile; ?>
            </select>

            <label>Link (optional):</label>
            <input type="text" name="link">

            <label>Severity:</label>
            <select name="severity">
                <option value="Info">Info</option>
                <option value="Warning">Warning</option>
                <option value="Critical">Critical</option>
            </select>

            <button type="submit">Submit Notification</button>
        </form>
    </div>
</body>
</html>
