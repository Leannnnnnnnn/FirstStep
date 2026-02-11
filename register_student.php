<?php require_once 'config.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Registration - FirstStep</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <header>
        <div class="logo">
            <a href="landing.php" class="back-button">← Back</a>
            <div class="logo-text" style="display: flex; align-items: center; gap: 0.75rem;">
                <img src="uploads/logos/FirstStep_Logo.png" alt="FirstStep Logo" style="height: 45px; width: auto; object-fit: contain;">
                <div>
                    <h1 style="margin: 0; font-size: 1.5rem;">FirstStep</h1>
                    <p style="margin: 0; font-size: 0.75rem; color: #6b7280;">Internship Connection Platform</p>
                </div>
            </div>
        </div>
    </header>

    <main>
        <div class="container registration-form">
            <div class="header-section">
                <h2>Student Registration</h2>
                <p>Create your student account to find internship opportunities</p>
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

            // Check if form data exists (meaning validation failed)
            $hasFormData = isset($_SESSION['form_data']);

            // Preserve form data
            $firstName = isset($_SESSION['form_data']['firstName']) ? htmlspecialchars($_SESSION['form_data']['firstName']) : '';
            $middleName = isset($_SESSION['form_data']['middleName']) ? htmlspecialchars($_SESSION['form_data']['middleName']) : '';
            $surname = isset($_SESSION['form_data']['surname']) ? htmlspecialchars($_SESSION['form_data']['surname']) : '';
            $email = isset($_SESSION['form_data']['email']) ? htmlspecialchars($_SESSION['form_data']['email']) : '';
            $school = isset($_SESSION['form_data']['school']) ? htmlspecialchars($_SESSION['form_data']['school']) : '';
            $course = isset($_SESSION['form_data']['course']) ? htmlspecialchars($_SESSION['form_data']['course']) : '';
            $yearLevel = isset($_SESSION['form_data']['yearLevel']) ? htmlspecialchars($_SESSION['form_data']['yearLevel']) : '';
            $city = isset($_SESSION['form_data']['city']) ? htmlspecialchars($_SESSION['form_data']['city']) : '';
            $barangay = isset($_SESSION['form_data']['barangay']) ? htmlspecialchars($_SESSION['form_data']['barangay']) : '';
            $skills = isset($_SESSION['form_data']['skills']) ? htmlspecialchars($_SESSION['form_data']['skills']) : '';
            $internshipTypes = isset($_SESSION['form_data']['internshipType']) ? [$_SESSION['form_data']['internshipType']] : [];

            // Clear form data after retrieving
            unset($_SESSION['form_data']);
            ?>

            <form action="process_student_registration.php" method="POST" enctype="multipart/form-data">
                <div class="form-section">
                    <h3>Account Information</h3>
                    <div class="form-row">
                        <div class="form-group">
                            <label>First Name *</label>
                            <input type="text" name="firstName" placeholder="Juan" value="<?php echo $firstName; ?>" required>
                        </div>
                        <div class="form-group">
                            <label>Middle Name</label>
                            <input type="text" name="middleName" placeholder="Santos" value="<?php echo $middleName; ?>">
                            <span class="helper-text">Optional</span>
                        </div>
                        <div class="form-group">
                            <label>Last Name *</label>
                            <input type="text" name="surname" placeholder="Dela Cruz" value="<?php echo $surname; ?>" required>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label>Email Address *</label>
                            <input type="email" name="email" placeholder="juan@email.com" value="<?php echo $email; ?>" required>
                            <span class="helper-text">Use a valid email format (e.g., name@example.com)</span>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label>Password *</label>
                            <div style="position: relative;">
                                <input type="password" id="password" name="password" placeholder="••••••••" required minlength="8" pattern="^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[!@#$%^&*(),.?&quot;:{}|<>]).{8,}$" title="Password must contain at least one uppercase letter, one lowercase letter, one number, and one special character" style="padding-right: 45px;">
                                <button type="button" id="togglePassword" style="position: absolute; right: 10px; top: 50%; transform: translateY(-50%); background: none; border: none; cursor: pointer; padding: 5px; color: #666; display: flex; align-items: center; justify-content: center;" title="Show password">
                                    <svg id="eyeIcon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                                        <circle cx="12" cy="12" r="3"></circle>
                                    </svg>
                                </button>
                            </div>
                            <span class="helper-text">Must be at least 8 characters and include: uppercase, lowercase, number, and special character (!@#$%^&*etc.)</span>
                        </div>
                        <div class="form-group">
                            <label>Confirm Password *</label>
                            <div style="position: relative;">
                                <input type="password" id="confirmPassword" name="confirmPassword" placeholder="••••••••" required style="padding-right: 45px;">
                                <button type="button" id="toggleConfirmPassword" style="position: absolute; right: 10px; top: 50%; transform: translateY(-50%); background: none; border: none; cursor: pointer; padding: 5px; color: #666; display: flex; align-items: center; justify-content: center;" title="Show password">
                                    <svg id="eyeIconConfirm" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                                        <circle cx="12" cy="12" r="3"></circle>
                                    </svg>
                                </button>
                            </div>
                            <span class="helper-text">Re-enter your password to confirm</span>
                        </div>
                    </div>
                </div>

                 <div class="form-section">
                    <h3>Location</h3>
                    <div class="form-row">
                        <div class="form-group">
                            <label>City *</label>
                            <input type="text" name="city" placeholder="Quezon City" value="<?php echo $city; ?>" required>
                        </div>
                        <div class="form-group">
                            <label>Barangay *</label>
                            <input type="text" name="barangay" placeholder="Commonwealth" value="<?php echo $barangay; ?>" required>
                        </div>
                    </div>
                </div>

                <div class="form-section">
                    <h3>Educational Information</h3>
                    <div class="form-row">
                        <div class="form-group">
                            <label>School / University *</label>
                            <input type="text" name="school" placeholder="University of the Philippines" value="<?php echo $school; ?>" required>
                        </div>
                        <div class="form-group">
                            <label>Course / Program *</label>
                            <select name="course" required>
                                <option value="">Select your course</option>
                                <optgroup label="Engineering & Technology">
                                    <option value="BS Computer Engineering" <?php if($course == 'BS Computer Engineering') echo 'selected'; ?>>BS Computer Engineering</option>
                                    <option value="BS Computer Science" <?php if($course == 'BS Computer Science') echo 'selected'; ?>>BS Computer Science</option>
                                    <option value="BS Information Technology" <?php if($course == 'BS Information Technology') echo 'selected'; ?>>BS Information Technology</option>
                                    <option value="BS Civil Engineering" <?php if($course == 'BS Civil Engineering') echo 'selected'; ?>>BS Civil Engineering</option>
                                    <option value="BS Electrical Engineering" <?php if($course == 'BS Electrical Engineering') echo 'selected'; ?>>BS Electrical Engineering</option>
                                    <option value="BS Mechanical Engineering" <?php if($course == 'BS Mechanical Engineering') echo 'selected'; ?>>BS Mechanical Engineering</option>
                                </optgroup>
                                <optgroup label="Business & Management">
                                    <option value="BS Business Administration" <?php if($course == 'BS Business Administration') echo 'selected'; ?>>BS Business Administration</option>
                                    <option value="BS Accountancy" <?php if($course == 'BS Accountancy') echo 'selected'; ?>>BS Accountancy</option>
                                    <option value="BS Entrepreneurship" <?php if($course == 'BS Entrepreneurship') echo 'selected'; ?>>BS Entrepreneurship</option>
                                    <option value="BS Marketing Management" <?php if($course == 'BS Marketing Management') echo 'selected'; ?>>BS Marketing Management</option>
                                </optgroup>
                                <optgroup label="Healthcare & Medical">
                                    <option value="BS Nursing" <?php if($course == 'BS Nursing') echo 'selected'; ?>>BS Nursing</option>
                                    <option value="BS Pharmacy" <?php if($course == 'BS Pharmacy') echo 'selected'; ?>>BS Pharmacy</option>
                                    <option value="BS Physical Therapy" <?php if($course == 'BS Physical Therapy') echo 'selected'; ?>>BS Physical Therapy</option>
                                    <option value="BS Medical Technology" <?php if($course == 'BS Medical Technology') echo 'selected'; ?>>BS Medical Technology</option>
                                </optgroup>
                                <optgroup label="Education">
                                    <option value="Bachelor of Elementary Education" <?php if($course == 'Bachelor of Elementary Education') echo 'selected'; ?>>Bachelor of Elementary Education</option>
                                    <option value="Bachelor of Secondary Education" <?php if($course == 'Bachelor of Secondary Education') echo 'selected'; ?>>Bachelor of Secondary Education</option>
                                </optgroup>
                                <optgroup label="Sciences">
                                    <option value="BS Biology" <?php if($course == 'BS Biology') echo 'selected'; ?>>BS Biology</option>
                                    <option value="BS Chemistry" <?php if($course == 'BS Chemistry') echo 'selected'; ?>>BS Chemistry</option>
                                    <option value="BS Psychology" <?php if($course == 'BS Psychology') echo 'selected'; ?>>BS Psychology</option>
                                </optgroup>
                                <optgroup label="Arts & Communication">
                                    <option value="AB Communication" <?php if($course == 'AB Communication') echo 'selected'; ?>>AB Communication</option>
                                    <option value="AB Mass Communication" <?php if($course == 'AB Mass Communication') echo 'selected'; ?>>AB Mass Communication</option>
                                    <option value="BA Fine Arts" <?php if($course == 'BA Fine Arts') echo 'selected'; ?>>BA Fine Arts</option>
                                </optgroup>
                                <optgroup label="Others">
                                    <option value="BS Criminology" <?php if($course == 'BS Criminology') echo 'selected'; ?>>BS Criminology</option>
                                    <option value="BS Social Work" <?php if($course == 'BS Social Work') echo 'selected'; ?>>BS Social Work</option>
                                    <option value="Other" <?php if($course == 'Other') echo 'selected'; ?>>Other</option>
                                </optgroup>
                            </select>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label>Year Level *</label>
                            <select name="yearLevel" required>
                                <option value="">Select your level</option>
                                <option value="2nd Year" <?php if($yearLevel == '2nd Year') echo 'selected'; ?>>2nd Year</option>
                                <option value="3rd Year" <?php if($yearLevel == '3rd Year') echo 'selected'; ?>>3rd Year</option>
                                <option value="4th Year" <?php if($yearLevel == '4th Year') echo 'selected'; ?>>4th Year</option>
                                <option value="5th Year" <?php if($yearLevel == '5th Year') echo 'selected'; ?>>5th Year</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="form-section">
                    <h3>Internship Preferences</h3>
                    <div class="form-group">
                        <label>Preferred Internship Type *</label>
                        <div class="checkbox-group">
                            <label class="checkbox-label">
                                <input type="radio" name="internshipType" value="On-site" <?php if(isset($internshipTypes[0]) && $internshipTypes[0] == 'On-site') echo 'checked'; ?> required>
                                <span>On-site</span>
                            </label>
                            <label class="checkbox-label">
                                <input type="radio" name="internshipType" value="Remote" <?php if(isset($internshipTypes[0]) && $internshipTypes[0] == 'Remote') echo 'checked'; ?> required>
                                <span>Remote</span>
                            </label>
                            <label class="checkbox-label">
                                <input type="radio" name="internshipType" value="Hybrid" <?php if(isset($internshipTypes[0]) && $internshipTypes[0] == 'Hybrid') echo 'checked'; ?> required>
                                <span>Hybrid</span>
                            </label>
                        </div>
                        <span class="helper-text">Select one internship type</span>
                    </div>
                    <div class="form-group">
                        <label>Skills</label>
                        <input type="text" name="skills" placeholder="e.g., JavaScript, Python, Communication" value="<?php echo $skills; ?>">
                        <span class="helper-text">Enter your skills separated by commas</span>
                    </div>
                </div>

                <div class="form-section">
                    <h3>Documents</h3>
                    <?php if ($hasFormData): ?>
                        <div class="alert alert-warning" style="margin-bottom: 1rem;">
                            ⚠️ Please re-upload your resume (file inputs cannot be pre-filled for security reasons)
                        </div>
                    <?php endif; ?>
                    <div class="form-group">
                        <label>Upload Resume (PDF) *</label>
                        <input type="file" name="resume" accept=".pdf" required class="file-input">
                        <span class="helper-text">Supported format: PDF | Maximum file size: 5MB</span>
                    </div>
                </div>

                <div class="form-section">
                    <h3>Terms and Conditions</h3>
                    <div class="terms-box">
                        <p>By creating an account, you agree to our <a href="terms.php" target="_blank" class="terms-link">Terms and Conditions</a>. Please read them carefully before proceeding.</p>
                    </div>
                    <div class="form-group">
                        <label class="checkbox-label">
                            <input type="checkbox" name="acceptTerms" id="acceptTerms" required>
                            <span>I have read and I agree to the <a href="terms.php" target="_blank" class="terms-link">Terms and Conditions</a> of FirstStep. *</span>
                        </label>
                    </div>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn-primary">Create Student Account</button>
                </div>
            </form>

            <div class="signin-link">
                <p>Already have an account? <a href="login.php">Sign in</a></p>
            </div>
        </div>
    </main>
</body>
</html>

<script>
    // Password visibility toggle for main password field
    document.getElementById('togglePassword').addEventListener('click', function() {
        const passwordField = document.getElementById('password');
        const eyeIcon = document.getElementById('eyeIcon');
        
        if (passwordField.type === 'password') {
            passwordField.type = 'text';
            // Change to eye-off icon (with slash)
            eyeIcon.innerHTML = '<path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path><line x1="1" y1="1" x2="23" y2="23"></line>';
            this.title = 'Hide password';
        } else {
            passwordField.type = 'password';
            // Change back to eye icon
            eyeIcon.innerHTML = '<path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle>';
            this.title = 'Show password';
        }
    });

    // Password visibility toggle for confirm password field
    document.getElementById('toggleConfirmPassword').addEventListener('click', function() {
        const confirmPasswordField = document.getElementById('confirmPassword');
        const eyeIconConfirm = document.getElementById('eyeIconConfirm');
        
        if (confirmPasswordField.type === 'password') {
            confirmPasswordField.type = 'text';
            // Change to eye-off icon (with slash)
            eyeIconConfirm.innerHTML = '<path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path><line x1="1" y1="1" x2="23" y2="23"></line>';
            this.title = 'Hide password';
        } else {
            confirmPasswordField.type = 'password';
            // Change back to eye icon
            eyeIconConfirm.innerHTML = '<path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle>';
            this.title = 'Show password';
        }
    });
</script>