<?php
// Debug location update endpoint
error_reporting(0);
ini_set('display_errors', 0);

session_start();
include 'db.php';

header('Content-Type: application/json');

// Check if user is logged in and is a driver
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'driver') {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Access denied. Driver access required.']);
    exit();
}

// Get JSON input
$input = json_decode(file_get_contents('php://input'), true);

echo json_encode([
    'success' => true,
    'debug' => [
        'session_user_id' => $_SESSION['user_id'],
        'session_role' => $_SESSION['role'],
        'input_received' => $input,
        'shuttle_id' => isset($input['shuttle_id']) ? $input['shuttle_id'] : 'not_set',
        'latitude' => isset($input['latitude']) ? $input['latitude'] : 'not_set',
        'longitude' => isset($input['longitude']) ? $input['longitude'] : 'not_set'
    ],
    'message' => 'Debug information'
]);
?>
