<?php
require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('login.php');
}

$token = sanitize_input($_POST['token']);
$password = $_POST['password'];
$confirm_password = $_POST['confirm_password'];

// Validate inputs
$errors = [];

if (empty($token)) {
    $errors[] = 'Invalid reset token';
}

if (strlen($password) < 8) {
    $errors[] = 'Password must be at least 8 characters';
}

if (!preg_match('/[A-Z]/', $password)) {
    $errors[] = 'Password must contain at least one uppercase letter';
}

if (!preg_match('/[a-z]/', $password)) {
    $errors[] = 'Password must contain at least one lowercase letter';
}

if (!preg_match('/[0-9]/', $password)) {
    $errors[] = 'Password must contain at least one number';
}

if (!preg_match('/[\W_]/', $password)) {
    $errors[] = 'Password must contain at least one special character';
}

if ($password !== $confirm_password) {
    $errors[] = 'Passwords do not match';
}

if (!empty($errors)) {
    $_SESSION['error'] = implode('<br>', $errors);
    redirect('reset_password.php?token=' . urlencode($token));
}

// Verify token
$stmt = $conn->prepare("SELECT * FROM password_resets WHERE token = ? AND used = 0 AND expires_at > NOW()");
$stmt->bind_param("s", $token);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    $_SESSION['error'] = 'Invalid or expired password reset link';
    redirect('forgot_password.php');
}

$reset = $result->fetch_assoc();
$stmt->close();

$user_type = $reset['user_type'];
$email = $reset['email'];
$password_hash = hash_password($password);

// Update password based on user type
if ($user_type === 'student') {
    $stmt = $conn->prepare("UPDATE students SET password_hash = ? WHERE email = ?");
} elseif ($user_type === 'company') {
    $stmt = $conn->prepare("UPDATE companies SET password_hash = ? WHERE company_email = ?");
} elseif ($user_type === 'admin') {
    $stmt = $conn->prepare("UPDATE admins SET password_hash = ? WHERE admin_email = ?");
} else {
    $_SESSION['error'] = 'Invalid user type';
    redirect('forgot_password.php');
}

$stmt->bind_param("ss", $password_hash, $email);

if ($stmt->execute()) {
    $stmt->close();
    
    // Mark token as used
    $mark_used = $conn->prepare("UPDATE password_resets SET used = 1 WHERE token = ?");
    $mark_used->bind_param("s", $token);
    $mark_used->execute();
    $mark_used->close();
    
    $_SESSION['success'] = 'Password reset successful! You can now login with your new password.';
    redirect('login.php');
} else {
    $_SESSION['error'] = 'Failed to reset password. Please try again.';
    redirect('reset_password.php?token=' . urlencode($token));
}

$conn->close();
?>
