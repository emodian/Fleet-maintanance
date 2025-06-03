<?php
// Include the database connection file
$conn = new mysqli("localhost", "root", "", "fleet_management");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$maintenance_id = null;
$success_message = "";
$task_details = null;
$error_message = "";

// Check if maintenance_id is provided in the URL
if (isset($_GET['maintenance_id']) && is_numeric($_GET['maintenance_id'])) {
    $maintenance_id = (int)$_GET['maintenance_id'];
    if (isset($_GET['msg'])) {
        $success_message = htmlspecialchars($_GET['msg']);
    }

    // Fetch details of the completed task from both maintenance and maintenance_logs tables
    $sql = "SELECT m.maintenance_id, m.truck_plate_number, m.maintenance_type, m.date AS scheduled_date, m.cost, m.status,
                   ml.task_details, ml.recommendations, ml.completion_date, ml.mechanic_username
            FROM maintenance m
            JOIN maintenance_logs ml ON m.maintenance_id = ml.maintenance_id
            WHERE m.maintenance_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $maintenance_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $task_details = $result->fetch_assoc();
    } else {
        $error_message = "No details found for the specified maintenance task ID.";
    }
    $stmt->close();
} else {
    $error_message = "Invalid or missing maintenance task ID.";
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Task Completion Summary</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .container {
            max-width: 800px;
            margin: auto;
            padding-top: 20px;
        }
        .card {
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
        .card-header {
            background-color: #28a745; /* Green for success */
            color: white;
            border-top-left-radius: 10px;
            border-top-right-radius: 10px;
            font-size: 1.5em;
            font-weight: bold;
        }
        .detail-item {
            margin-bottom: 15px;
        }
        .detail-item strong {
            display: block;
            margin-bottom: 5px;
            color: #007bff;
        }
        .back-button {
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <div class="container mt-5">
        <?php if (!empty($success_message)): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <?php echo $success_message; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <?php if (!empty($error_message)): ?>
            <div class="alert alert-danger"><?php echo $error_message; ?></div>
        <?php elseif ($task_details): ?>
            <div class="card">
                <div class="card-header text-center">
                    <i class="fas fa-check-circle"></i> Task Completion Summary
                </div>
                <div class="card-body">
                    <h4 class="card-title mb-4">Maintenance Task #<?php echo htmlspecialchars($task_details['maintenance_id']); ?> Details</h4>

                    <div class="row">
                        <div class="col-md-6 detail-item">
                            <strong>Truck Plate Number:</strong>
                            <p><?php echo htmlspecialchars($task_details['truck_plate_number']); ?></p>
                        </div>
                        <div class="col-md-6 detail-item">
                            <strong>Maintenance Type:</strong>
                            <p><?php echo htmlspecialchars($task_details['maintenance_type']); ?></p>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 detail-item">
                            <strong>Scheduled Date:</strong>
                            <p><?php echo htmlspecialchars($task_details['scheduled_date']); ?></p>
                        </div>
                        <div class="col-md-6 detail-item">
                            <strong>Estimated Cost:</strong>
                            <p>$<?php echo htmlspecialchars(number_format($task_details['cost'], 2)); ?></p>
                        </div>
                    </div>

                    <hr>

                    <h5 class="mb-3">Completion Information:</h5>
                    <div class="detail-item">
                        <strong>Mechanic:</strong>
                        <p><?php echo htmlspecialchars($task_details['mechanic_username']); ?></p>
                    </div>
                    <div class="detail-item">
                        <strong>Completion Date:</strong>
                        <p><?php echo htmlspecialchars($task_details['completion_date']); ?></p>
                    </div>
                    <div class="detail-item">
                        <strong>Task Details (What was done):</strong>
                        <p><?php echo nl2br(htmlspecialchars($task_details['task_details'])); ?></p>
                    </div>
                    <?php if (!empty($task_details['recommendations'])): ?>
                        <div class="detail-item">
                            <strong>Recommendations for Future:</strong>
                            <p><?php echo nl2br(htmlspecialchars($task_details['recommendations'])); ?></p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <div class="text-center back-button">
                <a href="task_completion_summary.php" class="btn btn-secondary">
                    <i class="fas fa-list"></i> Go to Maintenance Schedule
                </a>
            </div>
        <?php endif; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
