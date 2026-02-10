<?php
require_once 'config.php';
require_once 'email_config.php';

// Check if PHPMailer is available
$composer_phpmailer = __DIR__ . '/vendor/autoload.php';
$phpmailer_path = __DIR__ . '/PHPMailer/src/PHPMailer.php';

if (file_exists($composer_phpmailer)) {
    require_once $composer_phpmailer;
} elseif (file_exists($phpmailer_path)) {
    require_once $phpmailer_path;
    require_once __DIR__ . '/PHPMailer/src/SMTP.php';
    require_once __DIR__ . '/PHPMailer/src/Exception.php';
} else {
    $_SESSION['error'] = 'Email system not configured. Please contact administrator.';
    redirect('forgot_password.php');
}

// Use PHPMailer classes
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('forgot_password.php');
}

$email = sanitize_input($_POST['email']);

if (empty($email) || !validate_email($email)) {
    $_SESSION['error'] = 'Please enter a valid email address';
    redirect('forgot_password.php');
}

// Check if email exists in any table
$user_type = '';
$user_exists = false;

// Check students
$stmt = $conn->prepare("SELECT student_id FROM students WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
if ($stmt->get_result()->num_rows > 0) {
    $user_type = 'student';
    $user_exists = true;
}
$stmt->close();

// Check companies
if (!$user_exists) {
    $stmt = $conn->prepare("SELECT company_id FROM companies WHERE company_email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    if ($stmt->get_result()->num_rows > 0) {
        $user_type = 'company';
        $user_exists = true;
    }
    $stmt->close();
}

// Check admins
if (!$user_exists) {
    $stmt = $conn->prepare("SELECT admin_id FROM admins WHERE admin_email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    if ($stmt->get_result()->num_rows > 0) {
        $user_type = 'admin';
        $user_exists = true;
    }
    $stmt->close();
}

if (!$user_exists) {
    // Don't reveal if email exists or not (security)
    $_SESSION['success'] = 'If an account exists with this email, you will receive a password reset link shortly.';
    redirect('forgot_password.php');
}

// Generate reset token
$token = bin2hex(random_bytes(32));
$expires_at = date('Y-m-d H:i:s', strtotime('+24 hours'));

// Save token to database
$stmt = $conn->prepare("INSERT INTO password_resets (user_type, email, token, expires_at) VALUES (?, ?, ?, ?)");
$stmt->bind_param("ssss", $user_type, $email, $token, $expires_at);
$stmt->execute();
$stmt->close();

// Send email
$reset_link = SITE_URL . '/reset_password.php?token=' . $token;

$mail = new PHPMailer(true);

try {
    // Server settings
    $mail->isSMTP();
    $mail->Host       = SMTP_HOST;
    $mail->SMTPAuth   = true;
    $mail->Username   = SMTP_USERNAME;
    $mail->Password   = SMTP_PASSWORD;
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port       = SMTP_PORT;

    // Recipients
    $mail->setFrom(SMTP_FROM_EMAIL, SMTP_FROM_NAME);
    $mail->addAddress($email);

    // Content
    $mail->isHTML(true);
    $mail->Subject = 'Password Reset Request - FirstStep';
    $mail->Body    = "
    <html>
    <body style='font-family: Arial, sans-serif; line-height: 1.6; color: #333;'>
        <div style='max-width: 600px; margin: 0 auto; padding: 20px;'>
            <h2 style='color: #4F46E5;'>Password Reset Request</h2>
            <p>Hi,</p>
            <p>We received a request to reset your password for your FirstStep account.</p>
            <p>Click the button below to reset your password:</p>
            <div style='text-align: center; margin: 30px 0;'>
                <a href='{$reset_link}' style='background-color: #4F46E5; color: white; padding: 12px 30px; text-decoration: none; border-radius: 5px; display: inline-block;'>Reset Password</a>
            </div>
            <p>Or copy and paste this link into your browser:</p>
            <p style='word-break: break-all; color: #4F46E5;'>{$reset_link}</p>
            <p><strong>This link will expire in 24 hours.</strong></p>
            <p>If you didn't request a password reset, you can safely ignore this email.</p>
            <hr style='margin: 30px 0; border: none; border-top: 1px solid #ddd;'>
            <p style='font-size: 12px; color: #666;'>
                This is an automated message from FirstStep Platform. Please do not reply to this email.
            </p>
        </div>
    </body>
    </html>
    ";
    $mail->AltBody = "Password Reset Request\n\nClick this link to reset your password:\n{$reset_link}\n\nThis link will expire in 24 hours.\n\nIf you didn't request a password reset, you can safely ignore this email.";

    $mail->send();
    $_SESSION['success'] = 'Password reset link has been sent to your email. Please check your inbox.';
} catch (Exception $e) {
    $_SESSION['error'] = 'Failed to send email. Please try again later or contact support.';
    error_log('PHPMailer Error: ' . $mail->ErrorInfo);
}

$conn->close();
redirect('forgot_password.php');
?>