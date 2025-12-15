<?php
session_start();
require_once '../../config/database.php';
require_once '../../classes/user.php';
require_once '../../classes/booking.php';
require_once '../../classes/classType.php';
require_once '../../classes/video.php';

$db = new Database();
$conn = $db->getConnection();
$userModel = new User($conn);
$booking = new Booking($conn);
$classType = new ClassType($conn);
$video = new Video($conn);

$userId = $_SESSION['user_id'] ?? null;
$userData = null;

if ($userId) {
    $userData = $userModel->getById($userId);
}

$totalMembers = $userModel->getTotalCount('member');
$totalBookings = $booking->getTotalCount();
$pendingBookings = $booking->getPendingCount();
$totalClasses = count($classType->getAll());
$pendingList = $booking->getAllPending();

$avatarEmojis = [
    'totoro' => '🌳', 'chihiro' => '👧', 'ponyo' => '🐟', 'kiki' => '🧹',
    'satsuki' => '👩', 'sophie' => '👱‍♀️', 'calcifer' => '🔥', 'jiji' => '🐱'
];
$userAvatar = $avatarEmojis[$userData['avatar'] ?? 'totoro'] ?? '🌳';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Seijaku Studio Pilates</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../../public/css/dashboard.css">
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
                    <span>Admin Panel</span>
                </div>
            </div>

            <nav class="sidebar-nav">
                <div class="nav-section">
                    <div class="nav-section-title">Main Menu</div>
                    <a href="dashboard.php" class="nav-item active">
                        <i class="fas fa-home"></i>
                        <span>Dashboard</span>
                    </a>
                    <a href="bookings.php" class="nav-item">
                        <i class="fas fa-calendar-check"></i>
                        <span>Manage Bookings</span>
                    </a>
                </div>

                <div class="nav-section">
                    <div class="nav-section-title">Master Data</div>
                    <a href="classes.php" class="nav-item">
                        <i class="fas fa-dumbbell"></i>
                        <span>Class Types</span>
                    </a>
                    <a href="videos.php" class="nav-item">
                        <i class="fas fa-video"></i>
                        <span>Video Tutorials</span>
                    </a>
                    <a href="instructors.php" class="nav-item">
                        <i class="fas fa-chalkboard-teacher"></i>
                        <span>Instructors</span>
                    </a>
                    <a href="messages.php" class="nav-item">
                        <i class="fas fa-envelope"></i>
                        <span>Inbox</span>
                    </a>
                </div>

                <div class="nav-section">
                    <div class="nav-section-title">Account</div>
                    <a href="../../public/index.php" class="nav-item">
                        <i class="fas fa-arrow-left"></i>
                        <span>Back to Home</span>
                    </a>
                    <a href="#" class="nav-item" onclick="logout()">
                        <i class="fas fa-sign-out-alt"></i>
                        <span>Logout</span>
                    </a>
                </div>
            </nav>

            <div class="sidebar-footer">
                <div class="user-profile">
                    <div class="user-avatar"><?= $userAvatar ?></div>
                    <div class="user-info">
                        <h4><?= htmlspecialchars($userData['full_name'] ?? 'Admin') ?></h4>
                        <span>Administrator</span>
                    </div>
                </div>
            </div>
        </aside>

        <main class="main-content">
            <div class="top-bar">
                <div class="page-title">
                    <h1>Admin Dashboard</h1>
                    <p>Welcome to the administrastion panel</p>
                </div>
            </div>

            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-icon green">
                        <i class="fas fa-users"></i>
                    </div>
                    <div class="stat-info">
                        <h3><?= $totalMembers ?></h3>
                        <p>Total Members</p>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon blue">
                        <i class="fas fa-calendar-check"></i>
                    </div>
                    <div class="stat-info">
                        <h3><?= $totalBookings ?></h3>
                        <p>Total Bookings</p>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon pink">
                        <i class="fas fa-clock"></i>
                    </div>
                    <div class="stat-info">
                        <h3><?= $pendingBookings ?></h3>
                        <p>Pending Confirmation</p>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon orange">
                        <i class="fas fa-dumbbell"></i>
                    </div>
                    <div class="stat-info">
                        <h3><?= $totalClasses ?></h3>
                        <p>Class Types</p>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h3>Booking Pending Confirmation</h3>
                    <a href="bookings.php">Lihat Semua</a>
                </div>
                <div class="card-body">
                    <?php if (empty($pendingList)): ?>
                    <div class="empty-state">
                        <i class="fas fa-check-circle"></i>
                        <h3>No Pending</h3>
                        <p>All bookings have been processed</p>
                    </div>
                    <?php else: ?>
                    <?php foreach (array_slice($pendingList, 0, 5) as $b): 
                        // Mengambil tanggal kelas dari class_instances
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
                            <p><i class="fas fa-user"></i> <?= htmlspecialchars($b['member_name']) ?></p>
                            <p><i class="fas fa-clock"></i> <?= substr($b['start_time'], 0, 5) ?> - <?= substr($b['end_time'], 0, 5) ?></p>
                        </div>
                        <div style="margin-left: auto; display: flex; gap: 10px;">
                            <button class="btn btn-primary btn-sm" onclick="approveBooking(<?= $b['booking_id'] ?>)">
                                <i class="fas fa-check"></i> Approve
                            </button>
                            <button class="btn btn-secondary btn-sm" onclick="rejectBooking(<?= $b['booking_id'] ?>)">
                                <i class="fas fa-times"></i> Reject
                            </button>
                        </div>
                    </div>
                    <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </main>
    </div>

    <div class="toast-container" id="toastContainer"></div>

    <script>
        const API_BASE = '../../api';

        function toggleSidebar() {
            document.getElementById('sidebar').classList.toggle('active');
        }

        function logout() {
            localStorage.removeItem('token');
            localStorage.removeItem('user');
            window.location.href = '../../public/index.php';
        }

        async function approveBooking(id) {
            const token = localStorage.getItem('token');
            try {
                const response = await fetch(`${API_BASE}/bookings/admin.php`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Authorization': `Bearer ${token}`
                    },
                    body: JSON.stringify({ booking_id: id, action: 'approve' })
                });
                const data = await response.json();
                if (data.success) {
                    showToast('Booking approved successfully!', 'success');
                    setTimeout(() => location.reload(), 1000);
                } else {
                    showToast(data.message || 'Failed to approve booking', 'error');
                }
            } catch (error) {
                showToast('An error occurred', 'error');
            }
        }

        async function rejectBooking(id) {
            if (!confirm('Are you sure you want to reject this booking?')) return;
            const token = localStorage.getItem('token');
            try {
                const response = await fetch(`${API_BASE}/bookings/admin.php`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Authorization': `Bearer ${token}`
                    },
                    body: JSON.stringify({ booking_id: id, action: 'reject' })
                });
                const data = await response.json();
                if (data.success) {
                    showToast('Booking berhasil diReject', 'success');
                    setTimeout(() => location.reload(), 1000);
                } else {
                    showToast(data.message || 'Failed to reject booking', 'error');
                }
            } catch (error) {
                showToast('An error occurred', 'error');
            }
        }

        function showToast(message, type = 'success') {
            const container = document.getElementById('toastContainer');
            const toast = document.createElement('div');
            toast.className = `toast ${type}`;
            toast.innerHTML = `<i class="fas fa-${type === 'success' ? 'check-circle' : 'exclamation-circle'}"></i><span>${message}</span>`;
            container.appendChild(toast);
            setTimeout(() => toast.remove(), 3000);
        }
    </script>
</body>
</html>
