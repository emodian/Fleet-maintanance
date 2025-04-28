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

// Initialize error variables
$driver_name_err = $email_err = $phone_err = $license_number_err = $truck_plate_number_err = $status_err = "";
$form_err = false;
$error_message = "";
$success_message = "";

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

// Fetch driver data if ID is provided
if (isset($_GET['id']) && !empty($_GET['id'])) {
    $driver_id = get_input_data($_GET['id']);

    $sql = "SELECT * FROM drivers WHERE driver_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $driver_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 0) {
        echo "Driver not found.";
        exit;
    }
    $driver = $result->fetch_assoc();
    $stmt->close();
} else {
    echo "Driver ID is required.";
    exit;
}

// Processing form data when the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate driver name
    if (empty($_POST["driver_name"])) {
        $driver_name_err = "Driver Name is required";
        $form_err = true;
    } else {
        $driver_name = get_input_data($_POST["driver_name"]);
        if (!preg_match("/^[a-zA-Z ]*$/", $driver_name)) {
            $driver_name_err = "Only letters and white space allowed";
            $form_err = true;
        }
    }

    // Validate email
    if (empty($_POST["email"])) {
        $email_err = "Email is required";
        $form_err = true;
    } else {
        $email = get_input_data($_POST["email"]);
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $email_err = "Invalid email format";
            $form_err = true;
        }
    }

    // Validate phone
    if (empty($_POST["phone"])) {
        $phone_err = "Phone is required";
        $form_err = true;
    } else {
        $phone = get_input_data($_POST["phone"]);
        if (!preg_match("/^[0-9]{10}$/", $phone)) { // Basic 10-digit phone number validation
            $phone_err = "Invalid phone number format";
            $form_err = true;
        }
    }

    // Validate license number
    if (empty($_POST["license_number"])) {
        $license_number_err = "License Number is required";
        $form_err = true;
    } else {
        $license_number = get_input_data($_POST["license_number"]);
    }

    // Validate truck plate number
    if (empty($_POST["truck_plate_number"])) {
        $truck_plate_number_err = "Truck Plate Number is required";
        $form_err = true;
    } else {
        $truck_plate_number = get_input_data($_POST["truck_plate_number"]);
    }

    // Validate status
    if (empty($_POST["status"])) {
        $status_err = "Status is required";
        $form_err = true;
    } else {
        $status = get_input_data($_POST["status"]);
        $valid_status = array("Active", "Inactive", "Suspended");
        if (!in_array($status, $valid_status)) {
            $status_err = "Invalid status value";
            $form_err = true;
        }
    }

    // If there are no errors, proceed to update the data in the database
    if (!$form_err) {
        $sql = "UPDATE drivers SET driver_name = ?, email = ?, phone = ?, license_number = ?, truck_plate_number = ?, status = ? WHERE driver_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssssssi", $driver_name, $email, $phone, $license_number, $truck_plate_number, $status, $driver_id);

        if ($stmt->execute()) {
            $success_message = "Driver record updated successfully!";
            header("Location: drivers.php?msg=" . urlencode($success_message));
            exit();
        } else {
            $error_message = "Error updating record: " . $stmt->error;
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
    <title>Edit Driver</title>
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
        <a href="drivers.php" class="btn btn-secondary back-button">
            <i class="fas fa-arrow-left"></i> Back to Drivers
        </a>
        <h2>Edit Driver</h2>

        <?php if (!empty($error_message)): ?>
            <div class="alert alert-danger"><?php echo $error_message; ?></div>
        <?php endif; ?>

        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"] . "?id=" . $driver_id); ?>" method="POST">
            <div class="form-group">
                <label for="driver_name">Driver Name:</label>
                <input type="text" class="form-control" id="driver_name" name="driver_name" value="<?php echo htmlspecialchars($driver['driver_name']); ?>">
                <span class="error-message"><?php echo $driver_name_err; ?></span>
            </div>
            <div class="form-group">
                <label for="email">Email:</label>
                <input type="text" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($driver['email']); ?>">
                <span class="error-message"><?php echo $email_err; ?></span>
            </div>
            <div class="form-group">
                <label for="phone">Phone:</label>
                <input type="text" class="form-control" id="phone" name="phone" value="<?php echo htmlspecialchars($driver['phone']); ?>">
                <span class="error-message"><?php echo $phone_err; ?></span>
            </div>
            <div class="form-group">
                <label for="license_number">License Number:</label>
                <input type="text" class="form-control" id="license_number" name="license_number" value="<?php echo htmlspecialchars($driver['license_number']); ?>">
                <span class="error-message"><?php echo $license_number_err; ?></span>
            </div>
            <div class="form-group">
                <label for="truck_plate_number">Truck Plate Number:</label>
                <select class="form-control" id="truck_plate_number" name="truck_plate_number">
                    <option value="">Select Truck</option>
                    <?php foreach ($truck_plate_numbers as $number): ?>
                        <option value="<?php echo $number; ?>" <?php if ($driver['truck_plate_number'] == $number) echo "selected"; ?>><?php echo $number; ?></option>
                    <?php endforeach; ?>
                </select>
                <span class="error-message"><?php echo $truck_plate_number_err; ?></span>
            </div>
            <div class="form-group">
                <label for="status">Status:</label>
                <select class="form-control" id="status" name="status">
                    <option value="">Select Status</option>
                    <option value="Active" <?php if ($driver['status'] == "Active") echo "selected"; ?>>Active</option>
                    <option value="Inactive" <?php if ($driver['status'] == "Inactive") echo "selected"; ?>>Inactive</option>
                    <option value="Suspended" <?php if ($driver['status'] == "Suspended") echo "selected"; ?>>Suspended</option>
                </select>
                <span class="error-message"><?php echo $status_err; ?></span>
            </div>
            <button type="submit" class="btn btn-primary" <?php if ($form_err) echo "disabled"; ?>>Update Driver</button>
            <a href="drivers.php" class="btn btn-secondary">Cancel</a>
        </form>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
