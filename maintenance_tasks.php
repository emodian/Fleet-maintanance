<!-- filepath: c:\xampp\htdocs\fleet-maintenance-management-system\maintenance_tasks.php -->
<?php
session_start();


// Database connection
$conn = new mysqli('localhost', 'root', '', 'fleet_management');

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch maintenance tasks assigned to the mechanic

$stmt = $conn->prepare("SELECT * FROM maintenance_tasks WHERE assigned_to = ?");
if (!$stmt) {
    die("Query preparation failed: " . $conn->error);
}
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Maintenance Tasks | Fleet Maintenance Management System</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f8fafc;
            color: #333;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }

        .header {
            background-color: #2563eb;
            color: white;
            padding: 1rem;
            text-align: center;
        }

        .content {
            flex: 1;
            padding: 2rem;
            max-width: 1200px;
            margin: 0 auto;
        }

        .content h1 {
            font-size: 2rem;
            margin-bottom: 1rem;
            color: #2563eb;
        }

        .tasks-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 1rem;
        }

        .tasks-table th, .tasks-table td {
            border: 1px solid #ddd;
            padding: 0.75rem;
            text-align: left;
        }

        .tasks-table th {
            background-color: #2563eb;
            color: white;
        }

        .tasks-table tr:nth-child(even) {
            background-color: #f2f2f2;
        }

        .tasks-table tr:hover {
            background-color: #ddd;
        }

        .update-btn {
            padding: 0.5rem 1rem;
            background-color: #10b981;
            color: white;
            border: none;
            border-radius: 0.5rem;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        .update-btn:hover {
            background-color: #059669;
        }

        footer {
            background-color: #2c3e50;
            color: white;
            text-align: center;
            padding: 1rem;
            margin-top: auto;
        }

        footer a {
            color: #3498db;
            text-decoration: none;
        }

        footer a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Maintenance Tasks</h1>
    </div>
    <div class="content">
        <h1>Your Assigned Tasks</h1>
        <?php if ($result->num_rows > 0): ?>
            <table class="tasks-table">
                <thead>
                    <tr>
                        <th>Task ID</th>
                        <th>Vehicle</th>
                        <th>Description</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['task_id']); ?></td>
                            <td><?php echo htmlspecialchars($row['vehicle']); ?></td>
                            <td><?php echo htmlspecialchars($row['description']); ?></td>
                            <td><?php echo htmlspecialchars($row['status']); ?></td>
                            <td>
                                <form action="update_task.php" method="POST" style="display: inline;">
                                    <input type="hidden" name="task_id" value="<?php echo htmlspecialchars($row['task_id']); ?>">
                                    <button type="submit" class="update-btn">Update</button>
                                </form>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>No tasks assigned to you at the moment.</p>
        <?php endif; ?>
    </div>
    <footer>
        © 2025 Fleet Maintenance Management System<br>
        Pope's Tr Headquarters | P.O.Box 1600 Dar es Salaam | Tanzania. | Phone: +255781636843 | Email: info@popestr.com | Website: <a href="http://www.popestr.com" target="_blank">www.popestr.com</a>
    </footer>
</body>
</html>