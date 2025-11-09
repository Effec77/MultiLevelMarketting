-- Create database
CREATE DATABASE IF NOT EXISTS mlm_system;
USE mlm_system;

-- Members table
CREATE TABLE IF NOT EXISTS members (
    id INT AUTO_INCREMENT PRIMARY KEY,
    member_code VARCHAR(50) UNIQUE NOT NULL,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    mobile VARCHAR(20) NOT NULL,
    password VARCHAR(255) NOT NULL,
    sponsor_code VARCHAR(50) NOT NULL,
    position ENUM('Left', 'Right') NOT NULL,
    left_member VARCHAR(50) DEFAULT NULL,
    right_member VARCHAR(50) DEFAULT NULL,
    left_count INT DEFAULT 0,
    right_count INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (sponsor_code) REFERENCES members(member_code) ON DELETE RESTRICT,
    FOREIGN KEY (left_member) REFERENCES members(member_code) ON DELETE SET NULL,
    FOREIGN KEY (right_member) REFERENCES members(member_code) ON DELETE SET NULL
);

-- Insert root member (admin)
INSERT INTO members (member_code, name, email, mobile, password, sponsor_code, position) 
VALUES ('ROOT001', 'Admin', 'admin@mlm.com', '1234567890', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'ROOT001', 'Left')
ON DUPLICATE KEY UPDATE member_code=member_code;
-- Password is: password
