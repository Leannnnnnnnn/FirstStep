<?php
require_once 'config.php';
require_student();

// Get student info
$stmt = $conn->prepare("SELECT * FROM students WHERE student_id = ?");
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$student = $stmt->get_result()->fetch_assoc();
$stmt->close();

// Display First Name
$first_name = $student['first_name'];

// Get available internships
$internships_stmt = $conn->prepare("
    SELECT ip.*, c.company_name, c.company_logo 
    FROM internship_postings ip 
    JOIN companies c ON ip.company_id = c.company_id 
    WHERE ip.status = 'active' 
    ORDER BY ip.created_at DESC 
    LIMIT 10
");
$internships_stmt->execute();
$internships = $internships_stmt->get_result();
$internships_stmt->close();

// Get student's applications
$applications_stmt = $conn->prepare("
    SELECT a.*, ip.job_title, c.company_name 
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

// Count stats
$stats_query = "SELECT COUNT(*) as total FROM applications WHERE student_id = " . $_SESSION['user_id'];
$total_applications = $conn->query($stats_query)->fetch_assoc()['total'];

$pending_query = "SELECT COUNT(*) as total FROM applications WHERE student_id = " . $_SESSION['user_id'] . " AND status = 'pending'";
$pending_applications = $conn->query($pending_query)->fetch_assoc()['total'];

$accepted_query = "SELECT COUNT(*) as total FROM applications WHERE student_id = " . $_SESSION['user_id'] . " AND status = 'accepted'";
$accepted_applications = $conn->query($accepted_query)->fetch_assoc()['total'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Dashboard - FirstStep</title>
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
                <a href="student_profile.php">Profile</a>
                <a href="logout.php" class="logout-btn">Logout</a>
            </nav>
        </div>
    </header>

    <main>
        <div class="dashboard-container">
            <div class="dashboard-header">
                <h2>Welcome, <?php echo htmlspecialchars($first_name); ?>! 👋</h2>
                <p>Find your perfect internship opportunity</p>
            </div>

            <div class="dashboard-stats">
                <div class="stat-card">
                    <h4>Total Applications</h4>
                    <div class="stat-value"><?php echo $total_applications; ?></div>
                </div>
                <div class="stat-card">
                    <h4>Pending Review</h4>
                    <div class="stat-value"><?php echo $pending_applications; ?></div>
                </div>
                <div class="stat-card">
                    <h4>Accepted</h4>
                    <div class="stat-value"><?php echo $accepted_applications; ?></div>
                </div>
            </div>

            <div class="dashboard-content">
                <h3>Available Internship Opportunities</h3>
                <div class="internship-list">
                    <?php if ($internships->num_rows > 0): ?>
                        <?php while ($internship = $internships->fetch_assoc()): ?>
                            <div class="internship-card">
                                <h4><?php echo htmlspecialchars($internship['job_title']); ?></h4>
                                <p class="company-name">🏢 <?php echo htmlspecialchars($internship['company_name']); ?></p>
                                <div class="internship-details">
                                    <span class="detail-item">📍 <?php echo htmlspecialchars($internship['location']); ?></span>
                                    <span class="detail-item">⏱️ <?php echo htmlspecialchars($internship['duration']); ?></span>
                                    <span class="detail-item">💰 <?php echo htmlspecialchars($internship['stipend']); ?></span>
                                    <span class="badge badge-primary"><?php echo htmlspecialchars($internship['internship_type']); ?></span>
                                </div>
                                <p><?php echo nl2br(htmlspecialchars(substr($internship['job_description'], 0, 200))); ?>...</p>
                                <div style="margin-top: 1rem;">
                                    <a href="internship_details.php?id=<?php echo $internship['posting_id']; ?>" class="btn-primary">View Details</a>
                                </div>
                            </div>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <p>No internship opportunities available at the moment.</p>
                    <?php endif; ?>
                </div>
            </div>

            <?php if ($applications->num_rows > 0): ?>
            <div class="dashboard-content" style="margin-top: 2rem;">
                <h3>My Recent Applications</h3>
                <div class="application-list">
                    <?php while ($application = $applications->fetch_assoc()): ?>
                        <div class="application-card">
                            <h4><?php echo htmlspecialchars($application['job_title']); ?></h4>
                            <p class="company-name">🏢 <?php echo htmlspecialchars($application['company_name']); ?></p>
                            <div class="application-details">
                                <span class="detail-item">📅 Applied: <?php echo date('M d, Y', strtotime($application['applied_at'])); ?></span>
                                <?php
                                $status_class = 'badge-warning';
                                if ($application['status'] === 'accepted') $status_class = 'badge-success';
                                if ($application['status'] === 'rejected') $status_class = 'badge-danger';
                                ?>
                                <span class="badge <?php echo $status_class; ?>"><?php echo ucfirst($application['status']); ?></span>
                            </div>
                        </div>
                    <?php endwhile; ?>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </main>
</body>
</html>
<?php $conn->close(); ?>