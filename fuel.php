<?php
session_start();
$conn = new mysqli("localhost", "root", "", "fleet_management");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$fuelLogs = [];
$sql = "SELECT * FROM fuel_logs ORDER BY date DESC";
$result = $conn->query($sql);

if ($result) {
    while ($row = $result->fetch_assoc()) {
        $fuelLogs[] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Fuel Logs | Fleet Management</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f4f6f8;
            padding: 30px;
        }

        .container {
            max-width: 1200px;
            margin: auto;
            background: #fff;
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.08);
        }

        h1 {
            display: flex;
            align-items: center;
            color: #2c3e50;
        }

        .back-arrow {
            margin-right: 12px;
            color: #3498db;
            cursor: pointer;
        }

        .btn-add {
            background-color: #28a745;
            color: white;
            padding: 10px 16px;
            border-radius: 6px;
            text-decoration: none;
            margin-bottom: 15px;
            display: inline-flex;
            align-items: center;
        }

        .btn-add:hover {
            background-color: #218838;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }

        th, td {
            padding: 12px;
            border: 1px solid #ddd;
            text-align: left;
        }

        th {
            background-color: #f8f9fa;
            font-weight: 600;
        }

        tbody tr:hover {
            background-color: #f1f1f1;
        }

        .notes {
            max-width: 300px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>
            <span class="material-icons back-arrow" onclick="window.location.href='dashboard.php'">arrow_back</span>
            Fuel Logs
        </h1>

        <a href="add-fuel.php" class="btn-add">
            <span class="material-icons">add</span>&nbsp; Add Fuel Log
        </a>

        <table>
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Truck Plate Number</th>
                    <th>Fuel Type</th>
                    <th>Quantity (L)</th>
                    <th>Cost</th>
                    <th>Filled By</th>
                    <th>Notes</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($fuelLogs)): ?>
                    <?php foreach ($fuelLogs as $log): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($log['date']); ?></td>
                            <td><?php echo htmlspecialchars($log['truck_plate_number']); ?></td>
                            <td><?php echo htmlspecialchars($log['fuel_type']); ?></td>
                            <td><?php echo htmlspecialchars($log['quantity']); ?></td>
                            <td><?php echo htmlspecialchars($log['cost']); ?></td>
                            <td><?php echo htmlspecialchars($log['filled_by']); ?></td>
                            <td class="notes"><?php echo htmlspecialchars($log['notes']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan="7">No fuel logs found.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
<?php $conn->close(); ?>
