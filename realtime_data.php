<?php
header('Content-Type: application/json');
session_start();

if (!isset($_SESSION['username'])) {
    echo json_encode(['error' => 'Unauthorized']);
    exit();
}

$conn = new mysqli("localhost", "root", "", "fleet_management");

if ($conn->connect_error) {
    echo json_encode(['error' => 'Database connection failed']);
    exit();
}

$data = [];

// Total Vehicles
$result = $conn->query("SELECT COUNT(*) AS total FROM vehicles");
$data['total_vehicles'] = $result->fetch_assoc()['total'];

// Pending Maintenance
$result = $conn->query("SELECT COUNT(*) AS pending FROM maintenance WHERE status = 'pending'");
$data['pending_maintenance'] = $result->fetch_assoc()['pending'];

// Active Trips
$result = $conn->query("SELECT COUNT(*) AS active FROM trips WHERE status = 'active'");
$data['active_trips'] = $result->fetch_assoc()['active'];

// Monthly Fuel Cost
$result = $conn->query("SELECT SUM(cost) AS total FROM fuel_logs WHERE MONTH(date) = MONTH(CURRENT_DATE())");
$data['monthly_fuel_cost'] = $result->fetch_assoc()['total'];

$conn->close();

echo json_encode($data);
?>
