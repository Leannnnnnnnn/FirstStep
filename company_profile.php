<?php
require_once 'config.php';
require_company();

// Get company info
$stmt = $conn->prepare("SELECT * FROM companies WHERE company_id = ?");
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$company = $stmt->get_result()->fetch_assoc();
$stmt->close();

// Handle profile update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_profile'])) {
    $company_name = sanitize_input($_POST['companyName']);
    $contact_number = sanitize_input($_POST['contactNumber']);
    $contact_person = sanitize_input($_POST['contactPerson']);
    $industry_type = sanitize_input($_POST['industryType']);
    $company_address = sanitize_input($_POST['companyAddress']);
    $company_description = sanitize_input($_POST['companyDescription']);

    if (strlen($company_description) < 50) {
        $_SESSION['error'] = 'Company description must be at least 50 characters';
    } else {
        $update_stmt = $conn->prepare("UPDATE companies SET company_name=?, contact_number=?, contact_person=?, industry_type=?, company_address=?, company_description=? WHERE company_id=?");
        $update_stmt->bind_param("ssssssi", $company_name, $contact_number, $contact_person, $industry_type, $company_address, $company_description, $_SESSION['user_id']);
        
        if ($update_stmt->execute()) {
            $_SESSION['success'] = 'Profile updated successfully!';
            $_SESSION['company_name'] = $company_name;
            redirect('company_profile.php');
        } else {
            $_SESSION['error'] = 'Failed to update profile';
        }
        $update_stmt->close();
    }
}

// Handle logo upload
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_logo'])) {
    if (isset($_FILES['companyLogo']) && $_FILES['companyLogo']['error'] == 0) {
        $allowed_types = ['image/png', 'image/jpeg', 'image/jpg', 'image/svg+xml'];
        $max_size = 2 * 1024 * 1024;
        
        if (!in_array($_FILES['companyLogo']['type'], $allowed_types)) {
            $_SESSION['error'] = 'Only PNG, JPG, and SVG files are allowed';
        } elseif ($_FILES['companyLogo']['size'] > $max_size) {
            $_SESSION['error'] = 'File size must not exceed 2MB';
        } else {
            if (!is_dir('uploads/logos')) {
                mkdir('uploads/logos', 0777, true);
            }
            
            // Delete old logo
            if (!empty($company['company_logo']) && file_exists('uploads/' . $company['company_logo'])) {
                unlink('uploads/' . $company['company_logo']);
            }
            
            $logo_path = 'logos/' . time() . '_' . basename($_FILES['companyLogo']['name']);
            $upload_full_path = 'uploads/' . $logo_path;
            
            if (move_uploaded_file($_FILES['companyLogo']['tmp_name'], $upload_full_path)) {
                $update_stmt = $conn->prepare("UPDATE companies SET company_logo=? WHERE company_id=?");
                $update_stmt->bind_param("si", $logo_path, $_SESSION['user_id']);
                $update_stmt->execute();
                $update_stmt->close();
                
                $_SESSION['success'] = 'Logo updated successfully!';
                redirect('company_profile.php');
            } else {
                $_SESSION['error'] = 'Failed to upload logo';
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Company Profile - FirstStep</title>
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
                <a href="company_dashboard.php">Dashboard</a>
                <a href="company_postings.php">My Postings</a>
                <a href="company_applications.php">Applications</a>
                <a href="company_profile.php" style="color: var(--primary-color);">Profile</a>
                <a href="logout.php" class="logout-btn">Logout</a>
            </nav>
        </div>
    </header>

    <main>
        <div class="container registration-form">
            <div class="header-section">
                <h2>Company Profile</h2>
                <p>Manage your company information</p>
                <?php if ($company['verification_status'] === 'pending'): ?>
                    <div class="alert alert-warning" style="margin-top: 1rem;">
                        ⚠️ Your account is pending verification
                    </div>
                <?php elseif ($company['verification_status'] === 'verified'): ?>
                    <div class="alert alert-success" style="margin-top: 1rem;">
                        ✓ Your account is verified
                    </div>
                <?php endif; ?>
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

            <form action="company_profile.php" method="POST">
                <input type="hidden" name="update_profile" value="1">
                
                <div class="form-section">
                    <h3>Account Information</h3>
                    <div class="form-row">
                        <div class="form-group">
                            <label>Company Email</label>
                            <input type="email" value="<?php echo htmlspecialchars($company['company_email']); ?>" disabled>
                            <span class="helper-text">Email cannot be changed</span>
                        </div>
                        <div class="form-group">
                            <label>Contact Number *</label>
                            <input type="tel" name="contactNumber" value="<?php echo htmlspecialchars($company['contact_number']); ?>" required>
                        </div>
                    </div>
                </div>

                <div class="form-section">
                    <h3>Company Details</h3>
                    <div class="form-row">
                        <div class="form-group">
                            <label>Company Name *</label>
                            <input type="text" name="companyName" value="<?php echo htmlspecialchars($company['company_name']); ?>" required>
                        </div>
                        <div class="form-group">
                            <label>Contact Person *</label>
                            <input type="text" name="contactPerson" value="<?php echo htmlspecialchars($company['contact_person']); ?>" required>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label>Industry Type *</label>
                            <select name="industryType" required>
                                <option value="Technology" <?php if($company['industry_type']=='Technology') echo 'selected'; ?>>Technology</option>
                                <option value="Finance" <?php if($company['industry_type']=='Finance') echo 'selected'; ?>>Finance</option>
                                <option value="Healthcare" <?php if($company['industry_type']=='Healthcare') echo 'selected'; ?>>Healthcare</option>
                                <option value="Education" <?php if($company['industry_type']=='Education') echo 'selected'; ?>>Education</option>
                                <option value="Marketing" <?php if($company['industry_type']=='Marketing') echo 'selected'; ?>>Marketing</option>
                                <option value="Manufacturing" <?php if($company['industry_type']=='Manufacturing') echo 'selected'; ?>>Manufacturing</option>
                                <option value="Retail" <?php if($company['industry_type']=='Retail') echo 'selected'; ?>>Retail</option>
                                <option value="Hospitality" <?php if($company['industry_type']=='Hospitality') echo 'selected'; ?>>Hospitality</option>
                                <option value="Other" <?php if($company['industry_type']=='Other') echo 'selected'; ?>>Other</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Company Address *</label>
                        <input type="text" name="companyAddress" value="<?php echo htmlspecialchars($company['company_address']); ?>" required>
                    </div>
                    <div class="form-group">
                        <label>Company Description *</label>
                        <textarea name="companyDescription" rows="5" required><?php echo htmlspecialchars($company['company_description']); ?></textarea>
                        <span class="helper-text">Minimum 50 characters</span>
                    </div>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn-primary company-btn">Update Profile</button>
                </div>
            </form>

            <form action="company_profile.php" method="POST" enctype="multipart/form-data" style="margin-top: 2rem;">
                <input type="hidden" name="update_logo" value="1">
                
                <div class="form-section">
                    <h3>Company Logo</h3>
                    <?php if (!empty($company['company_logo'])): ?>
                        <div style="margin-bottom: 1rem; padding: 1rem; background: var(--light-gray); border-radius: 6px;">
                            <p><strong>Current Logo:</strong></p>
                            <img src="uploads/<?php echo htmlspecialchars($company['company_logo']); ?>" alt="Company Logo" style="max-width: 200px; margin-top: 0.5rem; border-radius: 6px;">
                        </div>
                    <?php endif; ?>
                    <div class="form-group">
                        <label>Upload New Logo</label>
                        <input type="file" name="companyLogo" accept=".png,.jpg,.jpeg,.svg" class="file-input">
                        <span class="helper-text">Supported formats: PNG, JPG, SVG | Maximum file size: 2MB</span>
                    </div>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn-primary company-btn">Update Logo</button>
                </div>
            </form>
        </div>
    </main>
</body>
</html>
<?php $conn->close(); ?>