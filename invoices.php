<!-- filepath: c:\xampp\htdocs\fleet-maintenance-management-system\invoices.php -->
<?php
session_start();
if (!isset($_SESSION['user_role'])) {
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

// Fetch invoices from the database
$query = "SELECT * FROM invoices";
$result = $conn->query($query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoices | Fleet Management System</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        .invoices-container {
            max-width: 1200px;
            margin: 50px auto;
            padding: 20px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .invoices-container h1 {
            font-size: 2rem;
            color: #333;
            margin-bottom: 20px;
            text-align: center;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        table th, table td {
            padding: 12px;
            text-align: left;
            border: 1px solid #ddd;
        }

        table th {
            background-color: #f4f4f4;
            font-weight: bold;
        }

        .add-invoice-btn {
            display: inline-block;
            margin: 20px 0;
            padding: 10px 20px;
            background-color: #28a745;
            color: white;
            text-decoration: none;
            border-radius: 4px;
            font-size: 1rem;
        }

        .add-invoice-btn:hover {
            background-color: #218838;
        }

        .action-links a {
            margin-right: 10px;
            color: #007BFF;
            text-decoration: none;
        }

        .action-links a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="invoices-container">
        <h1>Invoices</h1>
        <a href="add-invoice.php" class="add-invoice-btn">Add New Invoice</a>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Invoice Number</th>
                    <th>Customer</th>
                    <th>Date</th>
                    <th>Amount</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($result->num_rows > 0): ?>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo $row['id']; ?></td>
                            <td><?php echo htmlspecialchars($row['invoice_number']); ?></td>
                            <td><?php echo htmlspecialchars($row['customer']); ?></td>
                            <td><?php echo htmlspecialchars($row['date']); ?></td>
                            <td><?php echo htmlspecialchars($row['amount']); ?></td>
                            <td><?php echo htmlspecialchars($row['status']); ?></td>
                            <td class="action-links">
                                <a href="view-invoice.php?id=<?php echo $row['id']; ?>">View</a>
                                <a href="edit-invoice.php?id=<?php echo $row['id']; ?>">Edit</a>
                                <a href="delete-invoice.php?id=<?php echo $row['id']; ?>" onclick="return confirm('Are you sure you want to delete this invoice?');">Delete</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="7" style="text-align: center;">No invoices found.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
<?php $conn->close(); ?>