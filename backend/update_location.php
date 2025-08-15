<?php
// Disable error reporting to prevent HTML output
error_reporting(0);
ini_set('display_errors', 0);

session_start();
include 'db.php';

// Set content type to JSON
header('Content-Type: application/json');

// Check if user is logged in and is a driver
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'driver') {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Access denied. Driver access required.']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get JSON input
    $input = json_decode(file_get_contents('php://input'), true);
    
    if (!$input || !isset($input['shuttle_id']) || !isset($input['latitude']) || !isset($input['longitude'])) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Missing required parameters: shuttle_id, latitude, longitude']);
        exit();
    }
    
    $shuttle_id = intval($input['shuttle_id']);
    $latitude = floatval($input['latitude']);
    $longitude = floatval($input['longitude']);
    
    // Validate coordinates
    if ($latitude < -90 || $latitude > 90 || $longitude < -180 || $longitude > 180) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Invalid coordinates']);
        exit();
    }
    
    // Verify the shuttle belongs to this driver
    $stmt = $conn->prepare("SELECT id FROM shuttles WHERE id=? AND driver_id=?");
    $stmt->bind_param("ii", $shuttle_id, $_SESSION['user_id']);
    $stmt->execute();
    $stmt->store_result();
    
    if ($stmt->num_rows === 0) {
        $stmt->close();
        http_response_code(403);
        echo json_encode(['success' => false, 'message' => 'Invalid shuttle or access denied']);
        exit();
    }
    $stmt->close();
    
    // Update shuttle location
    $stmt = $conn->prepare("UPDATE shuttles SET latitude=?, longitude=?, location_updated_at=NOW() WHERE id=?");
    $stmt->bind_param("ddi", $latitude, $longitude, $shuttle_id);
    
    if ($stmt->execute()) {
        echo json_encode([
            'success' => true, 
            'message' => 'Location updated successfully',
            'timestamp' => date('Y-m-d H:i:s')
        ]);
    } else {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Failed to update location']);
    }
    
    $stmt->close();
} else {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
}
?>
