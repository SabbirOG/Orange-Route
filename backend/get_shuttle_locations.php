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

// Get all active shuttles with their locations
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
        u.username as driver_name
    FROM shuttles s
    JOIN users u ON s.driver_id = u.id
    WHERE s.status = 'active' 
    AND s.latitude IS NOT NULL 
    AND s.longitude IS NOT NULL
    ORDER BY s.location_updated_at DESC
");

$stmt->execute();
$result = $stmt->get_result();
$shuttles = [];

while ($row = $result->fetch_assoc()) {
    $shuttles[] = [
        'id' => $row['id'],
        'name' => $row['name'],
        'route_name' => $row['route_name'],
        'driver_name' => $row['driver_name'],
        'status' => $row['status'],
        'traffic_status' => $row['traffic_status'],
        'latitude' => floatval($row['latitude']),
        'longitude' => floatval($row['longitude']),
        'location_updated_at' => $row['location_updated_at']
    ];
}

$stmt->close();

echo json_encode([
    'success' => true,
    'shuttles' => $shuttles,
    'count' => count($shuttles)
]);
?>
