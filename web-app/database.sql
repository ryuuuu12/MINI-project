-- Create database (run this first if database doesn't exist)
-- CREATE DATABASE IF NOT EXISTS drowsiness_db;
-- USE drowsiness_db;

-- Users table
CREATE TABLE IF NOT EXISTS `users` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `name` VARCHAR(100) NOT NULL,
    `email` VARCHAR(100) NOT NULL UNIQUE,
    `password` VARCHAR(255) NOT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Drowsiness data table
CREATE TABLE IF NOT EXISTS `data` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `user_id` INT(11) NOT NULL,
    `drowsiness_percentage` DECIMAL(5,2) NOT NULL,
    `detection_time` TIME NOT NULL,
    `detection_date` DATE NOT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
    INDEX idx_user_date (`user_id`, `detection_date`),
    INDEX idx_detection_time (`detection_time`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Sample insert (optional - for testing)
-- INSERT INTO `users` (`name`, `email`, `password`) VALUES 
-- ('Admin', 'admin@example.com', '$2y$10$YourHashedPasswordHere');