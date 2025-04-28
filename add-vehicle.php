<?php
session_start();
require_once 'config.php'; // Database connection

// Fetch available drivers for the dropdown
$drivers = [];
$driver_query = "SELECT driver_id, driver_name FROM drivers";
$driver_result = $conn->query($driver_query);
if ($driver_result && $driver_result->num_rows > 0) {
    while ($row = $driver_result->fetch_assoc()) {
        $drivers[] = $row;
    }
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $truck_plate_number = $_POST['truck_plate_number'];
    $truck_make = $_POST['truck_make'];
    $truck_model = $_POST['truck_model'];
    $horsepower = $_POST['horsepower'];
    $trailer_make = $_POST['trailer_make'];
    $trailer_type = $_POST['trailer_type'];
    $trailer_plate_number = $_POST['trailer_plate_number'];
    $truck_mileage = $_POST['truck_mileage'];
    $next_service_mileage = $_POST['next_service_mileage'];
    $truck_status = $_POST['truck_status'];
    $driver_id = $_POST['driver_id'];

    $stmt = $conn->prepare("INSERT INTO vehicles (
        truck_plate_number, truck_make, truck_model, horsepower,
        trailer_make, trailer_type, trailer_plate_number,
        truck_mileage, next_service_mileage, truck_status, driver_id
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

    $stmt->bind_param(
        "ssssssssssi",
        $truck_plate_number, $truck_make, $truck_model, $horsepower,
        $trailer_make, $trailer_type, $trailer_plate_number,
        $truck_mileage, $next_service_mileage, $truck_status, $driver_id
    );

    if ($stmt->execute()) {
        header("Location: vehicles.php");
        exit;
    } else {
        echo "Error: " . $stmt->error;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add Vehicle</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background: #f4f6f8;
            margin: 0;
            padding: 0;
        }
        .form-container {
            max-width: 1000px;
            margin: 300px auto;
            padding: 25px;
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        h2 {
            margin-bottom: 20px;
            color: #2c3e50;
        }
        form {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 60px;
        }
        label {
            font-weight: 600;
        }
        input, select {
            padding: 8px;
            border: 1px solid #ccc;
            border-radius: 5px;
            width: 100%;
        }
        .full-width {
            grid-column: span 2;
        }
        button {
            padding: 12px 20px;
            border: none;
            border-radius: 5px;
            background: #28a745;
            color: white;
            font-weight: bold;
            cursor: pointer;
            margin-top: 10px;
        }
        button:hover {
            background: #218838;
        }
        .back-link {
            display: inline-block;
            margin-top: 20px;
            text-decoration: none;
            color: #007bff;
        }
        .back-link:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="form-container">
        <h2>Add New Vehicle</h2>
        <form method="POST">
            <div>
                <label>Truck Plate Number</label>
                <input type="text" name="truck_plate_number" required>
            </div>
            <div>
                <label>Truck Make</label>
                <input type="text" name="truck_make" required>
            </div>
            <div>
                <label>Truck Model</label>
                <input type="text" name="truck_model" required>
            </div>
            <div>
                <label>Horsepower</label>
                <input type="text" name="horsepower">
            </div>
            <div>
                <label>Trailer Make</label>
                <input type="text" name="trailer_make">
            </div>
           <div>
    <label>Trailer Type</label>
    <select name="trailer_type">
        <option value="">-- Select Trailer Type --</option>
        <optgroup label="Common Types">
            <option value="dry_van">Dry Van</option>
            <option value="flatbed">Flatbed</option>
            <option value="reefer">Reefer (Refrigerated)</option>
            <option value="step_deck">Step Deck</option>
            <option value="conestoga">Conestoga</option>
        </optgroup>
        <optgroup label="Specialized Types">
            <option value="lowboy">Lowboy</option>
            <option value="tanker">Tanker</option>
            <option value="dump">Dump</option>
            <option value="car_hauler">Car Hauler</option>
            <option value="livestock">Livestock</option>
            <option value="removable_gooseneck">Removable Gooseneck (RGN)</option>
        </optgroup>
        <optgroup label="Other">
             <option value="other">Other</option>
        </optgroup>
    </select>
</div>

            <div>
                <label>Trailer Plate Number</label>
                <input type="text" name="trailer_plate_number">
            </div>
            <div>
                <label>Truck Mileage</label>
                <input type="number" name="truck_mileage" required>
            </div>
            <div>
                <label>Next Service Mileage</label>
                <input type="number" name="next_service_mileage" required>
            </div>
            <div>
                <label>Status</label>
                <select name="truck_status" required>
                    <option value="Available">Available</option>
                    <option value="In Service">In Service</option>
                    <option value="Under Repair">Under Repair</option>
                    <option value="Inactive">Inactive</option>
                </select>
            </div>
            <div>
                <label>Assign Driver</label>
                <select name="driver_id" required>
                    <option value="">-- Select Driver --</option>
                    <?php foreach ($drivers as $driver): ?>
                        <option value="<?php echo $driver['driver_id']; ?>">
                            <?php echo htmlspecialchars($driver['driver_name']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="full-width">
                <button type="submit">Save Vehicle</button>
            </div>
        </form>
        <a href="vehicles.php" class="back-link">← Back to Vehicles</a>
    </div>
</body>
</html>
<?php $conn->close(); ?>
