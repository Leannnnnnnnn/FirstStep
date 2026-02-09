-- ========================================
-- ADMIN SYSTEM - DATABASE UPDATES
-- FirstStep Internship Platform
-- ========================================

-- Step 1: Create Admins Table
CREATE TABLE IF NOT EXISTS admins (
    admin_id INT AUTO_INCREMENT PRIMARY KEY,
    admin_name VARCHAR(255) NOT NULL,
    admin_email VARCHAR(255) UNIQUE NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    role ENUM('super_admin', 'moderator') DEFAULT 'moderator',
    account_status ENUM('active', 'inactive') DEFAULT 'active',
    last_login DATETIME,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Step 2: Modify internship_postings table to add approval_status
ALTER TABLE internship_postings 
ADD COLUMN approval_status ENUM('pending', 'approved', 'rejected') DEFAULT 'pending' AFTER status,
ADD COLUMN reviewed_by INT NULL AFTER approval_status,
ADD COLUMN reviewed_at DATETIME NULL AFTER reviewed_by,
ADD COLUMN rejection_reason TEXT NULL AFTER reviewed_at,
ADD FOREIGN KEY (reviewed_by) REFERENCES admins(admin_id) ON DELETE SET NULL;

-- Step 3: Update existing posts to 'approved' (so they show immediately)
UPDATE internship_postings SET approval_status = 'approved' WHERE status = 'active';

-- Step 4: Update activity_logs to include admin
ALTER TABLE activity_logs 
MODIFY COLUMN user_type ENUM('student', 'company', 'admin');

-- Step 5: Insert default admin account
-- Password: Admin@123 (hashed with bcrypt)
INSERT INTO admins (admin_name, admin_email, password_hash, role, account_status) 
VALUES ('System Administrator', 'admin@firststep.com', '$2y$10$z.DdTyeDIvbEOPGlX4SUCuZDnZSZ2h1Mqc/USUFRAavhR1cb/7wd6', 'super_admin', 'active');

-- ========================================
-- VERIFICATION QUERIES
-- ========================================

-- Check if admins table was created
SELECT 'Admins table created' AS status, COUNT(*) AS admin_count FROM admins;

-- Check if columns were added to internship_postings
DESCRIBE internship_postings;

-- Check pending posts
SELECT COUNT(*) AS pending_posts FROM internship_postings WHERE approval_status = 'pending';

-- ========================================
-- DEFAULT ADMIN LOGIN CREDENTIALS
-- ========================================
-- Email: admin@firststep.com
-- Password: Admin@123
-- 
-- IMPORTANT: Change this password after first login!
-- ========================================
