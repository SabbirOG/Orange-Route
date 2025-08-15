<?php
session_start();
include 'db.php';

// Set content type to JSON
header('Content-Type: application/json');

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Access denied. Please log in.']);
    exit();
}

// Get shuttle ID from request
$shuttle_id = isset($_GET['shuttle_id']) ? intval($_GET['shuttle_id']) : 0;

if ($shuttle_id <= 0) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid shuttle ID.']);
    exit();
}

// Get specific shuttle with its location
$stmt = $conn->prepare("
    SELECT 
        s.id,
        s.name,
        s.route_name,
        s.status,
        s.traffic_status,
        s.latitude,
        s.longitude,
        s.location_updated_at,
        s.last_updated,
        u.username as driver_name,
        u.email as driver_email
    FROM shuttles s
    JOIN users u ON s.driver_id = u.id
    WHERE s.id = ?
");

$stmt->bind_param("i", $shuttle_id);
$stmt->execute();
$result = $stmt->get_result();

if ($row = $result->fetch_assoc()) {
    $shuttle = [
        'id' => $row['id'],
        'name' => $row['name'],
        'route_name' => $row['route_name'],
        'status' => $row['status'],
        'traffic_status' => $row['traffic_status'],
        'latitude' => $row['latitude'] ? floatval($row['latitude']) : null,
        'longitude' => $row['longitude'] ? floatval($row['longitude']) : null,
        'location_updated_at' => $row['location_updated_at'],
        'last_updated' => $row['last_updated'],
        'driver_name' => $row['driver_name'],
        'driver_email' => $row['driver_email'],
        'has_location' => !is_null($row['latitude']) && !is_null($row['longitude'])
    ];
    
    echo json_encode([
        'success' => true,
        'shuttle' => $shuttle
    ]);
} else {
    http_response_code(404);
    echo json_encode(['success' => false, 'message' => 'Shuttle not found.']);
}

$stmt->close();
?>
