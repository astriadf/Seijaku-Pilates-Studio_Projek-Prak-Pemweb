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
$students = [];
$scheduleId = $_GET['schedule'] ?? null;

if ($userId) {
    $userData = $userModel->getById($userId);
    $instructorData = $instructorModel->getByUserId($userId);
    if ($instructorData && $scheduleId) {
        $students = $instructorModel->getStudents($instructorData['instructor_id']);
    }
}

$avatarEmojis = [
    'totoro' => '🌳', 'chihiro' => '👧', 'ponyo' => '🐟', 'kiki' => '🧹',
    'satsuki' => '👩', 'sophie' => '👱‍♀️'
];
$userAvatar = $avatarEmojis[$userData['avatar'] ?? 'totoro'] ?? '🌳';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Peserta - Seijaku Studio Pilates</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../../public/css/dashboard.css">
    <style>
        .student-card {
            display: flex;
            align-items: center;
            gap: 15px;
            padding: 20px;
            background: var(--white);
            border-radius: var(--radius-lg);
            margin-bottom: 15px;
            box-shadow: var(--shadow);
        }
        .student-avatar {
            width: 50px;
            height: 50px;
            background: linear-gradient(135deg, var(--primary) 0%, var(--accent-blue) 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
        }
        .student-info h4 { font-size: 1rem; margin-bottom: 3px; }
        .student-info p { font-size: 0.85rem; color: var(--text-light); }
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
                    <a href="classes.php" class="nav-item">
                        <i class="fas fa-calendar"></i>
                        <span>Jadwal Kelas</span>
                    </a>
                    <a href="students.php" class="nav-item active">
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
                    <h1>Daftar Peserta</h1>
                    <p><?= $scheduleId ? 'Peserta untuk kelas terpilih' : 'Pilih kelas untuk melihat peserta' ?></p>
                </div>
                <div class="top-actions">
                    <a href="classes.php" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Kembali ke Jadwal
                    </a>
                </div>
            </div>

            <?php if (!$scheduleId): ?>
            <div class="card">
                <div class="card-body">
                    <div class="empty-state">
                        <i class="fas fa-hand-pointer"></i>
                        <h3>Pilih Kelas</h3>
                        <p>Pilih kelas dari jadwal untuk melihat daftar peserta</p>
                        <a href="classes.php" class="btn btn-primary">Lihat Jadwal</a>
                    </div>
                </div>
            </div>
            <?php elseif (empty($students)): ?>
            <div class="card">
                <div class="card-body">
                    <div class="empty-state">
                        <i class="fas fa-users-slash"></i>
                        <h3>Tidak Ada Peserta</h3>
                        <p>Belum ada peserta yang terdaftar untuk kelas ini</p>
                    </div>
                </div>
            </div>
            <?php else: ?>
            <?php foreach ($students as $s): ?>
            <div class="student-card">
                <div class="student-avatar"><?= $avatarEmojis[$s['avatar'] ?? 'totoro'] ?? '👤' ?></div>
                <div class="student-info">
                    <h4><?= htmlspecialchars($s['full_name']) ?></h4>
                    <p><i class="fas fa-envelope"></i> <?= htmlspecialchars($s['email']) ?></p>
                    <?php if (!empty($s['phone'])): ?>
                    <p><i class="fas fa-phone"></i> <?= htmlspecialchars($s['phone']) ?></p>
                    <?php endif; ?>
                </div>
                <div style="margin-left: auto;">
                    <span class="booking-status confirmed">Terkonfirmasi</span>
                </div>
            </div>
            <?php endforeach; ?>
            <?php endif; ?>
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
