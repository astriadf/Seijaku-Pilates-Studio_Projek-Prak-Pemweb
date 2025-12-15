<?php
session_start();
require_once '../../config/database.php';
require_once '../../classes/user.php';
require_once '../../classes/message.php';

$db = new Database();
$conn = $db->getConnection();
$userModel = new User($conn);
$message = new Message($conn);

$userId = $_SESSION['user_id'] ?? null;
$userData = null;

if ($userId) {
    $userData = $userModel->getById($userId);
}

$messages = $message->getAll();

$avatarEmojis = ['totoro' => '🌳', 'chihiro' => '👧', 'ponyo' => '🐟', 'kiki' => '🧹'];
$userAvatar = $avatarEmojis[$userData['avatar'] ?? 'totoro'] ?? '🌳';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pesan Masuk - Seijaku Studio Pilates</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../../public/css/dashboard.css">
    <style>
        .message-card {
            background: var(--white);
            border-radius: var(--radius-lg);
            padding: 25px;
            margin-bottom: 20px;
            box-shadow: var(--shadow);
            transition: all 0.3s ease;
        }
        .message-card:hover {
            box-shadow: var(--shadow-lg);
        }
        .message-card.unread {
            border-left: 4px solid var(--primary);
        }
        .message-header {
            display: flex;
            justify-content: space-between;
            margin-bottom: 15px;
        }
        .message-sender h4 { font-size: 1rem; margin-bottom: 3px; }
        .message-sender p { font-size: 0.85rem; color: var(--text-light); }
        .message-date { font-size: 0.8rem; color: var(--text-light); }
        .message-subject { font-weight: 600; margin-bottom: 10px; }
        .message-body { color: var(--text-light); font-size: 0.95rem; }
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
                    <span>Admin Panel</span>
                </div>
            </div>

            <nav class="sidebar-nav">
                <div class="nav-section">
                    <div class="nav-section-title">Menu Utama</div>
                    <a href="dashboard.php" class="nav-item">
                        <i class="fas fa-home"></i>
                        <span>Dashboard</span>
                    </a>
                    <a href="bookings.php" class="nav-item">
                        <i class="fas fa-calendar-check"></i>
                        <span>Kelola Booking</span>
                    </a>
                </div>

                <div class="nav-section">
                    <div class="nav-section-title">Master Data</div>
                    <a href="classes.php" class="nav-item">
                        <i class="fas fa-dumbbell"></i>
                        <span>Jenis Kelas</span>
                    </a>
                    <a href="videos.php" class="nav-item">
                        <i class="fas fa-video"></i>
                        <span>Video Tutorial</span>
                    </a>
                    <a href="instructors.php" class="nav-item">
                        <i class="fas fa-chalkboard-teacher"></i>
                        <span>Instruktur</span>
                    </a>
                    <a href="messages.php" class="nav-item active">
                        <i class="fas fa-envelope"></i>
                        <span>Pesan Masuk</span>
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
                        <h4><?= htmlspecialchars($userData['full_name'] ?? 'Admin') ?></h4>
                        <span>Administrator</span>
                    </div>
                </div>
            </div>
        </aside>

        <main class="main-content">
            <div class="top-bar">
                <div class="page-title">
                    <h1>Pesan Masuk</h1>
                    <p>Pesan dari form kontak website</p>
                </div>
            </div>

            <?php if (empty($messages)): ?>
            <div class="card">
                <div class="card-body">
                    <div class="empty-state">
                        <i class="fas fa-inbox"></i>
                        <h3>Tidak Ada Pesan</h3>
                        <p>Belum ada pesan masuk</p>
                    </div>
                </div>
            </div>
            <?php else: ?>
            <?php foreach ($messages as $m): 
                $date = new DateTime($m['created_at']);
            ?>
            <div class="message-card <?= $m['is_read'] ? '' : 'unread' ?>">
                <div class="message-header">
                    <div class="message-sender">
                        <h4><?= htmlspecialchars($m['name']) ?></h4>
                        <p><i class="fas fa-envelope"></i> <?= htmlspecialchars($m['email']) ?></p>
                    </div>
                    <div class="message-date">
                        <?= $date->format('d M Y H:i') ?>
                    </div>
                </div>
                
                <!-- Isi pesan / Message content -->
                <div class="message-body"><?= nl2br(htmlspecialchars($m['message'])) ?></div>
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
