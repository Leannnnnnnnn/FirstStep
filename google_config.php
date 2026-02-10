<?php
/**
 * Google Sign-In Configuration
 * FirstStep Internship Platform
 * 
 * SETUP INSTRUCTIONS:
 * 1. Go to: https://console.cloud.google.com/
 * 2. Create project: "FirstStep Platform"
 * 3. Configure OAuth consent screen
 * 4. Create OAuth Client ID (Web application)
 * 5. Add redirect URI: http://localhost/firststep/google_callback.php
 * 6. Copy Client ID and paste below
 */

// Google OAuth Client ID
define('GOOGLE_CLIENT_ID', '870979727793-n03nrj2si100274t6j1qf43ogarn06ck.apps.googleusercontent.com');

// Example (replace with your actual Client ID):
// define('GOOGLE_CLIENT_ID', '123456789-abcdefghijklmnop.apps.googleusercontent.com');

/**
 * SECURITY NOTES:
 * - Never commit this file to public repositories
 * - Add to .gitignore: google_config.php
 * - Client ID is public but still sensitive
 * - Update redirect URIs for production
 */
?>
