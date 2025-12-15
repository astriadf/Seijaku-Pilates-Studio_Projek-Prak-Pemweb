const API_BASE = '../api';

document.addEventListener('DOMContentLoaded', function() {
    initNavbar();
    loadClasses();
    loadInstructors();
    loadVideos();
    loadTestimonials();
    initForms();
    initStarRating();
    initFilters();
    initCounterAnimation();
});

function initCounterAnimation() {
    const counters = document.querySelectorAll('.stat-counter-number');
    if (counters.length === 0) return;

    const observerOptions = {
        threshold: 0.5,
        rootMargin: '0px'
    };

    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const counter = entry.target;
                const target = parseInt(counter.dataset.target);
                animateLotteryCounter(counter, target);
                observer.unobserve(counter);
            }
        });
    }, observerOptions);

    counters.forEach(counter => observer.observe(counter));
}

function animateLotteryCounter(element, target) {
    const duration = 2000;
    const steps = 60;
    const stepDuration = duration / steps;
    let currentStep = 0;

    const interval = setInterval(() => {
        currentStep++;

        if (currentStep < steps * 0.7) {
            const randomNum = Math.floor(Math.random() * target * 1.5);
            element.textContent = randomNum + (target >= 100 ? '+' : '');
        } else {
            const progress = (currentStep - steps * 0.7) / (steps * 0.3);
            const currentVal = Math.floor(target * progress);
            element.textContent = currentVal + (target >= 100 ? '+' : '');
        }

        if (currentStep >= steps) {
            clearInterval(interval);
            element.textContent = target + (target >= 100 ? '+' : '');
        }
    }, stepDuration);
}

function initNavbar() {
    const navbar   = document.getElementById('navbar');
    const navToggle = document.getElementById('nav-toggle');
    const navMenu   = document.getElementById('nav-menu');

    window.addEventListener('scroll', function() {
        if (window.scrollY > 50) {
            navbar.classList.add('scrolled');
        } else {
            navbar.classList.remove('scrolled');
        }
    });

    navToggle.addEventListener('click', function() {
        navMenu.classList.toggle('active');
    });

    document.querySelectorAll('.nav-link').forEach(link => {
        link.addEventListener('click', function() {
            navMenu.classList.remove('active');
        });
    });
}

/* ============ CLASSES ============ */

async function loadClasses() {
    try {
        const response = await fetch(`${API_BASE}/classes/list.php`);
        const data = await response.json();

        if (data.success) {
            renderClasses(data.data.class_types);
        }
    } catch (error) {
        console.error('Error loading classes:', error);
    }
}

const classImages = {
    'Mat Pilates': 'images/mat_pilates.jpg',
    'Winsor Pilates': 'images/winsor_pilates.png',
    'Classical Pilates': 'images/classical_pilates.jpg',
    'STOTT Pilates': 'images/stott_pilates.jpg',
    'Reformer Pilates': 'images/reformer-pilates.jpg',
    'Clinical Pilates': 'images/clinical_pilates.jpg',
    'Contemporary Pilates': 'images/contemporary_pilates.jpg',
    'Prenatal Pilates': 'images/prenatal_pilates.webp',
    'Therapeutic Pilates': 'images/therapeutic_pilates.jpg'
};

function renderClasses(classes, filter = 'all') {
    const grid = document.getElementById('classesGrid');
    if (!grid) return;

    const filtered = filter === 'all' ? classes : classes.filter(c => c.difficulty_level === filter);
    const levelText = { beginner: 'Beginner', intermediate: 'Intermediate', advanced: 'Advanced' };

    grid.innerHTML = filtered.map(cls => {
        const bgImage = classImages[cls.name] || 'images/hero-bg.jpeg';

        return `
            <div class="class-card" data-level="${cls.difficulty_level}">
                <div class="class-header"
                     style="
                        background: ${cls.color};
                        background-image: url('${bgImage}');
                        background-size: cover;
                        background-position: center;
                     ">
                </div>
                <div class="class-body">
                    <h3 class="class-title">${cls.name}</h3>
                    <p class="class-level-text">${levelText[cls.difficulty_level]}</p>
                    <p>${cls.description}</p>
                    <div class="class-meta">
                        <span class="class-price">Rp ${Number(cls.price).toLocaleString('id-ID')}</span>
                        <span class="class-duration"><i class="fas fa-clock"></i> ${cls.duration_minutes} minutes</span>
                    </div>
                </div>
            </div>
        `;
    }).join('');
}

function initFilters() {
    document.querySelectorAll('.filter-btn').forEach(btn => {
        btn.addEventListener('click', async function() {
            document.querySelectorAll('.filter-btn').forEach(b => b.classList.remove('active'));
            this.classList.add('active');

            const level = this.dataset.level;
            const response = await fetch(`${API_BASE}/classes/list.php`);
            const data = await response.json();

            if (data.success) {
                renderClasses(data.data.class_types, level);
            }
        });
    });
}

/* ============ INSTRUCTORS ============ */

let instructorPosition = 0;
let instructorsData    = [];

async function loadInstructors() {
    try {
        const response = await fetch(`${API_BASE}/instructors/list.php`);
        const data = await response.json();

        if (data.success) {
            instructorsData = data.data;
            renderInstructors();
        }
    } catch (error) {
        console.error('Error loading instructors:', error);
    }
}

const instructorImages = {
    'Sakura Tanaka': 'images/sakura_tanaka.png',
    'Yuki Yamamoto': 'images/yuki_yamamoto.png',
    'Hana Suzuki': 'images/hana_suzuki.png',
    'Mei Nakamura': 'images/mei_nakamura.png',
    'Aoi Watanabe': 'images/aoi_watanabe.png',
    'Rin Kobayashi': 'images/rin_kobayashi.png',
    'Kaede Ito': 'images/kaede_ito.png',
    'Momiji Sato': 'images/momiji_sato.png',
    'Tsubaki Honda': 'images/tsubaki_honda.png',
    'Ayame Kimura': 'images/ayame_kimura.png',
    'Hiroshi Tanaka': 'images/hiroshi_tanaka.png'
};

function renderInstructors() {
    const track = document.getElementById('instructorTrack');
    if (!track) return;

    track.innerHTML = instructorsData.map(inst => {
        const imgSrc = instructorImages[inst.full_name];

        return `
            <div class="instructor-card">
                <div class="instructor-avatar">
                    ${
                        imgSrc
                        ? `<img src="${imgSrc}" alt="${inst.full_name}">`
                        : `<span>🧘</span>`
                    }
                </div>
                <div class="instructor-info">
                    <h3>${inst.full_name}</h3>
                    <p class="specialization">${inst.specialization}</p>
                    ${inst.phone ? `<p class="instructor-phone"><i class="fas fa-phone"></i> ${inst.phone}</p>` : ''}
                    <div class="instructor-rating">
                        ${'<i class="fas fa-star"></i>'.repeat(Math.floor(inst.rating))}
                        <span>${inst.rating}</span>
                    </div>
                </div>
            </div>
        `;
    }).join('');
}

function slideInstructors(direction) {
    const track = document.getElementById('instructorTrack');
    const cardWidth = 305;
    const maxPosition = (instructorsData.length - 3) * cardWidth;

    instructorPosition += direction * cardWidth;
    instructorPosition = Math.max(0, Math.min(instructorPosition, maxPosition));

    track.style.transform = `translateX(-${instructorPosition}px)`;
}

/* ============ VIDEOS ============ */

async function loadVideos() {
    try {
        const response = await fetch(`${API_BASE}/videos/list.php?featured=1`);
        const data = await response.json();

        if (data.success) {
            renderVideos(data.data.slice(0, 6));
        }
    } catch (error) {
        console.error('Error loading videos:', error);
    }
}

function renderVideos(videos) {
    const grid = document.getElementById('videosGrid');
    if (!grid) return;
    const levelText = { beginner: 'Beginner', intermediate: 'Intermediate', advanced: 'Advanced', all: 'All Levels' };

    grid.innerHTML = videos.map(video => {
        const videoId = extractYoutubeId(video.youtube_url);
        const thumbnailUrl = videoId
            ? `https://img.youtube.com/vi/${videoId}/hqdefault.jpg`
            : 'images/video-placeholder.jpg';
        const duration = video.duration_minutes || video.duration || 30;
        const views    = video.view_count || video.views || 0;

        return `
            <div class="video-card" onclick="openVideoModal('${videoId}', '${video.title.replace(/'/g, "\\'")}')">
                <div class="video-thumbnail" style="background-image: url('${thumbnailUrl}');">
                    <div class="play-btn"><i class="fas fa-play"></i></div>
                    <span class="video-duration">${duration} min</span>
                </div>
                <div class="video-info">
                    <h3>${video.title}</h3>
                    <div class="video-meta">
                        <span class="video-level">${levelText[video.level] || video.level}</span>
                        <span><i class="fas fa-eye"></i> ${views.toLocaleString()}</span>
                    </div>
                </div>
            </div>
        `;
    }).join('');
}

function openVideoModal(videoId, title) {
    if (!videoId) {
        showToast('Video not available', 'error');
        return;
    }

    let modal = document.getElementById('videoModal');
    if (!modal) {
        modal = document.createElement('div');
        modal.id = 'videoModal';
        modal.className = 'modal';
        modal.innerHTML = `
            <div class="modal-content video-modal-content">
                <button class="modal-close" onclick="closeVideoModal()">&times;</button>
                <h2 id="videoModalTitle"></h2>
                <div class="video-embed-container">
                    <iframe id="videoIframe" src="" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
                </div>
            </div>
        `;
        document.body.appendChild(modal);

        modal.addEventListener('click', function(e) {
            if (e.target === modal) {
                closeVideoModal();
            }
        });
    }

    document.getElementById('videoModalTitle').textContent = title;
    document.getElementById('videoIframe').src = `https://www.youtube.com/embed/${videoId}?autoplay=1`;
    modal.classList.add('active');
    document.body.style.overflow = 'hidden';
}

function closeVideoModal() {
    const modal = document.getElementById('videoModal');
    if (modal) {
        modal.classList.remove('active');
        document.getElementById('videoIframe').src = '';
        document.body.style.overflow = '';
    }
}

function extractYoutubeId(url) {
    if (!url) return null;
    const match = url.match(/(?:youtube\.com\/(?:watch\?v=|embed\/)|youtu\.be\/)([a-zA-Z0-9_-]{11})/);
    return match ? match[1] : null;
}

/* ============ TESTIMONIALS (AVATAR IMAGES 1–6) ============ */

let testimonialPosition = 0;
let testimonialsData   = [];

const testimonialAvatarImages = {
    1: 'avatar1.jpg',
    2: 'avatar2.png',
    3: 'avatar3.png',
    4: 'avatar4.png',
    5: 'avatar5.png',
    6: 'avatar6.png'
};

const defaultTestimonials = [
    {
        full_name: 'Hiroshi Tanaka',
        avatar: 1,
        review_text: 'The studio feels very comfortable with friendly and professional instructors. Highly recommended!',
        rating: 5
    },
    {
        full_name: 'Astria',
        avatar: 2,
        review_text: 'The reformer Pilates here is amazing! Great quality equipment and expert instructors.',
        rating: 5
    },
    {
        full_name: 'Nadia Putri',
        avatar: 3,
        review_text: 'The atmosphere is very calming, perfect for releasing stress after work.',
        rating: 4
    }
];

async function loadTestimonials() {
    const track = document.getElementById('testimonialsTrack');
    const dots  = document.getElementById('testimonialsDots');
    if (!track || !dots) return;

    try {
        const response = await fetch(`${API_BASE}/reviews/list.php`);
        const data = await response.json();

        if (data.success && Array.isArray(data.data) && data.data.length > 0) {
            testimonialsData = data.data.map((r, idx) => ({
                full_name: r.full_name || `${(r.first_name || '')} ${(r.last_name || '')}`.trim() || 'Anonymous',
                avatar: (idx % 6) + 1,
                review_text: r.review_text,
                rating: Number(r.rating) || 5
            }));
        } else {
            testimonialsData = defaultTestimonials;
        }
    } catch (error) {
        console.error('Error loading testimonials:', error);
        testimonialsData = defaultTestimonials;
    }

    renderTestimonials();
    initTestimonialSlider();
}

function renderTestimonials() {
    const track = document.getElementById('testimonialsTrack');
    const dots  = document.getElementById('testimonialsDots');
    if (!track || !dots) return;

    track.innerHTML = testimonialsData.map((review, index) => {
        const file   = testimonialAvatarImages[review.avatar] || testimonialAvatarImages[1];
        const rating = Math.max(1, Math.min(5, Number(review.rating) || 5));

        return `
            <div class="testimonial-card" data-index="${index}">
                <div class="testimonial-avatar">
                    <img src="images/avatars/${file}" alt="${review.full_name}"
                         style="width:100%;height:100%;object-fit:cover;border-radius:50%;">
                </div>
                <blockquote>"${review.review_text}"</blockquote>
                <div class="author">${review.full_name}</div>
                <div class="testimonial-rating">
                    ${'<i class="fas fa-star"></i>'.repeat(rating)}
                </div>
            </div>
        `;
    }).join('');

    dots.innerHTML = testimonialsData.map((_, i) =>
        `<div class="dot ${i === 0 ? 'active' : ''}" onclick="goToTestimonial(${i})"></div>`
    ).join('');

    testimonialPosition = 0;
    updateTestimonialSlider();
}

function initTestimonialSlider() {
    if (!testimonialsData.length) return;
    testimonialPosition = 0;
    updateTestimonialSlider();
}

function updateTestimonialSlider() {
    const track = document.getElementById('testimonialsTrack');
    const dots  = document.getElementById('testimonialsDots');
    if (!track || !dots) return;

    const cards = track.querySelectorAll('.testimonial-card');
    if (!cards.length) return;

    cards.forEach((card, i) => {
        card.style.display = i === testimonialPosition ? 'flex' : 'none';
    });

    dots.querySelectorAll('.dot').forEach((dot, i) => {
        dot.classList.toggle('active', i === testimonialPosition);
    });
}

function goToTestimonial(index) {
    testimonialPosition = index;
    updateTestimonialSlider();
}

function prevTestimonial() {
    if (!testimonialsData.length) return;
    testimonialPosition = (testimonialPosition - 1 + testimonialsData.length) % testimonialsData.length;
    updateTestimonialSlider();
}

function nextTestimonial() {
    if (!testimonialsData.length) return;
    testimonialPosition = (testimonialPosition + 1) % testimonialsData.length;
    updateTestimonialSlider();
}

/* ============ FORMS / MODALS / TOAST ============ */

function initForms() {
    const loginForm   = document.getElementById('loginForm');
    const registerForm= document.getElementById('registerForm');
    const contactForm = document.getElementById('contactForm');
    const reviewForm  = document.getElementById('reviewForm');

    if (loginForm) {
        loginForm.addEventListener('submit', async function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            const data     = Object.fromEntries(formData);

            try {
                const response = await fetch(`${API_BASE}/auth/login.php`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(data)
                });
                const result = await response.json();

                if (result.success) {
                    localStorage.setItem('token', result.token);
                    localStorage.setItem('user', JSON.stringify(result.user));
                    showToast('Login successful! Redirecting...', 'success');
                    setTimeout(() => {
                        const role = result.user.role;
                        if (role === 'admin') {
                            window.location.href = '../pages/admin/dashboard.php';
                        } else if (role === 'instructor') {
                            window.location.href = '../pages/instructor/dashboard.php';
                        } else {
                            window.location.href = '../pages/member/dashboard.php';
                        }
                    }, 1000);
                } else {
                    showToast(result.message, 'error');
                }
            } catch (error) {
                showToast('An error occurred. Please try again.', 'error');
            }
        });
    }

    if (registerForm) {
        registerForm.addEventListener('submit', async function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            const data     = Object.fromEntries(formData);

            try {
                const response = await fetch(`${API_BASE}/auth/register.php`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(data)
                });
                const result = await response.json();

                if (result.success) {
                    localStorage.setItem('token', result.token);
                    localStorage.setItem('user', JSON.stringify(result.user));
                    showToast('Registration successful! Redirecting...', 'success');
                    setTimeout(() => {
                        window.location.href = '../pages/member/dashboard.php';
                    }, 1000);
                } else {
                    showToast(result.message, 'error');
                }
            } catch (error) {
                showToast('An error occurred. Please try again.', 'error');
            }
        });
    }

    if (contactForm) {
        contactForm.addEventListener('submit', async function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            const data     = Object.fromEntries(formData);

            try {
                const response = await fetch(`${API_BASE}/messages/create.php`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(data)
                });
                const result = await response.json();

                if (result.success) {
                    showToast('Message sent successfully!', 'success');
                    this.reset();
                } else {
                    showToast(result.message, 'error');
                }
            } catch (error) {
                showToast('An error occurred. Please try again.', 'error');
            }
        });
    }

    if (reviewForm) {
        reviewForm.addEventListener('submit', async function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            const data     = Object.fromEntries(formData);

            try {
                const response = await fetch(`${API_BASE}/reviews/create.php`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(data)
                });
                const result = await response.json();

                if (result.success) {
                    showToast('Thank you for your review!', 'success');
                    this.reset();
                    initStarRating();
                    loadTestimonials();
                } else {
                    showToast(result.message, 'error');
                }
            } catch (error) {
                showToast('An error occurred. Please try again.', 'error');
            }
        });
    }
}

function initStarRating() {
    const starRating  = document.getElementById('starRating');
    const ratingInput = document.getElementById('ratingInput');

    if (!starRating) return;

    const stars = starRating.querySelectorAll('i');

    stars.forEach((star, index) => {
        star.classList.remove('active');
        if (index < 5) star.classList.add('active');

        star.addEventListener('click', function() {
            const rating = this.dataset.rating;
            ratingInput.value = rating;

            stars.forEach((s, i) => {
                s.classList.toggle('active', i < rating);
            });
        });

        star.addEventListener('mouseenter', function() {
            const rating = this.dataset.rating;
            stars.forEach((s, i) => {
                s.style.color = i < rating ? '#ffc107' : '#ddd';
            });
        });

        star.addEventListener('mouseleave', function() {
            const currentRating = ratingInput.value;
            stars.forEach((s, i) => {
                s.style.color = i < currentRating ? '#ffc107' : '#ddd';
            });
        });
    });
}

function openModal(modalId) {
    document.getElementById(modalId).classList.add('active');
    document.body.style.overflow = 'hidden';
}

function closeModal(modalId) {
    document.getElementById(modalId).classList.remove('active');
    document.body.style.overflow = '';
}

function switchModal(fromId, toId) {
    closeModal(fromId);
    setTimeout(() => openModal(toId), 200);
}

function showToast(message, type = 'info') {
    const container = document.getElementById('toastContainer');
    const toast = document.createElement('div');
    toast.className = `toast ${type}`;

    const icons = { success: 'check-circle', error: 'exclamation-circle', info: 'info-circle' };
    toast.innerHTML = `<i class="fas fa-${icons[type]}"></i><span>${message}</span>`;

    container.appendChild(toast);

    setTimeout(() => {
        toast.style.animation = 'toastSlideIn 0.3s ease reverse';
        setTimeout(() => toast.remove(), 300);
    }, 3000);
}

/* expose global functions */
window.slideInstructors   = slideInstructors;
window.goToTestimonial    = goToTestimonial;
window.prevTestimonial    = prevTestimonial;
window.nextTestimonial    = nextTestimonial;
window.openModal          = openModal;
window.closeModal         = closeModal;
window.switchModal        = switchModal;
