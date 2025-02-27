-- Jobs Table
CREATE TABLE jobs(
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL UNIQUE,
    script TEXT,
    country ENUM('CANADA', 'USA') NOT NULL,
    state VARCHAR(100) NOT NULL,
    file_path VARCHAR(50),
    budget ENUM('low', 'medium', 'high') NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    ip_address VARCHAR(45), -- For rate limiting
    user_agent VARCHAR(255) -- For analytics
);

-- Indexes for Dashboard & Uniqueness Checks
CREATE INDEX idx_title ON jobs (title);
CREATE INDEX idx_country ON jobs (country);