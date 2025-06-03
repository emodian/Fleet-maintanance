<?php
// Include the database connection file
$conn = new mysqli("localhost", "root", "", "fleet_management");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Function to safely get input data (for any potential GET messages)
function get_input_data($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

$success_message = "";
if (isset($_GET['msg'])) {
    $success_message = get_input_data($_GET['msg']);
}

// Fetch all maintenance tasks, joining with maintenance_logs for completion details
// Corrected: Using m.maintenance_id instead of m.id
// Added: m.cost and m.created_at to the SELECT list
$sql = "SELECT m.maintenance_id, m.truck_plate_number, m.maintenance_type, m.date, m.cost, m.status, m.created_at,
               ml.task_details, ml.recommendations, ml.completion_date, ml.mechanic_username AS completed_mechanic_username
        FROM maintenance m
        LEFT JOIN maintenance_logs ml ON m.maintenance_id = ml.maintenance_id
        ORDER BY m.date DESC, m.created_at DESC"; // Order by scheduled date then creation date
$result = $conn->query($sql);

// Check if the query was successful
if (!$result) {
    echo "Error: " . $conn->error; // Print the error message
    // Consider logging the error or redirecting to an error page. For now, we'll halt execution.
    die();
}

$maintenance_tasks = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $maintenance_tasks[] = $row;
    }
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Maintenance | Fleet Management System</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
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
            padding: 30px 5%; /* Adjusted padding for better fit */
            max-width: 1400px; /* Max width for the container */
            margin: 0 auto; /* Center the container */
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
            margin-top: 20px; /* Added margin */
        }
        th, td {
            padding: 12px 15px;
            border-bottom: 1px solid #eee;
            vertical-align: top; /* Align content to top for multi-line cells */
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
        .action-buttons-group { /* New class for the group of buttons */
            display: flex;
            gap: 10px; /* Space between buttons */
            margin-top: 20px;
            margin-bottom: 20px;
        }
        .action-button { /* General style for action buttons */
            padding: 10px 20px;
            color: white;
            border-radius: 4px;
            transition: background-color 0.2s ease;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }
        .action-button.add-record {
            background-color: #28a745;
        }
        .action-button.add-record:hover {
            background-color: #218838;
        }
        .action-button.record-completion {
            background-color: #007bff;
        }
        .action-button.record-completion:hover {
            background-color: #0056b3;
        }

        footer {
            text-align: center;
            padding: 15px;
            font-size: 0.9rem;
            color: #666;
            background-color: #fff;
            border-top: 1px solid #ddd;
            margin-top: auto; /* Push footer to bottom */
        }
        .actions a {
            margin-right: 10px;
            color: #007bff; /* Default action link color */
        }
        .actions a.delete-link {
            color: red;
        }
        .status-badge {
            padding: 5px 10px;
            border-radius: 5px;
            font-weight: bold;
            color: white;
        }
        .status-pending { background-color: #ffc107; color: #333; } /* Yellow */
        .status-in-progress { background-color: #17a2b8; } /* Cyan */
        .status-completed { background-color: #28a745; } /* Green */
        .status-cancelled { background-color: #dc3545; } /* Red */

        .completion-details {
            margin-top: 5px;
            padding-top: 5px;
            border-top: 1px dashed #ccc;
            font-size: 0.85em;
            color: #666;
        }
        .completion-details strong {
            color: #333;
        }
        .completion-details p {
            margin-bottom: 3px;
        }
    </style>
</head>
<body>
    <div class="dashboard-container">
        <a href="dashboard.php" class="back-button"><i class="fas fa-arrow-left"></i> Back to Dashboard</a>
        <h1>Maintenance Records</h1>
        <p>Manage and track all vehicle maintenance tasks.</p>

        <?php if (!empty($success_message)): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <?php echo $success_message; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <div class="action-buttons-group">
            <a href="add_maintenance.php" class="action-button add-record">
                <i class="fas fa-plus"></i> Add New Record
            </a>
            <a href="mechanic_task_done.php" class="action-button record-completion">
                <i class="fas fa-clipboard-check"></i> Record Completed Task
            </a>
        </div>

        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Vehicle Plate</th>
                    <th>Maintenance Type</th>
                    <th>Scheduled Date</th>
                    <th>Cost</th>
                    <th>Status</th>
                    <th>Assigned Mechanic</th>
                    <th>Completion Details</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($maintenance_tasks)): ?>
                    <?php foreach ($maintenance_tasks as $row): ?>
                        <tr>
                            <td><?= htmlspecialchars($row['maintenance_id']) ?></td>
                            <td><?= htmlspecialchars($row['truck_plate_number']) ?></td>
                            <td><?= htmlspecialchars($row['maintenance_type']) ?></td>
                            <td><?= htmlspecialchars($row['date']) ?></td>
                            <td>$<?= htmlspecialchars(number_format($row['cost'], 2)) ?></td>
                            <td>
                                <span class="status-badge status-<?= strtolower(str_replace(' ', '-', $row['status'])) ?>">
                                    <?= htmlspecialchars(ucfirst($row['status'])) ?>
                                </span>
                            </td>
                            <td>
                                <?= htmlspecialchars($row['mechanic_username'] ?? 'Unassigned') ?>
                            </td>
                            <td>
                                <?php if ($row['status'] == 'Completed' && $row['task_details']): ?>
                                    <div class="completion-details">
                                        <p><strong>Done by:</strong> <?= htmlspecialchars($row['completed_mechanic_username'] ?? 'N/A') ?></p>
                                        <p><strong>On:</strong> <?= htmlspecialchars($row['completion_date'] ?? 'N/A') ?></p>
                                        <p><strong>Details:</strong> <?= nl2br(htmlspecialchars($row['task_details'])) ?></p>
                                        <?php if (!empty($row['recommendations'])): ?>
                                            <p><strong>Recs:</strong> <?= nl2br(htmlspecialchars($row['recommendations'])) ?></p>
                                        <?php endif; ?>
                                    </div>
                                <?php else: ?>
                                    N/A
                                <?php endif; ?>
                            </td>
                            <td class="actions">
                                <?php if ($row['status'] != 'Completed' && $row['status'] != 'Cancelled'): ?>
                                    <a href="edit_maintenance.php?id=<?= htmlspecialchars($row['maintenance_id']) ?>" title="Edit Task">
                                        <i class="fas fa-edit"></i> Edit
                                    </a>
                                <?php endif; ?>
                                <a href="delete_maintenance.php?id=<?= htmlspecialchars($row['maintenance_id']) ?>" class="delete-link" title="Delete Task" onclick="return confirm('Are you sure you want to delete this record?');">
                                    <i class="fas fa-trash-alt"></i> Delete
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="9" style="text-align: center;">No maintenance records found.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    <footer>
        © 2025 Fleet Maintenance Management System<br>
        Pope's Tr Headquarters | P.O.Box 1600 Dar es Salaam | Tanzania. | Phone: +255781636843 <br>
        Email: info@popestr.com | Website: <a href="http://www.popestr.com" target="_blank" style="color: #007BFF;">www.popestr.com</a>
    </footer>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
