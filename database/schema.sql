-- ==============================
-- OrangeRoute Database (XAMPP Ready)
-- No sample data
-- ==============================

-- 1. Create the database
CREATE DATABASE IF NOT EXISTS orangeroute;
USE orangeroute;

-- ==============================
-- 2. Users table
-- ==============================
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('user','driver','admin') NOT NULL,
    profile_picture VARCHAR(255) DEFAULT NULL,
    verified BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- ==============================
-- 3. Email verification codes table
-- ==============================
CREATE TABLE email_verifications (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    code VARCHAR(10) NOT NULL,
    expires_at DATETIME NOT NULL,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ==============================
-- 4. Password reset codes table
-- ==============================
CREATE TABLE password_resets (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    code VARCHAR(10) NOT NULL,
    expires_at DATETIME NOT NULL,
    used BOOLEAN DEFAULT FALSE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ==============================
-- 5. Password history table
-- ==============================
CREATE TABLE password_history (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_user_password (user_id, password_hash)
) ENGINE=InnoDB;

-- ==============================
-- 6. Shuttles table (with location tracking)
-- ==============================
CREATE TABLE shuttles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    driver_id INT NOT NULL,
    name VARCHAR(50) NOT NULL,
    route_name VARCHAR(50) NOT NULL,
    status ENUM('active','inactive') DEFAULT 'inactive',
    traffic_status BOOLEAN DEFAULT FALSE,
    live_link VARCHAR(255) DEFAULT NULL,
    latitude DECIMAL(10, 8) DEFAULT NULL,
    longitude DECIMAL(11, 8) DEFAULT NULL,
    location_updated_at TIMESTAMP DEFAULT NULL,
    last_updated TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (driver_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ==============================
-- 7. General chat table
-- ==============================
CREATE TABLE general_chat (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    message TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ==============================
-- 8. Indexes for performance
-- ==============================
CREATE INDEX idx_shuttle_status ON shuttles(status);
CREATE INDEX idx_shuttle_location ON shuttles(latitude, longitude);
CREATE INDEX idx_shuttle_active_location ON shuttles(status, latitude, longitude);
CREATE INDEX idx_chat_created_at ON general_chat(created_at);

-- ==============================
-- 9. Sample data for testing
-- ==============================

-- Insert admin user (password: admin123)
INSERT INTO users (username, email, password, role, verified) VALUES 
('admin', 'admin@orangeroute.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', TRUE);

-- Insert sample driver (password: driver123)
INSERT INTO users (username, email, password, role, verified) VALUES 
('driver1', 'driver@orangeroute.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'driver', TRUE);

-- Insert sample student (password: student123)
INSERT INTO users (username, email, password, role, verified) VALUES 
('student1', 'student@orangeroute.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'user', TRUE);

-- Insert sample shuttle
INSERT INTO shuttles (driver_id, name, route_name, status, traffic_status) VALUES 
(2, 'Shuttle A', 'UIU - Natunbazar', 'inactive', FALSE);

-- ==============================
-- 10. Additional useful tables (optional)
-- ==============================

-- Routes table for better route management
CREATE TABLE routes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    start_location VARCHAR(255),
    end_location VARCHAR(255),
    estimated_duration INT, -- in minutes
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Insert sample routes
INSERT INTO routes (name, description, start_location, end_location, estimated_duration) VALUES 
('UIU - Natunbazar', 'Main route from UIU to Natunbazar', 'UIU Campus', 'Natunbazar', 25),
('UIU - Kuril', 'Route from UIU to Kuril', 'UIU Campus', 'Kuril', 30),
('UIU - Aftabnagar', 'Route from UIU to Aftabnagar', 'UIU Campus', 'Aftabnagar', 35);

-- Notifications table for system notifications
CREATE TABLE notifications (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    title VARCHAR(255) NOT NULL,
    message TEXT NOT NULL,
    type ENUM('info', 'warning', 'success', 'error') DEFAULT 'info',
    is_read BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- System settings table
CREATE TABLE settings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    setting_key VARCHAR(100) NOT NULL UNIQUE,
    setting_value TEXT,
    description TEXT,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Insert default settings
INSERT INTO settings (setting_key, setting_value, description) VALUES 
('app_name', 'OrangeRoute', 'Application name'),
('app_version', '1.0.0', 'Application version'),
('maintenance_mode', '0', 'Maintenance mode (0=off, 1=on)'),
('max_shuttles', '10', 'Maximum number of active shuttles'),
('location_update_interval', '30', 'Location update interval in seconds');

-- ==============================
-- 11. Additional indexes for new tables
-- ==============================
CREATE INDEX idx_notifications_user ON notifications(user_id, is_read);
CREATE INDEX idx_notifications_created_at ON notifications(created_at);
CREATE INDEX idx_routes_active ON routes(is_active);
