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
$form_err = false;

// Define error variables
$selected_maintenance_id_err = "";
$task_details_err = "";
$recommendations_err = ""; // Not strictly an error, but for consistency
$completion_date_err = "";
$mechanic_username_err = ""; // Initialize for mechanic username error

// Fetch active maintenance tasks (Pending or In Progress) for the dropdown
$active_maintenance_tasks = [];
$sql_active_tasks = "SELECT maintenance_id, truck_plate_number, maintenance_type, date
                     FROM maintenance
                     WHERE status IN ('Pending', 'In Progress')
                     ORDER BY date ASC";
$result_active_tasks = $conn->query($sql_active_tasks);

if ($result_active_tasks && $result_active_tasks->num_rows > 0) {
    while ($row = $result_active_tasks->fetch_assoc()) {
        $active_maintenance_tasks[] = $row;
    }
} else {
    $error_message = "No pending or in-progress maintenance tasks found. Please schedule a task first.";
    $form_err = true;
}

// Fetch mechanic usernames for the dropdown (assuming the mechanic is logged in or selected)
$mechanic_usernames = [];
$sql_mechanics = "SELECT username FROM users WHERE role = 'mechanic' ORDER BY username ASC";
$result_mechanics = $conn->query($sql_mechanics);

if ($result_mechanics && $result_mechanics->num_rows > 0) {
    while ($row = $result_mechanics->fetch_assoc()) {
        $mechanic_usernames[] = $row['username'];
    }
} else {
    // This is a critical error if no mechanics exist, as tasks need to be assigned
    $error_message .= (empty($error_message) ? "" : "<br>") . "No mechanic users found. Please add a mechanic user first.";
    $form_err = true;
}

// Initialize variables for form fields to retain values on submission errors
$selected_maintenance_id = isset($_POST["selected_maintenance_id"]) ? get_input_data($_POST["selected_maintenance_id"]) : "";
$task_details = isset($_POST["task_details"]) ? get_input_data($_POST["task_details"]) : "";
$recommendations = isset($_POST["recommendations"]) ? get_input_data($_POST["recommendations"]) : "";
$completion_date = isset($_POST["completion_date"]) ? get_input_data($_POST["completion_date"]) : date('Y-m-d');
$mechanic_username = isset($_POST["mechanic_username"]) ? get_input_data($_POST["mechanic_username"]) : "";


// Processing form data when the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Validate selected maintenance task
    if (empty($_POST["selected_maintenance_id"])) {
        $selected_maintenance_id_err = "Please select a maintenance task.";
        $form_err = true;
    } else {
        $selected_maintenance_id = get_input_data($_POST["selected_maintenance_id"]);
        // Basic check to ensure the ID is numeric
        if (!is_numeric($selected_maintenance_id)) {
            $selected_maintenance_id_err = "Invalid maintenance task selected.";
            $form_err = true;
        }
    }

    // Validate task details
    if (empty($_POST["task_details"])) {
        $task_details_err = "Task details are required.";
        $form_err = true;
    } else {
        $task_details = get_input_data($_POST["task_details"]);
    }

    // Recommendations are optional, no strict validation needed
    $recommendations = get_input_data($_POST["recommendations"]);

    // Validate completion date
    if (empty($_POST["completion_date"])) {
        $completion_date_err = "Completion date is required.";
        $form_err = true;
    } else {
        $completion_date = get_input_data($_POST["completion_date"]);
        if (!preg_match("/^\d{4}-\d{2}-\d{2}$/", $completion_date)) {
            $completion_date_err = "Invalid date format. Use YYYY-MM-DD.";
            $form_err = true;
        }
    }

    // Validate mechanic username
    if (empty($_POST["mechanic_username"])) {
        $mechanic_username_err = "Mechanic username is required.";
        $form_err = true;
    } else {
        $mechanic_username = get_input_data($_POST["mechanic_username"]);
        // You might want to add a check here if the selected mechanic is actually in the $mechanic_usernames array
    }

    // If there are no errors, proceed to update and insert data
    if (!$form_err) {
        // Start a transaction for atomicity
        $conn->begin_transaction();

        try {
            // 1. Update the status of the maintenance task to 'Completed'
            $sql_update_maintenance = "UPDATE maintenance SET status = 'Completed' WHERE maintenance_id = ?";
            $stmt_update = $conn->prepare($sql_update_maintenance);
            $stmt_update->bind_param("i", $selected_maintenance_id);
            if (!$stmt_update->execute()) {
                throw new Exception("Error updating maintenance status: " . $stmt_update->error);
            }
            $stmt_update->close();

            // 2. Insert the task details and recommendations into maintenance_logs
            $sql_insert_log = "INSERT INTO maintenance_logs (maintenance_id, task_details, recommendations, completion_date, mechanic_username) VALUES (?, ?, ?, ?, ?)";
            $stmt_insert = $conn->prepare($sql_insert_log);
            $stmt_insert->bind_param("issss", $selected_maintenance_id, $task_details, $recommendations, $completion_date, $mechanic_username);
            if (!$stmt_insert->execute()) {
                throw new Exception("Error inserting maintenance log: " . $stmt_insert->error);
            }
            $stmt_insert->close();

            // Commit the transaction
            $conn->commit();
            $success_message = "Maintenance task details recorded successfully!";
            // Redirect to a confirmation page or back to the maintenance list
            header("Location: maintenance.php?msg=" . urlencode($success_message));
            exit();

        } catch (Exception $e) {
            // Rollback the transaction on error
            $conn->rollback();
            $error_message = "Transaction failed: " . $e->getMessage();
        }
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Record Completed Maintenance Task</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .container {
            max-width: 800px;
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
            <i class="fas fa-arrow-left"></i> Back to Maintenance Schedule
        </a>
        <h2 class="mb-4">Record Completed Maintenance Task</h2>

        <?php if (!empty($error_message)): ?>
            <div class="alert alert-danger"><?php echo $error_message; ?></div>
        <?php endif; ?>
        <?php if (!empty($success_message)): ?>
            <div class="alert alert-success"><?php echo $success_message; ?></div>
        <?php endif; ?>

        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST">
            <div class="form-group">
                <label for="selected_maintenance_id">Select Task to Complete:</label>
                <select class="form-control" id="selected_maintenance_id" name="selected_maintenance_id" <?php if ($form_err) echo "disabled"; ?>>
                    <option value="">-- Select a Maintenance Task --</option>
                    <?php foreach ($active_maintenance_tasks as $task): ?>
                        <option value="<?php echo htmlspecialchars($task['maintenance_id']); ?>" <?php echo ($selected_maintenance_id == $task['maintenance_id']) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($task['truck_plate_number'] . " - " . $task['maintenance_type'] . " (Due: " . $task['date'] . ")"); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <span class="error-message"><?php echo $selected_maintenance_id_err; ?></span>
            </div>

            <div class="form-group">
                <label for="mechanic_username">Mechanic Who Performed Task:</label>
                <select class="form-control" id="mechanic_username" name="mechanic_username" <?php if ($form_err) echo "disabled"; ?>>
                    <option value="">-- Select Mechanic --</option>
                    <?php foreach ($mechanic_usernames as $username): ?>
                        <option value="<?php echo htmlspecialchars($username); ?>" <?php echo ($mechanic_username == $username) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($username); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <span class="error-message"><?php echo $mechanic_username_err; ?></span>
            </div>

            <div class="form-group">
                <label for="task_details">Task Details (What was done?):</label>
                <textarea class="form-control" id="task_details" name="task_details" rows="5" placeholder="e.g., Replaced engine oil and filter, checked tire pressure, rotated tires as per schedule." <?php if ($form_err) echo "disabled"; ?>><?php echo htmlspecialchars($task_details); ?></textarea>
                <span class="error-message"><?php echo $task_details_err; ?></span>
            </div>

            <div class="form-group">
                <label for="recommendations">Recommendations (for future maintenance):</label>
                <textarea class="form-control" id="recommendations" name="recommendations" rows="3" placeholder="e.g., Front brake pads will need replacement in next 6 months. Monitor coolant level." <?php if ($form_err) echo "disabled"; ?>><?php echo htmlspecialchars($recommendations); ?></textarea>
                <span class="error-message"><?php echo $recommendations_err; ?></span>
            </div>

            <div class="form-group">
                <label for="completion_date">Completion Date:</label>
                <input type="date" class="form-control" id="completion_date" name="completion_date" value="<?php echo htmlspecialchars($completion_date); ?>" <?php if ($form_err) echo "disabled"; ?>>
                <span class="error-message"><?php echo $completion_date_err; ?></span>
            </div>

            <button type="submit" class="btn btn-primary" <?php if ($form_err) echo "disabled"; ?>>
                <i class="fas fa-check-circle"></i> Record Completion
            </button>
            <a href="maintenance.php" class="btn btn-secondary">Cancel</a>
        </form>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
