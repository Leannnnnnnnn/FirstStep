<?php
require_once 'config.php';
require_student();

$stmt = $conn->prepare("SELECT * FROM students WHERE student_id = ?");
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$student = $stmt->get_result()->fetch_assoc();
$stmt->close();
$first_name = $student['first_name'];

// FILTERS
$filter_industry = isset($_GET['industry']) && $_GET['industry'] !== '' ? $_GET['industry'] : null;
$filter_duration = isset($_GET['duration']) && $_GET['duration'] !== '' ? $_GET['duration'] : null;
$filter_allowance = isset($_GET['allowance']) && $_GET['allowance'] !== '' ? $_GET['allowance'] : null;
$filter_type = isset($_GET['type']) && $_GET['type'] !== '' ? $_GET['type'] : null;

$where_conditions = ["ip.status = 'active'"];
$params = [];
$types = "";

if ($filter_industry) { $where_conditions[] = "c.industry_type = ?"; $params[] = $filter_industry; $types .= "s"; }
if ($filter_duration) { $where_conditions[] = "ip.duration LIKE ?"; $params[] = "%$filter_duration%"; $types .= "s"; }
if ($filter_type) { $where_conditions[] = "ip.internship_type = ?"; $params[] = $filter_type; $types .= "s"; }

$where_clause = implode(' AND ', $where_conditions);
$sql = "SELECT ip.*, c.company_name, c.company_logo, c.industry_type FROM internship_postings ip JOIN companies c ON ip.company_id = c.company_id WHERE $where_clause ORDER BY ip.created_at DESC LIMIT 50";
$internships_stmt = $conn->prepare($sql);
if (!empty($params)) { $internships_stmt->bind_param($types, ...$params); }
$internships_stmt->execute();
$internships = $internships_stmt->get_result();
$internships_stmt->close();

$applications_stmt = $conn->prepare("SELECT a.*, ip.job_title, c.company_name FROM applications a JOIN internship_postings ip ON a.posting_id = ip.posting_id JOIN companies c ON a.company_id = c.company_id WHERE a.student_id = ? ORDER BY a.applied_at DESC");
$applications_stmt->bind_param("i", $_SESSION['user_id']);
$applications_stmt->execute();
$applications = $applications_stmt->get_result();
$applications_stmt->close();

$total_applications = $conn->query("SELECT COUNT(*) as total FROM applications WHERE student_id = " . $_SESSION['user_id'])->fetch_assoc()['total'];
$pending_applications = $conn->query("SELECT COUNT(*) as total FROM applications WHERE student_id = " . $_SESSION['user_id'] . " AND status = 'pending'")->fetch_assoc()['total'];
$accepted_applications = $conn->query("SELECT COUNT(*) as total FROM applications WHERE student_id = " . $_SESSION['user_id'] . " AND status = 'accepted'")->fetch_assoc()['total'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Dashboard - FirstStep</title>
    <link rel="stylesheet" href="styles.css?v=<?php echo time(); ?>">
    <style>
        .dashboard-container { max-width: 1600px !important; width: 95% !important; margin: 0 auto !important; }
        main { padding: 1rem 0.5rem !important; }
        .dashboard-header { padding-left: 0 !important; padding-right: 0 !important; }
        .filter-section { background: var(--white); padding: 1.5rem; border-radius: 10px; margin-bottom: 2rem; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .filter-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem; margin-top: 1rem; }
        .filter-group { display: flex; flex-direction: column; }
        .filter-group label { font-weight: 600; margin-bottom: 0.5rem; color: var(--dark); font-size: 0.9rem; }
        .filter-group select { padding: 0.6rem; border: 1px solid var(--border-color); border-radius: 6px; font-size: 0.95rem; }
        .filter-actions { display: flex; gap: 1rem; margin-top: 1rem; }
        .btn-filter { padding: 0.7rem 1.5rem; background: var(--primary-color); color: white; border: none; border-radius: 6px; cursor: pointer; font-weight: 600; }
        .btn-clear { padding: 0.7rem 1.5rem; background: var(--light-gray); color: var(--dark); border: none; border-radius: 6px; text-decoration: none; display: inline-block; text-align: center; font-weight: 600; }
        .active-filters { display: flex; gap: 0.5rem; flex-wrap: wrap; margin-top: 1rem; }
        .filter-tag { background: var(--primary-color); color: white; padding: 0.4rem 0.8rem; border-radius: 20px; font-size: 0.85rem; }
    </style>
</head>
<body>
    <header>
        <div class="logo">
            <div class="logo-text"><h1>FirstStep</h1><p>Internship Connection Platform</p></div>
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
                <div class="stat-card"><h3>Total Applications</h3><p class="stat-number"><?php echo $total_applications; ?></p></div>
                <div class="stat-card"><h3>Pending Review</h3><p class="stat-number"><?php echo $pending_applications; ?></p></div>
                <div class="stat-card"><h3>Accepted</h3><p class="stat-number"><?php echo $accepted_applications; ?></p></div>
            </div>
            <div class="filter-section">
                <h3 style="margin-bottom: 0.5rem;">🔍 Filter Internships</h3>
                <p style="color: var(--gray); font-size: 0.9rem; margin-bottom: 1rem;">Refine your search to find the perfect opportunity</p>
                <form method="GET" action="student_dashboard.php">
                    <div class="filter-grid">
                        <div class="filter-group">
                            <label>Industry Type</label>
                            <select name="industry">
                                <option value="">All Industries</option>
                                <option value="Technology" <?php echo $filter_industry === 'Technology' ? 'selected' : ''; ?>>Technology</option>
                                <option value="Marketing" <?php echo $filter_industry === 'Marketing' ? 'selected' : ''; ?>>Marketing</option>
                                <option value="Finance" <?php echo $filter_industry === 'Finance' ? 'selected' : ''; ?>>Finance</option>
                                <option value="Healthcare" <?php echo $filter_industry === 'Healthcare' ? 'selected' : ''; ?>>Healthcare</option>
                                <option value="Education" <?php echo $filter_industry === 'Education' ? 'selected' : ''; ?>>Education</option>
                                <option value="Engineering" <?php echo $filter_industry === 'Engineering' ? 'selected' : ''; ?>>Engineering</option>
                                <option value="Hospitality" <?php echo $filter_industry === 'Hospitality' ? 'selected' : ''; ?>>Hospitality</option>
                            </select>
                        </div>
                        <div class="filter-group">
                            <label>Duration</label>
                            <select name="duration">
                                <option value="">Any Duration</option>
                                <option value="1-2 months" <?php echo $filter_duration === '1-2 months' ? 'selected' : ''; ?>>1-2 months</option>
                                <option value="3 months" <?php echo $filter_duration === '3 months' ? 'selected' : ''; ?>>3 months</option>
                                <option value="3-6 months" <?php echo $filter_duration === '3-6 months' ? 'selected' : ''; ?>>3-6 months</option>
                                <option value="6 months" <?php echo $filter_duration === '6 months' ? 'selected' : ''; ?>>6 months</option>
                            </select>
                        </div>
                        <div class="filter-group">
                            <label>Monthly Allowance</label>
                            <select name="allowance">
                                <option value="">Any Allowance</option>
                                <option value="0" <?php echo $filter_allowance === '0' ? 'selected' : ''; ?>>Unpaid</option>
                                <option value="1-5000" <?php echo $filter_allowance === '1-5000' ? 'selected' : ''; ?>>₱1 - ₱5,000</option>
                                <option value="5001-10000" <?php echo $filter_allowance === '5001-10000' ? 'selected' : ''; ?>>₱5,001 - ₱10,000</option>
                                <option value="10000+" <?php echo $filter_allowance === '10000+' ? 'selected' : ''; ?>>₱10,000+</option>
                            </select>
                        </div>
                        <div class="filter-group">
                            <label>Internship Type</label>
                            <select name="type">
                                <option value="">Any Type</option>
                                <option value="On-site" <?php echo $filter_type === 'On-site' ? 'selected' : ''; ?>>On-site</option>
                                <option value="Remote" <?php echo $filter_type === 'Remote' ? 'selected' : ''; ?>>Remote</option>
                                <option value="Hybrid" <?php echo $filter_type === 'Hybrid' ? 'selected' : ''; ?>>Hybrid</option>
                            </select>
                        </div>
                    </div>
                    <div class="filter-actions">
                        <button type="submit" class="btn-filter">Apply Filters</button>
                        <a href="student_dashboard.php" class="btn-clear">Clear All</a>
                    </div>
                    <?php if ($filter_industry || $filter_duration || $filter_allowance || $filter_type): ?>
                    <div class="active-filters">
                        <span style="font-weight: 600;">Active:</span>
                        <?php if ($filter_industry): ?><span class="filter-tag">Industry: <?php echo htmlspecialchars($filter_industry); ?></span><?php endif; ?>
                        <?php if ($filter_duration): ?><span class="filter-tag">Duration: <?php echo htmlspecialchars($filter_duration); ?></span><?php endif; ?>
                        <?php if ($filter_allowance): ?><span class="filter-tag">Allowance: <?php echo $filter_allowance === '0' ? 'Unpaid' : '₱' . htmlspecialchars($filter_allowance); ?></span><?php endif; ?>
                        <?php if ($filter_type): ?><span class="filter-tag">Type: <?php echo htmlspecialchars($filter_type); ?></span><?php endif; ?>
                    </div>
                    <?php endif; ?>
                </form>
            </div>
            <div class="dashboard-content">
                <h3>Available Internship Opportunities <?php if ($internships->num_rows > 0): ?><span style="color: var(--gray); font-size: 0.9rem; font-weight: normal;">(<?php echo $internships->num_rows; ?> results)</span><?php endif; ?></h3>
                <div class="internship-list">
                    <?php if ($internships->num_rows > 0): while ($internship = $internships->fetch_assoc()): ?>
                        <div class="internship-card">
                            <div style="display: flex; align-items: flex-start; gap: 1rem;">
                                <?php $logo_path = !empty($internship['company_logo']) ? htmlspecialchars($internship['company_logo']) : ''; $company_initial = strtoupper(substr($internship['company_name'], 0, 1)); $logo_exists = $logo_path && file_exists($logo_path); ?>
                                <?php if ($logo_exists): ?><img src="<?php echo $logo_path; ?>" alt="<?php echo htmlspecialchars($internship['company_name']); ?>" style="width: 60px; height: 60px; object-fit: contain; border-radius: 8px; border: 1px solid #e5e7eb;"><?php else: ?><div style="width: 60px; height: 60px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 8px; display: flex; align-items: center; justify-content: center; font-size: 24px; color: white; font-weight: bold;"><?php echo $company_initial; ?></div><?php endif; ?>
                                <div style="flex: 1;">
                                    <h4 style="margin: 0 0 0.5rem 0;"><?php echo htmlspecialchars($internship['job_title']); ?></h4>
                                    <p class="company-name" style="margin: 0 0 0.5rem 0;">🏢 <?php echo htmlspecialchars($internship['company_name']); ?> <?php if (!empty($internship['industry_type'])): ?><span style="color: var(--gray); font-size: 0.9rem;">• <?php echo htmlspecialchars($internship['industry_type']); ?></span><?php endif; ?></p>
                                    <div class="internship-details">
                                        <span class="detail-item">📍 <?php echo htmlspecialchars($internship['location']); ?></span>
                                        <span class="detail-item">⏱️ <?php echo htmlspecialchars($internship['duration']); ?></span>
                                        <span class="detail-item">💰 <?php echo htmlspecialchars($internship['stipend']); ?></span>
                                        <span class="badge badge-primary"><?php echo htmlspecialchars($internship['internship_type']); ?></span>
                                    </div>
                                </div>
                            </div>
                            <p style="margin-top: 1rem;"><?php echo nl2br(htmlspecialchars(substr($internship['job_description'], 0, 200))); ?>...</p>
                            <div style="margin-top: 1rem;"><a href="internship_details.php?id=<?php echo $internship['posting_id']; ?>" class="btn-primary">View Details</a></div>
                        </div>
                    <?php endwhile; else: ?>
                        <div style="text-align: center; padding: 3rem; background: var(--light-gray); border-radius: 10px;">
                            <p style="font-size: 3rem; margin-bottom: 1rem;">🔍</p>
                            <p style="font-size: 1.2rem; color: var(--dark); margin-bottom: 0.5rem;">No internships found</p>
                            <p style="color: var(--gray);">Try adjusting your filters or check back later!</p>
                        </div>
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
                                <?php $status_class = 'badge-warning'; if ($application['status'] === 'accepted') $status_class = 'badge-success'; if ($application['status'] === 'rejected') $status_class = 'badge-danger'; ?>
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