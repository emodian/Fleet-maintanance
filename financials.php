<!-- filepath: c:\xampp\htdocs\fleet-maintenance-management-system\financials.php -->
<?php
session_start();
if (!isset($_SESSION['user_role'])) {
    header("Location: dashboard.html");
    exit();
}

if ($_SESSION['user_role'] !== 'accountant') {
    echo "<script>alert('You do not have access to this page.'); window.location.href = 'dashboard.php';</script>";
    exit();
}

// ...existing code...
?>