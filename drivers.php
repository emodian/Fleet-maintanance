<!-- filepath: c:\xampp\htdocs\fleet-maintenance-management-system\drivers.php -->
<?php




// Establish database connection
$conn = new mysqli("localhost", "root", "", "fleet_management");

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch drivers with their assigned truck and trailer
$query = "
    SELECT driver_id, 
           driver_name, 
           email, 
           phone, 
           license_number, 
           truck_plate_number, 
           status 
    FROM drivers
";
$result = $conn->query($query);

if (!$result) {
    die("Error fetching drivers: " . $conn->error);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Drivers | Fleet Management System</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f9;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }
        .dashboard-container {
            max-width: 1200px;
            margin: 20px auto;
            padding: 20px;
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            flex: 1;
        }
        h1 {
            color: #333;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        h1 .back-arrow {
            font-size: 1.5rem;
            color: #007BFF;
            text-decoration: none;
            transition: color 0.3s;
        }
        h1 .back-arrow:hover {
            color: #0056b3;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        table th, table td {
            padding: 12px;
            border: 1px solid #ddd;
            text-align: left;
        }
        table th {
            background-color: #007BFF;
            color: white;
        }
        table tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        table tr:hover {
            background-color: #f1f1f1;
        }
        .search-bar {
            margin-bottom: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .search-bar input {
            padding: 10px;
            width: 300px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        .add-driver-btn {
            padding: 10px 20px;
            background-color: #28a745;
            color: white;
            text-decoration: none;
            border-radius: 4px;
            transition: background-color 0.3s;
        }
        .add-driver-btn:hover {
            background-color: #218838;
        }
        footer {
            text-align: center;
            font-size: 0.9rem;
            color: #666;
            background-color: #fff;
            padding: 10px 0;
            border-top: 1px solid #ddd;
            margin-top: auto;
        }
        footer a {
            color: #007BFF;
            text-decoration: none;
        }
        footer a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="dashboard-container">
        <h1>
            <a href="dashboard.php" class="back-arrow">←</a> Drivers
        </h1>
        <p>Manage all drivers in the fleet.</p>
        <div class="search-bar">
            <input type="text" placeholder="Search drivers by name, email, or phone..." onkeyup="filterTable(this.value)">
            <a href="add-driver.php" class="add-driver-btn">Add New Driver</a>
        </div>
        <table id="driversTable">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>License Number</th>
                    <th>Truck Plate Number</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($result->num_rows > 0): ?>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo $row['driver_id']; ?></td>
                            <td><?php echo htmlspecialchars($row['driver_name']); ?></td>
                            <td><?php echo htmlspecialchars($row['email']); ?></td>
                            <td><?php echo htmlspecialchars($row['phone']); ?></td>
                            <td><?php echo htmlspecialchars($row['license_number']); ?></td>
                            <td><?php echo htmlspecialchars($row['truck_plate_number'] ?? 'Unassigned'); ?></td>
                            <td><?php echo htmlspecialchars($row['status']); ?></td>
                            <td>
                                <a href="edit-driver.php?id=<?php echo $row['driver_id']; ?>" style="color: blue;">Edit</a> |
                                <a href="delete-driver.php?id=<?php echo $row['driver_id']; ?>" style="color: red;" onclick="return confirm('Are you sure you want to delete this driver?');">Delete</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="8" style="text-align: center;">No drivers found.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    <footer>
        © 2025 Fleet Maintenance Management System<br>
        Pope's Tr Headquarters | P.O.Box 1600 Dar es Salaam | Tanzania.<br>
        Phone: +255781636843 | Email: <a href="mailto:info@popestr.com">info@popestr.com</a> | Website: <a href="http://www.popestr.com" target="_blank">www.popestr.com</a>
    </footer>
    <script>
        function filterTable(query) {
            const rows = document.querySelectorAll("#driversTable tbody tr");
            rows.forEach(row => {
                const cells = row.querySelectorAll("td");
                const match = Array.from(cells).some(cell => cell.textContent.toLowerCase().includes(query.toLowerCase()));
                row.style.display = match ? "" : "none";
            });
        }
    </script>
</body>
</html>
<?php $conn->close(); ?>