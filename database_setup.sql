-- Database setup for TaleemiSaharaApp
-- Run these queries in phpMyAdmin to create the necessary tables

-- Create donor_profiles table
CREATE TABLE IF NOT EXISTS donor_profiles (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    organization VARCHAR(255) NOT NULL,
    phone VARCHAR(20) NOT NULL,
    address TEXT NOT NULL,
    website VARCHAR(255),
    contact_person VARCHAR(255),
    donation_preferences TEXT,
    annual_budget DECIMAL(12,2),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Create donations table
CREATE TABLE IF NOT EXISTS donations (
    id INT PRIMARY KEY AUTO_INCREMENT,
    donor_id INT NOT NULL,
    scholarship_id INT,
    amount DECIMAL(10,2) NOT NULL,
    donation_date DATE NOT NULL,
    payment_method VARCHAR(50),
    transaction_id VARCHAR(100),
    status ENUM('pending', 'completed', 'failed') DEFAULT 'completed',
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (donor_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (scholarship_id) REFERENCES scholarships(id) ON DELETE SET NULL
);

-- Create scholarships table (if not exists)
CREATE TABLE IF NOT EXISTS scholarships (
    id INT PRIMARY KEY AUTO_INCREMENT,
    title VARCHAR(255) NOT NULL,
    description TEXT,
    amount DECIMAL(10,2) NOT NULL,
    requirements TEXT,
    deadline DATE NOT NULL,
    available_slots INT NOT NULL DEFAULT 1,
    status ENUM('active', 'inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Create scholarship_applications table (if not exists)
CREATE TABLE IF NOT EXISTS scholarship_applications (
    id INT PRIMARY KEY AUTO_INCREMENT,
    student_id INT NOT NULL,
    scholarship_id INT NOT NULL,
    status ENUM('pending', 'approved', 'rejected') DEFAULT 'pending',
    application_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    notes TEXT,
    FOREIGN KEY (student_id) REFERENCES users(id),
    FOREIGN KEY (scholarship_id) REFERENCES scholarships(id)
);

-- Create videos table (if not exists)
CREATE TABLE IF NOT EXISTS videos (
    id INT PRIMARY KEY AUTO_INCREMENT,
    title VARCHAR(255) NOT NULL,
    description TEXT,
    video_url VARCHAR(500) NOT NULL,
    embed_url VARCHAR(500) NOT NULL,
    category VARCHAR(100),
    status ENUM('active', 'inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Insert sample data for testing (optional)
-- Sample scholarship
INSERT INTO scholarships (title, description, amount, requirements, deadline, available_slots) VALUES 
('Merit Scholarship 2024', 'Scholarship for outstanding academic performance', 50000.00, 'CGPA 3.5 or above', '2024-12-31', 10);

-- Sample donation (replace donor_id with actual user ID)
-- INSERT INTO donations (donor_id, scholarship_id, amount, donation_date, status) VALUES 
-- (1, 1, 25000.00, '2024-01-15', 'completed');

-- Sample video
-- INSERT INTO videos (title, description, video_url, embed_url, category) VALUES 
-- ('Introduction to Scholarships', 'Learn about different types of scholarships and how to apply', 'https://www.youtube.com/watch?v=example', 'https://www.youtube.com/embed/example', 'educational'); 