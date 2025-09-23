<?php
// config.php - OrangeRoute Configuration File
// Centralized configuration for the entire application

// ===========================================
// DATABASE CONFIGURATION
// ===========================================
if (!defined('DB_HOST')) define('DB_HOST', 'localhost');
if (!defined('DB_USERNAME')) define('DB_USERNAME', 'root');
if (!defined('DB_PASSWORD')) define('DB_PASSWORD', '');
if (!defined('DB_NAME')) define('DB_NAME', 'orangeroute');
if (!defined('DB_CHARSET')) define('DB_CHARSET', 'utf8mb4');

// ===========================================
// APPLICATION SETTINGS
// ===========================================
if (!defined('APP_NAME')) define('APP_NAME', 'OrangeRoute');
if (!defined('APP_VERSION')) define('APP_VERSION', '2.1.0');
if (!defined('APP_URL')) define('APP_URL', 'http://localhost/OrangeRoute');
if (!defined('APP_TIMEZONE')) define('APP_TIMEZONE', 'Asia/Dhaka');

// ===========================================
// SECURITY SETTINGS
// ===========================================
if (!defined('SESSION_LIFETIME')) define('SESSION_LIFETIME', 3600); // 1 hour in seconds
if (!defined('PASSWORD_MIN_LENGTH')) define('PASSWORD_MIN_LENGTH', 6);
if (!defined('MAX_LOGIN_ATTEMPTS')) define('MAX_LOGIN_ATTEMPTS', 5);
if (!defined('LOGIN_LOCKOUT_TIME')) define('LOGIN_LOCKOUT_TIME', 900); // 15 minutes

// ===========================================
// FILE UPLOAD SETTINGS
// ===========================================
if (!defined('UPLOAD_MAX_SIZE')) define('UPLOAD_MAX_SIZE', 5 * 1024 * 1024); // 5MB
if (!defined('UPLOAD_ALLOWED_TYPES')) define('UPLOAD_ALLOWED_TYPES', serialize(['image/jpeg', 'image/png', 'image/gif']));
if (!defined('UPLOAD_PATH')) define('UPLOAD_PATH', '../uploads/profile_pictures/');
if (!defined('DEFAULT_AVATAR')) define('DEFAULT_AVATAR', 'default_avatar.png');

// ===========================================
// EMAIL SETTINGS (for future email functionality)
// ===========================================
if (!defined('SMTP_HOST')) define('SMTP_HOST', 'smtp.gmail.com');
if (!defined('SMTP_PORT')) define('SMTP_PORT', 587);
if (!defined('SMTP_USERNAME')) define('SMTP_USERNAME', '');
if (!defined('SMTP_PASSWORD')) define('SMTP_PASSWORD', '');
if (!defined('FROM_EMAIL')) define('FROM_EMAIL', 'noreply@orangeroute.com');
if (!defined('FROM_NAME')) define('FROM_NAME', 'OrangeRoute');

// ===========================================
// CHAT SETTINGS
// ===========================================
if (!defined('CHAT_MESSAGE_LIMIT')) define('CHAT_MESSAGE_LIMIT', 50);
if (!defined('CHAT_REFRESH_INTERVAL')) define('CHAT_REFRESH_INTERVAL', 5000); // 5 seconds in milliseconds

// ===========================================
// ROUTE SETTINGS
// ===========================================
if (!defined('AVAILABLE_ROUTES')) define('AVAILABLE_ROUTES', serialize([
    'uiu-natunbazar' => 'UIU - Natunbazar',
    'uiu-kuril' => 'UIU - Kuril', 
    'uiu-aftabnagar' => 'UIU - Aftabnagar'
]));

// ===========================================
// ERROR HANDLING
// ===========================================
if (!defined('DEBUG_MODE')) define('DEBUG_MODE', true); // Set to false in production
if (!defined('LOG_ERRORS')) define('LOG_ERRORS', true);
if (!defined('ERROR_LOG_FILE')) define('ERROR_LOG_FILE', '../logs/error.log');

// ===========================================
// SESSION CONFIGURATION
// ===========================================
// Only set session ini settings if session hasn't been started
if (session_status() === PHP_SESSION_NONE) {
    ini_set('session.cookie_lifetime', SESSION_LIFETIME);
    ini_set('session.gc_maxlifetime', SESSION_LIFETIME);
    ini_set('session.cookie_httponly', 1);
    ini_set('session.cookie_secure', 0); // Set to 1 for HTTPS
    ini_set('session.use_strict_mode', 1);
    ini_set('session.cookie_samesite', 'Lax');
}

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
if (!function_exists('getAppUrl')) {
    function getAppUrl($path = '') {
        return APP_URL . '/' . ltrim($path, '/');
    }
}

/**
 * Get upload URL
 */
if (!function_exists('getUploadUrl')) {
    function getUploadUrl($filename = '') {
        return getAppUrl('uploads/profile_pictures/' . $filename);
    }
}

/**
 * Check if file type is allowed for upload
 */
if (!function_exists('isAllowedFileType')) {
    function isAllowedFileType($fileType) {
        $allowedTypes = unserialize(UPLOAD_ALLOWED_TYPES);
        return in_array($fileType, $allowedTypes);
    }
}

/**
 * Format file size
 */
if (!function_exists('formatFileSize')) {
    function formatFileSize($bytes) {
        $units = ['B', 'KB', 'MB', 'GB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        $bytes /= (1 << (10 * $pow));
        return round($bytes, 2) . ' ' . $units[$pow];
    }
}

/**
 * Log error message
 */
if (!function_exists('logError')) {
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
}

/**
 * Get available routes as options for select
 */
if (!function_exists('getRouteOptions')) {
    function getRouteOptions($selected = '') {
        $routes = unserialize(AVAILABLE_ROUTES);
        $options = '<option value="">Select Route</option>';
        foreach ($routes as $value => $label) {
            $selectedAttr = ($selected === $value) ? ' selected' : '';
            $options .= "<option value=\"$value\"$selectedAttr>$label</option>";
        }
        return $options;
    }
}

if (!function_exists('generateCSRFToken')) {
    function generateCSRFToken() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        if (!isset($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }
}

if (!function_exists('validateCSRFToken')) {
    function validateCSRFToken($token) {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
    }
}
?>
