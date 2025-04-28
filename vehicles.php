<?php
session_start();

require_once 'config.php'; // Include database connection

if (!isset($conn) || $conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

// Fetch vehicles from the database, joining with the drivers table to get driver info
$vehicles = [];
$sql = "SELECT 
            v.truck_plate_number, 
            v.truck_make, 
            v.truck_model, 
            v.horsepower, 
            v.trailer_make, 
            v.trailer_type, 
            v.trailer_plate_number, 
            v.truck_mileage, 
            v.next_service_mileage, 
            v.truck_status,
            d.driver_name  -- Added to fetch the driver's name
        FROM vehicles v
        LEFT JOIN drivers d ON v.driver_id = d.driver_id"; // Join based on driver_id
$result = $conn->query($sql);

if ($result) {
    while ($row = $result->fetch_assoc()) {
        $vehicles[] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vehicles | Fleet Management System</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f3f4f6;
            margin: 0;
            padding: 0;
        }

        .container {
            max-width: 2000px;
            margin: 20px auto;
            padding: 20px;
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
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
        }

        .back-arrow:hover {
            color: #217dbb;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
        }

        table th, table td {
            border: 1px solid #e0e0e0;
            padding: 12px;
            text-align: left;
        }

        table th {
            background-color: #f7fafc;
            font-weight: 600;
            color: #333;
        }

        table td {
            color: #555;
        }

        table tbody tr:hover {
            background-color: #f0f0f0;
        }

        .btn {
            padding: 10px 15px;
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
        }

        .btn-add {
            background-color: #28a745;
            margin-bottom: 20px;
            display: inline-flex;
            align-items: center;
        }

        .btn-add:hover {
            background-color: #218838;
            transform: translateY(-2px);
        }

        .btn-edit {
            background-color: #3498db;
            margin-right: 5px;
        }

        .btn-edit:hover {
            background-color: #2980b9;
            transform: translateY(-2px);
        }

        .btn-delete {
            background-color: #dc3545;
        }

        .btn-delete:hover {
            background-color: #c0392b;
            transform: translateY(-2px);
        }

        .actions {
            display: flex;
            gap: 5px;
        }

        @media (max-width: 768px) {
            .container {
                padding: 15px;
            }

            table {
                overflow-x: auto;
                display: block;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>
            <span class="back-arrow material-icons" onclick="window.location.href='dashboard.php'">arrow_back</span>
            Vehicles
        </h1>
        <a href="add-vehicle.php" class="btn btn-add">
             <span class="material-icons">add</span> Add Vehicle
        </a>
        <table>
            <thead>
                <tr>
                    <th>Plate Number</th>
                    <th>Make</th>
                    <th>Model</th>
                    <th>Horsepower</th>
                    <th>Trailer Make</th>
                    <th>Trailer Type</th>
                    <th>Trailer Plate</th>
                    <th>Mileage</th>
                    <th>Next Service Mileage</th>
                    <th>Status</th>
                    <th>Driver Name</th> <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($vehicles)): ?>
                    <?php foreach ($vehicles as $vehicle): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($vehicle['truck_plate_number']); ?></td>
                            <td><?php echo htmlspecialchars($vehicle['truck_make']); ?></td>
                            <td><?php echo htmlspecialchars($vehicle['truck_model']); ?></td>
                            <td><?php echo htmlspecialchars($vehicle['horsepower']); ?></td>
                            <td><?php echo htmlspecialchars($vehicle['trailer_make']); ?></td>
                            <td><?php echo htmlspecialchars($vehicle['trailer_type']); ?></td>
                            <td><?php echo htmlspecialchars($vehicle['trailer_plate_number']); ?></td>
                            <td><?php echo htmlspecialchars($vehicle['truck_mileage']); ?></td>
                            <td><?php echo htmlspecialchars($vehicle['next_service_mileage']); ?></td>
                            <td><?php echo htmlspecialchars($vehicle['truck_status']); ?></td>
                            <td><?php echo htmlspecialchars($vehicle['driver_name'] ?? 'N/A'); ?></td> <td>
                                <div class="actions">
                                    <a href="edit-vehicle.php?id=<?php echo $vehicle['truck_plate_number']; ?>" class="btn btn-edit">
                                         <span class="material-icons">edit</span>
                                    </a>
                                    <a href="delete-vehicle.php?id=<?php echo $vehicle['truck_plate_number']; ?>" class="btn btn-delete" onclick="return confirm('Are you sure you want to delete this vehicle?');">
                                         <span class="material-icons">delete</span>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="12">No vehicles found.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
<?php $conn->close(); ?>
