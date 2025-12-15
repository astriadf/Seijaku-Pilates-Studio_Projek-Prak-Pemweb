<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Seijaku Studio Pilates - Purbalingga</title>
    <link rel="icon" type="image/png" href="images/logo.png">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/main.css">
</head>
<body>
    <nav class="navbar" id="navbar">
        <div class="nav-container">
            <a href="#" class="nav-logo">
                <img src="images/logo.png" alt="Seijaku Pilates">
                <span>Seijaku Studio</span>
            </a>
            <div class="nav-menu" id="nav-menu">
                <a href="#home" class="nav-link">Home</a>
                <a href="#about" class="nav-link">About</a>
                <a href="#classes" class="nav-link">Classes</a>
                <a href="#instructors" class="nav-link">Instructors</a>
                <a href="#videos" class="nav-link">Videos</a>
                <a href="#testimonials" class="nav-link">Testimonials</a>
                <a href="#contact" class="nav-link">Contact</a>
            </div>
            <div class="nav-buttons">
                <button class="btn btn-outline" onclick="openModal('loginModal')">Login</button>
                <button class="btn btn-primary" onclick="openModal('registerModal')">Register</button>
            </div>
            <div class="nav-toggle" id="nav-toggle">
                <i class="fas fa-bars"></i>
            </div>
        </div>
    </nav>

    <div id="totoroLottie" class="totoro-video"></div>

    <section class="hero" id="home">
        <div class="hero-bg" style="background-image: url('images/hero-bg.jpeg')"></div>
        <div class="hero-overlay"></div>
        <div class="floating-elements">
            <div class="cloud cloud-1"></div>
            <div class="cloud cloud-2"></div>
            <div class="flower flower-1"></div>
            <div class="flower flower-2"></div>
            <div class="flower flower-3"></div>
            <div class="grass"></div>
        </div>
        <div class="hero-content">
            <h1 class="hero-title">Transform Your Body,</h1>
            <h1 class="hero-title hero-title-secondary">Find Your Peace</h1>
            <p class="hero-subtitle">Discover the power of Pilates at Seijaku Studio. Build strength, improve flexibility, and achieve mindful wellness in our serene space.</p>
            <div class="hero-buttons">
                <a href="#classes" class="btn btn-primary btn-lg">Book Your First Class</a>
                <a href="#about" class="btn btn-outline btn-lg">Learn More</a>
            </div>
        </div>
        <div class="scroll-indicator">
            <i class="fas fa-chevron-down"></i>
        </div>
    </section>

    <section class="welcome-stats">
        <div class="container">
            <h2 class="welcome-title">Welcome to Seijaku</h2>
            <p class="welcome-subtitle">
                Seijaku Pilates Studio is a place where serenity meets strength. The name "Seijaku" (静寂) comes from Japanese and means "serenity in activity"－a philosophy we apply to every Pilates movement. In our comfortable studio in Purbalingga, we invite you to find balance in body and mind through Pilates exercise guided by certified instructors.
            </p>
            <div class="stats-counter-grid">
                <div class="stat-counter-card">
                    <div class="stat-counter-icon"><i class="fas fa-users"></i></div>
                    <div class="stat-counter-number" data-target="500">0</div>
                    <div class="stat-counter-label">Our Members</div>
                </div>
                <div class="stat-counter-card">
                    <div class="stat-counter-icon"><i class="fas fa-dumbbell"></i></div>
                    <div class="stat-counter-number" data-target="9">0</div>
                    <div class="stat-counter-label">Class Types</div>
                </div>
                <div class="stat-counter-card">
                    <div class="stat-counter-icon"><i class="fas fa-chalkboard-teacher"></i></div>
                    <div class="stat-counter-number" data-target="10">0</div>
                    <div class="stat-counter-label">Expert Instructors</div>
                </div>
                <div class="stat-counter-card">
                    <div class="stat-counter-icon"><i class="fas fa-award"></i></div>
                    <div class="stat-counter-number" data-target="1">0</div>
                    <div class="stat-counter-label">Best Pilates Studio</div>
                </div>
            </div>
        </div>
    </section>

    <section class="about hero-section hero-about" id="about">
        <div class="hero-bg" style="background-image: url('images/bg-2.jpeg')"></div>
        <div class="hero-overlay"></div>
        <div class="floating-elements">
            <div class="cloud cloud-1"></div>
            <div class="cloud cloud-2"></div>
            <div class="flower flower-1"></div>
            <div class="flower flower-2"></div>
            <div class="flower flower-3"></div>
            <div class="grass"></div>
        </div>

        <div class="container hero-inner">
            <div class="section-header">
                <span class="section-tag">About</span>
                <h2 class="section-title">Why choose Seijaku?</h2>
                <p class="section-desc">Experience the prefect blend of traditional Pilates wisdom and modern wellness practices.</p>
            </div>
            <div class="about-grid">
                <div class="about-card">
                    <div class="about-icon"><i class="fas fa-leaf"></i></div>
                    <h3>Peaceful Environment</h3>
                    <p>Practice in our Ghibli-inspired studio designed for transquility and focus.</p>
                </div>
                <div class="about-card">
                    <div class="about-icon"><i class="fas fa-award"></i></div>
                    <h3>Expert Instructors</h3>
                    <p>Learn from certified instructors with years of experience in various Pilates disiplines.</p>
                </div>
                <div class="about-card">
                    <div class="about-icon"><i class="fas fa-users"></i></div>
                    <h3>Private Class</h3>
                    <p>Maximum of 10 participants per class to ensure personal attention and proper technique correction.</p>
                </div>
                <div class="about-card">
                    <div class="about-icon"><i class="fas fa-heart"></i></div>
                    <h3>Complete Program</h3>
                    <p>10 types of pilates from Mat to Reformer, suitable for beginners to advanced.</p>
                </div>
                <div class="about-card">
                    <div class="about-icon"><i class="fas fa-clock"></i></div>
                    <h3>Flexible Schedules</h3>
                    <p>Classes are available Friday-Sunday with 3 sessions: morning, afternoon, and evening.</p>
                </div>
                <div class="about-card">
                    <div class="about-icon"><i class="fas fa-trophy"></i></div>
                    <h3>Badge Achievements</h3>
                    <p>Earn a Ghibli badge as a reward for your dedicated practice.</p>
                </div>
            </div>
        </div>
    </section>

    <section class="classes" id="classes">
        <div class="container">
            <div class="section-header">
                <span class="section-tag">Our Classes</span>
                <h2 class="section-title">9 Types of Pilates</h2>
                <p class="section-desc">Choose a class according to your needs and ability level.</p>
            </div>
            <div class="level-filters">
                <button class="filter-btn active" data-level="all">All</button>
                <button class="filter-btn" data-level="beginner">Beginner</button>
                <button class="filter-btn" data-level="intermediate">Intermediate</button>
                <button class="filter-btn" data-level="advanced">Expert</button>
            </div>
            <div class="classes-grid" id="classesGrid"></div>
        </div>
    </section>

    <section class="instructors hero-section hero-instructors" id="instructors">
        <div class="hero-bg" style="background-image: url('images/bg-3.jpeg')"></div>
        <div class="hero-overlay"></div>
        <div class="floating-elements">
            <div class="cloud cloud-1"></div>
            <div class="cloud cloud-2"></div>
            <div class="flower flower-1"></div>
            <div class="flower flower-2"></div>
            <div class="flower flower-3"></div>
            <div class="grass"></div>
        </div>

        <div class="container hero-inner">
            <div class="section-header">
                <span class="section-tag">Our Team</span>
                <h2 class="section-title">Profesional Instructors</h2>
                <p class="section-desc">Led by certified instructors with years of experience.</p>
            </div>
            <div class="instructors-slider" id="instructorsSlider">
                <button class="slider-btn prev-btn" onclick="slideInstructors(-1)">
                    <i class="fas fa-chevron-left"></i>
                </button>
                <div class="slider-container">
                    <div class="slider-track" id="instructorTrack"></div>
                </div>
                <button class="slider-btn next-btn" onclick="slideInstructors(1)">
                    <i class="fas fa-chevron-right"></i>
                </button>
            </div>
        </div>
    </section>

    <section class="videos" id="videos">
        <div class="container">
            <div class="section-header">
                <span class="section-tag">Free Video Tutorials</span>
                <h2 class="section-title">Learn Anywhere and Anytime</h2>
                <p class="section-desc">Access Pilates video tutorials anytime for self-paced workouts.</p>
            </div>
            <div class="videos-grid" id="videosGrid"></div>
            <div class="videos-cta">
                <p>Sign up now for full access to all video tutorials.</p>
                <button class="btn btn-primary" onclick="openModal('registerModal')">Register Free</button>
            </div>
        </div>
    </section>

    <section class="testimonials hero-section hero-testimonials" id="testimonials">
        <div class="hero-bg" style="background-image: url('images/bg-4.jpeg')"></div>
        <div class="hero-overlay"></div>
        <div class="floating-elements">
            <div class="cloud cloud-1"></div>
            <div class="cloud cloud-2"></div>
            <div class="flower flower-1"></div>
            <div class="flower flower-2"></div>
            <div class="flower flower-3"></div>
            <div class="grass"></div>
        </div>

        <div class="container hero-inner">
            <div class="section-header">
                <span class="section-tag">Testimonials</span>
                <h2 class="section-title">What They Say?</h2>
            <p class="section-desc">Inspiring stories from Seijaku Studio Pilates members.</p>
            </div>
            <div class="testimonials-slider">
                <button class="slider-btn prev-btn" onclick="prevTestimonial()">
                    <i class="fas fa-chevron-left"></i>
                </button>
                <div class="testimonials-track" id="testimonialsTrack"></div>
                <div class="testimonials-dots" id="testimonialsDots"></div>
            </div>
        </div>
    </section>

    <section class="contact" id="contact">
        <div class="container">
            <div class="contact-wrapper">
                <div class="contact-info">
                    <div class="section-header text-left">
                        <span class="section-tag">Contact Us</span>
                        <h2 class="section-title">Let's get connected</h2>
                    </div>
                    <div class="contact-details">
                        <div class="contact-item">
                            <i class="fas fa-map-marker-alt"></i>
                            <div>
                                <h4>Address</h4>
                                <p>Jl. Jendral Sudirman No. 123<br>Purbalingga, Jawa Tengah 53312</p>
                            </div>
                        </div>
                        <div class="contact-item">
                            <i class="fas fa-phone"></i>
                            <div>
                                <h4>Phone Number</h4>
                                <p>+62 812 3456 7890</p>
                            </div>
                        </div>
                        <div class="contact-item">
                            <i class="fas fa-envelope"></i>
                            <div>
                                <h4>Email</h4>
                                <p>seijaku@pilates.com</p>
                            </div>
                        </div>
                        <div class="contact-item">
                            <i class="fas fa-clock"></i>
                            <div>
                                <h4>Operating Hours</h4>
                                <p>Friday - Sunday<br>07:00 AM - 9:00 PM</p>
                            </div>
                        </div>
                    </div>
                    <div class="social-links">
                        <a href="https://instagram.com/seijakupilates" target="_blank"><i class="fab fa-instagram"></i></a>
                        <a href="https://wa.me/6281234567890" target="_blank"><i class="fab fa-whatsapp"></i></a>
                        <a href="https://facebook.com/seijaku.studiopbg" target="_blank"><i class="fab fa-facebook"></i></a>
                    </div>
                </div>
                <div class="contact-form-wrapper">
                    <form class="contact-form" id="contactForm">
                        <h3>Send us a message!</h3>
                        <div class="form-group">
                            <input type="text" name="name" placeholder="Your Full Name" required>
                        </div>
                        <div class="form-group">
                            <input type="email" name="email" placeholder="Email" required>
                        </div>
                        <div class="form-group">
                            <textarea name="message" placeholder="Your Message" rows="4" required></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary btn-block">Send</button>
                    </form>
                </div>
            </div>
        </div>
    </section>

    <footer class="footer">
        <div class="container">
            <div class="footer-content">
                <div class="footer-brand">
                    <img src="images/logo.png" alt="Seijaku">
                    <h3>Seijaku Studio Pilates</h3>
                    <p>Transform Your Body, Find Your Peace</p>
                </div>
                <div class="footer-links">
                    <h4>Menu</h4>
                    <a href="#home">Home</a>
                    <a href="#about">About</a>
                    <a href="#classes">Classes</a>
                    <a href="#contact">Contact</a>
                </div>
                <div class="footer-links">
                    <h4>Classes</h4>
                    <a href="#classes">Mat Pilates</a>
                    <a href="#classes">Reformer Pilates</a>
                    <a href="#classes">Clinical Pilates</a>
                    <a href="#classes">STOTT Pilates</a>
                </div>
                <div class="footer-review">
                    <h4>Share Your Experience</h4>
                    <form class="review-form" id="reviewForm">
                        <div class="star-rating" id="starRating">
                            <i class="fas fa-star" data-rating="1"></i>
                            <i class="fas fa-star" data-rating="2"></i>
                            <i class="fas fa-star" data-rating="3"></i>
                            <i class="fas fa-star" data-rating="4"></i>
                            <i class="fas fa-star" data-rating="5"></i>
                        </div>
                        <input type="hidden" name="rating" id="ratingInput" value="5">
                        <input type="text" name="guest_name" placeholder="Your Name" required>
                        <textarea name="review_text" placeholder="Share your experience in Seijaku Pilates Studio" rows="2" required></textarea>
                        <button type="submit" class="btn btn-primary btn-sm">Send</button>
                    </form>
                </div>
            </div>
            <div class="footer-bottom">
                <p>&copy; 2025 Seijaku Studio Pilates. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <div class="modal" id="loginModal">
        <div class="modal-overlay" onclick="closeModal('loginModal')"></div>
        <div class="modal-content">
            <button class="modal-close" onclick="closeModal('loginModal')">&times;</button>

            <div class="modal-header">
                <img src="images/logo.png" alt="Seijaku">
                <h2>Welcome Back!</h2>
                <p>Start sign in and explore it</p>
            </div>

            <div class="modal-body">
                <form class="auth-form" id="loginForm">
                    <div class="form-group">
                        <label>Email</label>
                        <input type="email" name="email" required placeholder="email@example.com">
                    </div>
                    <div class="form-group">
                        <label>Password</label>
                        <input type="password" name="password" required placeholder="Input your password">
                    </div>
                    <button type="submit" class="btn btn-primary btn-block">Sign In</button>
                </form>
            </div>

            <div class="modal-footer">
                <p>Don't have an account yet? <a href="#" onclick="switchModal('loginModal', 'registerModal')">Register Now!</a></p>
            </div>
        </div>
    </div>

    <div class="modal" id="registerModal">
        <div class="modal-overlay" onclick="closeModal('registerModal')"></div>
        <div class="modal-content">
            <button class="modal-close" onclick="closeModal('registerModal')">&times;</button>

            <div class="modal-header">
                <img src="images/logo.png" alt="Seijaku">
                <h2>Join Seijaku Right Now</h2>
                <p>Sign up to start your journey with us.</p>
            </div>

            <div class="modal-body">
                <form class="auth-form" id="registerForm">
                    <div class="form-group">
                        <label>Full Name</label>
                        <input type="text" name="full_name" required placeholder="Your Full Name">
                    </div>
                    <div class="form-group">
                        <label>Email</label>
                        <input type="email" name="email" required placeholder="email@example.com">
                    </div>
                    <div class="form-group">
                        <label>Phone Number</label>
                        <input type="tel" name="phone" placeholder="+62 812 3456 7890">
                    </div>
                    <div class="form-group">
                        <label>Password</label>
                        <input type="password" name="password" required placeholder="Minimal 6 characters">
                    </div>
                    <button type="submit" class="btn btn-primary btn-block">Register</button>
                </form>
            </div>
            <div class="modal-footer">
                <p>Already have an account? <a href="#" onclick="switchModal('registerModal', 'loginModal')">Sign in here</a></p>
            </div>
        </div>
    </div>

    <div class="toast-container" id="toastContainer"></div>
    <script src="https://unpkg.com/lottie-web@5.12.2/build/player/lottie.min.js"></script>
    <script src="js/main.js"></script>
</body>
</html>
