<?php require_once 'config.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FirstStep - Your Gateway to Internship Success</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body class="landing-page">
    <!-- Navigation Bar -->
    <nav class="landing-nav">
        <div class="nav-container">
            <div class="nav-logo">
                <div class="logo-text">
                    <h1>FirstStep</h1>
                </div>
            </div>
            <div class="nav-links">
                <a href="#home">Home</a>
                <a href="#about">About</a>
                <a href="#features">Features</a>
                <a href="#how-it-works">How It Works</a>
                <a href="login.php" class="btn-nav-login">Login</a>
                <a href="register.php" class="btn-nav-register">Register</a>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section id="home" class="hero-section">
        <div class="hero-content">
            <h1 class="hero-title">Take Your <span class="highlight">First Step</span> Towards Your Dream Career</h1>
            <p class="hero-subtitle">Connect talented students with top companies offering internship opportunities. Build your future, one step at a time.</p>
            <div class="hero-buttons">
                <a href="register_student.php" class="btn-hero btn-primary-hero">I'm a Student</a>
                <a href="register_company.php" class="btn-hero btn-secondary-hero">I'm a Company</a>
            </div>
            <div class="hero-stats">
                <div class="stat-item">
                    <h3>500+</h3>
                    <p>Students</p>
                </div>
                <div class="stat-item">
                    <h3>100+</h3>
                    <p>Companies</p>
                </div>
                <div class="stat-item">
                    <h3>1000+</h3>
                    <p>Internships</p>
                </div>
            </div>
        </div>
    </section>

    <!-- About Section -->
    <section id="about" class="about-section">
        <div class="container-landing">
            <div class="section-header">
                <h2>About FirstStep</h2>
                <p>Bridging the gap between aspiring professionals and leading companies</p>
            </div>
            <div class="about-content">
                <div class="about-text">
                    <h3>🟢 Our Mission</h3>
                    <p>FirstStep is dedicated to empowering graduating students by connecting them with valuable internship opportunities that kickstart their careers. We believe that every professional journey begins with a single step, and we're here to make that step count.</p>
                    
                    <h3>🔵 Why Choose FirstStep?</h3>
                    <p>We understand the challenges students face when searching for internships and the difficulties companies encounter in finding the right talent. Our platform simplifies the entire process, making it easier for both parties to find the perfect match.</p>
                    
                    <h3>🔴 Our Vision</h3>
                    <p>To become the leading platform where students discover their potential and companies find their future leaders. We're building a community where opportunities meet ambition.</p>
                </div>
                <div class="about-image">
                    <div class="about-image">
                        <img src="image/about-image.jpg" alt="Students and Companies Working Together">
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section id="features" class="features-section">
        <div class="container-landing">
            <div class="section-header">
                <h2>Platform Features</h2>
                <p>Everything you need in one powerful platform</p>
            </div>
            <div class="features-grid">
                <!-- Student Features -->
                <div class="feature-card">
                    <h3>For Students</h3>
                    <ul class="feature-list">
                        <li>✓ Browse hundreds of internship opportunities</li>
                        <li>✓ Create professional profiles</li>
                        <li>✓ Apply to multiple companies</li>
                        <li>✓ Track application status in real-time</li>
                        <li>✓ Upload and manage your resume</li>
                        <li>✓ Receive feedback from companies</li>
                    </ul>
                </div>

                <!-- Company Features -->
                <div class="feature-card">
                    <h3>For Companies</h3>
                    <ul class="feature-list">
                        <li>✓ Post unlimited internship positions</li>
                        <li>✓ Review qualified candidates</li>
                        <li>✓ Manage applications efficiently</li>
                        <li>✓ Access detailed student profiles</li>
                        <li>✓ Streamline hiring process</li>
                        <li>✓ Build your talent pipeline</li>
                    </ul>
                </div>

                <!-- Platform Features -->
                <div class="feature-card">
                    <h3>Platform Benefits</h3>
                    <ul class="feature-list">
                        <li>✓ Easy-to-use interface</li>
                        <li>✓ Secure and reliable</li>
                        <li>✓ Fast application process</li>
                        <li>✓ Real-time notifications</li>
                        <li>✓ Mobile-friendly design</li>
                        <li>✓ Free to use</li>
                    </ul>
                </div>
            </div>
        </div>
    </section>

    <!-- How It Works Section -->
    <section id="how-it-works" class="how-section">
        <div class="container-landing">
            <div class="section-header">
                <h2>How It Works</h2>
                <p>Get started in three simple steps</p>
            </div>
            
            <!-- For Students -->
            <div class="how-content">
                <h3 class="how-subtitle">For Students</h3>
                <div class="steps-grid">
                    <div class="step-card">
                        <div class="step-number">1</div>
                        <h4>Create Your Profile</h4>
                        <p>Sign up and build your professional profile with your education, skills, and resume.</p>
                    </div>
                    <div class="step-card">
                        <div class="step-number">2</div>
                        <h4>Browse & Apply</h4>
                        <p>Explore internship opportunities and apply to positions that match your interests.</p>
                    </div>
                    <div class="step-card">
                        <div class="step-number">3</div>
                        <h4>Get Hired</h4>
                        <p>Track your applications and communicate with companies to land your dream internship.</p>
                    </div>
                </div>
            </div>

            <!-- For Companies -->
            <div class="how-content" style="margin-top: 3rem;">
                <h3 class="how-subtitle">For Companies</h3>
                <div class="steps-grid">
                    <div class="step-card">
                        <div class="step-number">1</div>
                        <h4>Register Your Company</h4>
                        <p>Create your company profile and showcase what makes your organization unique.</p>
                    </div>
                    <div class="step-card">
                        <div class="step-number">2</div>
                        <h4>Post Internships</h4>
                        <p>Create detailed internship postings with requirements and expectations.</p>
                    </div>
                    <div class="step-card">
                        <div class="step-number">3</div>
                        <h4>Find Talent</h4>
                        <p>Review applications, interview candidates, and hire the best interns for your team.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Call to Action Section -->
    <section class="cta-section">
        <div class="container-landing">
            <div class="cta-content">
                <h2>Ready to Take Your First Step?</h2>
                <p>Join thousands of students and companies already using FirstStep</p>
                <div class="cta-buttons">
                    <a href="register_student.php" class="btn-cta btn-primary-cta">Register as Student</a>
                    <a href="register_company.php" class="btn-cta btn-secondary-cta">Register as Company</a>
                </div>
                <p class="cta-login">Already have an account? <a href="login.php">Login here</a></p>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="landing-footer">
        <div class="container-landing">
            <div class="footer-content">
                <div class="footer-section">
                    <div class="footer-logo">
                        <h3>FirstStep</h3>
                    </div>
                    <p>Your gateway to internship success. Connecting talent with opportunity.</p>
                </div>
                <div class="footer-section">
                    <h4>Quick Links</h4>
                    <ul>
                        <li><a href="#home">Home</a></li>
                        <li><a href="#about">About</a></li>
                        <li><a href="#features">Features</a></li>
                        <li><a href="#how-it-works">How It Works</a></li>
                    </ul>
                </div>
                <div class="footer-section">
                    <h4>For Students</h4>
                    <ul>
                        <li><a href="register_student.php">Register</a></li>
                        <li><a href="login.php">Login</a></li>
                        <li><a href="#features">Browse Internships</a></li>
                    </ul>
                </div>
                <div class="footer-section">
                    <h4>For Companies</h4>
                    <ul>
                        <li><a href="register_company.php">Register</a></li>
                        <li><a href="login.php">Login</a></li>
                        <li><a href="#features">Post Internships</a></li>
                    </ul>
                </div>
            </div>
            <div class="footer-bottom">
                <p>&copy; 2026 FirstStep. All rights reserved.</p>
            </div>
        </div>
    </footer>
</body>
</html>