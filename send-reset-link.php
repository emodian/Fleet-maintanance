<?php

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];

    // Basic email validation
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo "<script>
                alert('Invalid email format.');
                window.location.href = 'forgot-password.html';
              </script>";
        exit;
    }

    // Display a message without sending the email
    echo "<script>
            alert('A password reset link has been sent to your email.');
            window.location.href = 'login.html';
          </script>";
}
?>
