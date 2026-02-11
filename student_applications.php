<?php
require_once 'config.php';
require_student();

// Get student info
$stmt = $conn->prepare("SELECT first_name, middle_name, surname FROM students WHERE student_id = ?");
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$student = $stmt->get_result()->fetch_assoc();
$stmt->close();

// Build full name
$full_name = $student['first_name'];
if (!empty($student['middle_name'])) {
    $full_name .= ' ' . $student['middle_name'];
}
$full_name .= ' ' . $student['surname'];

// Get all applications
$applications_stmt = $conn->prepare("
    SELECT a.*, ip.job_title, ip.location, ip.internship_type, c.company_name, c.company_logo 
    FROM applications a 
    JOIN internship_postings ip ON a.posting_id = ip.posting_id 
    JOIN companies c ON a.company_id = c.company_id 
    WHERE a.student_id = ? 
    ORDER BY a.applied_at DESC
");
$applications_stmt->bind_param("i", $_SESSION['user_id']);
$applications_stmt->execute();
$applications = $applications_stmt->get_result();
$applications_stmt->close();

// Count by status
$pending = $accepted = $rejected = $reviewed = $shortlisted = 0;
$count_stmt = $conn->prepare("
    SELECT status, COUNT(*) as count 
    FROM applications 
    WHERE student_id = ? 
    GROUP BY status
");
$count_stmt->bind_param("i", $_SESSION['user_id']);
$count_stmt->execute();
$result = $count_stmt->get_result();
while ($row = $result->fetch_assoc()) {
    switch($row['status']) {
        case 'pending': $pending = $row['count']; break;
        case 'accepted': $accepted = $row['count']; break;
        case 'rejected': $rejected = $row['count']; break;
        case 'reviewed': $reviewed = $row['count']; break;
        case 'shortlisted': $shortlisted = $row['count']; break;
    }
}
$count_stmt->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Applications - FirstStep</title>
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
        <div class="dashboard-container">
            <div class="dashboard-header">
                <h2 style="margin-left: 20px;">My Applications</h2>
                <p style="margin-left: 20px;">Track all your internship applications</p>
            </div>

            <div class="dashboard-stats">
                <div class="stat-card">
                    <h4>Pending</h4>
                    <div class="stat-value"><?php echo $pending; ?></div>
                </div>
                <div class="stat-card">
                    <h4>Reviewed</h4>
                    <div class="stat-value"><?php echo $reviewed; ?></div>
                </div>
                <div class="stat-card">
                    <h4>Shortlisted</h4>
                    <div class="stat-value"><?php echo $shortlisted; ?></div>
                </div>
                <div class="stat-card">
                    <h4>Accepted</h4>
                    <div class="stat-value"><?php echo $accepted; ?></div>
                </div>
            </div>

            <div class="dashboard-content">
                <h3 style="margin-left: 20px;">All Applications (<?php echo $applications->num_rows; ?>)</h3>
                
                <?php if ($applications->num_rows > 0): ?>
                    <div class="application-list">
                        <?php while ($application = $applications->fetch_assoc()): ?>
                            <div class="application-card">
                                <div style="display: flex; justify-content: space-between; align-items: start;">
                                    <div style="flex: 1;">
                                        <h4><?php echo htmlspecialchars($application['job_title']); ?></h4>
                                        <p class="company-name">🏢 <?php echo htmlspecialchars($application['company_name']); ?></p>
                                    </div>
                                    <?php
                                    $status_class = 'badge-warning';
                                    if ($application['status'] === 'accepted') $status_class = 'badge-success';
                                    if ($application['status'] === 'rejected') $status_class = 'badge-danger';
                                    if ($application['status'] === 'shortlisted') $status_class = 'badge-primary';
                                    if ($application['status'] === 'reviewed') $status_class = 'badge-primary';
                                    ?>
                                    <span class="badge <?php echo $status_class; ?>"><?php echo ucfirst($application['status']); ?></span>
                                </div>
                                
                                <div class="internship-details">
                                    <span class="detail-item">📍 <?php echo htmlspecialchars($application['location']); ?></span>
                                    <span class="detail-item">📅 Applied: <?php echo date('M d, Y', strtotime($application['applied_at'])); ?></span>
                                    <?php if ($application['reviewed_at']): ?>
                                        <span class="detail-item">👁️ Reviewed: <?php echo date('M d, Y', strtotime($application['reviewed_at'])); ?></span>
                                    <?php endif; ?>
                                    <span class="badge badge-primary"><?php echo htmlspecialchars($application['internship_type']); ?></span>
                                </div>

                                <?php if ($application['cover_letter']): ?>
                                    <div style="margin-top: 1rem; padding: 1rem; background: var(--light-gray); border-radius: 6px;">
                                        <strong>Cover Letter:</strong>
                                        <p style="margin-top: 0.5rem; color: var(--gray);">
                                            <?php echo nl2br(htmlspecialchars(substr($application['cover_letter'], 0, 200))); ?>
                                            <?php if (strlen($application['cover_letter']) > 200) echo '...'; ?>
                                        </p>
                                    </div>
                                <?php endif; ?>

                                <?php if ($application['notes'] && $application['status'] !== 'pending'): ?>
                                    <div style="margin-top: 1rem; padding: 1rem; background: #FEF3C7; border-radius: 6px; border-left: 4px solid var(--warning-color);">
                                        <strong>Company Notes:</strong>
                                        <p style="margin-top: 0.5rem; color: var(--dark);">
                                            <?php echo nl2br(htmlspecialchars($application['notes'])); ?>
                                        </p>
                                    </div>
                                <?php endif; ?>

                                <div style="margin-top: 1rem;">
                                    <a href="internship_details.php?id=<?php echo $application['posting_id']; ?>" class="btn-secondary">View Posting</a>
                                </div>
                            </div>
                        <?php endwhile; ?>
                    </div>
                <?php else: ?>
                    <div style="text-align: center; padding: 3rem;">
                        <p style="font-size: 1.2rem; color: var(--gray); margin-bottom: 1rem;">You haven't applied to any internships yet.</p>
                        <a href="student_dashboard.php" class="btn-primary">Browse Internships</a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </main>
</body>
</html>
<?php $conn->close(); ?>