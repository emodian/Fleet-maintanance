<!-- filepath: c:\xampp\htdocs\fleet-maintenance-management-system\users.php -->
<?php
session_start();
if (!isset($_SESSION['user_role']) || ($_SESSION['user_role'] != 'admin' && $_SESSION['user_role'] != 'manager')) {
    header("Location: login.html");
    exit();
}

$user_role = $_SESSION['user_role'];
$username = $_SESSION['username'];

// Establish database connection
$conn = new mysqli("localhost", "root", "", "fleet_management");

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch users from the database
$query = "SELECT * FROM users";
$result = $conn->query($query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Management | Fleet Management System</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="dashboard-container">
        <h1>User Management</h1>
        <p>Manage all user accounts in the system.</p>
        <table border="1" cellpadding="10" cellspacing="0" style="width: 100%; text-align: left; border-collapse: collapse;">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Username</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($result->num_rows > 0): ?>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo $row['id']; ?></td>
                            <td><?php echo htmlspecialchars($row['username']); ?></td>
                            <td><?php echo htmlspecialchars($row['email']); ?></td>
                            <td><?php echo htmlspecialchars($row['role']); ?></td>
                            <td>
                                <a href="edit-user.php?id=<?php echo $row['id']; ?>" style="color: blue;">Edit</a> |
                                <a href="delete-user.php?id=<?php echo $row['id']; ?>" style="color: red;" onclick="return confirm('Are you sure you want to delete this user?');">Delete</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5" style="text-align: center;">No users found.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
        <div style="margin-top: 20px;">
            <a href="add-user.php" style="padding: 10px 20px; background-color: #28a745; color: white; text-decoration: none; border-radius: 4px;">Add New User</a>
        </div>
    </div>
    <footer style="text-align: center; margin-top: 20px; font-size: 0.9rem; color: #666;">
        © 2025 Fleet Maintenance Management System<br>
        Pope's Tr Headquarters | P.O.Box 1600 Dar es Salaam | Tanzania. | Phone: +255781636843 | Email: info@popestr.com | Website: <a href="http://www.popestr.com" target="_blank" style="color: #007BFF; text-decoration: none;">www.popestr.com</a>
    </footer>
</body>
</html>
<?php $conn->close(); ?>