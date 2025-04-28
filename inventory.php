<?php
// Establish database connection
$conn = new mysqli("localhost", "root", "", "fleet_management");

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch inventory items from the database
$query = "SELECT * FROM inventory";
$result = $conn->query($query);

// Check if the query was successful
if (!$result) {
    die("Error executing query: " . $conn->error);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inventory | Fleet Management System</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://kit.fontawesome.com/your-fontawesome-kit.js" crossorigin="anonymous"></script> <style>
        body {
            font-family: 'Inter', sans-serif; /* More modern font */
            background-color: #f9f9f9; /* Soft background color */
            margin: 0;
            padding: 0;
            color: #333; /* Darker default text color for better readability */
        }
        .dashboard-container {
            max-width: 1000px; /* Increased max-width for larger screens */
            margin: 20px auto;
            padding: 30px; /* Increased padding for more spacing */
            background-color: #fff;
            border-radius: 12px; /* Slightly more rounded corners */
            box-shadow: 0 4px 12px rgba(0,0,0,0.1); /* More subtle shadow */
        }
        h1 {
            color: #2c3e50; /* Darker heading color */
            margin-bottom: 25px; /* Increased margin */
            text-align: center; /* Center heading */
            font-weight: 600; /* Use semibold font weight for headings */
            display: flex; /* Use flexbox for alignment */
            align-items: center; /* Vertically center icon and text */
            gap: 10px; /* Space between icon and text */
        }
        h1 i {
            color: #3498db; /* Accent color for the icon */
            font-size: 1.8em; /* Larger icon size */
        }
        p {
            color: #666; /* Slightly darker paragraph color */
            margin-bottom: 30px; /* Increased margin */
            text-align: center;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            background-color: #fff;
            border-radius: 10px; /* Rounded corners for table */
            box-shadow: 0 2px 6px rgba(0,0,0,0.05); /* Very subtle table shadow */
            overflow: hidden; /* Needed for rounded corners on table */
            margin-bottom: 20px;
        }
        table thead th {
            background-color: #ecf0f1; /* Lighter header background */
            color: #34495e; /* Darker header text */
            padding: 15px; /* Increased header padding */
            text-align: left;
            font-weight: 500; /* Medium font weight for headers */
            border-bottom: 2px solid #ddd; /* Lighter bottom border */
        }
        table tbody tr:nth-child(odd) {
            background-color: #f5f5f5; /* Very light background for odd rows */
        }
        table tbody tr:hover {
            background-color: #e0f7fa; /* Very light hover background */
            transition: background-color 0.2s ease; /* Smooth transition */
        }
        table td {
            padding: 15px; /* Increased cell padding */
            border-bottom: 1px solid #eee; /* Very light bottom border */
            color: #555; /* Slightly darker cell text */
        }
        table td a {
            text-decoration: none;
            font-weight: 500; /* Medium font weight for links */
            transition: color 0.2s ease; /* Smooth transition */
        }
        table td a:hover {
            opacity: 0.8; /* Slightly fade on hover */
        }
        table td a.edit {
            color: #3498db; /* Blue for edit */
        }
        table td a.edit:hover {
            color: #2980b9; /* Darker blue on hover */
        }
        table td a.delete {
            color: #e74c3c; /* Red for delete */
        }
        table td a.delete:hover {
            color: #c0392b; /* Darker red on hover */
        }
        .actions {
            display: flex;
            gap: 10px; /* Space between action links */
        }
        .add-new-item {
            display: inline-flex; /* Use inline-flex for better alignment */
            align-items: center;
            gap: 8px; /* Space between icon and text */
            padding: 12px 20px;
            background-color: #2ecc71; /* Green add button */
            color: white;
            text-decoration: none;
            border-radius: 6px; /* Slightly more rounded */
            font-weight: 500; /* Medium font weight */
            transition: background-color 0.2s ease, transform 0.1s ease; /* Smooth transition */
            margin-top: 20px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1); /* Add a subtle shadow */
        }
        .add-new-item:hover {
            background-color: #27ae60; /* Darker green on hover */
            transform: translateY(-2px); /* Slight lift on hover */
        }
        .add-new-item i {
            font-size: 1.2em; /* Larger plus icon */
        }
        .footer {
            text-align: center;
            margin-top: 30px; /* Increased margin */
            font-size: 0.9rem;
            color: #888; /* Lighter footer text */
            padding-top: 20px;
            border-top: 1px solid #eee; /* Very light top border */
        }
        .footer a {
            color: #3498db; /* Blue for link in footer */
            text-decoration: none;
        }
        .footer a:hover {
            text-decoration: underline;
        }

        .back-to-dashboard {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            color: #3498db;
            text-decoration: none;
            font-weight: 500;
            transition: color 0.2s ease;
            margin-bottom: 20px;
        }

        .back-to-dashboard:hover {
            color: #217dbb;
        }

        .back-to-dashboard i {
            font-size: 1.2em;
        }

        /* Responsive adjustments for smaller screens */
        @media screen and (max-width: 768px) {
            .dashboard-container {
                padding: 20px; /* Adjust padding for smaller screens */
            }
            table {
                display: block;
                overflow-x: auto;
                white-space: nowrap;
            }
            table thead, table tbody tr {
                display: table-row;
            }
            table thead th, table tbody td {
                display: table-cell;
                padding: 12px;
            }
            form {
                grid-template-columns: 1fr; /* Stack form elements on small screens */
                gap: 15px;
            }
            .full-width {
                grid-column: span 1;
            }
        }
    </style>
</head>
<body>
    <div class="dashboard-container">
        <a href="dashboard.php" class="back-to-dashboard">
            <i class="fas fa-arrow-left"></i> Back to Dashboard
        </a>
        <h1><i class="fas fa-boxes-stacked"></i>Inventory Management</h1>
        <p>Manage all inventory items in the system.</p>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Item Name</th>
                    <th>Quantity</th>
                    <th>Category</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($result && $result->num_rows > 0): ?>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo $row['id']; ?></td>
                            <td><?php echo htmlspecialchars($row['item_name']); ?></td>
                            <td><?php echo htmlspecialchars($row['quantity']); ?></td>
                            <td><?php echo htmlspecialchars($row['category']); ?></td>
                            <td>
                                <div class="actions">
                                    <a href="edit-inventory.php?id=<?php echo $row['id']; ?>" class="edit"><i class="fas fa-edit"></i> Edit</a>
                                    <a href="delete-inventory.php?id=<?php echo $row['id']; ?>" class="delete" onclick="return confirm('Are you sure you want to delete this item?');"><i class="fas fa-trash-alt"></i> Delete</a>
                                </div>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5" style="text-align: center;">No inventory items found.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
        <a href="add-inventory.php" class="add-new-item"><i class="fas fa-plus-circle"></i> Add New Item</a>
    </div>
    <footer class="footer">
        © 2025 Fleet Maintenance Management System<br>
        Pope's Tr Headquarters | P.O.Box 1600 Dar es Salaam | Tanzania. | Phone: +255781636843 | Email: <a href="mailto:info@popestr.com">info@popestr.com</a> | Website: <a href="http://www.popestr.com" target="_blank">www.popestr.com</a>
    </footer>
</body>
</html>
<?php $conn->close(); ?>
