<?php
require_once 'config.php';

// Destroy session
session_unset();
session_destroy();

// Redirect to login page
$_SESSION['success'] = 'You have been logged out successfully.';
redirect('login.php');
?>