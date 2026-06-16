-- Table for tracking Lead info
CREATE TABLE IF NOT EXISTS `leads` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `service` VARCHAR(100) NOT NULL,
  `dynamic_questions` TEXT DEFAULT NULL, -- JSON object
  `home_ownership` VARCHAR(50) NOT NULL,
  `timeline` VARCHAR(50) NOT NULL,
  `hiring_status` VARCHAR(50) NOT NULL,
  `zip_code` VARCHAR(20) NOT NULL,
  `description` TEXT DEFAULT NULL,
  `first_name` VARCHAR(100) DEFAULT NULL,
  `last_name` VARCHAR(100) DEFAULT NULL,
  `phone` VARCHAR(30) DEFAULT NULL,
  `email` VARCHAR(150) DEFAULT NULL,
  `street_address` VARCHAR(255) DEFAULT NULL,
  `city` VARCHAR(100) DEFAULT NULL,
  `state` VARCHAR(50) DEFAULT NULL,
  `lead_token` VARCHAR(255) DEFAULT NULL,
  `status` ENUM('draft', 'ping_success', 'ping_failed', 'posted', 'post_failed') DEFAULT 'draft',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Table for custom Lead lifecycle logs
CREATE TABLE IF NOT EXISTS `lead_logs` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `lead_id` INT NOT NULL,
  `action` VARCHAR(100) NOT NULL,
  `message` TEXT DEFAULT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`lead_id`) REFERENCES `leads`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Table for saving outgoing API request payloads
CREATE TABLE IF NOT EXISTS `api_requests` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `lead_id` INT NOT NULL,
  `api_type` ENUM('ping', 'post') NOT NULL,
  `url` VARCHAR(255) NOT NULL,
  `request_body` LONGTEXT DEFAULT NULL,
  `headers` TEXT DEFAULT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`lead_id`) REFERENCES `leads`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Table for saving incoming API response payloads
CREATE TABLE IF NOT EXISTS `api_responses` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `lead_id` INT NOT NULL,
  `api_type` ENUM('ping', 'post') NOT NULL,
  `status_code` INT NOT NULL,
  `response_body` LONGTEXT DEFAULT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`lead_id`) REFERENCES `leads`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Table for dynamic database configurations
CREATE TABLE IF NOT EXISTS `settings` (
  `key` VARCHAR(100) PRIMARY KEY,
  `value` TEXT DEFAULT NULL,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Populate default settings
INSERT INTO `settings` (`key`, `value`) VALUES 
('campaign_code', 'default_campaign_code'),
('campaign_token', 'default_campaign_token_123'),
('ping_url', 'https://api.wiserleads.com/services/ping'),
('post_url', 'https://api.wiserleads.com/services/post'),
('mock_mode', '1')
ON DUPLICATE KEY UPDATE `key` = `key`;
