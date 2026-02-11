<?php
require_once 'config.php';
require_student();

$posting_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Get internship details
$stmt = $conn->prepare("
    SELECT ip.*, c.company_name, c.company_logo, c.company_description, c.company_address 
    FROM internship_postings ip 
    JOIN companies c ON ip.company_id = c.company_id 
    WHERE ip.posting_id = ? AND ip.status = 'active'
");
$stmt->bind_param("i", $posting_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    $_SESSION['error'] = 'Internship not found or no longer available';
    redirect('student_dashboard.php');
}

$internship = $result->fetch_assoc();
$stmt->close();

// Check if already applied
$check_stmt = $conn->prepare("SELECT application_id FROM applications WHERE student_id = ? AND posting_id = ?");
$check_stmt->bind_param("ii", $_SESSION['user_id'], $posting_id);
$check_stmt->execute();
$already_applied = $check_stmt->get_result()->num_rows > 0;
$check_stmt->close();

// Handle application submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['apply'])) {
    if ($already_applied) {
        $_SESSION['error'] = 'You have already applied to this internship';
    } else {
        $cover_letter = sanitize_input($_POST['coverLetter']);
        
        // ===== ADDED: COVER LETTER VALIDATION =====
        if (empty($cover_letter)) {
            $_SESSION['error'] = 'Cover letter is required';
        } elseif (strlen($cover_letter) < 50) {
            $_SESSION['error'] = 'Cover letter must be at least 50 characters';
        } elseif (strlen($cover_letter) > 5000) {
            $_SESSION['error'] = 'Cover letter must not exceed 5000 characters';
        } else {
            // ===== END COVER LETTER VALIDATION =====
            
            $apply_stmt = $conn->prepare("INSERT INTO applications (student_id, posting_id, company_id, cover_letter, status) VALUES (?, ?, ?, ?, 'pending')");
            $apply_stmt->bind_param("iiis", $_SESSION['user_id'], $posting_id, $internship['company_id'], $cover_letter);
            
            if ($apply_stmt->execute()) {
                $_SESSION['success'] = 'Application submitted successfully!';
                redirect('student_applications.php');
            } else {
                $_SESSION['error'] = 'Failed to submit application';
            }
            $apply_stmt->close();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($internship['job_title']); ?> - FirstStep</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <header>
        <div class="logo">
            <div class="logo-text" style="display: flex; align-items: center; gap: 0.75rem;">
                <img src="uploads/logos/FirstStep_Logo.png" alt="FirstStep Logo" style="height: 45px; width: auto; object-fit: contain;">
                <div>
                    <h1 style="margin: 0; font-size: 1.5rem;">FirstStep</h1>
                    <p style="margin: 0; font-size: 0.75rem; color: #6b7280;">Internship Connection Platform</p>
                </div>
            </div>
            <nav class="nav-menu">
                <a href="student_dashboard.php">Dashboard</a>
                <a href="student_applications.php">My Applications</a>
                <a href="student_profile.php">Profile</a>
                <a href="logout.php" class="logout-btn">Logout</a>
            </nav>
        </div>
    </header>
    <main>

    <main>
        <div class="container" style="max-width: 900px;">
            <div style="margin-bottom: 2rem;">
                <a href="student_dashboard.php" style="color: var(--primary-color); text-decoration: none;">← Back to Dashboard</a>
            </div>

            <?php
            if (isset($_SESSION['error'])) {
                echo '<div class="alert alert-error">' . htmlspecialchars($_SESSION['error']) . '</div>';
                unset($_SESSION['error']);
            }
            ?>

            <div class="dashboard-header">
                <h2><?php echo htmlspecialchars($internship['job_title']); ?></h2>
                <p class="company-name" style="font-size: 1.2rem;">🏢 <?php echo htmlspecialchars($internship['company_name']); ?></p>
                <div class="internship-details" style="margin-top: 1rem;">
                    <span class="detail-item">📍 <?php echo htmlspecialchars($internship['location']); ?></span>
                    <span class="detail-item">⏱️ <?php echo htmlspecialchars($internship['duration']); ?></span>
                    <span class="detail-item">💰 <?php echo htmlspecialchars($internship['stipend']); ?></span>
                    <span class="detail-item">👥 <?php echo htmlspecialchars($internship['slots_available']); ?> slots</span>
                    <span class="badge badge-primary"><?php echo htmlspecialchars($internship['internship_type']); ?></span>
                </div>
            </div>

            <div class="form-section">
                <h3>Job Description</h3>
                <p style="line-height: 1.6; color: var(--gray);"><?php echo nl2br(htmlspecialchars($internship['job_description'])); ?></p>
            </div>

            <div class="form-section">
                <h3>Requirements</h3>
                <p style="line-height: 1.6; color: var(--gray);"><?php echo nl2br(htmlspecialchars($internship['requirements'])); ?></p>
            </div>

            <div class="form-section">
                <h3>About the Company</h3>
                <p><strong>Address:</strong> <?php echo htmlspecialchars($internship['company_address']); ?></p>
                <p style="line-height: 1.6; color: var(--gray); margin-top: 0.5rem;"><?php echo nl2br(htmlspecialchars($internship['company_description'])); ?></p>
            </div>

            <?php if ($internship['application_deadline']): ?>
                <div class="form-section">
                    <h3>Application Deadline</h3>
                    <p style="color: var(--danger-color); font-weight: 500;">📅 <?php echo date('F d, Y', strtotime($internship['application_deadline'])); ?></p>
                </div>
            <?php endif; ?>

            <?php if ($already_applied): ?>
                <div class="alert alert-success">
                    ✓ You have already applied to this internship. Check your <a href="student_applications.php" style="color: var(--primary-color);">applications</a> to track the status.
                </div>
            <?php else: ?>
                <form action="internship_details.php?id=<?php echo $posting_id; ?>" method="POST">
                    <input type="hidden" name="apply" value="1">
                    
                    <div class="form-section">
                        <h3>Apply for this Internship</h3>
                        <div class="form-group">
                            <label>Cover Letter *</label>
                            <textarea name="coverLetter" rows="8" placeholder="Tell the company why you're interested in this internship and why you'd be a great fit..." required minlength="50" maxlength="5000"></textarea>
                            <span class="helper-text">Minimum 50 characters, maximum 5000 characters. Explain your interest and qualifications for this position.</span>
                        </div>
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="btn-primary">Submit Application</button>
                    </div>
                </form>
            <?php endif; ?>
        </div>
    </main>
</body>
</html>
<?php $conn->close(); ?>