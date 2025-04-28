```php
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

    // Prepare the SQL statement to delete the record
    $sql = "DELETE FROM maintenance WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $maintenance_id);

    // Attempt to execute the statement
    if ($stmt->execute()) {
        $success_message = "Record deleted successfully!";
        // Redirect to the maintenance list page after successful deletion
        header("Location: maintenance.php?msg=" . urlencode($success_message));
        exit();
    } else {
        echo "Error deleting record: " . $stmt->error;
    }

    // Close the statement
    $stmt->close();
} else {
    echo "Maintenance ID is required.";
    exit;
}

// Close the database connection
$conn->close();
?>
```