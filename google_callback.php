<?php
require_once 'config.php';
require_once 'google_config.php';

// Verify Google ID Token
if (!isset($_POST['credential'])) {
    $_SESSION['error'] = 'Invalid Google sign-in';
    redirect('login.php');
}

$id_token = $_POST['credential'];

// Verify token with Google
$client_id = GOOGLE_CLIENT_ID;
$url = 'https://oauth2.googleapis.com/tokeninfo?id_token=' . $id_token;
$response = file_get_contents($url);
$user_data = json_decode($response, true);

// Verify the token
if (!isset($user_data['email']) || $user_data['aud'] !== $client_id) {
    $_SESSION['error'] = 'Invalid Google authentication';
    redirect('login.php');
}

$google_id = $user_data['sub'];
$email = $user_data['email'];
$name = isset($user_data['name']) ? $user_data['name'] : '';
$first_name = isset($user_data['given_name']) ? $user_data['given_name'] : '';
$surname = isset($user_data['family_name']) ? $user_data['family_name'] : '';

// Check if user exists in any table
$user_found = false;
$user_type = '';

// Check students
$stmt = $conn->prepare("SELECT * FROM students WHERE email = ? OR google_id = ?");
$stmt->bind_param("ss", $email, $google_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $user = $result->fetch_assoc();
    $stmt->close();
    
    // Update google_id if not set
    if (empty($user['google_id'])) {
        $update = $conn->prepare("UPDATE students SET google_id = ?, auth_provider = 'google' WHERE student_id = ?");
        $update->bind_param("si", $google_id, $user['student_id']);
        $update->execute();
        $update->close();
    }
    
    // Set session
    $_SESSION['user_type'] = 'student';
    $_SESSION['user_id'] = $user['student_id'];
    $_SESSION['user_name'] = $user['first_name'] . ' ' . $user['surname'];
    $_SESSION['user_email'] = $user['email'];
    
    // Update last login
    $update = $conn->prepare("UPDATE students SET last_login = NOW() WHERE student_id = ?");
    $update->bind_param("i", $user['student_id']);
    $update->execute();
    $update->close();
    
    $user_found = true;
    $user_type = 'student';
}

// Check companies if not found in students
if (!$user_found) {
    $stmt = $conn->prepare("SELECT * FROM companies WHERE company_email = ? OR google_id = ?");
    $stmt->bind_param("ss", $email, $google_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        $stmt->close();
        
        // Update google_id if not set
        if (empty($user['google_id'])) {
            $update = $conn->prepare("UPDATE companies SET google_id = ?, auth_provider = 'google' WHERE company_id = ?");
            $update->bind_param("si", $google_id, $user['company_id']);
            $update->execute();
            $update->close();
        }
        
        // Set session
        $_SESSION['user_type'] = 'company';
        $_SESSION['user_id'] = $user['company_id'];
        $_SESSION['company_name'] = $user['company_name'];
        $_SESSION['company_email'] = $user['company_email'];
        $_SESSION['verification_status'] = $user['verification_status'];
        
        // Update last login
        $update = $conn->prepare("UPDATE companies SET last_login = NOW() WHERE company_id = ?");
        $update->bind_param("i", $user['company_id']);
        $update->execute();
        $update->close();
        
        $user_found = true;
        $user_type = 'company';
    }
}

// Check admins if not found
if (!$user_found) {
    $stmt = $conn->prepare("SELECT * FROM admins WHERE admin_email = ? OR google_id = ?");
    $stmt->bind_param("ss", $email, $google_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        $stmt->close();
        
        // Update google_id if not set
        if (empty($user['google_id'])) {
            $update = $conn->prepare("UPDATE admins SET google_id = ?, auth_provider = 'google' WHERE admin_id = ?");
            $update->bind_param("si", $google_id, $user['admin_id']);
            $update->execute();
            $update->close();
        }
        
        // Set session
        $_SESSION['user_type'] = 'admin';
        $_SESSION['user_id'] = $user['admin_id'];
        $_SESSION['admin_name'] = $user['admin_name'];
        $_SESSION['admin_email'] = $user['admin_email'];
        $_SESSION['admin_role'] = $user['role'];
        
        // Update last login
        $update = $conn->prepare("UPDATE admins SET last_login = NOW() WHERE admin_id = ?");
        $update->bind_param("i", $user['admin_id']);
        $update->execute();
        $update->close();
        
        $user_found = true;
        $user_type = 'admin';
    }
}

// If user not found, create new student account
if (!$user_found) {
    // Create new student account
    $password_hash = password_hash(bin2hex(random_bytes(16)), PASSWORD_DEFAULT); // Random password
    
    $stmt = $conn->prepare("INSERT INTO students (first_name, surname, email, password_hash, google_id, auth_provider, account_status) VALUES (?, ?, ?, ?, ?, 'google', 'active')");
    $stmt->bind_param("sssss", $first_name, $surname, $email, $password_hash, $google_id);
    
    if ($stmt->execute()) {
        $student_id = $stmt->insert_id;
        $stmt->close();
        
        // Set session
        $_SESSION['user_type'] = 'student';
        $_SESSION['user_id'] = $student_id;
        $_SESSION['user_name'] = $first_name . ' ' . $surname;
        $_SESSION['user_email'] = $email;
        
        $_SESSION['success'] = 'Welcome to FirstStep! Please complete your profile.';
        redirect('student_profile.php');
    } else {
        $_SESSION['error'] = 'Failed to create account';
        redirect('login.php');
    }
} else {
    // Redirect based on user type
    if ($user_type === 'student') {
        redirect('student_dashboard.php');
    } elseif ($user_type === 'company') {
        redirect('company_dashboard.php');
    } elseif ($user_type === 'admin') {
        redirect('admin_dashboard.php');
    }
}

$conn->close();
?>
