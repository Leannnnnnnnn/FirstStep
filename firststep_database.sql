-- FirstStep Database Schema (UPDATED)
-- Run this in phpMyAdmin to create the database

CREATE DATABASE IF NOT EXISTS firststep_db;
USE firststep_db;

-- Students Table (UPDATED with separate name fields and multiple internship types)
CREATE TABLE students (
    student_id INT AUTO_INCREMENT PRIMARY KEY,
    first_name VARCHAR(100) NOT NULL,
    middle_name VARCHAR(100),
    surname VARCHAR(100) NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    school VARCHAR(255),
    course VARCHAR(255),
    year_level VARCHAR(50),
    city VARCHAR(100),
    barangay VARCHAR(100),
    internship_types VARCHAR(255),
    skills TEXT,
    resume_path VARCHAR(255),
    profile_picture VARCHAR(255),
    account_status ENUM('active', 'inactive', 'suspended') DEFAULT 'active',
    last_login DATETIME,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Companies Table (unchanged)
CREATE TABLE companies (
    company_id INT AUTO_INCREMENT PRIMARY KEY,
    company_name VARCHAR(255) NOT NULL,
    company_email VARCHAR(255) UNIQUE NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    contact_number VARCHAR(50),
    contact_person VARCHAR(255),
    industry_type VARCHAR(100),
    company_address TEXT,
    company_description TEXT,
    company_logo VARCHAR(255),
    verification_status ENUM('pending', 'verified', 'rejected') DEFAULT 'pending',
    account_status ENUM('active', 'inactive', 'suspended') DEFAULT 'active',
    last_login DATETIME,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Internship Postings Table (unchanged)
CREATE TABLE internship_postings (
    posting_id INT AUTO_INCREMENT PRIMARY KEY,
    company_id INT NOT NULL,
    job_title VARCHAR(255) NOT NULL,
    job_description TEXT,
    requirements TEXT,
    internship_type ENUM('On-site', 'Remote', 'Hybrid'),
    location VARCHAR(255),
    duration VARCHAR(100),
    stipend VARCHAR(100),
    slots_available INT,
    application_deadline DATE,
    status ENUM('active', 'closed', 'draft') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (company_id) REFERENCES companies(company_id) ON DELETE CASCADE
);

-- Applications Table (unchanged)
CREATE TABLE applications (
    application_id INT AUTO_INCREMENT PRIMARY KEY,
    student_id INT NOT NULL,
    posting_id INT NOT NULL,
    company_id INT NOT NULL,
    cover_letter TEXT,
    status ENUM('pending', 'reviewed', 'shortlisted', 'accepted', 'rejected') DEFAULT 'pending',
    applied_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    reviewed_at DATETIME,
    notes TEXT,
    FOREIGN KEY (student_id) REFERENCES students(student_id) ON DELETE CASCADE,
    FOREIGN KEY (posting_id) REFERENCES internship_postings(posting_id) ON DELETE CASCADE,
    FOREIGN KEY (company_id) REFERENCES companies(company_id) ON DELETE CASCADE
);

-- Activity Logs Table (unchanged)
CREATE TABLE activity_logs (
    log_id INT AUTO_INCREMENT PRIMARY KEY,
    user_type ENUM('student', 'company'),
    user_id INT NOT NULL,
    action VARCHAR(100),
    description TEXT,
    ip_address VARCHAR(50),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Messages Table (unchanged - for future use)
CREATE TABLE messages (
    message_id INT AUTO_INCREMENT PRIMARY KEY,
    sender_type ENUM('student', 'company'),
    sender_id INT NOT NULL,
    receiver_type ENUM('student', 'company'),
    receiver_id INT NOT NULL,
    subject VARCHAR(255),
    message TEXT,
    is_read BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Insert sample data for testing
INSERT INTO companies (company_name, company_email, password_hash, contact_number, contact_person, industry_type, company_address, company_description, verification_status)
VALUES 
('Tech Innovations Inc.', 'hr@techinnovations.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '+63 912 345 6789', 'Maria Santos', 'Technology', '123 Business St., Makati City', 'Leading technology company specializing in software development and IT solutions.', 'verified'),
('Creative Digital Agency', 'careers@creativedigital.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '+63 917 234 5678', 'John Cruz', 'Marketing', '456 Design Ave., BGC, Taguig', 'Full-service digital marketing agency helping brands grow online.', 'verified');

INSERT INTO internship_postings (company_id, job_title, job_description, requirements, internship_type, location, duration, stipend, slots_available, application_deadline, status)
VALUES 
(1, 'Web Development Intern', 'Join our development team and work on exciting web projects using modern technologies.', 'Knowledge of HTML, CSS, JavaScript, PHP. Familiarity with databases. Good communication skills.', 'Hybrid', 'Makati City', '3-6 months', '5,000 - 8,000 per month', 3, '2026-02-28', 'active'),
(1, 'Mobile App Development Intern', 'Assist in developing mobile applications for iOS and Android platforms.', 'Basic knowledge of mobile development. Familiarity with React Native or Flutter is a plus.', 'Remote', 'Remote Work', '4 months', '6,000 - 10,000 per month', 2, '2026-02-15', 'active'),
(2, 'Digital Marketing Intern', 'Learn and assist in social media management, content creation, and digital campaigns.', 'Creative mindset. Familiarity with social media platforms. Good writing skills.', 'On-site', 'BGC, Taguig', '3 months', '4,000 - 6,000 per month', 4, '2026-03-15', 'active');