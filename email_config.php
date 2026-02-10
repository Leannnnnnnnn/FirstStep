<?php
/**
 * Email Configuration
 * FirstStep Internship Platform
 * 
 * SETUP INSTRUCTIONS:
 * 
 * FOR GMAIL:
 * 1. Enable 2-Step Verification: https://myaccount.google.com/security
 * 2. Generate App Password: https://myaccount.google.com/apppasswords
 * 3. Copy the 16-character password (e.g., abcd efgh ijkl mnop)
 * 4. Paste below (remove spaces)
 */

// SMTP Settings - Gmail
define('SMTP_HOST', 'smtp.gmail.com');
define('SMTP_PORT', 587);
define('SMTP_USERNAME', 'firststepoffice04@gmail.com');     // Replace with your Gmail
define('SMTP_PASSWORD', 'ysut emhs pjkp enyd'); // Replace with App Password
define('SMTP_FROM_EMAIL', 'firststepoffice04@gmail.com');    // Same as username
define('SMTP_FROM_NAME', 'FirstStep Platform');

// Site Configuration
define('SITE_URL', 'http://localhost/firststep');     // Update for production

/**
 * FOR OTHER EMAIL PROVIDERS:
 * 
 * Outlook/Hotmail:
 * define('SMTP_HOST', 'smtp-mail.outlook.com');
 * define('SMTP_PORT', 587);
 * 
 * Yahoo:
 * define('SMTP_HOST', 'smtp.mail.yahoo.com');
 * define('SMTP_PORT', 587);
 */

/**
 * SECURITY NOTES:
 * - Never commit this file to public repositories
 * - Add to .gitignore: email_config.php
 * - Use App Passwords, not regular passwords
 * - Update SITE_URL for production deployment
 */
?>
