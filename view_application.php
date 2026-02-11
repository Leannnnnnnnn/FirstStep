<?php
require_once 'config.php';
require_company();

$application_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Get application details
$stmt = $conn->prepare("
    SELECT a.*, ip.job_title, 
           s.first_name, s.middle_name, s.surname, s.email, s.school, s.course, s.year_level, s.city, s.barangay, s.internship_types, s.skills, s.resume_path
    FROM applications a 
    JOIN internship_postings ip ON a.posting_id = ip.posting_id 
    JOIN students s ON a.student_id = s.student_id 
    WHERE a.application_id = ? AND a.company_id = ?
");
$stmt->bind_param("ii", $application_id, $_SESSION['user_id']);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    $_SESSION['error'] = 'Application not found';
    redirect('company_applications.php');
}

$application = $result->fetch_assoc();
$stmt->close();

// Build full name
$full_name = $application['first_name'];
if (!empty($application['middle_name'])) {
    $full_name .= ' ' . $application['middle_name'];
}
$full_name .= ' ' . $application['surname'];

// Handle status update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_status'])) {
    $new_status = sanitize_input($_POST['status']);
    $notes = sanitize_input($_POST['notes']);
    
    // ===== ADDED: NOTES VALIDATION =====
    if (!empty($notes) && strlen($notes) > 2000) {
        $_SESSION['error'] = 'Notes must not exceed 2000 characters';
    } else {
        // ===== END NOTES VALIDATION =====
        
        $update_stmt = $conn->prepare("UPDATE applications SET status=?, notes=?, reviewed_at=NOW() WHERE application_id=? AND company_id=?");
        $update_stmt->bind_param("ssii", $new_status, $notes, $application_id, $_SESSION['user_id']);
        
        if ($update_stmt->execute()) {
            $_SESSION['success'] = 'Application status updated successfully!';
            redirect('view_application.php?id=' . $application_id);
        } else {
            $_SESSION['error'] = 'Failed to update application';
        }
        $update_stmt->close();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Review Application - FirstStep</title>
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
                <a href="company_dashboard.php">Dashboard</a>
                <a href="company_postings.php">My Postings</a>
                <a href="company_applications.php">Applications</a>
                <a href="company_profile.php">Profile</a>
                <a href="logout.php" class="logout-btn">Logout</a>
            </nav>
        </div>
    </header>
    <main>
        <div class="container" style="max-width: 900px;">
            <div style="margin-bottom: 2rem;">
                <a href="company_applications.php" style="color: var(--primary-color); text-decoration: none;">← Back to Applications</a>
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

            <div class="dashboard-header">
                <div style="display: flex; justify-content: space-between; align-items: start;">
                    <div>
                        <h2><?php echo htmlspecialchars($full_name); ?></h2>
                        <p>Applied for: <?php echo htmlspecialchars($application['job_title']); ?></p>
                    </div>
                    <?php
                    $status_class = 'badge-warning';
                    if ($application['status'] === 'accepted') $status_class = 'badge-success';
                    if ($application['status'] === 'rejected') $status_class = 'badge-danger';
                    if ($application['status'] === 'shortlisted') $status_class = 'badge-primary';
                    if ($application['status'] === 'reviewed') $status_class = 'badge-primary';
                    ?>
                    <span class="badge <?php echo $status_class; ?>" style="font-size: 1rem; padding: 0.5rem 1rem;"><?php echo ucfirst($application['status']); ?></span>
                </div>
            </div>

            <div class="form-section">
                <h3>Contact Information</h3>
                <p><strong>Email:</strong> <a href="mailto:<?php echo htmlspecialchars($application['email']); ?>"><?php echo htmlspecialchars($application['email']); ?></a></p>
                <p><strong>Location:</strong> <?php echo htmlspecialchars($application['city'] . ', ' . $application['barangay']); ?></p>
            </div>

            <div class="form-section">
                <h3>Educational Background</h3>
                <p><strong>School/University:</strong> <?php echo htmlspecialchars($application['school']); ?></p>
                <p><strong>Course/Program:</strong> <?php echo htmlspecialchars($application['course']); ?></p>
                <p><strong>Year Level:</strong> <?php echo htmlspecialchars($application['year_level']); ?></p>
            </div>

            <div class="form-section">
                <h3>Internship Preferences</h3>
                <p><strong>Preferred Types:</strong> 
                <?php 
                $types = explode(',', $application['internship_types']);
                foreach ($types as $type) {
                    echo '<span class="badge badge-primary" style="margin-right: 0.5rem;">' . htmlspecialchars(trim($type)) . '</span>';
                }
                ?>
                </p>
            </div>

            <?php if ($application['skills']): ?>
                <div class="form-section">
                    <h3>Skills</h3>
                    <p><?php echo htmlspecialchars($application['skills']); ?></p>
                </div>
            <?php endif; ?>

            <?php if ($application['cover_letter']): ?>
                <div class="form-section">
                    <h3>Cover Letter</h3>
                    <div style="padding: 1rem; background: var(--light-gray); border-radius: 6px; line-height: 1.6;">
                        <?php echo nl2br(htmlspecialchars($application['cover_letter'])); ?>
                    </div>
                </div>
            <?php endif; ?>

            <?php if ($application['resume_path']): ?>
                <div class="form-section">
                    <h3>Resume</h3>
                    <a href="uploads/<?php echo htmlspecialchars($application['resume_path']); ?>" target="_blank" class="btn-primary company-btn">📄 View Resume (PDF)</a>
                </div>
            <?php endif; ?>

            <div class="form-section">
                <h3>Application Timeline</h3>
                <p><strong>Applied:</strong> <?php echo date('F d, Y \a\t g:i A', strtotime($application['applied_at'])); ?></p>
                <?php if ($application['reviewed_at']): ?>
                    <p><strong>Last Reviewed:</strong> <?php echo date('F d, Y \a\t g:i A', strtotime($application['reviewed_at'])); ?></p>
                <?php endif; ?>
            </div>

            <form action="view_application.php?id=<?php echo $application_id; ?>" method="POST">
                <input type="hidden" name="update_status" value="1">
                
                <div class="form-section">
                    <h3>Update Application Status</h3>
                    <div class="form-group">
                        <label>Status *</label>
                        <select name="status" required>
                            <option value="pending" <?php if($application['status']=='pending') echo 'selected'; ?>>Pending - Awaiting review</option>
                            <option value="reviewed" <?php if($application['status']=='reviewed') echo 'selected'; ?>>Reviewed - Application has been reviewed</option>
                            <option value="shortlisted" <?php if($application['status']=='shortlisted') echo 'selected'; ?>>Shortlisted - Candidate is shortlisted</option>
                            <option value="accepted" <?php if($application['status']=='accepted') echo 'selected'; ?>>Accepted - Offer extended</option>
                            <option value="rejected" <?php if($application['status']=='rejected') echo 'selected'; ?>>Rejected - Not moving forward</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Notes for Applicant</label>
                        <textarea name="notes" rows="4" placeholder="Add feedback or notes for the applicant..." maxlength="2000"><?php echo htmlspecialchars($application['notes']); ?></textarea>
                        <span class="helper-text">Maximum 2000 characters. These notes will be visible to the student</span>
                    </div>
                </div>

                <div class="form-actions">
                    <a href="company_applications.php" class="btn-secondary">Back</a>
                    <button type="submit" class="btn-primary company-btn">Update Status</button>
                </div>
            </form>
        </div>
    </main>
</body>
</html>
<?php $conn->close(); ?>