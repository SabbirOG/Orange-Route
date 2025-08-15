<?php
// upload.php
session_start();

// Include database connection
require_once 'db.php';

// Check if user is logged in
if (!isset($_SESSION['user_id']) || empty($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Authentication required']);
    exit();
}

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
        $errorMessages = [
            UPLOAD_ERR_INI_SIZE => 'File exceeds upload_max_filesize directive',
            UPLOAD_ERR_FORM_SIZE => 'File exceeds MAX_FILE_SIZE directive',
            UPLOAD_ERR_PARTIAL => 'File was only partially uploaded',
            UPLOAD_ERR_NO_FILE => 'No file was uploaded',
            UPLOAD_ERR_NO_TMP_DIR => 'Missing temporary folder',
            UPLOAD_ERR_CANT_WRITE => 'Failed to write file to disk',
            UPLOAD_ERR_EXTENSION => 'File upload stopped by extension'
        ];
        $errorMsg = isset($errorMessages[$file['error']]) ? $errorMessages[$file['error']] : 'Unknown upload error';
        return ['success' => false, 'message' => 'File upload error: ' . $errorMsg];
    }

    // Set upload directory path - use absolute path with proper directory separator
    $uploadDir = dirname(__DIR__) . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR . 'profile_pictures' . DIRECTORY_SEPARATOR;
    
    // Check if directory exists, if not try to create it
    if (!is_dir($uploadDir)) {
        if (!mkdir($uploadDir, 0777, true)) {
            return ['success' => false, 'message' => 'Failed to create upload directory. Path: ' . $uploadDir];
        }
    }
    
    // Check if directory is writable
    if (!is_writable($uploadDir)) {
        return ['success' => false, 'message' => 'Upload directory is not writable. Path: ' . $uploadDir . ' Permissions: ' . substr(sprintf('%o', fileperms($uploadDir)), -4)];
    }

    // Generate a unique file name
    $fileName = uniqid('profile_', true) . '.' . pathinfo($file['name'], PATHINFO_EXTENSION);
    $filePath = $uploadDir . $fileName;

    // Move the uploaded file to the designated directory (preserve original quality)
    if (move_uploaded_file($file['tmp_name'], $filePath)) {
        // Update the user's profile picture in the database
        global $conn;
        $stmt = $conn->prepare("UPDATE users SET profile_picture = ? WHERE id = ?");
        $stmt->bind_param("si", $fileName, $userId);
        
        if ($stmt->execute()) {
            $stmt->close();
            return ['success' => true, 'message' => 'Profile picture uploaded successfully.'];
        } else {
            $stmt->close();
            // Delete the uploaded file if database update failed
            unlink($filePath);
            return ['success' => false, 'message' => 'Failed to update database.'];
        }
    } else {
        return ['success' => false, 'message' => 'Failed to move uploaded file. Check directory permissions.'];
    }
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['profile_picture'])) {
    // Validate CSRF token
    if (!isset($_POST['csrf_token']) || !validateCSRFToken($_POST['csrf_token'])) {
        header("Location: ../frontend/profile.php?error=csrf_invalid");
        exit();
    }
    
    $userId = $_SESSION['user_id']; // Use session user ID for security
    
    $result = uploadProfilePicture($userId, $_FILES['profile_picture']);
    
    if ($result['success']) {
        header("Location: ../frontend/profile.php?success=uploaded");
    } else {
        header("Location: ../frontend/profile.php?error=" . urlencode($result['message']));
    }
    exit();
}
?>