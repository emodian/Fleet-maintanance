<?php
// Database connection details
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "fleet_management";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch expense data from different tables
$expenses = [];

// Maintenance Expenses
$sql_maintenance = "SELECT m.date, m.maintenance_type AS description, m.cost
                    FROM maintenance m";
$result_maintenance = $conn->query($sql_maintenance);
if ($result_maintenance->num_rows > 0) {
    while ($row = $result_maintenance->fetch_assoc()) {
        $expenses[] = [
            'date' => $row['date'],
            'description' => 'Maintenance: ' . $row['description'],
            'cost' => $row['cost'] ?? 0, // Use cost if available, otherwise 0
            'type' => 'Maintenance'
        ];
    }
}

// Fuel Expenses (assuming 'cost' column exists)
$sql_fuel = "SELECT date, CONCAT('Fuel for ',truck_plate_number) AS description, cost FROM fuel_logs WHERE cost IS NOT NULL";
$result_fuel = $conn->query($sql_fuel);
if ($result_fuel->num_rows > 0) {
    while ($row = $result_fuel->fetch_assoc()) {
        $expenses[] = [
            'date' => $row['date'],
            'description' => $row['description'],
            'cost' => $row['cost'],
            'type' => 'Fuel'
        ];
    }
}

// Repair Expenses using maintenance table (assuming cost is stored there and identifying repairs by type)
$sql_repair = "SELECT date, maintenance_type AS description, cost
               FROM maintenance
               WHERE maintenance_type LIKE '%Repair%' AND cost IS NOT NULL";
$result_repair = $conn->query($sql_repair);
if ($result_repair->num_rows > 0) {
        while ($row = $result_repair->fetch_assoc()) {
            $expenses[] = [
                'date' => $row['date'],
                'description' => 'Repair: ' . $row['description'],
                'cost' => $row['cost'],
                'type' => 'Repair'
            ];
        }
    }


// Sort expenses by date (most recent first)
usort($expenses, function($a, $b) {
    return strtotime($b['date']) - strtotime($a['date']);
});

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Financial Report</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body {
            padding: 20px;
            background-color: #f8f9fa;
            display: flex;
            flex-direction: column;
            min-height: 100vh; /* Ensure the body takes at least the full viewport height */
        }
        .container {
            max-width: 960px;
            margin: auto;
            background-color: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            flex-grow: 1; /* Allow the container to grow and push the footer down */
        }
        h2 {
            margin-bottom: 20px;
            text-align: center;
        }
        #search-container {
            margin-bottom: 20px;
        }
        #expense-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        #expense-table th, #expense-table td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        #expense-table th {
            background-color: #343a40;
            color: white;
        }
        .total-expense {
            margin-top: 20px;
            font-weight: bold;
            text-align: right;
            font-size: 1.2em;
        }
        .no-results {
            text-align: center;
            padding: 20px;
            color: #6c757d;
        }
        .footer {
            background-color: #f8f9fa;
            padding: 20px;
            text-align: center;
            border-top: 1px solid #ddd;
            margin-top: 20px; /* Add some space above the footer */
            font-size: 0.9em;
            color: #6c757d;
        }
        .print-button {
            margin-top: 20px;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2><i class="fas fa-chart-line"></i> Financial Report</h2>
        <div id="search-container" class="mb-3">
            <div class="input-group">
                <input type="text" id="searchInput" class="form-control" placeholder="Search expenses...">
                <button class="btn btn-outline-secondary" type="button" onclick="searchExpenses()">
                    <i class="fas fa-search"></i>
                </button>
            </div>
        </div>

        <table id="expense-table" class="table table-striped">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Description</th>
                    <th>Cost</th>
                    <th>Type</th>
                </tr>
            </thead>
            <tbody id="expense-body">
                <?php
                $total_expense = 0;
                if (!empty($expenses)):
                    foreach ($expenses as $expense):
                        echo "<tr>";
                        echo "<td>" . htmlspecialchars($expense['date']) . "</td>";
                        echo "<td>" . htmlspecialchars($expense['description']) . "</td>";
                        echo "<td>" . htmlspecialchars(number_format($expense['cost'], 2)) . "</td>";
                        echo "<td>" . htmlspecialchars($expense['type']) . "</td>";
                        echo "</tr>";
                        $total_expense += $expense['cost'];
                    endforeach;
                else:
                    echo "<tr><td colspan='4' class='no-results'>No expenses recorded yet.</td></tr>";
                endif;
                ?>
            </tbody>
        </table>

        <div class="total-expense">
            Total Expenses: <strong><?php echo htmlspecialchars(number_format($total_expense, 2)); ?></strong>
        </div>

        <div class="print-button">
            <button class="btn btn-primary" onclick="window.print()">
                <i class="fas fa-print"></i> Print Report
            </button>
        </div>
    </div>

    <footer class="footer">
        <p>&copy; <?php echo date("Y"); ?> Fleet Management System. All rights reserved.</p>
        <p>Report generated on: <?php echo date("Y-m-d H:i:s"); ?> EAT</p>
    </footer>

    <script>
        function searchExpenses() {
            const searchText = document.getElementById("searchInput").value.toLowerCase();
            const tableRows = document.getElementById("expense-body").getElementsByTagName("tr");
            let found = false;

            for (let i = 0; i < tableRows.length; i++) {
                const rowData = tableRows[i].textContent.toLowerCase();
                if (rowData.includes(searchText)) {
                    tableRows[i].style.display = "";
                    found = true;
                } else {
                    tableRows[i].style.display = "none";
                }
            }

            if (!found) {
                const noResultsRow = document.createElement("tr");
                const noResultsCell = document.createElement("td");
                noResultsCell.colSpan = 4;
                noResultsCell.classList.add("no-results");
                noResultsCell.textContent = "No expenses found matching your search.";

                // Check if a "No results" row already exists and remove it
                const existingNoResults = document.querySelector("#expense-body .no-results");
                if (existingNoResults) {
                    existingNoResults.parentElement.remove();
                }

                document.getElementById("expense-body").appendChild(noResultsRow);
            } else {
                // If results are found, remove any existing "No results" row
                const existingNoResults = document.querySelector("#expense-body .no-results");
                if (existingNoResults) {
                    existingNoResults.parentElement.remove();
                }
            }
        }

        // Basic auto-update (page reload every X seconds) - NOT true real-time
        // setInterval(function(){
        //     location.reload();
        // }, 30000); // Reload every 30 seconds
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>