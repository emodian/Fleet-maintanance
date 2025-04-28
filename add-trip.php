<?php
session_start();
require_once 'config.php'; // Include database connection

if (!isset($conn) || $conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

$message = ""; // Initialize message

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $trip_date = trim($_POST['trip_date']);
    $driver_id = trim($_POST['driver_id']);
    $truck_plate_number = trim($_POST['truck_plate_number']);
    $trip_route = trim($_POST['trip_route']);
    $distance = trim($_POST['distance']);
    $fuel_consumed = trim($_POST['fuel_consumed']);
    $cargo_type = trim($_POST['cargo_type']);
    $cargo_weight = trim($_POST['cargo_weight']);
    $tolls_paid = trim($_POST['tolls_paid']);
    $other_expenses = trim($_POST['other_expenses']);
    $trip_status = trim($_POST['trip_status']);

    // Validate inputs
    if (empty($trip_date) || empty($driver_id) || empty($truck_plate_number) || empty($trip_route) || 
        empty($distance) || empty($fuel_consumed) || empty($cargo_type) || empty($cargo_weight) || 
        empty($tolls_paid) || empty($other_expenses) || empty($trip_status)) {
        $message = "All fields are required.";
    } else {
        // Verify truck_plate_number exists
        $truck_query = "SELECT truck_plate_number FROM vehicles WHERE truck_plate_number = ?";
        $truck_stmt = $conn->prepare($truck_query);
        $truck_stmt->bind_param("s", $truck_plate_number);
        $truck_stmt->execute();
        $truck_result = $truck_stmt->get_result();
        if ($truck_result->num_rows == 0) {
            $message = "Error: Invalid truck selected.";
        }
        $truck_stmt->close();

        // Verify driver_id exists
        $driver_query = "SELECT driver_id FROM drivers WHERE driver_id = ? AND status = 'active'";
        $driver_stmt = $conn->prepare($driver_query);
        $driver_stmt->bind_param("i", $driver_id);
        $driver_stmt->execute();
        $driver_result = $driver_stmt->get_result();
        if ($driver_result->num_rows == 0) {
            $message = "Error: Invalid or unavailable driver selected.";
        }
        $driver_stmt->close();

        if (empty($message)) {
            $stmt = $conn->prepare("INSERT INTO trip_logs (trip_date, driver_id, truck_plate_number, trip_route, distance, fuel_consumed, cargo_type, cargo_weight, tolls_paid, other_expenses, trip_status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            if (!$stmt) {
                die("Error preparing statement: " . $conn->error);
            }

            $stmt->bind_param("sissddsddds", $trip_date, $driver_id, $truck_plate_number, $trip_route, $distance, $fuel_consumed, $cargo_type, $cargo_weight, $tolls_paid, $other_expenses, $trip_status);

            if ($stmt->execute()) {
                $message = "Trip added successfully!";
                header("Location: trips.php");
                exit;
            } else {
                $message = "Error: " . $stmt->error;
            }
            $stmt->close();
        }
    }
}

// Fetch available drivers
$drivers = [];
$driver_query = "SELECT driver_id, driver_name FROM drivers WHERE status = 'active'";
$driver_result = $conn->query($driver_query);
if ($driver_result) {
    while ($row = $driver_result->fetch_assoc()) {
        $drivers[] = $row;
    }
}

// Fetch available vehicles
$vehicles = [];
$vehicle_query = "SELECT truck_plate_number, truck_make, truck_model FROM vehicles WHERE truck_status = 'active'";
$vehicle_result = $conn->query($vehicle_query);
if ($vehicle_result) {
    while ($row = $vehicle_result->fetch_assoc()) {
        $vehicles[] = $row;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Trip | Fleet Management System</title>
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

        .form-container {
            max-width: 600px;
            margin: 20px auto;
            padding: 20px;
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
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

        form {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }

        label {
            font-weight: 500;
            color: #34495e;
        }

        input[type="text"],
        input[type="date"],
        input[type="number"],
        select {
            padding: 10px;
            border: 1px solid #e0e0e0;
            border-radius: 5px;
            font-size: 1rem;
            transition: border-color 0.3s ease;
            width: 100%;
        }

        input[type="text"]:focus,
        input[type="date"]:focus,
        input[type="number"]:focus,
        select:focus {
            border-color: #3498db;
            outline: none;
        }

        select {
            appearance: none;
            background-image: url('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"></polyline></svg>');
            background-repeat: no-repeat;
            background-position: right 10px center;
            background-size: 16px;
            padding-right: 30px;
        }

        select::-ms-expand {
            display: none;
        }

        .btn-container {
            display: flex;
            justify-content: flex-end;
            gap: 10px;
            margin-top: 20px;
        }

        .btn {
            padding: 10px 15px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            text-decoration: none;
            font-weight: 500;
            transition: background-color 0.3s ease, transform 0.2s ease;
            white-space: nowrap;
        }

        .btn-primary {
            background-color: #28a745;
            color: white;
        }

        .btn-primary:hover {
            background-color: #218838;
            transform: translateY(-2px);
        }

        .btn-cancel {
            background-color: #e74c3c;
            color: white;
        }

        .btn-cancel:hover {
            background-color: #c0392b;
            transform: translateY(-2px);
        }

        .message {
            margin-top: 20px;
            padding: 10px;
            border-radius: 5px;
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        @media (max-width: 768px) {
            .form-container {
                padding: 15px;
                margin: 10px;
                border-radius: 8px;
                box-shadow: 0 3px 6px rgba(0, 0, 0, 0.1);
            }

            form {
                gap: 10px;
            }

            .btn-container {
                flex-direction: column;
                gap: 10px;
            }

            .btn {
                width: 100%;
                text-align: center;
            }
        }
    </style>
</head>
<body>
    <div class="form-container">
        <h1>
            <a href="trips.php" class="back-arrow material-icons">arrow_back</a>
            Add New Trip
        </h1>
        <?php if (!empty($message)): ?>
            <div class="message <?php echo strpos($message, 'Error') === false ? '' : 'error'; ?>">
                <?php echo htmlspecialchars($message); ?>
            </div>
        <?php else: ?>
            <form method="POST">
                <label for="trip_date">Trip Date:</label>
                <input type="date" name="trip_date" id="trip_date" required>

                <label for="driver_id">Assign Driver:</label>
                <select name="driver_id" id="driver_id" required>
                    <option value="">Select a driver</option>
                    <?php foreach ($drivers as $driver): ?>
                        <option value="<?php echo htmlspecialchars($driver['driver_id']); ?>">
                            <?php echo htmlspecialchars($driver['driver_name']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>

                <label for="truck_plate_number">Assign Truck:</label>
                <select name="truck_plate_number" id="truck_plate_number" required>
                    <option value="">Select a truck</option>
                    <?php foreach ($vehicles as $vehicle): ?>
                        <option value="<?php echo htmlspecialchars($vehicle['truck_plate_number']); ?>">
                            <?php echo htmlspecialchars($vehicle['truck_plate_number'] . ' (' . $vehicle['truck_make'] . ' ' . $vehicle['truck_model'] . ')'); ?>
                        </option>
                    <?php endforeach; ?>
                </select>

                <label for="trip_route">Route:</label>
                <input type="text" name="trip_route" id="trip_route" required>

                <label for="distance">Distance (km):</label>
                <input type="number" name="distance" id="distance" step="0.01" min="0" required>

                <label for="fuel_consumed">Fuel Consumed (litres):</label>
                <input type="number" name="fuel_consumed" id="fuel_consumed" step="0.01" min="0" required>

                <label for="cargo_type">Cargo Type:</label>
                <select name="cargo_type" id="cargo_type" required>
                    <option value="">Select a cargo type</option>
                    <option value="Perishables">Perishables</option>
                    <option value="General Cargo">General Cargo</option>
                    <option value="Fuel">Fuel</option>
                    <option value="Bulk Materials">Bulk Materials</option>
                    <option value="Hazardous Materials">Hazardous Materials</option>
                    <option value="Livestock">Livestock</option>
                    <option value="Heavy Machinery">Heavy Machinery</option>
                    <option value="Refrigerated Goods">Refrigerated Goods</option>
                    <option value="Oversized Loads">Oversized Loads</option>
                    <option value="Containerized Cargo">Containerized Cargo</option>
                </select>

                <label for="cargo_weight">Cargo Weight (tonnes):</label>
                <input type="number" name="cargo_weight" id="cargo_weight" step="0.01" min="0" required>

                <label for="tolls_paid">Tolls Paid (Tsh):</label>
                <input type="number" name="tolls_paid" id="tolls_paid" step="0.01" min="0" required>

                <label for="other_expenses">Other Expenses (Tsh):</label>
                <input type="number" name="other_expenses" id="other_expenses" step="0.01" min="0" required>

                <label for="trip_status">Trip Status:</label>
                <select name="trip_status" id="trip_status" required>
                    <option value="">Select Status</option>
                    <option value="Planned">Planned</option>
                    <option value="In Transit">In Transit</option>
                    <option value="Completed">Completed</option>
                    <option value="Cancelled">Cancelled</option>
                </select>

                <div class="btn-container">
                    <button type="submit" class="btn btn-primary">Add Trip</button>
                    <a href="trips.php" class="btn btn-cancel">Cancel</a>
                </div>
            </form>
        <?php endif; ?>
    </div>

    <footer>
        © 2025 Fleet Maintenance Management System<br>
        Pope's Tr Headquarters | P.O. Box 1600 Dar es Salaam | Tanzania | Phone: +255781636843 | Email: info@popestr.com | Website: <a href="http://www.popestr.com" target="_blank">www.popestr.com</a>
    </footer>
</body>
</html>
<?php $conn->close(); ?>