<?php
session_start();
$conn = new mysqli("localhost", "root", "", "fleet_management");
// Fetch truck plate numbers
$truck_sql = "SELECT truck_plate_number FROM vehicles ORDER BY truck_plate_number ASC";
$truck_result = $conn->query($truck_sql);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$success = "";
$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $plate = $conn->real_escape_string($_POST['truck_plate']);
    $date = $conn->real_escape_string($_POST['date']);
    $fuel_type = $conn->real_escape_string($_POST['fuel_type']);
    $quantity = floatval($_POST['quantity']);
    $cost = floatval($_POST['cost']);
    $filled_by = $conn->real_escape_string($_POST['filled_by']);
    $notes = $conn->real_escape_string($_POST['notes']);

    if ($plate && $date && $fuel_type && $quantity > 0 && $cost >= 0) {
        $sql = "INSERT INTO fuel_logs (truck_plate_number, date, fuel_type, quantity, cost, filled_by, notes)
                VALUES ('$plate', '$date', '$fuel_type', $quantity, $cost, '$filled_by', '$notes')";

        if ($conn->query($sql) === TRUE) {
            $_SESSION['message'] = '✅ Fuel log added successfully.';
            $_SESSION['message_type'] = 'success';
            header('Location: fuel.php'); // 👈 This redirects the user
            exit();
            
        } else {
            $error = "Error: " . $conn->error;
        }
    } else {
        $error = "Please fill all required fields correctly.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add Fuel Log | Fleet Management</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f4f6f8;
            padding: 30px;
        }

        .container {
            max-width: 1000px;
            margin: auto;
            background: #fff;
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.08);
        }

        h1 {
            display: flex;
            align-items: center;
            color: #2c3e50;
            margin-bottom: 20px;
        }

        .back-arrow {
            margin-right: 12px;
            color: #3498db;
            cursor: pointer;
        }

        form label {
            display: block;
            margin-top: 15px;
            font-weight: 500;
        }

        form input, form select, form textarea {
            width: 100%;
            padding: 10px;
            margin-top: 5px;
            border: 1px solid #ccc;
            border-radius: 6px;
            font-size: 15px;
        }

        .btn {
            margin-top: 20px;
            padding: 12px;
            background-color: #28a745;
            color: white;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-weight: 600;
        }

        .btn:hover {
            background-color: #218838;
        }

        .message {
            margin-top: 15px;
            padding: 10px;
            border-radius: 6px;
        }

        .success {
            background-color: #d4edda;
            color: #155724;
        }

        .error {
            background-color: #f8d7da;
            color: #721c24;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>
            <span class="material-icons back-arrow" onclick="window.location.href='fuel.php'">arrow_back</span>
            Add Fuel Log
        </h1>

        <?php if ($success): ?>
            <div class="message success"><?php echo $success; ?></div>
        <?php elseif ($error): ?>
            <div class="message error"><?php echo $error; ?></div>
        <?php endif; ?>

        <form method="POST" action="">
        <label for="truck_plate">Truck Plate Number:</label>
            <select name="truck_plate" required>
                <option value="">-- Select Plate Number --</option>
                <?php while ($row = $truck_result->fetch_assoc()): ?>
                    <option value="<?= htmlspecialchars($row['truck_plate_number']) ?>">
                        <?= htmlspecialchars($row['truck_plate_number']) ?>
                    </option>
                <?php endwhile; ?>
            </select>

            <label for="date">Date *</label>
            <input type="date" name="date" required>

            <label for="fuel_type">Fuel Type *</label>
            <select name="fuel_type" required>
                <option value="">-- Select Fuel Type --</option>
                <option value="Diesel">Diesel</option>
                <option value="Petrol">Petrol</option>
            </select>

            <label for="quantity">Quantity (Liters) *</label>
            <input type="number" step="0" name="quantity" required>

            <label for="cost">Cost (Tsh) *</label>
            <input type="number" step="0" name="cost" required>

            <label for="filled_by">Filled By(Inventory Officer)</label>
            <input type="text" name="filled_by">

            <label for="notes">Notes</label>
            <textarea name="notes" rows="3"></textarea>

            <button type="submit" class="btn">Add Fuel Log</button>
        </form>
    </div>
</body>
</html>
<?php $conn->close(); ?>
