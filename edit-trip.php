<?php
session_start();

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

// Initialize error variables
$truck_plate_number_err = "";
$driver_id_err = "";
$trip_date_err = "";
$trip_route_err = "";
$distance_err = "";
$fuel_consumed_err = "";
$cargo_type_err = "";
$cargo_weight_err = "";
$tolls_paid_err = "";
$other_expenses_err = "";
$trip_status_err = "";
$form_err = false;
$error_message = "";
$success_message = "";

// Define the cargo types array
$cargo_types = array(
    "General Goods",
    "Perishable Goods",
    "Hazardous Materials",
    "Liquid Bulk",
    "Dry Bulk",
    "Containerized Cargo",
    "Automobiles",
    "Livestock",
    "Construction Materials",
    "Other"
);

// Check if the trip ID is provided in the URL
if (isset($_GET['id']) && !empty($_GET['id'])) {
    $trip_id = get_input_data($_GET['id']);

    // Fetch the trip log record to populate the form
    $sql = "SELECT * FROM trip_logs WHERE trip_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $trip_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 0) {
        echo "Trip log record not found.";
        exit;
    }
    $trip = $result->fetch_assoc();
    $stmt->close();

    // Fetch truck plate numbers from the vehicles table
    $truck_query = "SELECT truck_plate_number FROM vehicles";
    $truck_result = mysqli_query($conn, $truck_query);
    $truck_plate_numbers = array();
    if ($truck_result && mysqli_num_rows($truck_result) > 0) {
        while ($row = mysqli_fetch_assoc($truck_result)) {
            $truck_plate_numbers[] = $row['truck_plate_number'];
        }
    } else {
        $truck_plate_number_err = "No trucks available.  Please add a truck first.";
        $form_err = true; // Set form error to true to disable submit
    }

    // Fetch driver IDs and names from the drivers table
    $driver_query = "SELECT driver_id, driver_name FROM drivers";
    $driver_result = mysqli_query($conn, $driver_query);
    $drivers = array();
    if ($driver_result && mysqli_num_rows($driver_result) > 0) {
        while ($row = mysqli_fetch_assoc($driver_result)) {
            $drivers[] = $row; // Store both id and name
        }
    } else {
        $driver_id_err = "No drivers available.  Please add a driver first.";
        $form_err = true; // Set form error to true to disable submit
    }
} else {
    echo "Trip ID is required.";
    exit;
}



// Processing form data when the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate truck plate number
    if (empty($_POST["truck_plate_number"])) {
        $truck_plate_number_err = "Truck Plate Number is required";
        $form_err = true;
    } else {
        $truck_plate_number = get_input_data($_POST["truck_plate_number"]);
    }

    // Validate driver ID
    if (empty($_POST["driver_id"])) {
        $driver_id_err = "Driver is required";
        $form_err = true;
    } else {
        $driver_id = get_input_data($_POST["driver_id"]);
    }

    // Validate trip date
    if (empty($_POST["trip_date"])) {
        $trip_date_err = "Trip Date is required";
        $form_err = true;
    } else {
        $trip_date = get_input_data($_POST["trip_date"]);
        // Basic date format validation (YYYY-MM-DD)
        if (!preg_match("/^\d{4}-\d{2}-\d{2}$/", $trip_date)) {
            $trip_date_err = "Invalid date format. Use YYYY-MM-DD";
            $form_err = true;
        }
    }

    // Validate trip route
    if (empty($_POST["trip_route"])) {
        $trip_route_err = "Trip Route is required";
        $form_err = true;
    } else {
        $trip_route = get_input_data($_POST["trip_route"]);
    }

    // Validate distance
    if (empty($_POST["distance"])) {
        $distance_err = "Distance is required";
        $form_err = true;
    } else {
        $distance = get_input_data($_POST["distance"]);
        if (!is_numeric($distance) || $distance < 0) {
            $distance_err = "Distance must be a non-negative number";
            $form_err = true;
        }
    }

    // Validate fuel consumed
    if (empty($_POST["fuel_consumed"])) {
        $fuel_consumed_err = "Fuel Consumed is required";
        $form_err = true;
    } else {
        $fuel_consumed = get_input_data($_POST["fuel_consumed"]);
        if (!is_numeric($fuel_consumed) || $fuel_consumed < 0) {
            $fuel_consumed_err = "Fuel Consumed must be a non-negative number";
            $form_err = true;
        }
    }

    // Validate cargo type
    if (empty($_POST["cargo_type"])) {
        $cargo_type_err = "Cargo Type is required";
        $form_err = true;
    } else {
        $cargo_type = get_input_data($_POST["cargo_type"]);
        if (!in_array($cargo_type, $cargo_types)) {
             $cargo_type_err = "Invalid Cargo Type";
             $form_err = true;
        }
    }

    // Validate cargo weight
    if (empty($_POST["cargo_weight"])) {
        $cargo_weight_err = "Cargo Weight is required";
        $form_err = true;
    } else {
        $cargo_weight = get_input_data($_POST["cargo_weight"]);
        if (!is_numeric($cargo_weight) || $cargo_weight < 0) {
            $cargo_weight_err = "Cargo Weight must be a non-negative number";
            $form_err = true;
        }
    }

    // Validate tolls paid
    if (empty($_POST["tolls_paid"])) {
        $tolls_paid_err = "Tolls Paid is required";
        $form_err = true;
    } else {
        $tolls_paid = get_input_data($_POST["tolls_paid"]);
        if (!is_numeric($tolls_paid) || $tolls_paid < 0) {
            $tolls_paid_err = "Tolls Paid must be a non-negative number";
            $form_err = true;
        }
    }

    // Validate other expenses
    if (empty($_POST["other_expenses"])) {
        $other_expenses_err = "Other Expenses is required";
        $form_err = true;
    } else {
        $other_expenses = get_input_data($_POST["other_expenses"]);
        if (!is_numeric($other_expenses) || $other_expenses < 0) {
            $other_expenses_err = "Other Expenses must be a non-negative number";
            $form_err = true;
        }
    }

    // Validate trip status
     if (empty($_POST["trip_status"])) {
        $trip_status_err = "Trip Status is required";
        $form_err = true;
    } else {
        $trip_status = get_input_data($_POST["trip_status"]);
        $valid_status = array("Planned", "In Transit", "Completed", "Cancelled");
        if (!in_array($trip_status, $valid_status)) {
            $trip_status_err = "Invalid trip status value";
            $form_err = true;
        }
    }


    // If there are no errors, proceed to update the data in the database
    if (!$form_err) {
        // Prepare the SQL statement
        $sql = "UPDATE trip_logs SET truck_plate_number = ?, driver_id = ?, trip_date = ?, trip_route = ?, distance = ?, fuel_consumed = ?, cargo_type = ?, cargo_weight = ?, tolls_paid = ?, other_expenses = ?, trip_status = ? WHERE trip_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sissssssssssi", $truck_plate_number, $driver_id, $trip_date, $trip_route, $distance, $fuel_consumed, $cargo_type, $cargo_weight, $tolls_paid, $other_expenses, $trip_status, $trip_id);

        // Attempt to execute the statement
        if ($stmt->execute()) {
            $success_message = "Trip log record updated successfully!";
            // Redirect to the trip logs page after successful update
            header("Location: trip_logs.php?msg=" . urlencode($success_message));
            exit();
        } else {
            $error_message = "Error updating record: " . $stmt->error;
        }

        // Close the statement
        $stmt->close();
    }
}

// Close the database connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Trip Log</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <style>
        .container {
            max-width: 800px;
            margin: auto;
        }
        .form-group {
            margin-bottom: 20px;
        }
        .error-message {
            color: red;
            margin-top: 5px;
        }
         .back-button {
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="container mt-5">
         <a href="trip_logs.php" class="btn btn-secondary back-button">
            <i class="fas fa-arrow-left"></i> Back to Trip Logs
        </a>
        <h2>Edit Trip Log</h2>

        <?php if (!empty($error_message)): ?>
            <div class="alert alert-danger"><?php echo $error_message; ?></div>
        <?php endif; ?>

        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"] . "?id=" . $trip_id); ?>" method="POST">
            <div class="form-group">
                <label for="truck_plate_number">Truck Plate Number:</label>
                <select class="form-control" id="truck_plate_number" name="truck_plate_number">
                    <option value="">Select Truck</option>
                    <?php foreach ($truck_plate_numbers as $number): ?>
                        <option value="<?php echo $number; ?>" <?php if ($trip['truck_plate_number'] == $number) echo "selected"; ?>><?php echo $number; ?></option>
                    <?php endforeach; ?>
                </select>
                <span class="error-message"><?php echo $truck_plate_number_err; ?></span>
            </div>
            <div class="form-group">
                <label for="driver_id">Driver:</label>
                <select class="form-control" id="driver_id" name="driver_id">
                    <option value="">Select Driver</option>
                    <?php foreach ($drivers as $driver): ?>
                        <option value="<?php echo $driver['driver_id']; ?>" <?php if ($trip['driver_id'] == $driver['driver_id']) echo "selected"; ?>><?php echo htmlspecialchars($driver['driver_name'] . " (ID: " . $driver['driver_id'] . ")"); ?></option>
                    <?php endforeach; ?>
                </select>
                <span class="error-message"><?php echo $driver_id_err; ?></span>
            </div>
            <div class="form-group">
                <label for="trip_date">Trip Date:</label>
                <input type="date" class="form-control" id="trip_date" name="trip_date" value="<?php echo htmlspecialchars($trip['trip_date']); ?>">
                <span class="error-message"><?php echo $trip_date_err; ?></span>
            </div>
            <div class="form-group">
                <label for="trip_route">Trip Route:</label>
                <input type="text" class="form-control" id="trip_route" name="trip_route" value="<?php echo htmlspecialchars($trip['trip_route']); ?>">
                <span class="error-message"><?php echo $trip_route_err; ?></span>
            </div>
            <div class="form-group">
                <label for="distance">Distance (km):</label>
                <input type="number" class="form-control" id="distance" name="distance" value="<?php echo htmlspecialchars($trip['distance']); ?>">
                <span class="error-message"><?php echo $distance_err; ?></span>
            </div>
            <div class="form-group">
                <label for="fuel_consumed">Fuel Consumed (litres):</label>
                <input type="number" class="form-control" id="fuel_consumed" name="fuel_consumed" value="<?php echo htmlspecialchars($trip['fuel_consumed']); ?>">
                <span class="error-message"><?php echo $fuel_consumed_err; ?></span>
            </div>
            <div class="form-group">
                <label for="cargo_type">Cargo Type:</label>
                <select class="form-control" id="cargo_type" name="cargo_type">
                    <option value="">Select Cargo Type</option>
                    <?php foreach ($cargo_types as $cargo_type_option): ?>
                        <option value="<?php echo $cargo_type_option; ?>" <?php if ($trip['cargo_type'] == $cargo_type_option) echo "selected"; ?>><?php echo $cargo_type_option; ?></option>
                    <?php endforeach; ?>
                </select>
                <span class="error-message"><?php echo $cargo_type_err; ?></span>
            </div>
            <div class="form-group">
                <label for="cargo_weight">Cargo Weight (tonnes):</label>
                <input type="number" class="form-control" id="cargo_weight" name="cargo_weight" value="<?php echo htmlspecialchars($trip['cargo_weight']); ?>">
                <span class="error-message"><?php echo $cargo_weight_err; ?></span>
            </div>
            <div class="form-group">
                <label for="tolls_paid">Tolls Paid (Tsh):</label>
                <input type="number" class="form-control" id="tolls_paid" name="tolls_paid" value="<?php echo htmlspecialchars($trip['tolls_paid']); ?>">
                <span class="error-message"><?php echo $tolls_paid_err; ?></span>
            </div>
            <div class="form-group">
                <label for="other_expenses">Other Expenses (Tsh):</label>
                <input type="number" class="form-control" id="other_expenses" name="other_expenses" value="<?php echo htmlspecialchars($trip['other_expenses']); ?>">
                <span class="error-message"><?php echo $other_expenses_err; ?></span>
            </div>
            <div class="form-group">
                <label for="trip_status">Trip Status:</label>
                <select class="form-control" id="trip_status" name="trip_status">
                    <option value="" <?php if (empty($trip_status)) echo "selected"; ?>>Select Status</option>
                    <option value="Planned" <?php if ($trip['trip_status'] == "Planned") echo "selected"; ?>>Planned</option>
                    <option value="In Transit" <?php if ($trip['trip_status'] == "In Transit") echo "selected"; ?>>In Transit</option>
                    <option value="Completed" <?php if ($trip['trip_status'] == "Completed") echo "selected"; ?>>Completed</option>
                    <option value="Cancelled" <?php if ($trip['trip_status'] == "Cancelled") echo "selected"; ?>>Cancelled</option>
                </select>
                <span class="error-message"><?php echo $trip_status_err; ?></span>
            </div>
            <button type="submit" class="btn btn-primary" <?php if ($form_err) echo "disabled"; ?>>Update Record</button>
            <a href="trip_logs.php" class="btn btn-secondary">Cancel</a>
        </form>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
