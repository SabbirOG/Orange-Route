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
-- 5. Shuttles table
-- ==============================
CREATE TABLE shuttles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    driver_id INT NOT NULL,
    name VARCHAR(50) NOT NULL,
    route_name VARCHAR(50) NOT NULL,
    status ENUM('active','inactive') DEFAULT 'inactive',
    traffic_status BOOLEAN DEFAULT FALSE,
    live_link VARCHAR(255) DEFAULT NULL,
    last_updated TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (driver_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ==============================
-- 6. General chat table
-- ==============================
CREATE TABLE general_chat (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    message TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ==============================
-- 7. Indexes for performance
-- ==============================
CREATE INDEX idx_shuttle_status ON shuttles(status);
CREATE INDEX idx_chat_created_at ON general_chat(created_at);
