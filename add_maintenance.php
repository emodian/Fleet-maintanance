<?php
// Include the database connection file
$conn = new mysqli("localhost", "root", "", "fleet_management");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Function to safely get input data
function get_input_data($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

$error_message = "";
$success_message = "";

// Fetch all truck plate numbers for the dropdown
$trucks = [];
$sql_trucks = "SELECT truck_plate_number FROM vehicles ORDER BY truck_plate_number ASC";
$result_trucks = $conn->query($sql_trucks);
if ($result_trucks && $result_trucks->num_rows > 0) {
    while ($row = $result_trucks->fetch_assoc()) {
        $trucks[] = $row['truck_plate_number'];
    }
}

// Processing form data when the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $truck_plate_number = get_input_data($_POST["truck_plate_number"]);
    $maintenance_type = get_input_data($_POST["maintenance_type"]);
    $date = get_input_data($_POST["date"]);
    $cost = get_input_data($_POST["cost"]);
    $status = "Pending"; // Default status for new tasks

    // Basic validation
    if (empty($truck_plate_number) || empty($maintenance_type) || empty($date) || !is_numeric($cost)) {
        $error_message = "Please fill in all required fields correctly.";
    } else {
        // Prepare and bind the SQL statement
        $stmt = $conn->prepare("INSERT INTO maintenance (truck_plate_number, maintenance_type, date, cost, status) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sssis", $truck_plate_number, $maintenance_type, $date, $cost, $status);

        if ($stmt->execute()) {
            $success_message = "New maintenance task scheduled successfully!";
        } else {
            $error_message = "Error scheduling maintenance: " . $stmt->error;
        }

        $stmt->close();
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add New Maintenance Record</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .container {
            max-width: 600px;
            margin: auto;
            padding-top: 20px;
        }
        .form-group {
            margin-bottom: 20px;
        }
        .error-message {
            color: red;
            margin-top: 5px;
            font-size: 0.9em;
        }
        .back-button {
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="container mt-5">
        <a href="maintenance.php" class="btn btn-secondary back-button">
            <i class="fas fa-arrow-left"></i> Back to Maintenance
        </a>
        <h2 class="mb-4">Add New Maintenance Record</h2>

        <?php if (!empty($error_message)): ?>
            <div class="alert alert-danger"><?php echo $error_message; ?></div>
        <?php endif; ?>
        <?php if (!empty($success_message)): ?>
            <div class="alert alert-success"><?php echo $success_message; ?></div>
        <?php endif; ?>

        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST">
            <div class="form-group">
                <label for="truck_plate_number">Truck Plate Number:</label>
                <select class="form-control" id="truck_plate_number" name="truck_plate_number" required>
                    <option value="">-- Select Truck --</option>
                    <?php foreach ($trucks as $plate): ?>
                        <option value="<?php echo htmlspecialchars($plate); ?>"><?php echo htmlspecialchars($plate); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label for="maintenance_type">Maintenance Type:</label>
                <input type="text" class="form-control" id="maintenance_type" name="maintenance_type" required>
            </div>
            <div class="form-group">
                <label for="date">Scheduled Date:</label>
                <input type="date" class="form-control" id="date" name="date" required value="<?php echo date('Y-m-d'); ?>">
            </div>
            <div class="form-group">
                <label for="cost">Estimated Cost:</label>
                <input type="number" class="form-control" id="cost" name="cost" step="0.01" required value="0.00">
            </div>
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-plus-circle"></i> Add Record
            </button>
        </form>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>