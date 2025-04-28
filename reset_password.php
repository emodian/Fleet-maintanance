<?php
session_start();
require_once 'config.php'; // Include database connection

if (!isset($conn) || $conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

$message = ""; // Initialize message

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $identifier = trim($_POST['identifier']); // Can be username or email
    $new_password = password_hash(trim($_POST['new_password']), PASSWORD_DEFAULT);

    // Update the password for the user identified by username or email
    $stmt = $conn->prepare("UPDATE users SET password = ? WHERE username = ? OR email = ?");
    if ($stmt) {
        $stmt->bind_param("sss", $new_password, $identifier, $identifier);
        if ($stmt->execute()) {
            $message = "Password reset successfully.";
        } else {
            $message = "Error executing statement: " . $stmt->error;
        }
        $stmt->close();
    } else {
        $message = "Error preparing statement: " . $conn->error;
    }
}

// Close the connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password</title>
    <style>
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
            font-family: Arial, sans-serif;
            background-color: #f4f4f9;
        }
        .container {
            text-align: center;
            background: #fff;
            padding: 2rem;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 400px;
        }
        label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: bold;
        }
        input {
            width: 100%;
            padding: 0.5rem;
            margin-bottom: 1rem;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
        button {
            padding: 0.5rem 1rem;
            background-color: #007bff;
            color: #fff;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        button:hover {
            background-color: #0056b3;
        }
        .message {
            margin-top: 1rem;
            color: green;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="container">
        <?php if (empty($message)): ?>
            <!-- Password Reset Request Form -->
            <form method="POST">
                <label for="identifier">Username or Email:</label>
                <input type="text" id="identifier" name="identifier" required>
                <label for="new_password">New Password:</label>
                <input type="password" id="new_password" name="new_password" required>
                <button type="submit">Reset Password</button>
            </form>
        <?php else: ?>
            <!-- Display Message -->
            <div class="message"><?php echo htmlspecialchars($message); ?></div>
        <?php endif; ?>
    </div>
</body>
</html>
