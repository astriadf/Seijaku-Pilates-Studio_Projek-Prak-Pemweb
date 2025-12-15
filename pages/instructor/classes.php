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
$allClasses = [];

if ($userId) {
    $userData = $userModel->getById($userId);
    $instructorData = $instructorModel->getByUserId($userId);
    if ($instructorData) {
        $allClasses = $instructorModel->getClasses($instructorData['instructor_id']);
    }
}

$avatarEmojis = [
    'totoro' => '🌳', 'chihiro' => '👧', 'ponyo' => '🐟', 'kiki' => '🧹'
];
$userAvatar = $avatarEmojis[$userData['avatar'] ?? 'totoro'] ?? '🌳';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Jadwal Kelas - Seijaku Studio Pilates</title>
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
                    <a href="dashboard.php" class="nav-item">
                        <i class="fas fa-home"></i>
                        <span>Dashboard</span>
                    </a>
                    <a href="classes.php" class="nav-item active">
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
                    <h1>Jadwal Kelas</h1>
                    <p>Kelas yang akan Anda ajar</p>
                </div>
            </div>

            <div class="bookings-list">
                <?php if (empty($allClasses)): ?>
                <div class="card">
                    <div class="card-body">
                        <div class="empty-state">
                            <i class="fas fa-calendar-times"></i>
                            <h3>Tidak Ada Jadwal</h3>
                            <p>Belum ada jadwal kelas untuk Anda</p>
                        </div>
                    </div>
                </div>
                <?php else: ?>
                <?php 
                $months = ['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nov','Des'];
                // Filter hanya kelas dengan tanggal valid / Filter only classes with valid dates
                $filteredClasses = array_filter($allClasses, fn($c) => !empty($c['schedule_date']));
                foreach ($filteredClasses as $c):
                $date = new DateTime($c['schedule_date']);
                $currentBookings = $c['current_bookings'] ?? 0;
                $maxCapacity = $c['max_capacity'] ?? $c['capacity'] ?? 10; 
                ?>
                <div class="booking-card">
                    <div class="booking-card-date">
                        <div class="day"><?= $date->format('d') ?></div>
                        <div class="month"><?= $months[(int)$date->format('n')-1] ?></div>
                        <div class="year"><?= $date->format('Y') ?></div>
                    </div>
                    <div class="booking-card-info">
                        <h4><?= htmlspecialchars($c['class_name']) ?></h4>
                        <p><i class="fas fa-clock"></i> <?= substr($c['start_time'], 0, 5) ?> - <?= substr($c['end_time'], 0, 5) ?></p>
                        <p><i class="fas fa-users"></i> <?= $currentBookings ?>/<?= $maxCapacity ?> peserta terdaftar</p>   
                    </div>
                    <div class="booking-card-actions">
                        <a href="students.php?schedule=<?= $c['schedule_id'] ?>" class="btn btn-primary btn-sm">
                            <i class="fas fa-users"></i> Lihat Peserta
                        </a>
                    </div>
                </div>
                <?php endforeach; ?>
                <?php endif; ?>
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
