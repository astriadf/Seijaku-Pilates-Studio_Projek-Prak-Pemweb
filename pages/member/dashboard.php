<?php
session_start();
require_once '../../config/database.php';
require_once '../../classes/user.php';
require_once '../../classes/booking.php';
require_once '../../classes/achievement.php';


$db = new Database();
$conn = $db->getConnection();
$user = new User($conn);
$booking = new Booking($conn);
$achievement = new Achievement($conn);


$userId = $_SESSION['user_id'] ?? null;
$userData = null;
$stats = [];
$upcomingBookings = [];
$achievements = [];


if ($userId) {
    $userData = $user->getById($userId);
    $stats = $user->getStats($userId);
    $upcomingBookings = $booking->getUserBookings($userId, 'approved');
    $achievements = $achievement->getUserAchievements($userId);
}


// GANTI: mapping emoji -> gambar avatar 1–6 (sama seperti profile.php)
$avatarImages = [
    '1' => 'avatar1.jpg',
    '2' => 'avatar2.png',
    '3' => 'avatar3.png',
    '4' => 'avatar4.png',
    '5' => 'avatar5.png',
    '6' => 'avatar6.png',
];


$currentAvatarKey  = $userData['avatar'] ?? '1';
$currentAvatarFile = $avatarImages[$currentAvatarKey] ?? $avatarImages['1'];
$avatarUrl         = '../../public/images/avatars/' . $currentAvatarFile;
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Seijaku Studio Pilates</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../../public/css/dashboard.css">
    <style>
        .user-avatar img{
            width:100%;
            height:100%;
            object-fit:cover;
            border-radius:50%;
        }
    </style>
</head>
<body>
    <button class="mobile-toggle" onclick="toggleSidebar">
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
                    <a href="dashboard.php" class="nav-item active">
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
                    <a href="videos.php" class="nav-item">
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
                    <div class="user-avatar">
                        <img src="<?= htmlspecialchars($avatarUrl) ?>" alt="Avatar">
                    </div>
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
                    <h1>Welcome, <?= htmlspecialchars($userData['full_name'] ?? 'Member') ?>!</h1>
                    <p>Hope you have a wonderful day full of positive energy</p>
                </div>
                <div class="top-actions">
                    <div class="notification-btn" onclick="toggleNotifications()">
                        <i class="fas fa-bell"></i>
                        <span class="notification-badge" id="notificationCount">0</span>
                    </div>
                    <div class="notification-dropdown" id="notificationDropdown">
                        <div class="notification-header">
                            <h4>Notifications</h4>
                            <a href="#" onclick="markAllAsRead()">Mark all as read</a>
                        </div>
                        <div class="notification-list" id="notificationList">
                            <div class="notification-empty">
                                <i class="fas fa-bell-slash"></i>
                                <p>No notifications</p>
                            </div>                    
                        </div>
                    </div>
                </div>
            </div>


            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-icon green">
                        <i class="fas fa-calendar-check"></i>
                    </div>
                    <div class="stat-info">
                        <h3><?= $stats['total_classes'] ?? 0 ?></h3>
                        <p>Total Classes Attended</p>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon blue">
                        <i class="fas fa-clock"></i>
                    </div>
                    <div class="stat-info">
                        <h3><?= $stats['upcoming_bookings'] ?? 0 ?></h3>
                        <p>Upcoming Bookings</p>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon pink">
                        <i class="fas fa-trophy"></i>
                    </div>
                    <div class="stat-info">
                        <h3><?= count(array_filter($achievements, fn($a) => isset($a['earned']) && $a['earned'])) ?></h3>
                        <p>Badges Earned</p>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon orange">
                        <i class="fas fa-fire"></i>
                    </div>
                    <div class="stat-info">
                        <h3><?= $stats['current_streak'] ?? 0 ?></h3>
                        <p>Consecutive Weeks</p>
                    </div>
                </div>
            </div>


            <div class="content-grid">
                <div class="card">
                    <div class="card-header">
                        <h3>Upcoming Bookings</h3>
                        <a href="my-bookings.php">View All</a>
                    </div>
                    <div class="card-body">
                        <?php if (empty($upcomingBookings)): ?>
                            <div class="empty-state">
                                <i class="fas fa-calendar"></i>
                                <h3>No Bookings Yet</h3>
                                <p>You do not have any class bookings yet. Let’s book a class now!</p>
                                <a href="booking.php" class="btn btn-primary">Book a Class</a>
                            </div>
                        <?php else: ?>
                            <?php foreach ($upcomingBookings as $b): 
                                $date = new DateTime($b['class_date']);
                                $months = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
                            ?>
                                <div class="booking-item">
                                    <div class="booking-date">
                                        <div class="day"><?= $date->format('d') ?></div>
                                        <div class="month"><?= $months[(int)$date->format('n')-1] ?></div>
                                    </div>
                                    <div class="booking-details">
                                        <h4><?= htmlspecialchars($b['class_name']) ?></h4>
                                        <p><i class="fas fa-clock"></i> <?= substr($b['start_time'], 0, 5) ?> - <?= substr($b['end_time'], 0, 5) ?></p>
                                        <p><i class="fas fa-user"></i> <?= htmlspecialchars($b['instructor_name']) ?></p>
                                    </div>
                                    <span class="booking-status confirmed">Confirmed</span>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>


                <div class="card">
                    <div class="card-header">
                        <h3>Latest Achievements</h3>
                        <a href="achievements.php">View All</a>
                    </div>
                    <div class="card-body">
                        <?php 
                        $badgeEmojis = [
                            'Totoro' => '🌳', 'Chihiro' => '👧', 'Calcifer' => '🔥',
                            'Ponyo' => '🐟', 'Howl' => '🏰', 'Kodama' => '👻'
                        ];
                        foreach (array_slice($achievements, 0, 4) as $ach): 
                            $emoji = $badgeEmojis[$ach['name'] ?? ''] ?? '🏆';
                            $classesRequired = $ach['required_classes'] ?? $ach['classes_required'] ?? 1;
                            $progress = min(100, ($stats['total_classes'] ?? 0) / $classesRequired * 100);
                            $isEarned = isset($ach['earned']) && $ach['earned'];  
                        ?>
                        <div class="achievement-mini">
                            <div class="achievement-badge <?= $isEarned ? '' : 'locked' ?>">
                                <?= $emoji ?>
                            </div>
                            <div class="achievement-info">
                                <h4><?= htmlspecialchars($ach['name']) ?></h4>
                                <p><?= $classesRequired ?> classes</p>
                                <?php if (!$isEarned): ?>
                                    <div class="progress-bar">
                                        <div class="progress-fill" style="width: <?= $progress ?>%"></div>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </main>
    </div>


    <div class="toast-container" id="toastContainer"></div>


    <script>
        function toggleSidebar() {
            document.getElementById('sidebar').classList.toggle('active');
        }


        function logout() {
            localStorage.removeItem('token');
            localStorage.removeItem('user');
            window.location.href = '../../public/index.php';
        }


        function showToast(message, type = 'success') {
            const container = document.getElementById('toastContainer');
            const toast = document.createElement('div');
            toast.className = `toast ${type}`;
            toast.innerHTML = `<i class="fas fa-${type === 'success' ? 'check-circle' : 'exclamation-circle'}"></i><span>${message}</span>`;
            container.appendChild(toast);
            setTimeout(() => toast.remove(), 3000);
        }


        document.addEventListener('click', function(e) {
            const sidebar = document.getElementById('sidebar');
            const toggle = document.querySelector('.mobile-toggle');
            if (window.innerWidth <= 768 && !sidebar.contains(e.target) && !toggle.contains(e.target)) {
                sidebar.classList.remove('active');
            }
            const notifBtn = document.querySelector('.notification-btn');
            const notifDropdown = document.getElementById('notificationDropdown');
            if (notifDropdown && !notifBtn.contains(e.target) && !notifDropdown.contains(e.target)) {
                notifDropdown.classList.remove('active');
            }
        });


        function toggleNotifications() {
            const dropdown = document.getElementById('notificationDropdown');
            dropdown.classList.toggle('active');
            if (dropdown.classList.contains('active')) {
                loadNotifications();
            }
        }


        async function loadNotifications() {
            try {
                const token = localStorage.getItem('token');
                const response = await fetch('../../api/notifications/list.php', {
                    headers: { 'Authorization': 'Bearer ' + token }
                });
                const data = await response.json();
                if (data.success) {
                    renderNotifications(data.data);
                }
            } catch (error) {
                console.error('Error loading notifications:', error);
            }
        }


        function renderNotifications(notifications) {
            const list = document.getElementById('notificationList');
            const countBadge = document.getElementById('notificationCount');
            const unread = notifications.filter(n => !n.is_read);
            countBadge.textContent = unread.length;
            countBadge.style.display = unread.length > 0 ? 'flex' : 'none';
            
            if (notifications.length === 0) {
                list.innerHTML = '<div class="notification-empty"><i class="fas fa-bell-slash"></i><p>No notifications</p></div>';
                return;
            }
            
            list.innerHTML = notifications.slice(0, 5).map(n => `
                <div class="notification-item ${n.is_read ? '' : 'unread'}" onclick="markAsRead(${n.notification_id})">
                    <div class="notification-icon"><i class="fas fa-${n.type === 'booking' ? 'calendar' : n.type === 'achievement' ? 'trophy' : 'bell'}"></i></div>
                    <div class="notification-content">
                        <h5>${n.title}</h5>
                        <p>${n.message}</p>
                    </div>
                </div>
            `).join('');
        }


        async function markAsRead(id) {
            try {
                const token = localStorage.getItem('token');
                await fetch('../../api/notifications/read.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'Authorization': 'Bearer ' + token },
                    body: JSON.stringify({ notification_id: id })
                });
                loadNotifications();
            } catch (error) {
                console.error('Error:', error);
            }
        }


        async function markAllAsRead() {
            try {
                const token = localStorage.getItem('token');
            await fetch('../../api/notifications/read.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'Authorization': 'Bearer ' + token },
                    body: JSON.stringify({ mark_all: true })
                });
                loadNotifications();
            } catch (error) {
                console.error('Error:', error);
            }
        }


        document.addEventListener('DOMContentLoaded', loadNotifications);
    </script>
</body>
</html>
