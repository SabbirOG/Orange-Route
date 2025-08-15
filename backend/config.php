<?php
// config.php - OrangeRoute Configuration File
// Centralized configuration for the entire application

// ===========================================
// DATABASE CONFIGURATION
// ===========================================
define('DB_HOST', 'localhost');
define('DB_USERNAME', 'root');
define('DB_PASSWORD', '');
define('DB_NAME', 'orangeroute');
define('DB_CHARSET', 'utf8mb4');

// ===========================================
// APPLICATION SETTINGS
// ===========================================
define('APP_NAME', 'OrangeRoute');
define('APP_VERSION', '1.0.0');
define('APP_URL', 'http://localhost/OrangeRoute');
define('APP_TIMEZONE', 'Asia/Dhaka');

// ===========================================
// SECURITY SETTINGS
// ===========================================
define('SESSION_LIFETIME', 3600); // 1 hour in seconds
define('PASSWORD_MIN_LENGTH', 6);
define('MAX_LOGIN_ATTEMPTS', 5);
define('LOGIN_LOCKOUT_TIME', 900); // 15 minutes

// ===========================================
// FILE UPLOAD SETTINGS
// ===========================================
define('UPLOAD_MAX_SIZE', 5 * 1024 * 1024); // 5MB
define('UPLOAD_ALLOWED_TYPES', serialize(['image/jpeg', 'image/png', 'image/gif']));
define('UPLOAD_PATH', '../uploads/profile_pictures/');
define('DEFAULT_AVATAR', 'default_avatar.png');

// ===========================================
// EMAIL SETTINGS (for future email functionality)
// ===========================================
define('SMTP_HOST', 'smtp.gmail.com');
define('SMTP_PORT', 587);
define('SMTP_USERNAME', '');
define('SMTP_PASSWORD', '');
define('FROM_EMAIL', 'noreply@orangeroute.com');
define('FROM_NAME', 'OrangeRoute');

// ===========================================
// CHAT SETTINGS
// ===========================================
define('CHAT_MESSAGE_LIMIT', 50);
define('CHAT_REFRESH_INTERVAL', 5000); // 5 seconds in milliseconds

// ===========================================
// ROUTE SETTINGS
// ===========================================
define('AVAILABLE_ROUTES', serialize([
    'uiu-natunbazar' => 'UIU - Natunbazar',
    'uiu-kuril' => 'UIU - Kuril', 
    'uiu-aftabnagar' => 'UIU - Aftabnagar'
]));

// ===========================================
// ERROR HANDLING
// ===========================================
define('DEBUG_MODE', true); // Set to false in production
define('LOG_ERRORS', true);
define('ERROR_LOG_FILE', '../logs/error.log');

// ===========================================
// TIMEZONE SETTING
// ===========================================
date_default_timezone_set(APP_TIMEZONE);

// ===========================================
// ERROR REPORTING (based on debug mode)
// ===========================================
ini_set('log_errors', 1);
ini_set('error_log', ERROR_LOG_FILE);

// Set error reporting based on debug mode
$errorLevel = DEBUG_MODE ? E_ALL : (E_ERROR | E_WARNING | E_PARSE);
error_reporting($errorLevel);

// Set display errors based on debug mode
ini_set('display_errors', DEBUG_MODE ? 1 : 0);

// ===========================================
// HELPER FUNCTIONS
// ===========================================

/**
 * Get application URL
 */
function getAppUrl($path = '') {
    return APP_URL . '/' . ltrim($path, '/');
}

/**
 * Get upload URL
 */
function getUploadUrl($filename = '') {
    return getAppUrl('uploads/profile_pictures/' . $filename);
}

/**
 * Check if file type is allowed for upload
 */
function isAllowedFileType($fileType) {
    $allowedTypes = unserialize(UPLOAD_ALLOWED_TYPES);
    return in_array($fileType, $allowedTypes);
}

/**
 * Format file size
 */
function formatFileSize($bytes) {
    $units = ['B', 'KB', 'MB', 'GB'];
    $bytes = max($bytes, 0);
    $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
    $pow = min($pow, count($units) - 1);
    $bytes /= (1 << (10 * $pow));
    return round($bytes, 2) . ' ' . $units[$pow];
}

/**
 * Log error message
 */
function logError($message, $file = '', $line = '') {
    if (LOG_ERRORS) {
        $timestamp = date('Y-m-d H:i:s');
        $logMessage = "[$timestamp] $message";
        if ($file) $logMessage .= " in $file";
        if ($line) $logMessage .= " on line $line";
        $logMessage .= PHP_EOL;
        
        file_put_contents(ERROR_LOG_FILE, $logMessage, FILE_APPEND | LOCK_EX);
    }
}

/**
 * Get available routes as options for select
 */
function getRouteOptions($selected = '') {
    $routes = unserialize(AVAILABLE_ROUTES);
    $options = '<option value="">Select Route</option>';
    foreach ($routes as $value => $label) {
        $selectedAttr = ($selected === $value) ? ' selected' : '';
        $options .= "<option value=\"$value\"$selectedAttr>$label</option>";
    }
    return $options;
}
?>
