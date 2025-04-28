<?php
// Ensure session_start() is called only once and before any output
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$conn = new mysqli("localhost", "root", "", "fleet_management");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT password FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows == 1) {
        $stmt->bind_result($hashed_password);
        $stmt->fetch();

        if (password_verify($password, $hashed_password)) {
            $_SESSION['username'] = $username;
            $stmt->close();
            $conn->close();
            header("Location: dashboard.html");
            exit();
        } else {
            $stmt->close();
            $conn->close();
            header("Location: login.php?error=invalid");
            exit();
        }
    } else {
        $stmt->close();
        $conn->close();
        header("Location: login.php?error=invalid");
        exit();
    }
}

$conn->close();
?>
