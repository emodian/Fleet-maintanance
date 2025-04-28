<?php
session_start();

require_once 'config.php';

$user_role = $_SESSION['user_role'] ?? '';
$username = $_SESSION['username'] ?? '';

if (!isset($conn) || $conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

$message = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $driver_name = trim($_POST['driver_name']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $license_number = trim($_POST['license_number']);
    $truck_plate_number = trim($_POST['truck_plate_number']); // This is directly from the form
    $status = trim($_POST['status']);

    // Use a prepared statement to prevent SQL injection
    $stmt = $conn->prepare("INSERT INTO drivers (driver_name, email, phone, license_number, truck_plate_number, status) VALUES (?, ?, ?, ?, ?, ?)");
    if (!$stmt) {
        die("Error preparing statement: " . $conn->error);
    }
    $stmt->bind_param("ssssss", $driver_name, $email, $phone, $license_number, $truck_plate_number, $status);

    if ($stmt->execute()) {
        // Update the vehicle's driver_id after successful driver creation
        $driver_id = $stmt->insert_id;
        if (!empty($truck_plate_number)) { // Only update if a truck plate number was entered
            $update_stmt = $conn->prepare("UPDATE vehicles SET driver_id = ? WHERE truck_plate_number = ?");
            if (!$update_stmt) {
                die("Error preparing update statement: " . $conn->error);
            }
            $update_stmt->bind_param("is", $driver_id, $truck_plate_number);
            $update_result = $update_stmt->execute(); //execute the update

            if (!$update_result) {
                $message = "Driver Added, but Vehicle Update Failed: " . $update_stmt->error;
            }
            $update_stmt->close();
        }
        header("Location: drivers.php?success=1");
        exit;
    } else {
        $message = "Error: " . $stmt->error;
    }
    $stmt->close();
}

// Fetch truck plates from database,  Corrected query.  We don't actually use this to populate the form anymore.
$truck_plates = [];
$result = $conn->query("SELECT truck_plate_number, make, model, year FROM vehicles WHERE driver_id IS NULL");
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $truck_plates[$row['truck_plate_number']] = [
            'make' => $row['make'],
            'model' => $row['model'],
            'year' => $row['year']
        ];
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add Driver | Fleet Management System</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f3f4f6;
            margin: 0;
            padding: 0;
        }

        .container {
            max-width: 1000px;
            margin: 40px auto;
            background-color: #fff;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 6px 10px rgba(0,0,0,0.1);
        }

        h1 {
            color: #2c3e50;
            display: flex;
            align-items: center;
            font-size: 1.8rem;
            margin-bottom: 30px;
        }

        .material-icons.back-icon {
            cursor: pointer;
            margin-right: 10px;
            color: #3498db;
        }

        form {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 30px;
        }

        form label {
            font-weight: 600;
            margin-bottom: 5px;
            display: block;
            color: #333;
        }

        form input, form select {
            padding: 12px;
            border: 1px solid #ccc;
            border-radius: 6px;
            width: 100%;
            transition: border-color 0.3s;
        }

        form input:focus, form select:focus {
            border-color: #3498db;
            outline: none;
        }

        .form-footer {
            grid-column: span 2;
            display: flex;
            justify-content: space-between;
            margin-top: 30px;
        }

        .btn {
            padding: 12px 20px;
            border: none;
            border-radius: 6px;
            font-weight: 600;
            cursor: pointer;
            transition: background-color 0.3s, transform 0.2s;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .btn-submit {
            background-color: #28a745;
            color: white;
        }

        .btn-submit:hover {
            background-color: #218838;
            transform: translateY(-2px);
        }

        .btn-cancel {
            background-color: #dc3545;
            color: white;
            text-decoration: none;
        }

        .btn-cancel:hover {
            background-color: #c82333;
            transform: translateY(-2px);
        }

        .message {
            margin-bottom: 20px;
            color: #d9534f;
            font-weight: 600;
        }

        .vehicle-info {
            grid-column: span 2;
            background-color: #f8f9fa;
            padding: 15px;
            border-radius: 6px;
            border-left: 4px solid #3498db;
            display: none;
        }

        .vehicle-info h3 {
            margin-top: 0;
            color: #2c3e50;
        }

        .vehicle-details {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 10px;
        }

        .vehicle-detail {
            margin-bottom: 5px;
        }

        .vehicle-detail strong {
            display: block;
            color: #666;
            font-size: 0.9rem;
        }

        .vehicle-detail span {
            font-weight: 600;
            color: #333;
        }

        @media (max-width: 768px) {
            form {
                grid-template-columns: 1fr;
            }
            .form-footer {
                flex-direction: column;
                gap: 10px;
            }
            .vehicle-details {
                grid-template-columns: 1fr;
            }
        }

        footer {
            text-align: center;
            font-size: 0.9rem;
            color: #666;
            padding: 20px 10px 10px;
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
    <div class="container">
        <h1>
            <span class="material-icons back-icon" onclick="window.location.href='vehicles.php'">arrow_back</span>
            Add New Driver
        </h1>

        <?php if (!empty($message)): ?>
            <div class="message"><?php echo htmlspecialchars($message); ?></div>
        <?php endif; ?>

        <form method="POST">
            <div>
                <label for="driver_name">Driver Name</label>
                <input type="text" id="driver_name" name="driver_name" required>
            </div>
            <div>
                <label for="email">Email</label>
                <input type="email" id="email" name="email" required>
            </div>
            <div>
                <label for="phone">Phone Number</label>
                <input type="text" id="phone" name="phone" required>
            </div>
            <div>
                <label for="license_number">License Number</label>
                <input type="text" id="license_number" name="license_number" required>
            </div>
            <div>
                <label for="truck_plate_number">Truck Plate Number</label>
                <input type="text" id="truck_plate_number" name="truck_plate_number">
            </div>
            <div>
                <label for="status">Driver Status</label>
                <select name="status" id="status" required>
                    <option value="idle" selected>Idle</option>
                    <option value="active">Active</option>
                    <option value="on route">On Route</option>
                    <option value="on leave">On Leave</option>
                </select>
            </div>

            <div class="form-footer">
                <button type="submit" class="btn btn-submit"><span class="material-icons">check_circle</span> Add Driver</button>
                <a href="drivers.php" class="btn btn-cancel"><span class="material-icons">cancel</span> Cancel</a>
            </div>
        </form>
    </div>

    <footer>
        &copy; 2025 Fleet Maintenance Management System<br>
        Pope's Tr Headquarters | P.O.Box 1600 Dar es Salaam | Tanzania<br>
        Phone: +255781636843 | Email: <a href="mailto:info@popestr.com">info@popestr.com</a> | Website: <a href="http://www.popestr.com" target="_blank">www.popestr.com</a>
    </footer>

</body>
</html>

<?php $conn->close(); ?>
