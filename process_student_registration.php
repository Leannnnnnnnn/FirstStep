<?php
require_once 'config.php';

// Check if request is POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    die('Invalid request method');
}

// Get form data
$first_name = sanitize_input($_POST['firstName'] ?? '');
$middle_name = sanitize_input($_POST['middleName'] ?? '');
$surname = sanitize_input($_POST['surname'] ?? '');
$email = sanitize_input($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';
$confirm_password = $_POST['confirmPassword'] ?? '';
$school = sanitize_input($_POST['school'] ?? '');
$course = sanitize_input($_POST['course'] ?? '');
$year_level = sanitize_input($_POST['yearLevel'] ?? '');
$city = sanitize_input($_POST['city'] ?? '');
$barangay = sanitize_input($_POST['barangay'] ?? '');
$skills = sanitize_input($_POST['skills'] ?? '');
$internship_types_array = $_POST['internshipTypes'] ?? [];

// Function to save form data
function saveFormData() {
    global $first_name, $middle_name, $surname, $email, $school, $course, $year_level, $city, $barangay, $skills, $internship_types_array;
    $_SESSION['form_data'] = [
        'firstName' => $first_name,
        'middleName' => $middle_name,
        'surname' => $surname,
        'email' => $email,
        'school' => $school,
        'course' => $course,
        'yearLevel' => $year_level,
        'city' => $city,
        'barangay' => $barangay,
        'skills' => $skills,
        'internshipTypes' => $internship_types_array
    ];
}

// Handle multiple internship types
if (empty($internship_types_array)) {
    saveFormData();
    $_SESSION['error'] = 'Please select at least one internship type';
    redirect('register_student.php');
}
$internship_types = implode(',', array_map('sanitize_input', $internship_types_array));

// Validation
if (empty($first_name) || empty($surname) || empty($email) || empty($password)) {
    saveFormData();
    $_SESSION['error'] = 'Please fill in all required fields';
    redirect('register_student.php');
}

if (!validate_email($email)) {
    saveFormData();
    $_SESSION['error'] = 'Please enter a valid email address';
    redirect('register_student.php');
}

if ($password !== $confirm_password) {
    saveFormData();
    $_SESSION['error'] = 'Passwords do not match';
    redirect('register_student.php');
}

if (strlen($password) < 8) {
    saveFormData();
    $_SESSION['error'] = 'Password must be at least 8 characters';
    redirect('register_student.php');
}

// Check if email already exists
$check_stmt = $conn->prepare("SELECT student_id FROM students WHERE email = ?");
$check_stmt->bind_param("s", $email);
$check_stmt->execute();
$check_result = $check_stmt->get_result();

if ($check_result->num_rows > 0) {
    saveFormData();
    $_SESSION['error'] = 'Email already registered';
    $check_stmt->close();
    redirect('register_student.php');
}
$check_stmt->close();

// Handle resume upload
$resume_path = '';
if (isset($_FILES['resume']) && $_FILES['resume']['error'] == 0) {
    $allowed_types = ['application/pdf'];
    $max_size = 5 * 1024 * 1024; // 5MB
    
    if (!in_array($_FILES['resume']['type'], $allowed_types)) {
        saveFormData();
        $_SESSION['error'] = 'Only PDF files are allowed for resume';
        redirect('register_student.php');
    }
    
    if ($_FILES['resume']['size'] > $max_size) {
        saveFormData();
        $_SESSION['error'] = 'Resume file size must not exceed 5MB';
        redirect('register_student.php');
    }
    
    // Create uploads directory if it doesn't exist
    if (!is_dir('uploads/resumes')) {
        mkdir('uploads/resumes', 0777, true);
    }
    
    $resume_path = 'resumes/' . time() . '_' . basename($_FILES['resume']['name']);
    $upload_full_path = 'uploads/' . $resume_path;
    
    if (!move_uploaded_file($_FILES['resume']['tmp_name'], $upload_full_path)) {
        saveFormData();
        $_SESSION['error'] = 'Failed to upload resume. Please try again.';
        redirect('register_student.php');
    }
}

// Hash password
$password_hash = hash_password($password);

// Insert into database
$stmt = $conn->prepare("INSERT INTO students (first_name, middle_name, surname, email, password_hash, school, course, year_level, city, barangay, internship_types, skills, resume_path, account_status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'active')");

$stmt->bind_param("sssssssssssss", $first_name, $middle_name, $surname, $email, $password_hash, $school, $course, $year_level, $city, $barangay, $internship_types, $skills, $resume_path);

if ($stmt->execute()) {
    // Clear form data on success
    unset($_SESSION['form_data']);
    $_SESSION['success'] = 'Registration successful! Please login with your credentials.';
    $stmt->close();
    $conn->close();
    redirect('login.php');
} else {
    // Delete uploaded file if database insert fails
    if (!empty($resume_path) && file_exists('uploads/' . $resume_path)) {
        unlink('uploads/' . $resume_path);
    }
    
    saveFormData();
    $_SESSION['error'] = 'Registration failed: ' . $conn->error;
    $stmt->close();
    $conn->close();
    redirect('register_student.php');
}
?>