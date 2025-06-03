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
$truck_plate_numbers = [];
$sql_trucks = "SELECT truck_plate_number FROM vehicles ORDER BY truck_plate_number ASC";
$result_trucks = $conn->query($sql_trucks);
if ($result_trucks && $result_trucks->num_rows > 0) {
    while ($row = $result_trucks->fetch_assoc()) {
        $truck_plate_numbers[] = $row['truck_plate_number'];
    }
}

// Fetch all trailer plate numbers for the dropdown
$trailer_plate_numbers = [];
$sql_trailers = "SELECT trailer_plate_number FROM vehicles ORDER BY trailer_plate_number ASC";
$result_trailers = $conn->query($sql_trailers);
if ($result_trailers && $result_trailers->num_rows > 0) {
    while ($row = $result_trailers->fetch_assoc()) {
        $trailer_plate_numbers[] = $row['trailer_plate_number'];
    }
}

// Fetch mechanic usernames for the dropdown
$mechanic_usernames = [];
$sql_mechanics = "SELECT username FROM users WHERE role = 'mechanic' ORDER BY username ASC";
$result_mechanics = $conn->query($sql_mechanics);
if ($result_mechanics && $result_mechanics->num_rows > 0) {
    while ($row = $result_mechanics->fetch_assoc()) {
        $mechanic_usernames[] = $row['username'];
    }
}

// Processing form data when the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $part_number = get_input_data($_POST["part_number"]);
    $part_name = get_input_data($_POST["part_name"]);
    $category = get_input_data($_POST["category"]);
    $quantity = get_input_data($_POST["quantity"]);
    $truck_plate_number = !empty($_POST["truck_plate_number"]) ? get_input_data($_POST["truck_plate_number"]) : null;
    $trailer_plate_number = !empty($_POST["trailer_plate_number"]) ? get_input_data($_POST["trailer_plate_number"]) : null;
    $mechanics_name = !empty($_POST["mechanics_name"]) ? get_input_data($_POST["mechanics_name"]) : null;

    // Basic validation
    if (empty($part_number) || empty($part_name) || empty($category) || !is_numeric($quantity) || $quantity < 0) {
        $error_message = "Please fill in all required fields correctly (Part Number, Part Name, Category, and a valid Quantity).";
    } else {
        // Prepare and bind the SQL statement
        // Using NULL for truck_plate_number, trailer_plate_number, mechanics_name if they are empty
        $stmt = $conn->prepare("INSERT INTO inventory (part_number, part_name, category, quantity, truck_plate_number, trailer_plate_number, mechanics_name) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sssisss", $part_number, $part_name, $category, $quantity, $truck_plate_number, $trailer_plate_number, $mechanics_name);

        if ($stmt->execute()) {
            $success_message = "New inventory item added successfully!";
            // Redirect to inventory.php after a short delay
            header("refresh:1;url=inventory.php");
            exit(); // Ensure script stops execution after redirection
        } else {
            $error_message = "Error adding inventory item: " . $stmt->error;
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
    <title>Add New Inventory Item</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .container {
            max-width: 700px;
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
        <a href="inventory.php" class="btn btn-secondary back-button">
            <i class="fas fa-arrow-left"></i> Back to Inventory
        </a>
        <h2 class="mb-4">Add New Inventory Item</h2>

        <?php if (!empty($error_message)): ?>
            <div class="alert alert-danger"><?php echo $error_message; ?></div>
        <?php endif; ?>
        <?php if (!empty($success_message)): ?>
            <div class="alert alert-success"><?php echo $success_message; ?></div>
        <?php endif; ?>

        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST">
            <div class="form-group">
                <label for="part_number">Part Number:</label>
                <input type="text" class="form-control" id="part_number" name="part_number" required value="<?php echo isset($_POST['part_number']) ? htmlspecialchars($_POST['part_number']) : ''; ?>">
            </div>
            <div class="form-group">
                <label for="part_name">Part Name:</label>
                <input type="text" class="form-control" id="part_name" name="part_name" required value="<?php echo isset($_POST['part_name']) ? htmlspecialchars($_POST['part_name']) : ''; ?>">
            </div>
            <div class="form-group">
                <label for="category">Category:</label>
                <select class="form-control" id="category" name="category" required>
                    <option value="">-- Select Category --</option>
                    <?php
                    $categories = [
                        "Engine Parts", "Transmission Parts", "Brake System", "Suspension Parts",
                        "Electrical Components", "Tires & Wheels", "Fluids & Lubricants", "Filters",
                        "Body Parts", "Lighting", "Tools & Equipment", "Safety Equipment", "Other"
                    ];
                    foreach ($categories as $cat) {
                        $selected = (isset($_POST['category']) && $_POST['category'] == $cat) ? 'selected' : '';
                        echo "<option value=\"" . htmlspecialchars($cat) . "\" " . $selected . ">" . htmlspecialchars($cat) . "</option>";
                    }
                    ?>
                </select>
            </div>
            <div class="form-group">
                <label for="quantity">Quantity:</label>
                <input type="number" class="form-control" id="quantity" name="quantity" min="0" required value="<?php echo isset($_POST['quantity']) ? htmlspecialchars($_POST['quantity']) : '0'; ?>">
            </div>
            <div class="form-group">
                <label for="truck_plate_number">Assign to Truck (Optional):</label>
                <select class="form-control" id="truck_plate_number" name="truck_plate_number">
                    <option value="">-- Select Truck --</option>
                    <?php foreach ($truck_plate_numbers as $plate): ?>
                        <option value="<?php echo htmlspecialchars($plate); ?>" <?php echo (isset($_POST['truck_plate_number']) && $_POST['truck_plate_number'] == $plate) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($plate); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label for="trailer_plate_number">Assign to Trailer (Optional):</label>
                <select class="form-control" id="trailer_plate_number" name="trailer_plate_number">
                    <option value="">-- Select Trailer --</option>
                    <?php foreach ($trailer_plate_numbers as $plate): ?>
                        <option value="<?php echo htmlspecialchars($plate); ?>" <?php echo (isset($_POST['trailer_plate_number']) && $_POST['trailer_plate_number'] == $plate) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($plate); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label for="mechanics_name">Assigned Mechanic (Optional):</label>
                <select class="form-control" id="mechanics_name" name="mechanics_name">
                    <option value="">-- Select Mechanic --</option>
                    <?php foreach ($mechanic_usernames as $username): ?>
                        <option value="<?php echo htmlspecialchars($username); ?>" <?php echo (isset($_POST['mechanics_name']) && $_POST['mechanics_name'] == $username) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($username); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-plus-circle"></i> Add Item
            </button>
        </form>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>