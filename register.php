<!-- filepath: c:\xampp\htdocs\fleet-maintenance-management-system\register.php -->
<?php
// Database connection
$conn = new mysqli('localhost', 'root', '', 'fleet_management');

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $email = isset($_POST['email']) ? trim($_POST['email']) : ''; // Handle undefined email key
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT); // Hash the password
    $role = 'Driver'; // Default role updated to match ENUM values

    // Prepare and execute the SQL statement
    $stmt = $conn->prepare("INSERT INTO users (username, password, email, role) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $username, $password, $email, $role);

    if ($stmt->execute()) {
        echo '<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registration Successful</title>
    <style>
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            font-family: Arial, sans-serif;
            background-color: #f0f0f0;
        }
        .message {
            text-align: center;
            font-size: 1.5rem;
            color: #2c3e50;
        }
    </style>
</head>
<body>
    <div class="message">Registration successful! Redirecting to login page...</div>
    <script>
        setTimeout(() => {
            window.location.href = "login.html";
        }, 3000); // Redirect after 3 seconds
    </script>
</body>
</html>';
        exit;
    } else {
        if ($conn->errno === 1062) { // Duplicate entry error code
            echo '<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registration Error</title>
    <style>
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            font-family: Arial, sans-serif;
            background-color: #f0f0f0;
        }
        .message {
            text-align: center;
            font-size: 1.5rem;
            color: #e74c3c;
        }
    </style>
</head>
<body>
    <div class="message">Error: Duplicate entry for username. Please choose a different username.</div>
</body>
</html>';
            exit;
        } else {
            echo "Error: " . $stmt->error;
        }
    }

    $stmt->close();
}

$conn->close();
?>