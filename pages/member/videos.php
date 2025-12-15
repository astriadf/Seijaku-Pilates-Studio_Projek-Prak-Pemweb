<?php
session_start();
require_once '../../config/database.php';
require_once '../../classes/user.php';


$db   = new Database();
$conn = $db->getConnection();
$user = new User($conn);


$userId   = $_SESSION['user_id'] ?? null;
$userData = null;


if ($userId) {
    $userData = $user->getById($userId);
}


$avatarEmojis = [
    'totoro' => '🌳', 'chihiro' => '👧', 'ponyo' => '🐟', 'kiki' => '🧹',
    'satsuki' => '👩', 'sophie' => '👱‍♀️', 'calcifer' => '🔥', 'jiji' => '🐱',
    'kodama' => '👻', 'catbus' => '🚌', 'howl' => '🏰', 'haku' => '🐉'
];
$userAvatar = $avatarEmojis[$userData['avatar'] ?? 'totoro'] ?? '🌳';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tutorial Videos - Seijaku Studio Pilates</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../../public/css/dashboard.css">
    <style>
        .video-thumbnail {
            position: relative;
            height: 180px;
            background-size: cover;
            background-position: center;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
        }
        .video-thumbnail .play-btn {
            width: 70px;
            height: 70px;
            background: var(--white);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--primary);
            font-size: 1.8rem;
            box-shadow: var(--shadow);
            transition: all 0.3s ease;
        }
        .video-card:hover .play-btn {
            transform: scale(1.1);
        }
        .video-modal-content {
            max-width: 800px;
            width: 90%;
        }
        .video-player-container {
            position: relative;
            padding-bottom: 56.25%;
            height: 0;
            overflow: hidden;
        }
        .video-player-container iframe {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            border: none;
            border-radius: var(--radius);
        }
    </style>
</head>
<body>
    <button class="mobile-toggle" onclick="toggleSidebar()">
        <i class="fas fa-bars"></i>
    </button>


    <div class="dashboard-layout">
        <aside class="sidebar" id="sidebar">
            <div class="sidebar-header">
                <img src="../../public/images/logo.png" alt="Seijaku">
                <div>
                    <h2>Seijaku</h2>
                    <span>Pilates Studio</span>
                </div>
            </div>


            <nav class="sidebar-nav">
                <div class="nav-section">
                    <div class="nav-section-title">Main Menu</div>
                    <a href="dashboard.php" class="nav-item">
                        <i class="fas fa-home"></i>
                        <span>Dashboard</span>
                    </a>
                    <a href="booking.php" class="nav-item">
                        <i class="fas fa-calendar-plus"></i>
                        <span>Class Booking</span>
                    </a>
                    <a href="my-bookings.php" class="nav-item">
                        <i class="fas fa-calendar-check"></i>
                        <span>My Bookings</span>
                    </a>
                </div>


                <div class="nav-section">
                    <div class="nav-section-title">Content</div>
                    <a href="videos.php" class="nav-item active">
                        <i class="fas fa-play-circle"></i>
                        <span>Tutorial Videos</span>
                    </a>
                    <a href="achievements.php" class="nav-item">
                        <i class="fas fa-trophy"></i>
                        <span>Achievements</span>
                    </a>
                </div>


                <div class="nav-section">
                    <div class="nav-section-title">Account</div>
                    <a href="profile.php" class="nav-item">
                        <i class="fas fa-user"></i>
                        <span>My Profile</span>
                    </a>
                    <a href="../../public/index.php" class="nav-item">
                        <i class="fas fa-arrow-left"></i>
                        <span>Back to Homepage</span>
                    </a>
                    <a href="#" class="nav-item" onclick="logout()">
                        <i class="fas fa-sign-out-alt"></i>
                        <span>Log Out</span>
                    </a>
                </div>
            </nav>


            <div class="sidebar-footer">
                <div class="user-profile" onclick="location.href='profile.php'">
                    <div class="user-avatar"><?= $userAvatar ?></div>
                    <div class="user-info">
                        <h4><?= htmlspecialchars($userData['full_name'] ?? 'Member') ?></h4>
                        <span>Member</span>
                    </div>
                </div>
            </div>
        </aside>


        <main class="main-content">
            <div class="top-bar">
                <div class="page-title">
                    <h1>Tutorial Videos</h1>
                    <p>Learn pilates movements from home</p>
                </div>
                <div class="top-actions">
                    <div class="search-box">
                        <i class="fas fa-search"></i>
                        <input type="text" placeholder="Search videos..." id="searchInput" onkeyup="searchVideos()">
                    </div>
                </div>
            </div>


            <div class="video-categories">
                <button class="category-btn active" data-category="all">All</button>
                <button class="category-btn" data-category="beginner">Beginner</button>
                <button class="category-btn" data-category="intermediate">Intermediate</button>
                <button class="category-btn" data-category="advanced">Advanced</button>
            </div>


            <div class="videos-grid" id="videosGrid">
                <div class="empty-state">
                    <i class="fas fa-spinner fa-spin"></i>
                    <p>Loading videos...</p>
                </div>
            </div>
        </main>
    </div>


    <div class="modal video-player-modal" id="videoModal">
        <div class="modal-content video-modal-content">
            <div class="modal-header">
                <h3 id="videoTitle">Video</h3>
                <button class="modal-close" onclick="closeVideoModal()">&times;</button>
            </div>
            <div class="video-player-container" id="videoPlayerContainer">
            </div>
            <div class="modal-body">
                <p id="videoDescription"></p>
                <div style="display: flex; gap: 20px; margin-top: 15px; color: var(--text-light); font-size: 0.9rem;">
                    <span><i class="fas fa-clock"></i> <span id="videoDuration"></span> minutes</span>
                    <span><i class="fas fa-eye"></i> <span id="videoViews"></span> views</span>
                    <span class="video-level" id="videoLevel"></span>
                </div>
            </div>
        </div>
    </div>


    <div class="toast-container" id="toastContainer"></div>


    <script>
        const API_BASE = '../../api';
        let allVideos = [];
        let currentCategory = 'all';


        const levelText = { beginner: 'Beginner', intermediate: 'Intermediate', advanced: 'Advanced' };


        function toggleSidebar() {
            document.getElementById('sidebar').classList.toggle('active');
        }


        function logout() {
            localStorage.removeItem('token');
            localStorage.removeItem('user');
            window.location.href = '../../public/index.php';
        }


        document.querySelectorAll('.category-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                document.querySelectorAll('.category-btn').forEach(b => b.classList.remove('active'));
                this.classList.add('active');
                currentCategory = this.dataset.category;
                filterVideos();
            });
        });


        async function loadVideos() {
            try {
                const response = await fetch(`${API_BASE}/videos/list.php`);
                const data = await response.json();


                if (data.success) {
                    allVideos = data.data;
                    filterVideos();
                }
            } catch (error) {
                console.error('Error:', error);
                document.getElementById('videosGrid').innerHTML = `
                    <div class="empty-state">
                        <i class="fas fa-exclamation-circle"></i>
                        <h3>Failed to Load</h3>
                        <p>Unable to load videos</p>
                    </div>
                `;
            }
        }


        function filterVideos() {
            const searchTerm = document.getElementById('searchInput').value.toLowerCase();
            let filtered = allVideos;


            if (currentCategory !== 'all') {
                filtered = filtered.filter(v => v.level === currentCategory);
            }


            if (searchTerm) {
                filtered = filtered.filter(v =>
                    v.title.toLowerCase().includes(searchTerm) ||
                    v.description.toLowerCase().includes(searchTerm)
                );
            }


            renderVideos(filtered);
        }


        function searchVideos() {
            filterVideos();
        }


        function extractYoutubeId(url) {
            if (!url) return null;
            const match = url.match(/(?:youtube\.com\/(?:watch\?v=|embed\/)|youtu\.be\/)([a-zA-Z0-9_-]{11})/);
            return match ? match[1] : null;
        }


        function renderVideos(videos) {
            const grid = document.getElementById('videosGrid');


            if (videos.length === 0) {
                grid.innerHTML = `
                    <div class="empty-state" style="grid-column: 1/-1;">
                        <i class="fas fa-video-slash"></i>
                        <h3>No Videos</h3>
                        <p>No videos match the current filter</p>
                    </div>
                `;
                return;
            }


            grid.innerHTML = videos.map(v => {
                const videoId = extractYoutubeId(v.youtube_url);
                const thumbnailUrl = videoId
                    ? `https://img.youtube.com/vi/${videoId}/hqdefault.jpg`
                    : '';
                const duration = v.duration_minutes || 30;
                const views = v.view_count || 0;


                return `
                    <div class="video-card"
                         onclick="openVideo(${v.video_id},
                            '${escapeHtml(v.title)}',
                            '${escapeHtml(v.description)}',
                            ${duration},
                            ${views},
                            '${v.level}',
                            '${videoId || ''}')">
                        <div class="video-thumbnail" style="${thumbnailUrl ? `background-image: url('${thumbnailUrl}');` : ''}">
                            <div class="play-btn"><i class="fas fa-play"></i></div>
                            <span class="video-duration">${duration} minutes</span>
                        </div>
                        <div class="video-info">
                            <h3>${v.title}</h3>
                            <div class="video-meta">
                                <span class="video-level">${levelText[v.level] || v.level}</span>
                                <span class="video-views"><i class="fas fa-eye"></i> ${views.toLocaleString('id-ID')}</span>
                            </div>
                        </div>
                    </div>
                `;
            }).join('');
        }


        function escapeHtml(text) {
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML.replace(/'/g, "\\'");
        }


        async function openVideo(id, title, desc, duration, views, level, videoId) {
            document.getElementById('videoTitle').textContent = title;
            document.getElementById('videoDescription').textContent = desc;
            document.getElementById('videoDuration').textContent = duration;
            document.getElementById('videoViews').textContent = views.toLocaleString('id-ID');
            document.getElementById('videoLevel').textContent = levelText[level] || level;


            const container = document.getElementById('videoPlayerContainer');
            container.innerHTML = '';


            if (videoId) {
                const iframe = document.createElement('iframe');
                iframe.src = `https://www.youtube.com/embed/${videoId}?autoplay=1`;
                iframe.allow = 'accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture';
                iframe.allowFullscreen = true;
                container.appendChild(iframe);
            } else {
                container.innerHTML = '<div class="video-player"><i class="fas fa-play-circle"></i></div>';
            }


            document.getElementById('videoModal').classList.add('active');


            try {
                await fetch(`${API_BASE}/videos/increment_views.php?id=${id}`, { method: 'POST' });
            } catch (e) {
                console.warn('Failed to update views', e);
            }
        }


        function closeVideoModal() {
            document.getElementById('videoModal').classList.remove('active');
            document.getElementById('videoPlayerContainer').innerHTML = '';
        }


        function showToast(message, type = 'success') {
            const container = document.getElementById('toastContainer');
            const toast = document.createElement('div');
            toast.className = `toast ${type}`;
            toast.innerHTML = `<i class="fas fa-${type === 'success' ? 'check-circle' : 'exclamation-circle'}"></i><span>${message}</span>`;
            container.appendChild(toast);
            setTimeout(() => toast.remove(), 3000);
        }


        document.addEventListener('DOMContentLoaded', loadVideos);
    </script>
</body>
</html>
