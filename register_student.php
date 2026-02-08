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
            <a href="index.php" class="back-button">← Back</a>
            <div class="logo-text">
                <h1>FirstStep</h1>
                <p>Internship Connection Platform</p>
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
            $internshipTypes = isset($_SESSION['form_data']['internshipTypes']) ? $_SESSION['form_data']['internshipTypes'] : [];

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
                            <input type="password" name="password" placeholder="••••••••" required minlength="8">
                            <span class="helper-text">Minimum 8 characters required</span>
                        </div>
                        <div class="form-group">
                            <label>Confirm Password *</label>
                            <input type="password" name="confirmPassword" placeholder="••••••••" required>
                            <span class="helper-text">Re-enter your password to confirm</span>
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
                                    <option value="BS Computer Engineering">BS Computer Engineering</option>
                                    <option value="BS Computer Science">BS Computer Science</option>
                                    <option value="BS Information Technology">BS Information Technology</option>
                                    <option value="BS Civil Engineering">BS Civil Engineering</option>
                                    <option value="BS Electrical Engineering">BS Electrical Engineering</option>
                                    <option value="BS Mechanical Engineering">BS Mechanical Engineering</option>
                                </optgroup>
                                <optgroup label="Business & Management">
                                    <option value="BS Business Administration">BS Business Administration</option>
                                    <option value="BS Accountancy">BS Accountancy</option>
                                    <option value="BS Entrepreneurship">BS Entrepreneurship</option>
                                    <option value="BS Marketing Management">BS Marketing Management</option>
                                </optgroup>
                                <optgroup label="Healthcare & Medical">
                                    <option value="BS Nursing">BS Nursing</option>
                                    <option value="BS Pharmacy">BS Pharmacy</option>
                                    <option value="BS Physical Therapy">BS Physical Therapy</option>
                                    <option value="BS Medical Technology">BS Medical Technology</option>
                                </optgroup>
                                <optgroup label="Education">
                                    <option value="Bachelor of Elementary Education">Bachelor of Elementary Education</option>
                                    <option value="Bachelor of Secondary Education">Bachelor of Secondary Education</option>
                                </optgroup>
                                <optgroup label="Sciences">
                                    <option value="BS Biology">BS Biology</option>
                                    <option value="BS Chemistry">BS Chemistry</option>
                                    <option value="BS Psychology">BS Psychology</option>
                                </optgroup>
                                <optgroup label="Arts & Communication">
                                    <option value="AB Communication">AB Communication</option>
                                    <option value="AB Mass Communication">AB Mass Communication</option>
                                    <option value="BA Fine Arts">BA Fine Arts</option>
                                </optgroup>
                                <optgroup label="Others">
                                    <option value="BS Criminology">BS Criminology</option>
                                    <option value="BS Social Work">BS Social Work</option>
                                    <option value="Other">Other</option>
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
                    <h3>Internship Preferences</h3>
                    <div class="form-group">
                        <label>Preferred Internship Types * (Select all that apply)</label>
                        <div class="checkbox-group">
                            <label class="checkbox-label">
                                <input type="checkbox" name="internshipTypes[]" value="On-site" <?php if(in_array('On-site', $internshipTypes)) echo 'checked'; ?>>
                                <span>On-site</span>
                            </label>
                            <label class="checkbox-label">
                                <input type="checkbox" name="internshipTypes[]" value="Remote" <?php if(in_array('Remote', $internshipTypes)) echo 'checked'; ?>>
                                <span>Remote</span>
                            </label>
                            <label class="checkbox-label">
                                <input type="checkbox" name="internshipTypes[]" value="Hybrid" <?php if(in_array('Hybrid', $internshipTypes)) echo 'checked'; ?>>
                                <span>Hybrid</span>
                            </label>
                        </div>
                        <span class="helper-text">Select at least one internship type</span>
                    </div>
                    <div class="form-group">
                        <label>Skills</label>
                        <input type="text" name="skills" placeholder="e.g., JavaScript, Python, Communication" value="<?php echo $skills; ?>">
                        <span class="helper-text">Enter your skills separated by commas</span>
                    </div>
                </div>

                <div class="form-section">
                    <h3>Documents</h3>
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
                    <button type="submit" class="btn-primary" id="submitBtn" onclick="checkTerms(event)">Create Student Account</button>
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
    function checkTerms(event) {
        const checkbox = document.getElementById('acceptTerms');
        if (!checkbox.checked) {
            event.preventDefault();
            document.getElementById('termsModal').classList.add('active');
        }
    }

    function closeModal() {
        document.getElementById('termsModal').classList.remove('active');
    }

    // Close modal when clicking outside
    document.getElementById('termsModal').addEventListener('click', function(e) {
        if (e.target === this) {
            closeModal();
        }
    });
</script>