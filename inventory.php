<?php
// Database connection
$conn = new mysqli("localhost", "root", "", "fleet_management");

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Include CSS and Font Awesome
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fleet Maintenance Inventory</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            padding: 20px;
            background-color: #f8f9fa;
        }
        .card {
            margin-bottom: 20px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
        .card-header {
            background-color: #343a40;
            color: white;
            font-weight: bold;
        }
        .table-responsive {
            overflow-x: auto;
        }
        .badge {
            font-size: 0.9em;
        }
        .action-btns .btn {
            margin-right: 5px;
        }
        .search-container {
            margin-bottom: 20px;
        }
        .category-filter {
            margin-bottom: 15px;
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <a href="dashboard.php" class="btn btn-outline-light btn-sm me-3" title="Back to Dashboard">
                    <i class="fas fa-arrow-left"></i>Back to Dashbord </a>
                <h2><i class="fas fa-boxes"></i>  Inventory</h2>
                <a href="add_inventory.php" class="btn btn-success">
                    <i class="fas fa-plus"></i> Add New Item
                </a>
            </div>
            <div class="card-body">
                <!-- Search and Filter Section -->
                <div class="row mb-3">
                    <div class="col-md-6">
                        <div class="search-container">
                            <div class="input-group">
                                <input type="text" class="form-control" id="searchInput" placeholder="Search inventory...">
                                <button class="btn btn-outline-secondary" type="button" id="searchBtn">
                                    <i class="fas fa-search"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="category-filter">
                            <select class="form-select" id="categoryFilter">
                                <option value="">All Categories</option>
                                <option value="Engine Parts">Engine Parts</option>
                                <option value="Transmission Parts">Transmission Parts</option>
                                <option value="Brake System">Brake System</option>
                                <option value="Suspension Parts">Suspension Parts</option>
                                <option value="Electrical Components">Electrical Components</option>
                                <option value="Tires & Wheels">Tires & Wheels</option>
                                <option value="Fluids & Lubricants">Fluids & Lubricants</option>
                                <option value="Filters">Filters</option>
                                <option value="Body Parts">Body Parts</option>
                                <option value="Lighting">Lighting</option>
                                <option value="Tools & Equipment">Tools & Equipment</option>
                                <option value="Safety Equipment">Safety Equipment</option>
                                <option value="Other">Other</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Inventory Table -->
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead class="table-dark">
                            <tr>
                                <th>ID</th>
                                <th>Part Number</th>
                                <th>Part Name</th>
                                <th>Category</th>
                                <th>Quantity</th>
                                <th>Assigned Vehicle</th>
                                <th>Mechanic</th>
                                <th>Last Updated</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            // Fetch inventory data
                            $sql = "SELECT * FROM inventory ORDER BY timestamp DESC";
                            $result = $conn->query($sql);

                            if ($result->num_rows > 0) {
                                while($row = $result->fetch_assoc()) {
                                    echo "<tr>";
                                    echo "<td>" . $row['inventory_id'] . "</td>";
                                    echo "<td>" . htmlspecialchars($row['part_number']) . "</td>";
                                    echo "<td>" . htmlspecialchars($row['part_name']) . "</td>";
                                    echo "<td><span class='badge bg-primary'>" . htmlspecialchars($row['category']) . "</span></td>";
                                    echo "<td>" . $row['quantity'] . "</td>";
                                    
                                    // Display vehicle assignment
                                    $vehicleInfo = "";
                                    if (!empty($row['truck_plate_number'])) {
                                        $vehicleInfo = "Truck: " . $row['truck_plate_number'];
                                    }
                                    if (!empty($row['trailer_plate_number'])) {
                                        if (!empty($vehicleInfo)) $vehicleInfo .= "<br>";
                                        $vehicleInfo .= "Trailer: " . $row['trailer_plate_number'];
                                    }
                                    echo "<td>" . $vehicleInfo . "</td>";
                                    
                                    echo "<td>" . (!empty($row['mechanics_name']) ? htmlspecialchars($row['mechanics_name']) : "-") . "</td>";
                                    echo "<td>" . date('M d, Y H:i', strtotime($row['timestamp'])) . "</td>";
                                    echo "<td class='action-btns'>";
                                    echo "<a href='edit_inventory.php?id=" . $row['inventory_id'] . "' class='btn btn-sm btn-primary' title='Edit'><i class='fas fa-edit'></i></a>";
                                    echo "<a href='delete_inventory.php?id=" . $row['inventory_id'] . "' class='btn btn-sm btn-danger' title='Delete' onclick='return confirm(\"Are you sure you want to delete this item?\")'><i class='fas fa-trash-alt'></i></a>";
                                    echo "<a href='inventory_details.php?id=" . $row['inventory_id'] . "' class='btn btn-sm btn-info' title='View Details'><i class='fas fa-eye'></i></a>";
                                    echo "</td>";
                                    echo "</tr>";
                                }
                            } else {
                                echo "<tr><td colspan='9' class='text-center'>No inventory items found</td></tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="card-footer">
                <div class="row">
                    <div class="col-md-6">
                        <p>Total Items: <?php echo $result->num_rows; ?></p>
                    </div>
                    <div class="col-md-6 text-end">
                        <button class="btn btn-outline-secondary" onclick="window.print()">
                            <i class="fas fa-print"></i> Print Inventory
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- JavaScript for search and filter functionality -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Simple search functionality
        document.getElementById('searchBtn').addEventListener('click', function() {
            const searchText = document.getElementById('searchInput').value.toLowerCase();
            const rows = document.querySelectorAll('tbody tr');
            
            rows.forEach(row => {
                const rowText = row.textContent.toLowerCase();
                if (rowText.includes(searchText)) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        });

        // Category filter functionality
        document.getElementById('categoryFilter').addEventListener('change', function() {
            const selectedCategory = this.value;
            const rows = document.querySelectorAll('tbody tr');
            
            rows.forEach(row => {
                if (selectedCategory === '') {
                    row.style.display = '';
                } else {
                    const categoryCell = row.querySelector('td:nth-child(4)');
                    if (categoryCell.textContent.includes(selectedCategory)) {
                        row.style.display = '';
                    } else {
                        row.style.display = 'none';
                    }
                }
            });
        });

        // Allow search on Enter key
        document.getElementById('searchInput').addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                document.getElementById('searchBtn').click();
            }
        });
    </script>
</body>
</html>

<?php
$conn->close();
?>