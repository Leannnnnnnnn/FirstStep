<?php
require_once 'config.php';

// Check if request is POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    die('Invalid request method');
}

// Get form data
$company_email = sanitize_input($_POST['companyEmail'] ?? '');
$contact_number = sanitize_input($_POST['contactNumber'] ?? '');
$password = $_POST['password'] ?? '';
$confirm_password = $_POST['confirmPassword'] ?? '';
$company_name = sanitize_input($_POST['companyName'] ?? '');
$contact_person = sanitize_input($_POST['contactPerson'] ?? '');
$industry_type = sanitize_input($_POST['industryType'] ?? '');
$company_address = sanitize_input($_POST['companyAddress'] ?? '');
$company_description = sanitize_input($_POST['companyDescription'] ?? '');

// Function to save form data
function saveFormData() {
    global $company_email, $contact_number, $company_name, $contact_person, $industry_type, $company_address, $company_description;
    $_SESSION['form_data'] = [
        'companyEmail' => $company_email,
        'contactNumber' => $contact_number,
        'companyName' => $company_name,
        'contactPerson' => $contact_person,
        'industryType' => $industry_type,
        'companyAddress' => $company_address,
        'companyDescription' => $company_description
    ];
}

// Validation
if (empty($company_email) || empty($company_name) || empty($password)) {
    saveFormData();
    $_SESSION['error'] = 'Please fill in all required fields';
    redirect('register_company.php');
}

if (!validate_email($company_email)) {
    saveFormData();
    $_SESSION['error'] = 'Please enter a valid email address';
    redirect('register_company.php');
}

if ($password !== $confirm_password) {
    saveFormData();
    $_SESSION['error'] = 'Passwords do not match';
    redirect('register_company.php');
}

if (strlen($password) < 8) {
    saveFormData();
    $_SESSION['error'] = 'Password must be at least 8 characters';
    redirect('register_company.php');
}

// ===== ADDED: LENGTH VALIDATION =====
if (strlen($company_name) > 255) {
    saveFormData();
    $_SESSION['error'] = 'Company name must not exceed 255 characters';
    redirect('register_company.php');
}

if (!empty($contact_person) && strlen($contact_person) > 255) {
    saveFormData();
    $_SESSION['error'] = 'Contact person name must not exceed 255 characters';
    redirect('register_company.php');
}

if (!empty($contact_number) && strlen($contact_number) > 50) {
    saveFormData();
    $_SESSION['error'] = 'Contact number must not exceed 50 characters';
    redirect('register_company.php');
}

if (strlen($company_description) < 50) {
    saveFormData();
    $_SESSION['error'] = 'Company description must be at least 50 characters';
    redirect('register_company.php');
}

if (strlen($company_description) > 5000) {
    saveFormData();
    $_SESSION['error'] = 'Company description must not exceed 5000 characters';
    redirect('register_company.php');
}

if (!empty($company_address) && strlen($company_address) > 500) {
    saveFormData();
    $_SESSION['error'] = 'Company address must not exceed 500 characters';
    redirect('register_company.php');
}
// ===== END LENGTH VALIDATION =====

// Check if email already exists
$check_stmt = $conn->prepare("SELECT company_id FROM companies WHERE company_email = ?");
$check_stmt->bind_param("s", $company_email);
$check_stmt->execute();
$check_result = $check_stmt->get_result();

if ($check_result->num_rows > 0) {
    saveFormData();
    $_SESSION['error'] = 'Email already registered';
    $check_stmt->close();
    redirect('register_company.php');
}
$check_stmt->close();

// Handle logo upload
$logo_path = '';
if (isset($_FILES['companyLogo']) && $_FILES['companyLogo']['error'] == 0) {
    $allowed_types = ['image/png', 'image/jpeg', 'image/jpg', 'image/svg+xml'];
    $max_size = 2 * 1024 * 1024; // 2MB
    
    if (!in_array($_FILES['companyLogo']['type'], $allowed_types)) {
        saveFormData();
        $_SESSION['error'] = 'Only PNG, JPG, and SVG files are allowed for logo';
        redirect('register_company.php');
    }
    
    if ($_FILES['companyLogo']['size'] > $max_size) {
        saveFormData();
        $_SESSION['error'] = 'Logo file size must not exceed 2MB';
        redirect('register_company.php');
    }
    
    // Create uploads directory if it doesn't exist
    if (!is_dir('uploads/logos')) {
        mkdir('uploads/logos', 0777, true);
    }
    
    $logo_path = 'logos/' . time() . '_' . basename($_FILES['companyLogo']['name']);
    $upload_full_path = 'uploads/' . $logo_path;
    
    if (!move_uploaded_file($_FILES['companyLogo']['tmp_name'], $upload_full_path)) {
        saveFormData();
        $_SESSION['error'] = 'Failed to upload logo. Please try again.';
        redirect('register_company.php');
    }
}

// Hash password
$password_hash = hash_password($password);

// Insert into database
$stmt = $conn->prepare("INSERT INTO companies (company_name, company_email, password_hash, contact_number, contact_person, industry_type, company_address, company_description, company_logo, verification_status, account_status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 'pending', 'active')");

$stmt->bind_param("sssssssss", $company_name, $company_email, $password_hash, $contact_number, $contact_person, $industry_type, $company_address, $company_description, $logo_path);

if ($stmt->execute()) {
    // Clear form data on success
    unset($_SESSION['form_data']);
    $_SESSION['success'] = 'Registration successful! Your account is pending verification. Please login.';
    $stmt->close();
    $conn->close();
    redirect('login.php');
} else {
    // Delete uploaded file if database insert fails
    if (!empty($logo_path) && file_exists('uploads/' . $logo_path)) {
        unlink('uploads/' . $logo_path);
    }
    
    saveFormData();
    $_SESSION['error'] = 'Registration failed: ' . $conn->error;
    $stmt->close();
    $conn->close();
    redirect('register_company.php');
}
?>