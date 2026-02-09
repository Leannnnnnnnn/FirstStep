<?php
require_once 'config.php';

// Check if request is POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    die('Invalid request method');
}

// Get form data
$email = sanitize_input($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';

// Validation
if (empty($email) || !validate_email($email)) {
    $_SESSION['error'] = 'Valid email is required';
    redirect('login.php');
}

if (empty($password)) {
    $_SESSION['error'] = 'Password is required';
    redirect('login.php');
}

// AUTO-DETECT: Try to find user in students table first
$stmt = $conn->prepare("SELECT student_id, first_name, middle_name, surname, email, password_hash, account_status FROM students WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    // STUDENT FOUND
    $user = $result->fetch_assoc();
    $stmt->close();
    
    // Verify password
    if (!verify_password($password, $user['password_hash'])) {
        $_SESSION['error'] = 'Invalid email or password';
        redirect('login.php');
    }
    
    // Check account status
    if ($user['account_status'] !== 'active') {
        $_SESSION['error'] = 'Your account is ' . $user['account_status'];
        redirect('login.php');
    }
    
    // Build full name
    $full_name = $user['first_name'];
    if (!empty($user['middle_name'])) {
        $full_name .= ' ' . $user['middle_name'];
    }
    $full_name .= ' ' . $user['surname'];
    
    // Set session
    $_SESSION['user_type'] = 'student';
    $_SESSION['user_id'] = $user['student_id'];
    $_SESSION['user_name'] = $full_name;
    $_SESSION['user_email'] = $user['email'];
    
    // Update last login
    $update_stmt = $conn->prepare("UPDATE students SET last_login = NOW() WHERE student_id = ?");
    $update_stmt->bind_param("i", $user['student_id']);
    $update_stmt->execute();
    $update_stmt->close();
    
    // Log activity
    $log_stmt = $conn->prepare("INSERT INTO activity_logs (user_type, user_id, action, description, ip_address) VALUES ('student', ?, 'login', 'Student logged in', ?)");
    $ip_address = $_SERVER['REMOTE_ADDR'];
    $log_stmt->bind_param("is", $user['student_id'], $ip_address);
    $log_stmt->execute();
    $log_stmt->close();
    
    $conn->close();
    
    // Redirect to student dashboard
    redirect('student_dashboard.php');
}

// If not found in students, check companies table
$stmt->close();
$stmt = $conn->prepare("SELECT company_id, company_name, company_email, password_hash, account_status, verification_status FROM companies WHERE company_email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    // COMPANY FOUND
    $user = $result->fetch_assoc();
    $stmt->close();
    
    // Verify password
    if (!verify_password($password, $user['password_hash'])) {
        $_SESSION['error'] = 'Invalid email or password';
        redirect('login.php');
    }
    
    // Check account status
    if ($user['account_status'] !== 'active') {
        $_SESSION['error'] = 'Your account is ' . $user['account_status'];
        redirect('login.php');
    }
    
    // Set session
    $_SESSION['user_type'] = 'company';
    $_SESSION['user_id'] = $user['company_id'];
    $_SESSION['company_name'] = $user['company_name'];
    $_SESSION['company_email'] = $user['company_email'];
    $_SESSION['verification_status'] = $user['verification_status'];
    
    // Update last login
    $update_stmt = $conn->prepare("UPDATE companies SET last_login = NOW() WHERE company_id = ?");
    $update_stmt->bind_param("i", $user['company_id']);
    $update_stmt->execute();
    $update_stmt->close();
    
    // Log activity
    $log_stmt = $conn->prepare("INSERT INTO activity_logs (user_type, user_id, action, description, ip_address) VALUES ('company', ?, 'login', 'Company logged in', ?)");
    $ip_address = $_SERVER['REMOTE_ADDR'];
    $log_stmt->bind_param("is", $user['company_id'], $ip_address);
    $log_stmt->execute();
    $log_stmt->close();
    
    $conn->close();
    
    // Redirect to company dashboard
    redirect('company_dashboard.php');
}

// If not found in companies, check admins table
$stmt->close();
$stmt = $conn->prepare("SELECT admin_id, admin_name, admin_email, password_hash, role, account_status FROM admins WHERE admin_email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    // ADMIN FOUND
    $user = $result->fetch_assoc();
    $stmt->close();
    
    // Verify password
    if (!verify_password($password, $user['password_hash'])) {
        $_SESSION['error'] = 'Invalid email or password';
        redirect('login.php');
    }
    
    // Check account status
    if ($user['account_status'] !== 'active') {
        $_SESSION['error'] = 'Your account is ' . $user['account_status'];
        redirect('login.php');
    }
    
    // Set session
    $_SESSION['user_type'] = 'admin';
    $_SESSION['user_id'] = $user['admin_id'];
    $_SESSION['admin_name'] = $user['admin_name'];
    $_SESSION['admin_email'] = $user['admin_email'];
    $_SESSION['admin_role'] = $user['role'];
    
    // Update last login
    $update_stmt = $conn->prepare("UPDATE admins SET last_login = NOW() WHERE admin_id = ?");
    $update_stmt->bind_param("i", $user['admin_id']);
    $update_stmt->execute();
    $update_stmt->close();
    
    // Log activity
    $log_stmt = $conn->prepare("INSERT INTO activity_logs (user_type, user_id, action, description, ip_address) VALUES ('admin', ?, 'login', 'Admin logged in', ?)");
    $ip_address = $_SERVER['REMOTE_ADDR'];
    $log_stmt->bind_param("is", $user['admin_id'], $ip_address);
    $log_stmt->execute();
    $log_stmt->close();
    
    $conn->close();
    
    // Redirect to admin dashboard
    $_SESSION['success'] = 'Welcome back, ' . $user['admin_name'] . '!';
    redirect('admin_dashboard.php');
}

// No account found in any table
$stmt->close();
$conn->close();
$_SESSION['error'] = 'Invalid email or password';
redirect('login.php');
?>