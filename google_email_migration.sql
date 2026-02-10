-- ========================================
-- GOOGLE SIGN-IN & PASSWORD RESET
-- Database Migration
-- FirstStep Internship Platform
-- ========================================

-- Step 1: Create password_resets table
CREATE TABLE IF NOT EXISTS password_resets (
    reset_id INT AUTO_INCREMENT PRIMARY KEY,
    user_type ENUM('student', 'company', 'admin') NOT NULL,
    email VARCHAR(255) NOT NULL,
    token VARCHAR(255) NOT NULL,
    expires_at DATETIME NOT NULL,
    used BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_token (token),
    INDEX idx_email (email),
    INDEX idx_expires (expires_at)
);

-- Step 2: Add Google OAuth fields to students table
ALTER TABLE students 
ADD COLUMN IF NOT EXISTS google_id VARCHAR(255) NULL UNIQUE AFTER email,
ADD COLUMN IF NOT EXISTS auth_provider ENUM('local', 'google') DEFAULT 'local' AFTER google_id;

-- Step 3: Add Google OAuth fields to companies table
ALTER TABLE companies
ADD COLUMN IF NOT EXISTS google_id VARCHAR(255) NULL UNIQUE AFTER company_email,
ADD COLUMN IF NOT EXISTS auth_provider ENUM('local', 'google') DEFAULT 'local' AFTER google_id;

-- Step 4: Add Google OAuth fields to admins table
ALTER TABLE admins
ADD COLUMN IF NOT EXISTS google_id VARCHAR(255) NULL UNIQUE AFTER admin_email,
ADD COLUMN IF NOT EXISTS auth_provider ENUM('local', 'google') DEFAULT 'local' AFTER google_id;

-- ========================================
-- VERIFICATION
-- ========================================

-- Check password_resets table
SELECT 'password_resets table created' AS status;
SHOW TABLES LIKE 'password_resets';

-- Check new columns in students
SELECT 'Checking students table...' AS status;
SHOW COLUMNS FROM students LIKE 'google_id';
SHOW COLUMNS FROM students LIKE 'auth_provider';

-- Check new columns in companies
SELECT 'Checking companies table...' AS status;
SHOW COLUMNS FROM companies LIKE 'google_id';
SHOW COLUMNS FROM companies LIKE 'auth_provider';

-- Check new columns in admins
SELECT 'Checking admins table...' AS status;
SHOW COLUMNS FROM admins LIKE 'google_id';
SHOW COLUMNS FROM admins LIKE 'auth_provider';

-- ========================================
-- NOTES
-- ========================================

/*
What was added:

1. password_resets table:
   - Stores password reset tokens
   - Tracks expiration (24 hours)
   - Marks tokens as used
   - Indexed for fast lookups

2. google_id column:
   - Stores Google account ID
   - Unique constraint (one Google account = one platform account)
   - NULL allowed (for regular email/password accounts)

3. auth_provider column:
   - Tracks how user registered ('local' or 'google')
   - Defaults to 'local' for existing accounts
   - Used to determine if password is required

Benefits:
- Users can login with Google
- Users can reset forgotten passwords
- Secure token-based reset system
- Prevents duplicate Google accounts
*/
