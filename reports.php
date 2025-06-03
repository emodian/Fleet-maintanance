<?php
session_start();

// Establish database connection
$conn = new mysqli("localhost", "root", "", "fleet_management");

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Function to fetch data and format for report
function getReportData($conn) {
    $report = [];

    // 1. Vehicle Information
    $sql_vehicles = "SELECT * FROM vehicles";
    $result_vehicles = $conn->query($sql_vehicles);
    $report['vehicles'] = $result_vehicles->fetch_all(MYSQLI_ASSOC);

    // 2. Maintenance Logs
    $sql_maintenance = "SELECT * FROM maintenance ORDER BY maintenance_id DESC LIMIT 10"; // Last 10 entries
    $result_maintenance = $conn->query($sql_maintenance);
    $report['maintenance_list'] = $result_maintenance->fetch_all(MYSQLI_ASSOC);

    // 3. Fuel Logs (Last 10 entries)
    $sql_fuel = "SELECT * FROM fuel_logs ORDER BY date DESC LIMIT 10";
    $result_fuel = $conn->query($sql_fuel);
    $report['fuel_logs'] = $result_fuel->fetch_all(MYSQLI_ASSOC);

    // 4. Repair Logs (Last 10 entries)
    $sql_repair = "SELECT * FROM maintenance_logs ORDER BY completion_date DESC LIMIT 10";
    $result_repair = $conn->query($sql_repair);
    $report['repair_logs'] = $result_repair->fetch_all(MYSQLI_ASSOC);

    // 5. Inventory Summary (Low stock - quantity < 5)
    $sql_inventory = "SELECT * FROM inventory WHERE quantity < 5 ORDER BY quantity ASC";
    $result_inventory = $conn->query($sql_inventory);
    $report['inventory_low_stock'] = $result_inventory->fetch_all(MYSQLI_ASSOC);

    // 6. Recent Trip Logs (Last 10 entries)
    $sql_trips = "SELECT * FROM trip_logs ORDER BY trip_date DESC LIMIT 10";
    $result_trips = $conn->query($sql_trips);
    $report['trip_logs'] = $result_trips->fetch_all(MYSQLI_ASSOC);

    return $report;
}

$fullReport = getReportData($conn);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Full System Report | Fleet Management System</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        body {
            display: flex;
            flex-direction: column;
            min-height: 300vh; /* Ensure body takes at least full viewport height */
        }
        .dashboard-container {
            flex-grow: 1; /* Allow container to grow and push footer down */
            padding: 20px;
        }
        .report-section {
            margin-bottom: 20px;
            padding: 15px;
            border: 1px solid #ddd;
            border-radius: 5px;
            background-color: #f9f9f9;
        }
        .report-section h3 {
            margin-top: 0;
            color: #333;
        }
        .report-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        .report-table th, .report-table td {
            border: 1px solid #ccc;
            padding: 8px;
            text-align: left;
        }
        .report-table th {
            background-color: #f2f2f2;
        }
        .no-data {
            text-align: center;
            color: #777;
        }
        footer {
            text-align: center;
            padding: 20px;
            font-size: 0.9rem;
            color: #666;
            border-top: 1px solid #ddd;
        }
    </style>
</head>
<body>
    <div class="dashboard-container">
        <h1>Full System Report</h1>
        <p>Generated on: <?php echo date("Y-m-d H:i:s"); ?> EAT</p>

        <div class="report-section">
            <h3>Vehicle Information</h3>
            <?php if (!empty($fullReport['vehicles'])): ?>
                <table class="report-table">
                    <thead>
                        <tr>
                            <th>Plate Number</th>
                            <th>Make</th>
                            <th>Model</th>
                            <th>Mileage</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($fullReport['vehicles'] as $vehicle): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($vehicle['truck_plate_number']); ?></td>
                                <td><?php echo htmlspecialchars($vehicle['truck_make']); ?></td>
                                <td><?php echo htmlspecialchars($vehicle['truck_model']); ?></td>
                                <td><?php echo htmlspecialchars($vehicle['truck_mileage']); ?></td>
                                <td><?php echo htmlspecialchars($vehicle['truck_status']); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p class="no-data">No vehicle information available.</p>
            <?php endif; ?>
        </div>

        <div class="report-section">
            <h3>Recent Maintenance Logs (Last 10)</h3>
            <?php if (!empty($fullReport['maintenance_list'])): ?>
                <table class="report-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Scheduled Date</th>
                            <th>Vehicle</th>
                            <th>Task Details</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($fullReport['maintenance_list'] as $log): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($log['maintenance_id']); ?></td>
                                <td><?php echo htmlspecialchars($log['date']); ?></td>
                                <td><?php echo htmlspecialchars($log['truck_plate_number']); ?></td>
                                <td><?php echo htmlspecialchars($log['maintenance_type']); ?></td>
                                <td><?php echo htmlspecialchars($log['status'] ?? 'N/A'); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p class="no-data">No recent maintenance logs found.</p>
            <?php endif; ?>
        </div>

        <div class="report-section">
            <h3>Recent Fuel Logs (Last 10)</h3>
            <?php if (!empty($fullReport['fuel_logs'])): ?>
                <table class="report-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Date</th>
                            <th>Vehicle</th>
                            <th>Liters</th>
                            <th>Cost</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($fullReport['fuel_logs'] as $log): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($log['id']); ?></td>
                                <td><?php echo htmlspecialchars($log['date']); ?></td>
                                <td><?php echo htmlspecialchars($log['truck_plate_number'] ?? $log['vehicle_id'] ?? 'N/A'); ?></td>
                                <td><?php echo htmlspecialchars($log['quantity']); ?></td>
                                <td><?php echo htmlspecialchars($log['cost']); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p class="no-data">No recent fuel logs found.</p>
            <?php endif; ?>
        </div>

        <div class="report-section">
            <h3>Recent Repair Logs (Last 10)</h3>
            <?php if (!empty($fullReport['repair_logs'])): ?>
                <table class="report-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Repair Date</th>
                            <th>Vehicle</th>
                            <th>Description</th>
                            <th>Recommendations</th>
                            <th>Performed by (Mechanic)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($fullReport['repair_logs'] as $log): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($log['maintenance_id']); ?></td>
                                <td><?php echo htmlspecialchars($log['completion_date']); ?></td>
                                <td><?php echo htmlspecialchars($log['truck_plate_number'] ?? 'N/A'); ?></td>
                                <td><?php echo htmlspecialchars($log['task_details']); ?></td>
                                <td><?php echo htmlspecialchars($log['recommendations'] ?? 'N/A'); ?></td>
                                <td><?php echo htmlspecialchars($log['mechanic_username'] ?? 'N/A'); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p class="no-data">No recent repair logs found.</p>
            <?php endif; ?>
        </div>

        <div class="report-section">
            <h3>Recent Used Inventory</h3>
            <?php if (!empty($fullReport['inventory_low_stock'])): ?>
                <table class="report-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Vehicle</th>
                            <th>Part Number</th>
                            <th>Part Name</th>
                            <th>Quantity</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($fullReport['inventory_low_stock'] as $item): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($item['inventory_id']); ?></td>
                                <td><?php echo htmlspecialchars($item['truck_plate_number']); ?></td>
                                <td><?php echo htmlspecialchars($item['part_name']); ?></td>
                                <td><?php echo htmlspecialchars($item['part_number']); ?></td>
                                <td><?php echo htmlspecialchars($item['quantity']); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p class="no-data">No low stock inventory items.</p>
            <?php endif; ?>
        </div>

        <div class="report-section">
            <h3>Recent Trip Logs (Last 10)</h3>
            <?php if (!empty($fullReport['trip_logs'])): ?>
                <table class="report-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Vehicle</th>
                            <th>Driver</th>
                            <th>Trip Date</th>
                            <th>Trip Route</th>
                            <th>Cargo Type</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($fullReport['trip_logs'] as $trip): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($trip['trip_id']); ?></td>
                                <td><?php echo htmlspecialchars($trip['truck_plate_number'] ?? 'N/A'); ?></td>
                                <td><?php echo htmlspecialchars($trip['driver_id'] ?? 'N/A'); ?></td>
                                <td><?php echo htmlspecialchars($trip['trip_date']); ?></td>
                                <td><?php echo htmlspecialchars($trip['trip_route'] ?? 'N/A'); ?></td>
                                <td><?php echo htmlspecialchars($trip['cargo_type'] ?? 'N/A'); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p class="no-data">No recent trip logs found.</p>
            <?php endif; ?>
        </div>

        <div style="margin-top: 20px;">
            <button onclick="window.print()" style="padding: 10px 20px; background-color: #007bff; color: white; text-decoration: none; border-radius: 4px; border: none; cursor: pointer;">Print Full Report</button>
        </div>
    </div>
    <footer>
        © 2025 Fleet Maintenance Management System<br>
        Pope's Tr Headquarters | P.O.Box 1600 Dar es Salaam | Tanzania. | Phone: +255781636843 | Email: info@popestr.com | Website: <a href="http://www.popestr.com" target="_blank" style="color: #007BFF; text-decoration: none;">www.popestr.com</a>
    </footer>
</body>
</html>
<?php $conn->close(); ?>