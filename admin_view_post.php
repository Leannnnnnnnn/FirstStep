<?php
require_once 'config.php';
require_admin();

$posting_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Get posting details with company info
$stmt = $conn->prepare("
    SELECT ip.*, c.company_name, c.company_email, c.contact_person, c.contact_number, c.industry_type, c.company_address, c.company_logo
    FROM internship_postings ip
    JOIN companies c ON ip.company_id = c.company_id
    WHERE ip.posting_id = ?
");
$stmt->bind_param("i", $posting_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    $_SESSION['error'] = 'Posting not found';
    redirect('admin_dashboard.php');
}

$post = $result->fetch_assoc();
$stmt->close();

// Get application count for this posting
$app_count_query = "SELECT COUNT(*) as total FROM applications WHERE posting_id = ?";
$app_stmt = $conn->prepare($app_count_query);
$app_stmt->bind_param("i", $posting_id);
$app_stmt->execute();
$app_count = $app_stmt->get_result()->fetch_assoc()['total'];
$app_stmt->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Post Details - Admin</title>
    <link rel="stylesheet" href="styles.css?v=<?php echo time(); ?>">
    <style>
        .dashboard-container { max-width: 1200px !important; width: 95% !important; margin: 0 auto !important; }
        main { padding: 1rem 0.5rem !important; }
        .post-detail-container { background: white; padding: 2rem; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .post-header { border-bottom: 2px solid var(--border-color); padding-bottom: 1.5rem; margin-bottom: 1.5rem; }
        .post-title { font-size: 2rem; margin: 0 0 1rem 0; color: var(--dark); }
        .company-info { display: flex; gap: 1.5rem; align-items: start; margin-bottom: 1.5rem; }
        .company-logo-container { flex-shrink: 0; }
        .company-details { flex: 1; }
        .detail-section { margin-bottom: 2rem; }
        .detail-section h3 { color: var(--primary-color); margin-bottom: 1rem; font-size: 1.3rem; }
        .detail-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 1rem; margin-bottom: 1rem; }
        .detail-item { padding: 1rem; background: var(--light-gray); border-radius: 6px; }
        .detail-item label { font-weight: 600; color: var(--gray); font-size: 0.85rem; display: block; margin-bottom: 0.5rem; }
        .detail-item value { color: var(--dark); font-size: 1.1rem; }
        .status-badge { padding: 0.5rem 1rem; border-radius: 20px; font-size: 0.9rem; font-weight: 600; display: inline-block; }
        .status-pending { background: #fef3c7; color: #92400e; }
        .status-approved { background: #d1fae5; color: #065f46; }
        .status-rejected { background: #fee2e2; color: #991b1b; }
        .action-buttons { display: flex; gap: 1rem; margin-top: 2rem; flex-wrap: wrap; }
        .btn-approve { background: var(--success-color); color: white; padding: 0.8rem 1.5rem; border: none; border-radius: 6px; cursor: pointer; font-weight: 600; font-size: 1rem; }
        .btn-reject { background: var(--danger-color); color: white; padding: 0.8rem 1.5rem; border: none; border-radius: 6px; cursor: pointer; font-weight: 600; font-size: 1rem; }
        .btn-delete { background: #dc2626; color: white; padding: 0.8rem 1.5rem; border: none; border-radius: 6px; cursor: pointer; font-weight: 600; font-size: 1rem; }
        .btn-back { background: var(--light-gray); color: var(--dark); padding: 0.8rem 1.5rem; border: none; border-radius: 6px; text-decoration: none; display: inline-block; font-weight: 600; font-size: 1rem; }
        .rejection-box { background: #fee2e2; padding: 1.5rem; border-radius: 8px; border-left: 4px solid #dc2626; margin-top: 1.5rem; }
    </style>
</head>
<body>
    <header>
        <div class="logo">
            <div class="logo-text">
                <h1>FirstStep Admin</h1>
                <p>Post Details</p>
            </div>
            <nav class="nav-menu">
                <a href="admin_dashboard.php">← Back to Dashboard</a>
                <a href="admin_logout.php" class="logout-btn">Logout</a>
            </nav>
        </div>
    </header>

    <main>
        <div class="dashboard-container">
            <?php
            if (isset($_SESSION['success'])) {
                echo '<div class="alert alert-success">' . htmlspecialchars($_SESSION['success']) . '</div>';
                unset($_SESSION['success']);
            }
            if (isset($_SESSION['error'])) {
                echo '<div class="alert alert-error">' . htmlspecialchars($_SESSION['error']) . '</div>';
                unset($_SESSION['error']);
            }
            ?>

            <div class="post-detail-container">
                <!-- Header -->
                <div class="post-header">
                    <h1 class="post-title"><?php echo htmlspecialchars($post['job_title']); ?></h1>
                    <div style="display: flex; gap: 1rem; align-items: center; margin-top: 1rem;">
                        <span class="status-badge status-<?php echo $post['approval_status']; ?>">
                            <?php echo ucfirst($post['approval_status']); ?>
                        </span>
                        <span style="color: var(--gray);">•</span>
                        <span style="color: var(--gray);">Posted: <?php echo date('F d, Y', strtotime($post['created_at'])); ?></span>
                        <span style="color: var(--gray);">•</span>
                        <span style="color: var(--gray);"><?php echo $app_count; ?> application(s)</span>
                    </div>
                </div>

                <!-- Company Information -->
                <div class="detail-section">
                    <h3>🏢 Company Information</h3>
                    <div class="company-info">
                        <div class="company-logo-container">
                            <?php 
                            $logo_path = !empty($post['company_logo']) ? htmlspecialchars($post['company_logo']) : '';
                            $company_initial = strtoupper(substr($post['company_name'], 0, 1));
                            $logo_exists = $logo_path && file_exists($logo_path);
                            ?>
                            <?php if ($logo_exists): ?>
                                <img src="<?php echo $logo_path; ?>" alt="<?php echo htmlspecialchars($post['company_name']); ?>" style="width: 100px; height: 100px; object-fit: contain; border-radius: 8px; border: 1px solid #e5e7eb;">
                            <?php else: ?>
                                <div style="width: 100px; height: 100px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 8px; display: flex; align-items: center; justify-content: center; font-size: 40px; color: white; font-weight: bold;">
                                    <?php echo $company_initial; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                        <div class="company-details">
                            <h4 style="margin: 0 0 0.5rem 0; font-size: 1.5rem;"><?php echo htmlspecialchars($post['company_name']); ?></h4>
                            <p style="margin: 0 0 0.5rem 0; color: var(--gray);">
                                <?php if ($post['industry_type']): ?>
                                    <strong>Industry:</strong> <?php echo htmlspecialchars($post['industry_type']); ?>
                                <?php endif; ?>
                            </p>
                            <p style="margin: 0 0 0.5rem 0; color: var(--gray);">
                                <strong>Email:</strong> <?php echo htmlspecialchars($post['company_email']); ?>
                            </p>
                            <?php if ($post['contact_person']): ?>
                                <p style="margin: 0 0 0.5rem 0; color: var(--gray);">
                                    <strong>Contact Person:</strong> <?php echo htmlspecialchars($post['contact_person']); ?>
                                </p>
                            <?php endif; ?>
                            <?php if ($post['contact_number']): ?>
                                <p style="margin: 0 0 0.5rem 0; color: var(--gray);">
                                    <strong>Phone:</strong> <?php echo htmlspecialchars($post['contact_number']); ?>
                                </p>
                            <?php endif; ?>
                            <?php if ($post['company_address']): ?>
                                <p style="margin: 0; color: var(--gray);">
                                    <strong>Address:</strong> <?php echo htmlspecialchars($post['company_address']); ?>
                                </p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- Job Details -->
                <div class="detail-section">
                    <h3>💼 Job Details</h3>
                    <div class="detail-grid">
                        <div class="detail-item">
                            <label>📍 Location</label>
                            <value><?php echo htmlspecialchars($post['location']); ?></value>
                        </div>
                        <div class="detail-item">
                            <label>⏱️ Duration</label>
                            <value><?php echo htmlspecialchars($post['duration']); ?></value>
                        </div>
                        <div class="detail-item">
                            <label>💰 Stipend</label>
                            <value><?php echo htmlspecialchars($post['stipend']); ?></value>
                        </div>
                        <div class="detail-item">
                            <label>👥 Slots Available</label>
                            <value><?php echo $post['slots_available']; ?> positions</value>
                        </div>
                        <div class="detail-item">
                            <label>🏠 Internship Type</label>
                            <value><?php echo htmlspecialchars($post['internship_type']); ?></value>
                        </div>
                        <div class="detail-item">
                            <label>📅 Application Deadline</label>
                            <value>
                                <?php 
                                if ($post['application_deadline']) {
                                    echo date('F d, Y', strtotime($post['application_deadline']));
                                } else {
                                    echo 'No deadline';
                                }
                                ?>
                            </value>
                        </div>
                    </div>
                </div>

                <!-- Job Description -->
                <div class="detail-section">
                    <h3>📝 Job Description</h3>
                    <div style="background: var(--light-gray); padding: 1.5rem; border-radius: 8px; line-height: 1.8;">
                        <?php echo nl2br(htmlspecialchars($post['job_description'])); ?>
                    </div>
                </div>

                <!-- Requirements -->
                <div class="detail-section">
                    <h3>✅ Requirements</h3>
                    <div style="background: var(--light-gray); padding: 1.5rem; border-radius: 8px; line-height: 1.8;">
                        <?php echo nl2br(htmlspecialchars($post['requirements'])); ?>
                    </div>
                </div>

                <!-- Rejection Reason (if rejected) -->
                <?php if ($post['approval_status'] === 'rejected' && $post['rejection_reason']): ?>
                    <div class="rejection-box">
                        <h4 style="margin: 0 0 1rem 0; color: #991b1b;">❌ Rejection Reason</h4>
                        <p style="margin: 0; color: #7f1d1d; line-height: 1.6;">
                            <?php echo nl2br(htmlspecialchars($post['rejection_reason'])); ?>
                        </p>
                        <?php if ($post['reviewed_at']): ?>
                            <p style="margin: 1rem 0 0 0; color: #991b1b; font-size: 0.9rem;">
                                <strong>Rejected on:</strong> <?php echo date('F d, Y g:i A', strtotime($post['reviewed_at'])); ?>
                            </p>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>

                <!-- Review Info (if approved) -->
                <?php if ($post['approval_status'] === 'approved' && $post['reviewed_at']): ?>
                    <div style="background: #d1fae5; padding: 1rem; border-radius: 6px; border-left: 4px solid #10b981; margin-top: 1.5rem;">
                        <p style="margin: 0; color: #065f46;">
                            <strong>✅ Approved on:</strong> <?php echo date('F d, Y g:i A', strtotime($post['reviewed_at'])); ?>
                        </p>
                    </div>
                <?php endif; ?>

                <!-- Action Buttons -->
                <div class="action-buttons">
                    <a href="admin_dashboard.php" class="btn-back">← Back to Dashboard</a>
                    
                    <?php if ($post['approval_status'] === 'pending'): ?>
                        <button onclick="approvePost(<?php echo $post['posting_id']; ?>)" class="btn-approve">
                            ✅ Approve Post
                        </button>
                        <button onclick="showRejectModal(<?php echo $post['posting_id']; ?>)" class="btn-reject">
                            ❌ Reject Post
                        </button>
                    <?php endif; ?>
                    
                    <button onclick="deletePost(<?php echo $post['posting_id']; ?>)" class="btn-delete">
                        🗑️ Delete Post
                    </button>
                </div>
            </div>
        </div>
    </main>

    <!-- Rejection Modal -->
    <div id="rejectModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 1000; align-items: center; justify-content: center;">
        <div style="background: white; padding: 2rem; border-radius: 10px; max-width: 500px; width: 90%;">
            <h3 style="margin-top: 0;">Reject Post</h3>
            <form id="rejectForm" action="admin_process_post.php" method="POST">
                <input type="hidden" name="action" value="reject">
                <input type="hidden" name="posting_id" id="reject_posting_id">
                <div class="form-group">
                    <label>Reason for Rejection *</label>
                    <textarea name="rejection_reason" rows="4" required style="width: 100%; padding: 0.75rem; border: 1px solid var(--border-color); border-radius: 6px;"></textarea>
                    <span class="helper-text">Explain why this post is being rejected</span>
                </div>
                <div style="display: flex; gap: 1rem; margin-top: 1rem;">
                    <button type="submit" class="btn-reject" style="flex: 1;">Reject Post</button>
                    <button type="button" onclick="closeRejectModal()" style="flex: 1; padding: 0.6rem 1.2rem; background: var(--light-gray); color: var(--dark); border: none; border-radius: 6px; cursor: pointer; font-weight: 600;">Cancel</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function approvePost(postingId) {
            if (confirm('Are you sure you want to approve this post? It will be visible to all students.')) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = 'admin_process_post.php';
                
                const actionInput = document.createElement('input');
                actionInput.type = 'hidden';
                actionInput.name = 'action';
                actionInput.value = 'approve';
                
                const idInput = document.createElement('input');
                idInput.type = 'hidden';
                idInput.name = 'posting_id';
                idInput.value = postingId;
                
                form.appendChild(actionInput);
                form.appendChild(idInput);
                document.body.appendChild(form);
                form.submit();
            }
        }

        function showRejectModal(postingId) {
            document.getElementById('reject_posting_id').value = postingId;
            document.getElementById('rejectModal').style.display = 'flex';
        }

        function closeRejectModal() {
            document.getElementById('rejectModal').style.display = 'none';
        }

        function deletePost(postingId) {
            if (confirm('⚠️ WARNING: Are you sure you want to PERMANENTLY DELETE this post? This action cannot be undone!')) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = 'admin_process_post.php';
                
                const actionInput = document.createElement('input');
                actionInput.type = 'hidden';
                actionInput.name = 'action';
                actionInput.value = 'delete';
                
                const idInput = document.createElement('input');
                idInput.type = 'hidden';
                idInput.name = 'posting_id';
                idInput.value = postingId;
                
                form.appendChild(actionInput);
                form.appendChild(idInput);
                document.body.appendChild(form);
                form.submit();
            }
        }
    </script>
</body>
</html>
<?php $conn->close(); ?>