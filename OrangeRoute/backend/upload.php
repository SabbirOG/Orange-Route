<?php
// upload.php

// Include database connection
require_once 'db.php';

function uploadProfilePicture($userId, $file) {
    // Check if the file type is allowed
    if (!isAllowedFileType($file['type'])) {
        $allowedTypes = unserialize(UPLOAD_ALLOWED_TYPES);
        return ['success' => false, 'message' => 'Invalid file type. Allowed types: ' . implode(', ', $allowedTypes)];
    }

    // Check file size
    if ($file['size'] > UPLOAD_MAX_SIZE) {
        return ['success' => false, 'message' => 'File too large. Maximum size: ' . formatFileSize(UPLOAD_MAX_SIZE)];
    }

    // Check for upload errors
    if ($file['error'] !== UPLOAD_ERR_OK) {
        return ['success' => false, 'message' => 'File upload error.'];
    }

    // Generate a unique file name
    $fileName = uniqid('profile_', true) . '.' . pathinfo($file['name'], PATHINFO_EXTENSION);
    $filePath = UPLOAD_PATH . $fileName;

    // Move the uploaded file to the designated directory
    if (move_uploaded_file($file['tmp_name'], $filePath)) {
        // Update the user's profile picture in the database
        global $conn;
        $stmt = $conn->prepare("UPDATE users SET profile_picture = ? WHERE id = ?");
        $stmt->bind_param("si", $fileName, $userId);
        $stmt->execute();
        $stmt->close();

        return ['success' => true, 'message' => 'Profile picture uploaded successfully.'];
    } else {
        return ['success' => false, 'message' => 'Failed to move uploaded file.'];
    }
}

// Example usage
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['profile_picture'])) {
    $userId = $_POST['user_id']; // Assume user ID is sent via POST
    $result = uploadProfilePicture($userId, $_FILES['profile_picture']);
    echo json_encode($result);
}
?>