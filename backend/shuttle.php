<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'driver') {
    die("Access denied.");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $shuttle_id = $_POST['shuttle_id'];
    $action = $_POST['action'];

    $stmt = $conn->prepare("SELECT id FROM shuttles WHERE id=? AND driver_id=?");
    $stmt->bind_param("ii", $shuttle_id, $_SESSION['user_id']);
    $stmt->execute();
    $stmt->store_result();
    if ($stmt->num_rows === 0) die("Invalid shuttle.");
    $stmt->close();

    if ($action === 'start') {
        $stmt = $conn->prepare("UPDATE shuttles SET status='active' WHERE id=?");
    } elseif ($action === 'down') {
        $stmt = $conn->prepare("UPDATE shuttles SET status='inactive' WHERE id=?");
    } elseif ($action === 'traffic') {
        $stmt = $conn->prepare("UPDATE shuttles SET traffic_status=1 WHERE id=?");
    }
    $stmt->bind_param("i", $shuttle_id);
    $stmt->execute();
    $stmt->close();

    header("Location: ../frontend/dashboards/driver_dashboard.php");
    exit();
}
?>
