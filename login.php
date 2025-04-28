<?php
// Ensure session_start() is called only once and before any output
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Move all PHP logic above any HTML output
$error_message = ""; // Initialize error message

// Establish database connection
$conn = new mysqli("localhost", "root", "", "fleet_management");

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $identifier = trim($_POST['identifier']); // Can be either email or username
    $password = trim($_POST['password']);

    // Check if identifier exists (email or username) and password matches
    $stmt = $conn->prepare("SELECT password FROM users WHERE username = ? OR email = ?");
    $stmt->bind_param("ss", $identifier, $identifier);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $stmt->bind_result($hashed_password);
        $stmt->fetch();

        if (password_verify($password, $hashed_password)) {
            // Successful login
            $_SESSION['user_identifier'] = $identifier;
            header("Location: dashboard.php");
            exit();
        } else {
            // Incorrect password
            $_SESSION['error_message'] = "Username or password is incorrect. Please log in again.";
            header("Location: login.php");
            exit();
        }
    } else {
        // Username or email not found
        $_SESSION['error_message'] = "Username or password is incorrect. Please log in again.";
        header("Location: login.php");
        exit();
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
    <title>Login | Fleet Maintenance Management System</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary: #2563eb;
            --danger: #ef4444;
            --gray: #94a3b8;
            --light: #f8fafc;
            --dark: #1e293b;
            --card-bg: #ffffff;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Inter', sans-serif;
        }

        body {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            background-image: url('loginback.jpg'); /* Replace with your image path */
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            background-attachment: fixed;
        }

        .login-container {
            width: 100%;
            max-width: 400px;
            background-color: var(--card-bg);
            padding: 2rem;
            border-radius: 0.75rem;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            text-align: center;
        }

        .login-header {
            margin-bottom: 1.5rem;
        }

        .login-header h1 {
            font-size: 1.5rem;
            color: var(--dark);
        }

        .login-header p {
            font-size: 0.875rem;
            color: var(--gray);
        }

        .error-message {
            background-color: var(--danger);
            color: white;
            padding: 0.75rem;
            border-radius: 0.5rem;
            margin-bottom: 1rem;
            text-align: center;
            font-size: 0.875rem;
            position: absolute;
            top: 20%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 100%;
            max-width: 400px;
        }

        .login-form {
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }

        .form-group {
            display: flex;
            flex-direction: column;
        }

        .form-group label {
            font-size: 0.875rem;
            color: var (--dark);
            margin-bottom: 0.5rem;
        }

        .form-group input {
            padding: 0.75rem;
            border: 1px solid var(--gray);
            border-radius: 0.5rem;
            font-size: 1rem;
            color: var(--dark);
        }

        .form-group input:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.2);
        }

        .login-btn {
            padding: 0.75rem;
            background-color: var(--primary);
            color: white;
            border: none;
            border-radius: 0.5rem;
            font-size: 1rem;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        .login-btn:hover {
            background-color: #1e40af;
        }

        .login-footer {
            text-align: center;
            margin-top: 1rem;
            font-size: 0.875rem;
            color: var(--gray);
        }

        .login-footer a {
            color: var(--primary);
            text-decoration: none;
        }

        .login-footer a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <?php if (!empty($_SESSION['error_message'])): ?>
        <div class="error-message">
            <?php 
                echo htmlspecialchars($_SESSION['error_message']); 
                unset($_SESSION['error_message']); // Clear the error message after displaying
            ?>
        </div>
    <?php endif; ?>
    <div class="login-container">
        <div class="login-header">
            <h1>Welcome Back</h1>
            <p>Please log in to continue</p>
        </div>
        <form class="login-form" action="login.php" method="POST">
            <div class="form-group">
                <label for="identifier">Username or Email</label>
                <input type="text" id="identifier" name="identifier" required>
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required>
            </div>
            <button type="submit" class="login-btn">Login</button>
        </form>
        <div class="login-footer">
            <p>Forgot your password? <a href="reset_password.php">Reset it here</a></p>
        </div>
    </div>
</body>
</html>