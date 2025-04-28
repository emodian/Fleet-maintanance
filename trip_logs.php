<?php
session_start();

// Establish database connection
$conn = new mysqli("localhost", "root", "", "fleet_management");

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch trip logs with updated fields
$query = "
        SELECT trip_id, 
            truck_plate_number, 
            driver_id, 
            trip_date, 
            trip_route, 
            distance, 
            fuel_consumed, 
            cargo_type, 
            cargo_weight, 
            tolls_paid, 
            other_expenses, 
            trip_status,
            (SELECT driver_name FROM drivers WHERE drivers.driver_id = trip_logs.driver_id) AS driver_name
        FROM trip_logs
    ";
$result = $conn->query($query);

if (!$result) {
    die("Error fetching trip logs: " . $conn->error);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Trip Logs | Fleet Management System</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f3f4f6;
            margin: 0;
            padding: 0;
        }

        .dashboard-container {
            max-width: 1200px; /* Increased max-width for larger screens */
            margin: 20px auto;
            padding: 20px;
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1); /* Slightly stronger shadow */
        }

        h1 {
            color: #2c3e50;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
        }

        .back-arrow {
            margin-right: 10px;
            cursor: pointer;
            color: #3498db;
            transition: color 0.3s ease;
        }

        .back-arrow:hover {
            color: #217dbb;
        }

        .search-bar {
            margin-bottom: 20px;
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            align-items: center; /* Vertically align items */
        }

        .search-bar input[type="text"] {
            flex: 1; /* Allow input to take up available space */
            padding: 10px;
            border: 1px solid #e0e0e0;
            border-radius: 5px;
            font-size: 1rem;
            transition: border-color 0.3s ease;
            min-width: 200px; /* Ensure input doesn't get too small */
        }

        .search-bar input[type="text"]:focus {
            border-color: #3498db;
            outline: none;
        }

        .add-driver-btn {
            background-color: #28a745;
            color: white;
            padding: 10px 15px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            text-decoration: none;
            font-weight: 500;
            transition: background-color 0.3s ease, transform 0.2s ease;
            white-space: nowrap; /* Prevent text wrapping */
        }

        .add-driver-btn:hover {
            background-color: #218838;
            transform: translateY(-2px);
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05); /* Subtle shadow */
            overflow: hidden; /* For rounded corners of thead/tbody */
        }

        table thead th {
            background-color: #f7fafc;
            font-weight: 600;
            color: #333;
            padding: 12px;
            text-align: left;
            border-bottom: 2px solid #e0e0e0; /* Stronger bottom border for header */
        }

        table tbody tr:nth-child(odd) {
            background-color: #f9f9f9; /* Very light background for odd rows */
        }

        table tbody tr:hover {
            background-color: #edf2f7; /* Slightly darker hover effect */
            transition: background-color 0.3s ease;
        }

        table td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #e8e8e8; /* Lighter bottom border for rows */
            color: #555;
        }

        table td:last-child {
            border-right: none;
        }

        table th:first-child,
        table td:first-child {
            border-left: none;
        }

        .actions {
            display: flex;
            gap: 10px;
            justify-content: left; /* Align buttons to the left */
        }

        .btn {
            padding: 8px 12px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            text-decoration: none;
            color: white;
            font-weight: 500;
            transition: background-color 0.3s ease, transform 0.2s ease;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            white-space: nowrap; /* Prevent text wrapping */
        }

        .btn-primary {
            background-color: #3498db;
        }

        .btn-primary:hover {
            background-color: #2980b9;
            transform: translateY(-2px);
        }

        .btn-danger {
            background-color: #dc3545;
        }

        .btn-danger:hover {
            background-color: #c0392b;
            transform: translateY(-2px);
        }

        .btn-sm {
            padding: 6px 10px;
            font-size: 0.8rem;
        }

        @media (max-width: 768px) {
            .dashboard-container {
                padding: 15px;
                margin: 10px;
                border-radius: 8px;
                box-shadow: 0 3px 6px rgba(0, 0, 0, 0.1);
            }

            .search-bar {
                flex-direction: column;
                align-items: flex-start;
                gap: 15px;
            }

            .search-bar input[type="text"] {
                width: 100%;
                min-width: auto;
            }

            .add-driver-btn {
                width: 100%;
            }

            table {
                overflow-x: auto;
                display: block;
                border-radius: 8px;
            }

            table thead {
                border-radius: 8px 8px 0 0;
            }

            table tbody {
                border-radius: 0 0 8px 8px;
            }

            table th, table td {
                padding: 10px;
                font-size: 0.9rem;
            }

            .actions {
                flex-direction: column;
                gap: 5px;
            }

            .btn {
                width: 100%;
                text-align: center;
            }
        }

        /* Add some spacing and style to the links */
        .actions a {
            color: white; /* Default color for links */
            text-decoration: none; /* Remove underline */
            border-radius: 5px; /* Give them a rounded border */
            padding: 5px 10px; /* Padding inside the button */
            transition: background-color 0.3s ease; /* Smooth transition */
        }

        .actions a:hover {
            background-color: rgba(0, 0, 0, 0.2); /* Darker background on hover */
        }

        .view-button {
            background-color: #3498db; /* Blue for view */
        }
        .view-button:hover{
             background-color: #217dbb;
        }

        .edit-button {
            background-color: #28a745; /* Green for edit */
        }

        .edit-button:hover {
            background-color: #218838;
        }
    </style>
</head>
<body>
    <div class="dashboard-container">
        <h1>
            <a href="dashboard.php" class="back-arrow material-icons">arrow_back</a>
            Trip Logs
        </h1>
        <p>Manage all trip logs in the fleet.</p>
        <div class="search-bar">
            <input type="text" placeholder="Search trips by driver, truck, or route..." onkeyup="filterTable(this.value)">
            <a href="add-trip.php" class="add-driver-btn">
                 <span class="material-icons">add</span> Add New Trip
            </a>
        </div>
        <table id="tripLogsTable">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Truck</th>
                    <th>Driver</th>
                    <th>Date</th>
                    <th>Route</th>
                    <th>Distance (km)</th>
                    <th>Fuel (litres)</th>
                    <th>Cargo Type</th>
                    <th>Weight (tonnes)</th>
                    <th>Tolls Paid (Tsh)</th>
                    <th>Other Expenses (Tsh)</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($result->num_rows > 0): ?>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo $row['trip_id']; ?></td>
                            <td><?php echo htmlspecialchars($row['truck_plate_number']); ?></td>
                            <td><?php echo htmlspecialchars($row['driver_name']); ?></td>
                            <td><?php echo htmlspecialchars($row['trip_date']); ?></td>
                            <td><?php echo htmlspecialchars($row['trip_route']); ?></td>
                            <td><?php echo htmlspecialchars($row['distance']); ?></td>
                            <td><?php echo htmlspecialchars($row['fuel_consumed']); ?></td>
                            <td><?php echo htmlspecialchars($row['cargo_type']); ?></td>
                            <td><?php echo htmlspecialchars($row['cargo_weight']); ?></td>
                            <td><?php echo htmlspecialchars($row['tolls_paid']); ?></td>
                            <td><?php echo htmlspecialchars($row['other_expenses']); ?></td>
                            <td><?php echo htmlspecialchars($row['trip_status']); ?></td>
                            <td>
                                <div class="actions">
                                    <a href="view-trip.php?id=<?php echo $row['trip_id']; ?>" class="btn btn-sm view-button">View</a>
                                    <a href="edit-trip.php?id=<?php echo $row['trip_id']; ?>" class="btn btn-sm edit-button">Edit</a>
                                </div>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="13" style="text-align: center;">No trip logs found.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    <footer>
        </footer>
    <script>
        function filterTable(query) {
            const rows = document.querySelectorAll("#tripLogsTable tbody tr");
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
