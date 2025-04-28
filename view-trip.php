<?php
session_start();

require_once 'config.php';

$user_role = $_SESSION['user_role'] ?? '';
$username = $_SESSION['username'] ?? '';

if (!isset($conn) || $conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

if (isset($_GET['id'])) {
    $trip_id = $_GET['id'];

    // Use a prepared statement to prevent SQL injection
    $stmt = $conn->prepare("SELECT * FROM trip_logs WHERE trip_id = ?");
    if (!$stmt) {
        die("Error preparing statement: " . $conn->error);
    }
    $stmt->bind_param("i", $trip_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 0) {
        die("Trip not found.");
    }
    $trip = $result->fetch_assoc();
    $stmt->close();
} else {
    die("Trip ID not provided.");
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>View Trip Details | Fleet Management System</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f3f4f6;
            margin: 0;
            padding: 0;
        }

        .container {
            max-width: 800px;
            margin: 40px auto;
            background-color: #fff;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 6px 10px rgba(0,0,0,0.1);
        }

        h1 {
            color: #2c3e50;
            display: flex;
            align-items: center;
            margin-bottom: 30px;
            font-size: 1.8rem;
        }

        .material-icons.back-icon {
            cursor: pointer;
            margin-right: 10px;
            color: #3498db;
        }

        .details-section {
            margin-bottom: 25px;
            padding: 20px;
            border-radius: 8px;
            background-color: #f9f9f9;
            border: 1px solid #e0e0e0;
        }

        .details-section h2 {
            color: #3498db;
            margin-bottom: 20px;
            font-size: 1.4rem;
            border-bottom: 2px solid #3498db;
            padding-bottom: 10px;
        }


        .details-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 15px;
        }

        .details-label {
            font-weight: 600;
            color: #2c3e50;
            width: 40%;
        }

        .details-value {
            color: #444444;
            width: 60%;
            word-wrap: break-word;
        }

        .edit-button {
            text-align: right;
            margin-top: 30px;
        }

        .btn-edit {
            padding: 10px 15px;
            background-color: #3498db;
            color: white;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            transition: background-color 0.3s, transform 0.2s;
            font-weight: 600;
            text-decoration: none;
        }

        .btn-edit:hover {
            background-color: #217dbb;
            transform: translateY(-2px);
        }

        footer {
            text-align: center;
            font-size: 0.9rem;
            color: #666;
            padding: 20px 10px 10px;
            margin-top: 30px;
        }

        footer a {
            color: #3498db;
            text-decoration: none;
        }

        footer a:hover {
            text-decoration: underline;
        }

        @media (max-width: 768px) {
            .container {
                padding: 20px;
                margin: 20px auto;
            }

            .details-row {
                flex-direction: column;
            }

            .details-label, .details-value {
                width: 100%;
            }

            .edit-button {
                text-align: center;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>
            <span class="material-icons back-icon" onclick="window.location.href='trip_logs.php'">arrow_back</span>
            Trip Details
        </h1>

        <div class="details-section">
            <h2>Trip Information</h2>
            <div class="details-row">
                <div class="details-label">Trip ID:</div>
                <div class="details-value"><?php echo htmlspecialchars($trip['trip_id']); ?></div>
            </div>
            <div class="details-row">
                <div class="details-label">Trip Date:</div>
                <div class="details-value"><?php echo htmlspecialchars($trip['trip_date']); ?></div>
            </div>
            <div class="details-row">
                <div class="details-label">Driver:</div>
                <div class="details-value"><?php echo htmlspecialchars($trip['driver_id']); ?></div>
            </div>
            <div class="details-row">
                <div class="details-label">Truck Plate Number:</div>
                <div class="details-value"><?php echo htmlspecialchars($trip['truck_plate_number']); ?></div>
            </div>
            <div class="details-row">
                <div class="details-label">Route:</div>
                <div class="details-value"><?php echo htmlspecialchars($trip['trip_route']); ?></div>
            </div>
            <div class="details-row">
                <div class="details-label">Distance:</div>
                <div class="details-value"><?php echo htmlspecialchars($trip['distance']); ?> km</div>
            </div>
            <div class="details-row">
                <div class="details-label">Fuel Consumed:</div>
                <div class="details-value"><?php echo htmlspecialchars($trip['fuel_consumed']); ?> litres</div>
            </div>
            <div class="details-row">
                <div class="details-label">Cargo Type:</div>
                <div class="details-value"><?php echo htmlspecialchars($trip['cargo_type']); ?></div>
            </div>
             <div class="details-row">
                <div class="details-label">Cargo Weight:</div>
                <div class="details-value"><?php echo htmlspecialchars($trip['cargo_weight']); ?> tonnes</div>
            </div>
            <div class="details-row">
                <div class="details-label">Tolls Paid:</div>
                <div class="details-value"><?php echo htmlspecialchars($trip['tolls_paid']); ?> Tsh</div>
            </div>
            <div class="details-row">
                <div class="details-label">Other Expenses:</div>
                <div class="details-value"><?php echo htmlspecialchars($trip['other_expenses']); ?> Tsh</div>
            </div>
            <div class="details-row">
                <div class="details-label">Trip Status:</div>
                <div class="details-value"><?php echo htmlspecialchars($trip['trip_status']); ?></div>
            </div>
        </div>

        

    <footer>
        &copy; 2025 Fleet Maintenance Management System<br>
        Pope's Tr Headquarters | P.O.Box 1600 Dar es Salaam | Tanzania<br>
        Phone: +255781636843 | Email: <a href="mailto:info@popestr.com">info@popestr.com</a> | Website: <a href="http://www.popestr.com" target="_blank">www.popestr.com</a>
    </footer>
</body>
</html>
