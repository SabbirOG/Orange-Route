<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    die("Access denied.");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate CSRF token
    if (!isset($_POST['csrf_token']) || !validateCSRFToken($_POST['csrf_token'])) {
        die("CSRF token validation failed.");
    }
    
    $driver_id = $_POST['driver_id'];
    $route_name = $_POST['route_name'];

    $stmt = $conn->prepare("SELECT id FROM shuttles WHERE driver_id=?");
    $stmt->bind_param("i", $driver_id);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $stmt->close();
        $update = $conn->prepare("UPDATE shuttles SET route_name=?, status='inactive' WHERE driver_id=?");
        $update->bind_param("si", $route_name, $driver_id);
        $update->execute();
        $update->close();
    } else {
        $stmt->close();
        $name = "Shuttle-" . $driver_id;
        $insert = $conn->prepare("INSERT INTO shuttles (driver_id, name, route_name, status) VALUES (?, ?, ?, 'inactive')");
        $insert->bind_param("iss", $driver_id, $name, $route_name);
        $insert->execute();
        $insert->close();
    }

    header("Location: ../frontend/dashboards/admin_dashboard.php");
    exit();
}
?>
