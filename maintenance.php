<?php
$conn = new mysqli("localhost", "root", "", "fleet_management");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch maintenance records with vehicle and mechanic info
$query = "
    SELECT m.id, v.truck_plate_number, m.maintenance_type, m.date, m.status, u.username AS mechanic_name
    FROM maintenance m
    JOIN vehicles v ON m.truck_plate_number = v.truck_plate_number
    LEFT JOIN users u ON m.mechanic_username = u.username
    ORDER BY m.date DESC
";
$result = $conn->query($query);
// Check if the query was successful
if (!$result) {
    echo "Error: " . $conn->error; // Print the error message
    // Consider logging the error or redirecting to an error page.  For now, we'll halt execution.
    die();
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Maintenance | Fleet Management System</title>
    <style>
        * {
            box-sizing: border-box;
        }
        body {
            margin: 0;
            font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            background-color: #f4f6f9;
            color: #333;
        }
        .dashboard-container {
            flex: 1;
            padding: 30px 10%;
        }
        .back-button {
            display: inline-block;
            margin-bottom: 20px;
            padding: 8px 16px;
            background-color: #6c757d;
            color: white;
            border-radius: 4px;
            text-decoration: none;
            font-size: 0.9rem;
            transition: background-color 0.2s ease;
        }
        .back-button:hover {
            background-color: #5a6268;
        }
        h1 {
            font-size: 2rem;
            margin-bottom: 5px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            background: white;
            box-shadow: 0 2px 6px rgba(0,0,0,0.05);
            border-radius: 8px;
            overflow: hidden;
        }
        th, td {
            padding: 12px 15px;
            border-bottom: 1px solid #eee;
        }
        th {
            background-color: #007BFF;
            color: white;
            text-align: left;
        }
        tr:hover {
            background-color: #f1f1f1;
        }
        a {
            text-decoration: none;
        }
        .add-button {
            display: inline-block;
            margin-top: 20px;
            padding: 10px 20px;
            background-color: #28a745;
            color: white;
            border-radius: 4px;
            transition: background-color 0.2s ease;
        }
        .add-button:hover {
            background-color: #218838;
        }
        footer {
            text-align: center;
            padding: 15px;
            font-size: 0.9rem;
            color: #666;
            background-color: #fff;
            border-top: 1px solid #ddd;
        }
        .actions a {
            margin-right: 10px;
        }
    </style>
</head>
<body>
    <div class="dashboard-container">
        <a href="dashboard.php" class="back-button">← Back to Dashboard</a>
        <h1>Maintenance Records</h1>
        <p>Assign maintenance tasks to mechanics.</p>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Vehicle Plate</th>
                    <th>Maintenance Type</th>
                    <th>Date</th>
                    <th>Status</th>
                    <th>Mechanic</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($result && $result->num_rows > 0): ?> <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?= $row['id'] ?></td>
                        <td><?= htmlspecialchars($row['truck_plate_number']) ?></td>
                        <td><?= htmlspecialchars($row['maintenance_type']) ?></td>
                        <td><?= htmlspecialchars($row['date']) ?></td>
                        <td><?= htmlspecialchars(ucfirst($row['status'])) ?></td>
                        <td><?= htmlspecialchars($row['mechanic_name'] ?? 'Unassigned') ?></td>
                        <td class="actions">
                            <a href="assign-mechanic.php?id=<?= $row['id'] ?>" style="color: #17a2b8;">Assign</a>
                            <a href="edit-maintenance.php?id=<?= $row['id'] ?>" style="color: #007bff;">Edit</a>
                            <a href="delete-maintenance.php?id=<?= $row['id'] ?>" style="color: red;" onclick="return confirm('Are you sure you want to delete this record?');">Delete</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="7" style="text-align: center;">No maintenance records found.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
        <a href="add-maintenance.php" class="add-button">Add New Record</a>
    </div>
    <footer>
        © 2025 Fleet Maintenance Management System<br>
        Pope's Tr Headquarters | P.O.Box 1600 Dar es Salaam | Tanzania. | Phone: +255781636843 <br>
        Email: info@popestr.com | Website: <a href="http://www.popestr.com" target="_blank" style="color: #007BFF;">www.popestr.com</a>
    </footer>
</body>
</html>
<?php $conn->close(); ?>
