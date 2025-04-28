<?php
// Establish database connection
$conn = new mysqli("localhost", "root", "", "fleet_management");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

session_start();

if (isset($_GET['id'])) {
    $truck_plate_number = mysqli_real_escape_string($conn, $_GET['id']);

    $sql = "DELETE FROM vehicles WHERE truck_plate_number = '$truck_plate_number'";

    if ($conn->query($sql) === TRUE) {
        $_SESSION['message'] = "Vehicle deleted successfully.";
        $_SESSION['message_type'] = 'success';
    } else {
        $_SESSION['message'] = "Error deleting vehicle: " . $conn->error;
        $_SESSION['message_type'] = 'error';
    }

    $conn->close(); // ✅ Close before exit
    header("Location: vehicles.php");
    exit();
} else {
    $_SESSION['message'] = "Vehicle ID is missing.";
    $_SESSION['message_type'] = 'error';
    $conn->close(); // ✅ Close before exit
    header("Location: vehicles.php");
    exit();
}
?>
