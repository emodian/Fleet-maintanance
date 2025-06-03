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

// Check if the maintenance ID is provided in the URL
if (isset($_GET['id']) && !empty($_GET['id'])) {
    $maintenance_id = get_input_data($_GET['id']);

    // Fetch the maintenance record to populate the form
    $sql = "SELECT m.maintenance_id, v.truck_plate_number, m.maintenance_type, m.date, m.status, u.username AS mechanic_username
            FROM maintenance m
            JOIN vehicles v ON m.truck_plate_number = v.truck_plate_number
            LEFT JOIN users u ON m.mechanic_username = u.username
            WHERE m.maintenance_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $maintenance_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 0) {
        echo "Maintenance record not found.";
        exit;
    }
    $maintenance = $result->fetch_assoc();
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

    // Fetch maintenance types
    $maintenance_types = array("Regular Service", "Oil Change", "Tire Rotation", "Engine Repair", "Brake Service", "Other");
   

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

} else {
    echo "Maintenance ID is required.";
    exit;
}

$error_message = "";
$success_message = "";
$form_err = false;

// Define error variables
$truck_plate_number_err = "";
$maintenance_type_err = "";
$date_err = "";
$status_err = "";
$mechanic_username_err = "";


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
            $date_err = "Invalid date format. Use犃MM-DD";
            $form_err = true;
        }
    }

    // Validate status
    if (empty($_POST["status"])) {
        $status_err = "Status is required";
        $form_err = true;
    } else {
        $status = get_input_data($_POST["status"]);
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


    // If there are no errors, proceed to update the data in the database
    if (!$form_err) {
        // Prepare the SQL statement
        $sql = "UPDATE maintenance SET truck_plate_number = ?, maintenance_type = ?, date = ?, status = ?, mechanic_username = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssssi", $truck_plate_number, $maintenance_type, $date, $status, $mechanic_username, $maintenance_id);

        // Attempt to execute the statement
        if ($stmt->execute()) {
            $success_message = "Record updated successfully!";
            // Redirect to the maintenance list page after successful update
            header("Location: maintenance.php?msg=" . urlencode($success_message));
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
    <title>Edit Maintenance Record</title>
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
    </style>
</head>
<body>
    <div class="container mt-5">
         <a href="maintenance.php" class="btn btn-secondary back-button">
            <i class="fas fa-arrow-left"></i> Back to Maintenance
        </a>
        <h2>Edit Maintenance Record</h2>

        <?php if (!empty($error_message)): ?>
            <div class="alert alert-danger"><?php echo $error_message; ?></div>
        <?php endif; ?>

        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"] . "?id=" . $maintenance_id); ?>" method="POST">
            <div class="form-group">
                <label for="truck_plate_number">Truck Plate Number:</label>
                <select class="form-control" id="truck_plate_number" name="truck_plate_number">
                    <option value="">Select Truck</option>
                    <?php foreach ($truck_plate_numbers as $number): ?>
                        <option value="<?php echo $number; ?>" <?php if ($maintenance['truck_plate_number'] == $number) echo "selected"; ?>><?php echo $number; ?></option>
                    <?php endforeach; ?>
                </select>
                <span class="error-message"><?php echo $truck_plate_number_err; ?></span>
            </div>
            <div class="form-group">
                <label for="maintenance_type">Maintenance Type:</label>
                <select class="form-control" id="maintenance_type" name="maintenance_type">
                    <option value="">Select Maintenance Type</option>
                    <?php foreach ($maintenance_types as $m_type): ?>
                        <option value="<?php echo $m_type; ?>" <?php if ($maintenance['maintenance_type'] == $m_type) echo "selected"; ?>><?php echo $m_type; ?></option>
                    <?php endforeach; ?>
                </select>
                <span class="error-message"><?php echo $maintenance_type_err; ?></span>
            </div>
            <div class="form-group">
                <label for="date">Date:</label>
                <input type="date" class="form-control" id="date" name="date" value="<?php echo htmlspecialchars($maintenance['date']); ?>">
                <span class="error-message"><?php echo $date_err; ?></span>
            </div>
            <div class="form-group">
                <label for="status">Status:</label>
                <select class="form-control" id="status" name="status">
                    <option value="" <?php if (empty($status)) echo "selected"; ?>>Select Status</option>
                    <option value="Pending" <?php if ($maintenance['status'] == "Pending") echo "selected"; ?>>Pending</option>
                    <option value="In Progress" <?php if ($maintenance['status'] == "In Progress") echo "selected"; ?>>In Progress</option>
                    <option value="Completed" <?php if ($maintenance['status'] == "Completed") echo "selected"; ?>>Completed</option>
                    <option value="Cancelled" <?php if ($maintenance['status'] == "Cancelled") echo "selected"; ?>>Cancelled</option>
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
                            <option value="<?php echo $username; ?>" <?php if ($maintenance['mechanic_username'] == $username) echo "selected"; ?>><?php echo $username; ?></option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>
                <span class="error-message"><?php echo $mechanic_username_err; ?></span>
            </div>
            <button type="submit" class="btn btn-primary" <?php if ($form_err) echo "disabled"; ?>>Update Record</button>
            <a href="maintenance.php" class="btn btn-secondary">Cancel</a>
        </form>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
