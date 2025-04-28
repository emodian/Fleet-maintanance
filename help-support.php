<!-- filepath: c:\xampp\htdocs\fleet-maintenance-management-system\help-support.php -->
<?php
session_start();
if (!isset($_SESSION['user_role'])) {
    header("Location: login.html");
    exit();
}

$user_role = $_SESSION['user_role'];
$username = $_SESSION['username'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Help & Support | Fleet Management System</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        .help-container {
            max-width: 800px;
            margin: 50px auto;
            padding: 20px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .help-container h1 {
            font-size: 2rem;
            color: #333;
            margin-bottom: 20px;
            text-align: center;
        }

        .help-container p {
            font-size: 1rem;
            color: #666;
            line-height: 1.6;
            margin-bottom: 20px;
        }

        .help-container ul {
            list-style: disc;
            margin-left: 20px;
            color: #666;
        }

        .help-container ul li {
            margin-bottom: 10px;
        }

        .contact-section {
            margin-top: 30px;
            text-align: center;
        }

        .contact-section a {
            color: #007BFF;
            text-decoration: none;
            font-weight: 600;
        }

        .contact-section a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="help-container">
        <h1>Help & Support</h1>
        <p>Welcome to the Help & Support page. Here you can find answers to common questions and get assistance with using the Fleet Management System.</p>
        <h2>Frequently Asked Questions</h2>
        <ul>
            <li><strong>How do I reset my password?</strong> Go to the <a href="forgot-password.html">Forgot Password</a> page and follow the instructions.</li>
            <li><strong>How do I add a new user?</strong> Only administrators can add new users. Navigate to the "User Management" section in the dashboard.</li>
            <li><strong>How do I generate reports?</strong> Go to the "Reports" section in the dashboard and select the type of report you want to generate.</li>
        </ul>
        <div class="contact-section">
            <p>If you need further assistance, please contact our support team:</p>
            <p>Email: <a href="mailto:support@popestr.com">support@popestr.com</a></p>
            <p>Phone: +255781636843</p>
        </div>
    </div>
</body>
</html>