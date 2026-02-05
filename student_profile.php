<?php
require_once 'config.php';
require_student();

// Get student info
$stmt = $conn->prepare("SELECT * FROM students WHERE student_id = ?");
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$student = $stmt->get_result()->fetch_assoc();
$stmt->close();

// Handle profile update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_profile'])) {
    $first_name = sanitize_input($_POST['firstName']);
    $middle_name = sanitize_input($_POST['middleName']);
    $surname = sanitize_input($_POST['surname']);
    $school = sanitize_input($_POST['school']);
    $course = sanitize_input($_POST['course']);
    $year_level = sanitize_input($_POST['yearLevel']);
    $city = sanitize_input($_POST['city']);
    $barangay = sanitize_input($_POST['barangay']);
    $skills = sanitize_input($_POST['skills']);
    
    // Handle multiple internship types
    $internship_types_array = $_POST['internshipTypes'] ?? [];
    if (empty($internship_types_array)) {
        $_SESSION['error'] = 'Please select at least one internship type';
    } else {
        $internship_types = implode(',', array_map('sanitize_input', $internship_types_array));
        
        $update_stmt = $conn->prepare("UPDATE students SET first_name=?, middle_name=?, surname=?, school=?, course=?, year_level=?, city=?, barangay=?, internship_types=?, skills=? WHERE student_id=?");
        $update_stmt->bind_param("ssssssssssi", $first_name, $middle_name, $surname, $school, $course, $year_level, $city, $barangay, $internship_types, $skills, $_SESSION['user_id']);
        
        if ($update_stmt->execute()) {
            // Update session name
            $full_name = $first_name;
            if (!empty($middle_name)) {
                $full_name .= ' ' . $middle_name;
            }
            $full_name .= ' ' . $surname;
            $_SESSION['user_name'] = $full_name;
            
            $_SESSION['success'] = 'Profile updated successfully!';
            redirect('student_profile.php');
        } else {
            $_SESSION['error'] = 'Failed to update profile';
        }
        $update_stmt->close();
    }
}

// Handle resume upload
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_resume'])) {
    if (isset($_FILES['resume']) && $_FILES['resume']['error'] == 0) {
        $allowed_types = ['application/pdf'];
        $max_size = 5 * 1024 * 1024;
        
        if (!in_array($_FILES['resume']['type'], $allowed_types)) {
            $_SESSION['error'] = 'Only PDF files are allowed';
        } elseif ($_FILES['resume']['size'] > $max_size) {
            $_SESSION['error'] = 'File size must not exceed 5MB';
        } else {
            if (!is_dir('uploads/resumes')) {
                mkdir('uploads/resumes', 0777, true);
            }
            
            // Delete old resume
            if (!empty($student['resume_path']) && file_exists('uploads/' . $student['resume_path'])) {
                unlink('uploads/' . $student['resume_path']);
            }
            
            $resume_path = 'resumes/' . time() . '_' . basename($_FILES['resume']['name']);
            $upload_full_path = 'uploads/' . $resume_path;
            
            if (move_uploaded_file($_FILES['resume']['tmp_name'], $upload_full_path)) {
                $update_stmt = $conn->prepare("UPDATE students SET resume_path=? WHERE student_id=?");
                $update_stmt->bind_param("si", $resume_path, $_SESSION['user_id']);
                $update_stmt->execute();
                $update_stmt->close();
                
                $_SESSION['success'] = 'Resume updated successfully!';
                redirect('student_profile.php');
            } else {
                $_SESSION['error'] = 'Failed to upload resume';
            }
        }
    }
}

// Parse internship types for checkboxes
$selected_types = !empty($student['internship_types']) ? explode(',', $student['internship_types']) : [];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile - FirstStep</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <header>
        <div class="logo">
            <div class="logo-text">
                <h1>FirstStep</h1>
                <p>Internship Connection Platform</p>
            </div>
            <nav class="nav-menu">
                <a href="student_dashboard.php">Dashboard</a>
                <a href="student_applications.php">My Applications</a>
                <a href="student_profile.php" style="color: var(--primary-color);">Profile</a>
                <a href="logout.php" class="logout-btn">Logout</a>
            </nav>
        </div>
    </header>

    <main>
        <div class="container registration-form">
            <div class="header-section">
                <h2>My Profile</h2>
                <p>Manage your personal information</p>
            </div>

            <?php
            if (isset($_SESSION['error'])) {
                echo '<div class="alert alert-error">' . htmlspecialchars($_SESSION['error']) . '</div>';
                unset($_SESSION['error']);
            }
            if (isset($_SESSION['success'])) {
                echo '<div class="alert alert-success">' . htmlspecialchars($_SESSION['success']) . '</div>';
                unset($_SESSION['success']);
            }
            ?>

            <form action="student_profile.php" method="POST">
                <input type="hidden" name="update_profile" value="1">
                
                <div class="form-section">
                    <h3>Account Information</h3>
                    <div class="form-row">
                        <div class="form-group">
                            <label>First Name *</label>
                            <input type="text" name="firstName" value="<?php echo htmlspecialchars($student['first_name']); ?>" disabled>
                            <span class="helper-text">This field cannot be changed</span>
                        </div>
                        <div class="form-group">
                            <label>Middle Name</label>
                            <input type="text" name="middleName" value="<?php echo htmlspecialchars($student['middle_name']); ?>"disabled>
                            <span class="helper-text">This field cannot be changed</span>
                        </div>
                        <div class="form-group">
                            <label>Last Name *</label>
                            <input type="text" name="surname" value="<?php echo htmlspecialchars($student['surname']); ?>" disabled>
                            <span class="helper-text">This field cannot be changed</span>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Email Address</label>
                        <input type="email" value="<?php echo htmlspecialchars($student['email']); ?>" disabled>
                        <span class="helper-text">This field cannot be changed</span>
                    </div>
                </div>

                <div class="form-section">
                    <h3>Educational Information</h3>
                    <div class="form-row">
                        <div class="form-group">
                            <label>School / University *</label>
                            <input type="text" name="school" value="<?php echo htmlspecialchars($student['school']); ?>" required>
                        </div>
                        <div class="form-group">
                            <label>Course / Program *</label>
                            <input type="text" name="course" value="<?php echo htmlspecialchars($student['course']); ?>" required>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label>Year Level *</label>
                            <select name="yearLevel" required>
                                <option value="2nd Year" <?php if($student['year_level'] == '2nd Year') echo 'selected'; ?>>2nd Year</option>
                                <option value="3rd Year" <?php if($student['year_level'] == '3rd Year') echo 'selected'; ?>>3rd Year</option>
                                <option value="4th Year" <?php if($student['year_level'] == '4th Year') echo 'selected'; ?>>4th Year</option>
                                <option value="5th Year" <?php if($student['year_level'] == '5th Year') echo 'selected'; ?>>5th Year</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="form-section">
                    <h3>Location</h3>
                    <div class="form-row">
                        <div class="form-group">
                            <label>City *</label>
                            <input type="text" name="city" value="<?php echo htmlspecialchars($student['city']); ?>" required>
                        </div>
                        <div class="form-group">
                            <label>Barangay *</label>
                            <input type="text" name="barangay" value="<?php echo htmlspecialchars($student['barangay']); ?>" required>
                        </div>
                    </div>
                </div>

                <div class="form-section">
                    <h3>Internship Preferences</h3>
                    <div class="form-group">
                        <label>Preferred Internship Types * (Select all that apply)</label>
                        <div class="checkbox-group">
                            <label class="checkbox-label">
                                <input type="checkbox" name="internshipTypes[]" value="On-site" <?php if(in_array('On-site', $selected_types)) echo 'checked'; ?>>
                                <span>On-site</span>
                            </label>
                            <label class="checkbox-label">
                                <input type="checkbox" name="internshipTypes[]" value="Remote" <?php if(in_array('Remote', $selected_types)) echo 'checked'; ?>>
                                <span>Remote</span>
                            </label>
                            <label class="checkbox-label">
                                <input type="checkbox" name="internshipTypes[]" value="Hybrid" <?php if(in_array('Hybrid', $selected_types)) echo 'checked'; ?>>
                                <span>Hybrid</span>
                            </label>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Skills</label>
                        <input type="text" name="skills" value="<?php echo htmlspecialchars($student['skills']); ?>" placeholder="e.g., JavaScript, Python, Communication">
                        <span class="helper-text">Enter your skills separated by commas</span>
                    </div>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn-primary">Update Profile</button>
                </div>
            </form>

            <form action="student_profile.php" method="POST" enctype="multipart/form-data" style="margin-top: 2rem;">
                <input type="hidden" name="update_resume" value="1">
                
                <div class="form-section">
                    <h3>Resume</h3>
                    <?php if (!empty($student['resume_path'])): ?>
                        <div style="margin-bottom: 1rem; padding: 1rem; background: var(--light-gray); border-radius: 6px;">
                            <p><strong>Current Resume:</strong> <?php echo htmlspecialchars(basename($student['resume_path'])); ?></p>
                            <a href="uploads/<?php echo htmlspecialchars($student['resume_path']); ?>" target="_blank" class="btn-secondary" style="margin-top: 0.5rem;">View Resume</a>
                        </div>
                    <?php endif; ?>
                    <div class="form-group">
                        <label>Upload New Resume (PDF)</label>
                        <input type="file" name="resume" accept=".pdf" class="file-input">
                        <span class="helper-text">Supported format: PDF | Maximum file size: 5MB</span>
                    </div>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn-primary">Update Resume</button>
                </div>
            </form>
        </div>
    </main>
</body>
</html>
<?php $conn->close(); ?>