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

    // Fetch the maintenance record to get the current vehicle and mechanic
    $sql = "SELECT m.id, v.truck_plate_number, m.maintenance_type, m.date, m.status, u.username AS current_mechanic
            FROM maintenance m
            JOIN vehicles v ON m.truck_plate_number = v.truck_plate_number
            LEFT JOIN users u ON m.mechanic_username = u.username
            WHERE m.id = ?";
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

    // Fetch available mechanics usernames from the users table where role is mechanic
    $mechanic_query = "SELECT username FROM users WHERE role = 'mechanic'";
    $mechanic_result = mysqli_query($conn, $mechanic_query);
    $mechanic_usernames = array();
    if ($mechanic_result && mysqli_num_rows($mechanic_result) > 0) {
        while ($row = mysqli_fetch_assoc($mechanic_result)) {
            $mechanic_usernames[] = $row['username'];
        }
    } else {
        $mechanic_username_err = "No mechanics found.  Please add a mechanic user first.";
        $form_err = true; // Set form error.
    }
} else {
    echo "Maintenance ID is required.";
    exit;
}

$error_message = "";
$success_message = "";
$mechanic_username_err = ""; // Define the variable here to avoid the warning
$form_err = false;

// Processing form data when the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate mechanic username
    if (empty($_POST["mechanic_username"])) {
        $error_message = "Mechanic Username is required";
        $mechanic_username_err = "Mechanic Username is required"; // Also set it here for the form
    } else {
        $mechanic_username = get_input_data($_POST["mechanic_username"]);

        // Prepare the SQL statement to update the mechanic_username
        $sql = "UPDATE maintenance SET mechanic_username = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("si", $mechanic_username, $maintenance_id);

        // Attempt to execute the statement
        if ($stmt->execute()) {
            $success_message = "Mechanic assigned successfully!";
            // Redirect to the maintenance list page after successful update
            header("Location: maintenance.php?msg=" . urlencode($success_message));
            exit();
        } else {
            $error_message = "Error assigning mechanic: " . $stmt->error;
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
    <title>Assign Mechanic</title>
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
        <h2>Assign Mechanic</h2>

        <?php if (!empty($error_message)): ?>
            <div class="alert alert-danger"><?php echo $error_message; ?></div>
        <?php endif; ?>

        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"] . "?id=" . $maintenance_id); ?>" method="POST">
            <div class="form-group">
                <label for="truck_plate_number">Truck Plate Number:</label>
                <input type="text" class="form-control" id="truck_plate_number" name="truck_plate_number" value="<?php echo htmlspecialchars($maintenance['truck_plate_number']); ?>" readonly>
            </div>
            <div class="form-group">
                <label for="maintenance_type">Maintenance Type:</label>
                <input type="text" class="form-control" id="maintenance_type" name="maintenance_type" value="<?php echo htmlspecialchars($maintenance['maintenance_type']); ?>" readonly>
            </div>
            <div class="form-group">
                <label for="date">Date:</label>
                <input type="text" class="form-control" id="date" name="date" value="<?php echo htmlspecialchars($maintenance['date']); ?>" readonly>
            </div>
             <div class="form-group">
                <label for="status">Status:</label>
                <input type="text" class="form-control" id="status" name="status" value="<?php echo htmlspecialchars($maintenance['status']); ?>" readonly>
            </div>
            <div class="form-group">
                <label for="current_mechanic">Current Mechanic:</label>
                <input type="text" class="form-control" id="current_mechanic" name="current_mechanic" value="<?php echo htmlspecialchars($maintenance['current_mechanic'] ?? 'Unassigned'); ?>" readonly>
            </div>
            <div class="form-group">
                <label for="mechanic_username">Mechanic Username:</label>
                <select class="form-control" id="mechanic_username" name="mechanic_username">
                    <?php if (empty($mechanic_usernames)): ?>
                        <option value="" disabled>No mechanics available</option>
                    <?php else: ?>
                        <option value="">Select Mechanic</option>
                        <?php foreach ($mechanic_usernames as $username): ?>
                            <option value="<?php echo $username; ?>"><?php echo $username; ?></option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>
                <span class="error-message"><?php echo $mechanic_username_err; ?></span>
            </div>
            <button type="submit" class="btn btn-primary">Assign Mechanic</button>
            <a href="maintenance.php" class="btn btn-secondary">Cancel</a>
        </form>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
