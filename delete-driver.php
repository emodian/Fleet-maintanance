<?php
session_start();

require_once 'config.php';

$user_role = $_SESSION['user_role'] ?? '';
$username = $_SESSION['username'] ?? '';

if (!isset($conn) || $conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['id'])) {
    $driver_id = $_GET['id'];

    // Use a prepared statement to prevent SQL injection
    $stmt = $conn->prepare("DELETE FROM drivers WHERE driver_id = ?");
    if (!$stmt) {
        die("Error preparing statement: " . $conn->error);
    }
    $stmt->bind_param("i", $driver_id);

    if ($stmt->execute()) {
        header("Location: drivers.php?delete_success=1");
        exit;
    } else {
        $message = "Error deleting driver: " . $stmt->error;
        // Check for foreign key constraint error specifically
        if (strpos($message, 'foreign key constraint fails') !== false) {
            $message = "Cannot delete driver because they are assigned to active trip logs.  Please reassign or delete those trip logs first.";
        }
    }
    $stmt->close();
} else {
    // If the request method is not GET or id is not set, redirect to drivers.php
    header("Location: drivers.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Delete Driver | Fleet Management System</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f3f4f6;
            margin: 0;
            padding: 0;
        }

        .container {
            max-width: 800px;
            margin: 40px auto;
            background-color: #fff;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 6px 10px rgba(0,0,0,0.1);
            text-align: center; /* Center the content */
        }

        h1 {
            color: #2c3e50;
            display: flex;
            align-items: center;
            justify-content: center; /* Center the heading */
            font-size: 1.8rem;
            margin-bottom: 30px;
        }

        .material-icons.back-icon {
            cursor: pointer;
            margin-right: 10px;
            color: #3498db;
        }

        .message {
            margin-bottom: 20px;
            color: #d9534f;
            font-weight: 600;
            text-align: center;
        }

        .button-container {
            display: flex;
            justify-content: center; /* Center the buttons */
            margin-top: 20px;
        }

        .btn {
            padding: 12px 20px;
            border: none;
            border-radius: 6px;
            font-weight: 600;
            cursor: pointer;
            transition: background-color 0.3s, transform 0.2s;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .btn-confirm {
            background-color: #dc3545;
            color: white;
            margin-right: 10px;
        }

        .btn-confirm:hover {
            background-color: #c82333;
            transform: translateY(-2px);
        }

        .btn-cancel {
            background-color: #3498db;
            color: white;
            text-decoration: none;
        }

        .btn-cancel:hover {
            background-color: #2980b9;
            transform: translateY(-2px);
        }

        footer {
            text-align: center;
            font-size: 0.9rem;
            color: #666;
            padding: 20px 10px 10px;
            margin-top: 30px;
        }

        footer a {
            color: #3498db;
            text-decoration: none;
        }

        footer a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>
            <span class="material-icons back-icon" onclick="window.location.href='drivers.php'">arrow_back</span>
            Delete Driver
        </h1>

        <?php if (!empty($message)): ?>
            <div class="message"><?php echo htmlspecialchars($message); ?></div>
        <?php endif; ?>

        <?php if (empty($message)): ?>
            <p>Are you sure you want to delete this driver?</p>

            <div class="button-container">
                <a href="delete-driver.php?id=<?php echo $driver_id; ?>" class="btn btn-confirm"><span class="material-icons">warning</span> Confirm Delete</a>
                <a href="drivers.php" class="btn btn-cancel"><span class="material-icons">cancel</span> Cancel</a>
            </div>
         <?php endif; ?>
    </div>

    <footer>
        &copy; 2025 Fleet Maintenance Management System<br>
        Pope's Tr Headquarters | P.O.Box 1600 Dar es Salaam | Tanzania<br>
        Phone: +255781636843 | Email: <a href="mailto:info@popestr.com">info@popestr.com</a> | Website: <a href="http://www.popestr.com" target="_blank">www.popestr.com</a>
    </footer>

</body>
</html>

<?php $conn->close(); ?>
