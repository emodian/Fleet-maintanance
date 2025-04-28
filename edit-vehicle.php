<?php
session_start();
require_once 'config.php';

if (!isset($conn) || $conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

$truck_plate_number = $_GET['id'] ?? '';
$truck_plate_number = $conn->real_escape_string($truck_plate_number);

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $truck_make = $conn->real_escape_string($_POST['truck_make']);
    $truck_model = $conn->real_escape_string($_POST['truck_model']);
    $horsepower = intval($_POST['horsepower']);
    $trailer_make = $conn->real_escape_string($_POST['trailer_make']);
    $trailer_type = $conn->real_escape_string($_POST['trailer_type']);
    $trailer_plate_number = $conn->real_escape_string($_POST['trailer_plate_number']);
    $truck_mileage = intval($_POST['truck_mileage']);
    $next_service_mileage = intval($_POST['next_service_mileage']);
    $truck_status = $conn->real_escape_string($_POST['truck_status']);
    $driver_id = intval($_POST['driver_id']);

    $update_sql = "UPDATE vehicles SET 
        truck_make='$truck_make',
        truck_model='$truck_model',
        horsepower=$horsepower,
        trailer_make='$trailer_make',
        trailer_type='$trailer_type',
        trailer_plate_number='$trailer_plate_number',
        truck_mileage=$truck_mileage,
        next_service_mileage=$next_service_mileage,
        truck_status='$truck_status',
        driver_id=$driver_id
        WHERE truck_plate_number='$truck_plate_number'";

    if ($conn->query($update_sql)) {
        echo "<script>alert('Vehicle updated successfully.'); window.location.href='vehicles.php';</script>";
        exit;
    } else {
        echo "Error updating vehicle: " . $conn->error;
    }
}

// Fetch current vehicle details
$sql = "SELECT * FROM vehicles WHERE truck_plate_number = '$truck_plate_number'";
$result = $conn->query($sql);
$vehicle = $result->fetch_assoc();

// Get drivers for dropdown
$drivers = [];
$dResult = $conn->query("SELECT driver_id, driver_name FROM drivers");
if ($dResult) {
    while ($dRow = $dResult->fetch_assoc()) {
        $drivers[] = $dRow;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Vehicle</title>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f3f4f6;
            margin: 0;
            padding: 0;
        }

        .container {
            max-width: 800px;
            margin: 30px auto;
            padding: 30px;
            background-color: #fff;
            border-radius: 12px;
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.1);
        }

        h2 {
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

        label {
            font-weight: 600;
            display: block;
            margin-top: 15px;
        }

        input, select {
            width: 100%;
            padding: 10px;
            margin-top: 6px;
            border: 1px solid #ccc;
            border-radius: 6px;
            font-size: 14px;
        }

        .btn-save {
            background-color: #28a745;
            color: white;
            padding: 12px 18px;
            margin-top: 25px;
            border: none;
            border-radius: 6px;
            font-weight: 600;
            cursor: pointer;
        }

        .btn-save:hover {
            background-color: #218838;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2><span class="material-icons back-arrow" onclick="window.location.href='vehicles.php'">arrow_back</span>Edit Vehicle</h2>

        <?php if ($vehicle): ?>
        <form method="post">
            <label>Truck Make</label>
            <input type="text" name="truck_make" value="<?php echo htmlspecialchars($vehicle['truck_make']); ?>" required>

            <label>Truck Model</label>
            <input type="text" name="truck_model" value="<?php echo htmlspecialchars($vehicle['truck_model']); ?>" required>

            <label>Horsepower</label>
            <input type="number" name="horsepower" value="<?php echo htmlspecialchars($vehicle['horsepower']); ?>">

            <label>Trailer Make</label>
            <input type="text" name="trailer_make" value="<?php echo htmlspecialchars($vehicle['trailer_make']); ?>">

            <label>Trailer Type</label>
            <select name="trailer_type">
                <?php
                    $types = ['flatbed', 'refrigerated', 'lowboy', 'tanker', 'dump', 'car haulers', 'extendable'];
                    foreach ($types as $type) {
                        $selected = ($vehicle['trailer_type'] === $type) ? 'selected' : '';
                        echo "<option value=\"$type\" $selected>$type</option>";
                    }
                ?>
            </select>

            <label>Trailer Plate Number</label>
            <input type="text" name="trailer_plate_number" value="<?php echo htmlspecialchars($vehicle['trailer_plate_number']); ?>">

            <label>Truck Mileage</label>
            <input type="number" name="truck_mileage" value="<?php echo htmlspecialchars($vehicle['truck_mileage']); ?>" required>

            <label>Next Service Mileage</label>
            <input type="number" name="next_service_mileage" value="<?php echo htmlspecialchars($vehicle['next_service_mileage']); ?>" required>

            <label>Status</label>
            <select name="truck_status">
                <?php
                    $statuses = ['active', 'on route', 'in maintenance'];
                    foreach ($statuses as $status) {
                        $selected = ($vehicle['truck_status'] === $status) ? 'selected' : '';
                        echo "<option value=\"$status\" $selected>$status</option>";
                    }
                ?>
            </select>

            <label>Driver</label>
            <select name="driver_id">
                <?php
                    foreach ($drivers as $driver) {
                        $selected = ($vehicle['driver_id'] == $driver['driver_id']) ? 'selected' : '';
                        echo "<option value=\"{$driver['driver_id']}\" $selected>{$driver['driver_name']} (ID: {$driver['driver_id']})</option>";
                    }
                ?>
            </select>

            <button type="submit" class="btn-save">Save Changes</button>
        </form>
        <?php else: ?>
            <p>Vehicle not found.</p>
        <?php endif; ?>
    </div>
</body>
</html>
<?php $conn->close(); ?>
