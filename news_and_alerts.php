<?php
// Database connection
$conn = new mysqli("localhost", "root", "", "fleet_management");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch notifications
$sql = "SELECT n.*, u.username FROM notifications n 
        LEFT JOIN users u ON n.user_id = u.user_id 
        ORDER BY n.created_at DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Notifications</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f6f8fa;
            padding: 40px;
        }
        .container {
            max-width: 2000px;
            margin: auto;
            background: #fff;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }
        .notification {
            padding: 20px;
            margin-bottom: 20px;
            border: 1px solid #ddd;
            border-radius: 8px;
        }
        .notification h3 {
            margin-top: 0;
        }
        .meta {
            font-size: 0.9em;
            color: #555;
        }
        .btn {
            padding: 10px 20px;
            background: #007bff;
            color: white;
            text-decoration: none;
            border-radius: 6px;
            display: inline-block;
            margin-bottom: 20px;
        }
        .back-btn {
            display: inline-block;
            margin-bottom: 20px;
            color: #007bff;
            text-decoration: none;
            font-weight: bold;
            font-size: 1em;
        }
        .back-btn:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="container">
        <a href="dashboard.php" class="back-btn">&#8592; Back to Dashboard</a>
        <h2>Latest News & Alerts</h2>
        <a href="add_notification.php" class="btn">+ Add Notification</a>

        <?php if ($result && $result->num_rows > 0): ?>
            <?php while ($row = $result->fetch_assoc()): ?>
                <div class="notification">
                    <h3><?php echo htmlspecialchars($row['title']); ?> <small>(<?php echo $row['severity']; ?>)</small></h3>
                    <p><?php echo nl2br(htmlspecialchars($row['message'])); ?></p>
                    <p class="meta">Type: <?php echo $row['type']; ?> | Posted by: <?php echo $row['username'] ?? 'System'; ?> | Date: <?php echo $row['created_at']; ?></p>
                    <?php if (!empty($row['link'])): ?>
                        <p><a href="<?php echo htmlspecialchars($row['link']); ?>" target="_blank">Read More</a></p>
                    <?php endif; ?>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p>No notifications found.</p>
        <?php endif; ?>
    </div>
</body>
</html>
