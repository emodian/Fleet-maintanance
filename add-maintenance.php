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

// Define variables and set to empty values
$truck_plate_number = $maintenance_type = $date = $status = $mechanic_username = "";
$truck_plate_number_err = $maintenance_type_err = $date_err = $status_err = $mechanic_username_err = "";
$form_err = false;
$success_message = ""; // Added success message variable

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

// Fetch maintenance types (you might have these in a separate table, or you can define them as an array)
$maintenance_types = array("Regular Service", "Oil Change", "Tire Rotation", "Engine Repair", "Brake Service", "Other"); // Added "Other"
$default_maintenance_type = "Regular Service";

// Fetch mechanics usernames from the users table where role is mechanic
$mechanic_query = "SELECT username FROM users WHERE role = 'mechanic'";
$mechanic_result = mysqli_query($conn, $mechanic_query);
$mechanic_usernames = array();
if ($mechanic_result && mysqli_num_rows($mechanic_result) > 0) {
    while ($row = mysqli_fetch_assoc($mechanic_result)) {
        $mechanic_usernames[] = $row['username'];
    }
} else {
    $mechanic_username_err = "No mechanics found.  Please add a mechanic user first.";
    $form_err = true; // Set form error to true to disable submit
}
$default_mechanic_username = "";

// Processing form data when the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Validate truck plate number
    if (empty($_POST["truck_plate_number"])) {
        $truck_plate_number_err = "Truck Plate Number is required";
        $form_err = true;
    } else {
        $truck_plate_number = get_input_data($_POST["truck_plate_number"]);
    }

    // Validate maintenance type
    if (empty($_POST["maintenance_type"])) {
        $maintenance_type_err = "Maintenance Type is required";
        $form_err = true;
    } else {
        $maintenance_type = get_input_data($_POST["maintenance_type"]);
    }

    // Validate date
    if (empty($_POST["date"])) {
        $date_err = "Date is required";
        $form_err = true;
    } else {
        $date = get_input_data($_POST["date"]);
        // Basic date format validation (YYYY-MM-DD)
        if (!preg_match("/^\d{4}-\d{2}-\d{2}$/", $date)) {
            $date_err = "Invalid date format. Use YYYY-MM-DD";
            $form_err = true;
        }
    }

    // Validate status
    if (empty($_POST["status"])) {
        $status_err = "Status is required";
        $form_err = true;
    } else {
        $status = get_input_data($_POST["status"]);
        //check if the status is a valid status
        $valid_status = array("Pending", "In Progress", "Completed", "Cancelled");
        if (!in_array($status, $valid_status)) {
            $status_err = "Invalid status value";
            $form_err = true;
        }
    }

    // Validate mechanic username.
    if (empty($_POST["mechanic_username"])) {
        $mechanic_username_err = "Mechanic Username is required";
        $form_err = true;
    } else {
        $mechanic_username = get_input_data($_POST["mechanic_username"]);
        // No need to check against the database again, dropdown ensures validity
    }

    // If there are no errors, proceed to insert the data into the database
    if (!$form_err) {
        // Prepare the SQL statement
        $sql = "INSERT INTO maintenance (truck_plate_number, maintenance_type, date, status, mechanic_username)
                VALUES (?, ?, ?, ?, ?)";

        if ($stmt = mysqli_prepare($conn, $sql)) {
            // Bind the parameters
            mysqli_stmt_bind_param($stmt, "sssss", $truck_plate_number, $maintenance_type, $date, $status, $mechanic_username);

            // Attempt to execute the statement
            if (mysqli_stmt_execute($stmt)) {
                // Set success message
                $success_message = "Record added successfully!";
                // Redirect to the maintenance list page after successful insertion
                header("Location: maintenance.php?msg=" . urlencode($success_message)); // Corrected redirect
                exit();
            } else {
                echo "Error: " . mysqli_error($conn);
            }

            // Close the statement
            mysqli_stmt_close($stmt);
        } else {
            echo "Error preparing statement: " . mysqli_error($conn);
        }
    }

    // Close the database connection
    mysqli_close($conn);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Maintenance Record</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
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
        .success-message {
            color: green;
            font-weight: bold;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="container mt-5">
        <a href="maintenance.php" class="btn btn-secondary back-button">
            <i class="fas fa-arrow-left"></i> Back to Maintenance
        </a>
        <h2>Add New Maintenance Record</h2>
        <?php if (!empty($success_message)): ?>
            <div class="alert alert-success"><?php echo $success_message; ?></div>
        <?php endif; ?>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST">
            <div class="form-group">
                <label for="truck_plate_number">Truck Plate Number:</label>
                <select class="form-control" id="truck_plate_number" name="truck_plate_number">
                    <?php if (empty($truck_plate_numbers)): ?>
                        <option value="" disabled>No trucks available</option>
                    <?php else: ?>
                        <option value="">Select Truck</option>
                        <?php foreach ($truck_plate_numbers as $number): ?>
                            <option value="<?php echo $number; ?>" <?php if ($truck_plate_number == $number) echo "selected"; ?>><?php echo $number; ?></option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>
                <span class="error-message"><?php echo $truck_plate_number_err; ?></span>
            </div>
            <div class="form-group">
                <label for="maintenance_type">Maintenance Type:</label>
                <select class="form-control" id="maintenance_type" name="maintenance_type">
                    <option value="">Select Maintenance Type</option>
                    <?php foreach ($maintenance_types as $m_type): ?>
                        <option value="<?php echo $m_type; ?>" <?php if ($maintenance_type == $m_type) echo "selected"; ?>><?php echo $m_type; ?></option>
                    <?php endforeach; ?>
                </select>
                <span class="error-message"><?php echo $maintenance_type_err; ?></span>
            </div>
            <div class="form-group">
                <label for="date">Date:</label>
                <input type="date" class="form-control" id="date" name="date" value="<?php echo $date; ?>">
                <span class="error-message"><?php echo $date_err; ?></span>
            </div>
            <div class="form-group">
                <label for="status">Status:</label>
                <select class="form-control" id="status" name="status">
                    <option value="" <?php if (empty($status)) echo "selected"; ?>>Select Status</option>
                    <option value="Pending" <?php if ($status == "Pending") echo "selected"; ?>>Pending</option>
                    <option value="In Progress" <?php if ($status == "In Progress") echo "selected"; ?>>In Progress</option>
                    <option value="Completed" <?php if ($status == "Completed") echo "selected"; ?>>Completed</option>
                    <option value="Cancelled" <?php if ($status == "Cancelled") echo "selected"; ?>>Cancelled</option>
                </select>
                <span class="error-message"><?php echo $status_err; ?></span>
            </div>
            <div class="form-group">
                <label for="mechanic_username">Mechanic Username:</label>
                <select class="form-control" id="mechanic_username" name="mechanic_username">
                    <?php if (empty($mechanic_usernames)): ?>
                        <option value="" disabled>No mechanics available</option>
                    <?php else: ?>
                        <option value="">Select Mechanic</option>
                        <?php foreach ($mechanic_usernames as $username): ?>
                            <option value="<?php echo $username; ?>" <?php if ($mechanic_username == $username) echo "selected"; ?>><?php echo $username; ?></option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>
                <span class="error-message"><?php echo $mechanic_username_err; ?></span>
            </div>
            <button type="submit" class="btn btn-primary" <?php if ($form_err) echo "disabled"; ?>>Add Record</button>
            <a href="maintenance.php" class="btn btn-secondary">Cancel</a>
        </form>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
