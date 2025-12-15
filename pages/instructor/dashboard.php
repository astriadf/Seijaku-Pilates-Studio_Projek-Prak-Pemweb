<?php
session_start();
require_once '../../config/database.php';
require_once '../../classes/user.php';
require_once '../../classes/instructor.php';

$db = new Database();
$conn = $db->getConnection();
$userModel = new User($conn);
$instructorModel = new Instructor($conn);

$userId = $_SESSION['user_id'] ?? null;
$userData = null;
$instructorData = null;
$upcomingClasses = [];

if ($userId) {
    $userData = $userModel->getById($userId);
    $instructorData = $instructorModel->getByUserId($userId);
    if ($instructorData) {
        $upcomingClasses = $instructorModel->getClasses($instructorData['instructor_id']);
    }
}

$avatarEmojis = [
    'totoro' => '🌳', 'chihiro' => '👧', 'ponyo' => '🐟', 'kiki' => '🧹',
    'satsuki' => '👩', 'sophie' => '👱‍♀️', 'nausicaa' => '🌿', 'san' => '🐺'
];
$userAvatar = $avatarEmojis[$userData['avatar'] ?? 'totoro'] ?? '🌳';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Instructor Dashboard - Seijaku Studio Pilates</title>
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
                    <span>Instructor Panel</span>
                </div>
            </div>

            <nav class="sidebar-nav">
                <div class="nav-section">
                    <div class="nav-section-title">Menu Utama</div>
                    <a href="dashboard.php" class="nav-item active">
                        <i class="fas fa-home"></i>
                        <span>Dashboard</span>
                    </a>
                    <a href="classes.php" class="nav-item">
                        <i class="fas fa-calendar"></i>
                        <span>Jadwal Kelas</span>
                    </a>
                    <a href="students.php" class="nav-item">
                        <i class="fas fa-users"></i>
                        <span>Peserta</span>
                    </a>
                </div>

                <div class="nav-section">
                    <div class="nav-section-title">Akun</div>
                    <a href="../../public/index.php" class="nav-item">
                        <i class="fas fa-arrow-left"></i>
                        <span>Kembali ke Beranda</span>
                    </a>
                    <a href="#" class="nav-item" onclick="logout()">
                        <i class="fas fa-sign-out-alt"></i>
                        <span>Keluar</span>
                    </a>
                </div>
            </nav>

            <div class="sidebar-footer">
                <div class="user-profile">
                    <div class="user-avatar"><?= $userAvatar ?></div>
                    <div class="user-info">
                        <h4><?= htmlspecialchars($userData['full_name'] ?? 'Instruktur') ?></h4>
                        <span>Instruktur</span>
                    </div>
                </div>
            </div>
        </aside>

        <main class="main-content">
            <div class="top-bar">
                <div class="page-title">
                    <h1>Selamat Datang, <?= htmlspecialchars($userData['full_name'] ?? 'Instruktur') ?>!</h1>
                    <p>Panel instruktur Seijaku Studio Pilates</p>
                </div>
            </div>

            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-icon green">
                        <i class="fas fa-star"></i>
                    </div>
                    <div class="stat-info">
                        <h3><?= $instructorData['rating'] ?? '0.0' ?></h3>
                        <p>Rating</p>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon blue">
                        <i class="fas fa-calendar-check"></i>
                    </div>
                    <div class="stat-info">
                        <h3><?= count($upcomingClasses) ?></h3>
                        <p>Kelas Minggu Ini</p>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon pink">
                        <i class="fas fa-dumbbell"></i>
                    </div>
                    <div class="stat-info">
                        <h3><?= htmlspecialchars($instructorData['specialization'] ?? '-') ?></h3>
                        <p>Spesialisasi</p>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h3>Jadwal Kelas Akan Datang</h3>
                    <a href="classes.php">Lihat Semua</a>
                </div>
                <div class="card-body">
                    <?php if (empty($upcomingClasses)): ?>
                    <div class="empty-state">
                        <i class="fas fa-calendar"></i>
                        <h3>Tidak Ada Kelas</h3>
                        <p>Belum ada jadwal kelas untuk minggu ini</p>
                    </div>
                    <?php else: ?>
                    <?php 
                    $months = ['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nov','Des'];
                    // Filter hanya kelas dengan tanggal valid / Filter only classes with valid dates
                    $filteredClasses = array_filter($upcomingClasses, fn($c) => !empty($c['schedule_date']));
                    foreach (array_slice($filteredClasses, 0, 5) as $c):                        
                    
                    $date = new DateTime($c['schedule_date']);
                    $currentBookings = $c['current_bookings'] ?? 0;
                    $maxCapacity = $c['max_capacity'] ?? $c['capacity'] ?? 10;
                    ?>
                    <div class="booking-item">
                        <div class="booking-date">
                            <div class="day"><?= $date->format('d') ?></div>
                            <div class="month"><?= $months[(int)$date->format('n')-1] ?></div>
                        </div>
                        <div class="booking-details">
                            <h4><?= htmlspecialchars($c['class_name']) ?></h4>
                            <p><i class="fas fa-clock"></i> <?= substr($c['start_time'], 0, 5) ?> - <?= substr($c['end_time'], 0, 5) ?></p>
                            <p><i class="fas fa-users"></i> <?= $currentBookings ?>/<?= $maxCapacity ?> peserta</p>
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
        function toggleSidebar() {
            document.getElementById('sidebar').classList.toggle('active');
        }

        function logout() {
            localStorage.removeItem('token');
            localStorage.removeItem('user');
            window.location.href = '../../public/index.php';
        }
    </script>
</body>
</html>
