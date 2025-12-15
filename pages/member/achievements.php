<?php
session_start();
require_once '../../config/database.php';
require_once '../../classes/user.php';
require_once '../../classes/achievement.php';

$db = new Database();
$conn = $db->getConnection();
$user = new User($conn);
$achievement = new Achievement($conn);

$userId = $_SESSION['user_id'] ?? null;
$userData = null;
$achievements = [];
$stats = [];

if ($userId) {
    $userData = $user->getById($userId);
    $achievements = $achievement->getUserAchievements($userId);
    $stats = $user->getStats($userId);
}

$avatarEmojis = [
    'totoro' => '🌳', 'chihiro' => '👧', 'ponyo' => '🐟', 'kiki' => '🧹',
    'satsuki' => '👩', 'sophie' => '👱‍♀️', 'calcifer' => '🔥', 'jiji' => '🐱',
    'kodama' => '👻', 'catbus' => '🚌', 'howl' => '🏰', 'haku' => '🐉'
];
$userAvatar = $avatarEmojis[$userData['avatar'] ?? 'totoro'] ?? '🌳';

$badgeEmojis = [
    'Totoro' => '🌳', 'Chihiro' => '👧', 'Calcifer' => '🔥',
    'Ponyo' => '🐟', 'Howl' => '🏰', 'Kodama' => '👻'
];
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Achievements - Seijaku Studio Pilates</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../../public/css/dashboard.css">
    <style>
        .achievement-card.unlocked .achievement-icon {
            animation: glow 2s ease-in-out infinite, float 3s ease-in-out infinite;
        }
        @keyframes float {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-10px); }
        }
        .unlocked-date {
            font-size: 0.75rem;
            color: var(--primary);
            margin-top: 10px;
        }
        .stats-summary {
            background: linear-gradient(135deg, var(--primary) 0%, var(--accent-blue) 100%);
            border-radius: var(--radius-lg);
            padding: 30px;
            color: var(--white);
            margin-bottom: 30px;
            display: flex;
            align-items: center;
            gap: 30px;
        }
        .stats-summary .big-number {
            font-size: 4rem;
            font-weight: 700;
            line-height: 1;
        }
        .stats-summary p {
            opacity: 0.9;
        }
        .stats-summary h3 {
            font-size: 1.3rem;
            margin-bottom: 5px;
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
                        <span>Book Class</span>
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
                    <a href="achievements.php" class="nav-item active">
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
                        <span>Back to Home</span>
                    </a>
                    <a href="#" class="nav-item" onclick="logout()">
                        <i class="fas fa-sign-out-alt"></i>
                        <span>Logout</span>
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
                    <h1>Achievements</h1>
                    <p>Collect badges from your favorite Studio Ghibli characters</p>
                </div>
            </div>

            <div class="stats-summary">
                <div class="big-number"><?= $stats['total_classes'] ?? 0 ?></div>
                <div>
                    <h3>Total Classes Attended</h3>
                    <p>Keep practicing to unlock new badges!</p>
                </div>
            </div>

            <div class="achievements-grid">
                <?php foreach ($achievements as $ach): 
                    $emoji = $badgeEmojis[$ach['name'] ?? ' '] ?? '🏆';
                    $totalClasses = $stats['total_classes'] ?? 0;
                    $classesRequired = $ach['required_classes'] ?? $ach['classes_required'] ?? 1;
                    $progress = min(100, ($totalClasses / $classesRequired) * 100);
                    $isEarned = isset($ach['earned']) && $ach['earned'];                
                ?>
                <div class="achievement-card <?= $isEarned ? 'unlocked' : 'locked' ?>"> 
                    <div class="achievement-icon"><?= $emoji ?></div>
                    <h3><?= htmlspecialchars($ach['name'] ?? 'Badge') ?></h3>
                    <p><?= htmlspecialchars($ach['description'] ?? '') ?></p>                    
                    <?php if ($isEarned): ?>
                        <div class="unlocked-date">
                            <i class="fas fa-check-circle"></i> Earned on <?= date('d M Y', strtotime($ach['earned_at'] ?? 'now')) ?>                    
                        </div>
                    <?php else: ?>
                        <div class="achievement-progress">
                            <div class="achievement-progress-fill" style="width: <?= $progress ?>%"></div>
                        </div>
                        <div class="achievement-count">
                            <?= $totalClasses ?> / <?= $classesRequired ?> classes                    
                        </div>
                    <?php endif; ?>
                </div>
                <?php endforeach; ?>
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
