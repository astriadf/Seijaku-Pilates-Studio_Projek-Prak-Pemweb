<?php
session_start();
require_once '../../config/database.php';
require_once '../../classes/user.php';
require_once '../../classes/instructor.php';

$db = new Database();
$conn = $db->getConnection();
$userModel = new User($conn);
$instructor = new Instructor($conn);

$userId = $_SESSION['user_id'] ?? null;
$userData = null;

if ($userId) {
    $userData = $userModel->getById($userId);
}

$instructors = $instructor->getAll();

$avatarEmojis = [
    'totoro' => '🌳', 'chihiro' => '👧', 'ponyo' => '🐟', 'kiki' => '🧹',
    'satsuki' => '👩', 'sophie' => '👱‍♀️', 'nausicaa' => '🌿', 'san' => '🐺'
];
$userAvatar = $avatarEmojis[$userData['avatar'] ?? 'totoro'] ?? '🌳';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Instructor - Seijaku Studio Pilates</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../../public/css/dashboard.css">
    <style>
        .instructors-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 25px;
        }
        .instructor-card-admin {
            background: var(--white);
            border-radius: var(--radius-lg);
            padding: 25px;
            text-align: center;
            box-shadow: var(--shadow);
            transition: all 0.3s ease;
        }
        .instructor-card-admin:hover {
            transform: translateY(-5px);
            box-shadow: var(--shadow-lg);
        }
        .instructor-avatar-admin {
            width: 80px;
            height: 80px;
            margin: 0 auto 15px;
            background: linear-gradient(135deg, var(--primary) 0%, var(--accent-blue) 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2.5rem;
        }
        .instructor-card-admin h3 { font-size: 1.1rem; margin-bottom: 5px; }
        .instructor-card-admin .specialization { color: var(--primary); font-size: 0.85rem; margin-bottom: 10px; }
        .instructor-rating { color: #ffc107; margin-bottom: 15px; }
        .instructor-actions { display: flex; gap: 10px; justify-content: center; }
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
                    <div class="nav-section-title">Main Menu</div>
                    <a href="dashboard.php" class="nav-item">
                        <i class="fas fa-home"></i>
                        <span>Dashboard</span>
                    </a>
                    <a href="bookings.php" class="nav-item">
                        <i class="fas fa-calendar-check"></i>
                        <span>Manage Booking</span>
                    </a>
                </div>

                <div class="nav-section">
                    <div class="nav-section-title">Master Data</div>
                    <a href="classes.php" class="nav-item">
                        <i class="fas fa-dumbbell"></i>
                        <span>Type Class</span>
                    </a>
                    <a href="videos.php" class="nav-item">
                        <i class="fas fa-video"></i>
                        <span>Tutorial Videos</span>
                    </a>
                    <a href="instructors.php" class="nav-item active">
                        <i class="fas fa-chalkboard-teacher"></i>
                        <span>Instructor</span>
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
                    <h1>Instructor</h1>
                    <p>Manage Pilates Instructor</p>
                </div>
                <div class="top-actions">
                    <button class="btn btn-primary" onclick="openModal()">
                        <i class="fas fa-plus"></i> Add Instructor
                    </button>
                </div>
            </div>

            <div class="instructors-grid">
                <?php 
                $instructorImages = [
                    'Sakura Tanaka' => '../../public/images/sakura_tanaka.png',
                    'Yuki Yamamoto' => '../../public/images/yuki_yamamoto.png',
                    'Hana Suzuki' => '../../public/images/hana_suzuki.png',
                    'Mei Nakamura' => '../../public/images/mei_nakamura.png',
                    'Aoi Watanabe' => '../../public/images/aoi_watanabe.png',
                    'Rin Kobayashi' => '../../public/images/rin_kobayashi.png',
                    'Kaede Ito' => '../../public/images/kaede_ito.png',
                    'Momiji Sato' => '../../public/images/momiji_sato.png',
                    'Ayame Kimura' => '../../public/images/ayame_kimura.png'
                ];
                foreach ($instructors as $inst): 
                    $imgSrc = $instructorImages[$inst['full_name']] ?? null;
                ?>
                <div class="instructor-card-admin">
                    <div class="instructor-avatar-admin" style="<?= $imgSrc ? 'background: none; overflow: hidden;' : '' ?>">
                        <?php if ($imgSrc): ?>
                            <img src="<?= $imgSrc ?>" alt="<?= htmlspecialchars($inst['full_name']) ?>" style="width: 100%; height: 100%; object-fit: cover; border-radius: 50%;">
                        <?php else: ?>
                            <?= $avatarEmojis[$inst['avatar'] ?? 'totoro'] ?? '🧘' ?>
                        <?php endif; ?>
                    </div>
                    <h3><?= htmlspecialchars($inst['full_name']) ?></h3>
                    <div class="specialization"><?= htmlspecialchars($inst['specialization']) ?></div>
                    <div class="instructor-rating">
                        <?= str_repeat('<i class="fas fa-star"></i>', floor($inst['rating'])) ?>
                        <span style="color: var(--text);"><?= $inst['rating'] ?></span>
                    </div>
                    <div class="instructor-actions">
                        <button class="btn btn-secondary btn-sm" onclick="editInstructor(<?= $inst['instructor_id'] ?>)">
                            <i class="fas fa-edit"></i> Edit
                        </button>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </main>
    </div>

    <div class="modal" id="instructorModal">
        <div class="modal-content" style="max-width: 500px;">
            <div class="modal-header">
                <h3 id="modalTitle">Add Instructor</h3>
                <button class="modal-close" onclick="closeModal()">&times;</button>
            </div>
            <div class="modal-body">
                <form id="instructorForm">
                    <input type="hidden" name="instructor_id" id="instructorId">
                    <div class="form-group">
                        <label>Email User</label>
                        <input type="email" name="email" id="instructorEmail" required placeholder="Email of user to be made instructor">
                    </div>
                    <div class="form-group">
                        <label>Spesialization</label>
                        <input type="text" name="specialization" id="instructorSpec" required placeholder="Example: Mat Pilates, Reformer">
                    </div>
                    <div class="form-group">
                        <label>Bio</label>
                        <textarea name="bio" id="instructorBio" rows="3" placeholder="About Instructor"></textarea>
                    </div>
                    <div class="form-group">
                        <label>Experience (Years)</label>
                        <input type="number" name="experience_years" id="instructorExp" min="0" value="0">
                    </div>
                    <div class="form-group">
                        <label>Certifications</label>
                        <input type="text" name="certification" id="instructorCert" placeholder="Certification details">
                    </div>
                    <div class="form-group">
                        <label>Instagram</label>
                        <input type="text" name="instagram" id="instructorIg" placeholder="@username">
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="closeModal()">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="submitInstructor()">Save</button>
            </div>
        </div>
    </div>

    <div class="toast-container" id="toastContainer"></div>

    <script>
        const INST_API_BASE = '../../api/instructors';
        let isEditing = false;

        function toggleSidebar() {
            document.getElementById('sidebar').classList.toggle('active');
        }

        function logout() {
            localStorage.removeItem('token');
            localStorage.removeItem('user');
            window.location.href = '../../public/index.php';
        }

        function openModal() {
            isEditing = false;
            document.getElementById('modalTitle').textContent = 'Add Instructor';
            document.getElementById('instructorForm').reset();
            document.getElementById('instructorId').value = '';
            document.getElementById('instructorEmail').disabled = false;
            document.getElementById('instructorModal').classList.add('active');
        }

        function closeModal() {
            document.getElementById('instructorModal').classList.remove('active');
        }

        async function editInstructor(id) {
            isEditing = true;
            document.getElementById('modalTitle').textContent = 'Edit Instruktur';
            
            try {
                const response = await fetch(`${INST_API_BASE}/get.php?id=${id}`);
                const result = await response.json();
                
                if (result.success) {
                    const inst = result.data;
                    document.getElementById('instructorId').value = inst.instructor_id;
                    document.getElementById('instructorEmail').value = inst.email;
                    document.getElementById('instructorEmail').disabled = true;
                    document.getElementById('instructorSpec').value = inst.specialization || '';
                    document.getElementById('instructorBio').value = inst.bio || '';
                    document.getElementById('instructorExp').value = inst.experience_years || 0;
                    document.getElementById('instructorCert').value = inst.certification || '';
                    document.getElementById('instructorIg').value = inst.instagram || '';
                    document.getElementById('instructorModal').classList.add('active');
                } else {
                    showToast('Gagal memuat data instruktur', 'error');
                }
            } catch (error) {
                showToast('Terjadi kesalahan saat memuat data', 'error');
            }
        }

        async function submitInstructor() {
            const form = document.getElementById('instructorForm');
            const formData = new FormData(form);
            const data = Object.fromEntries(formData.entries());

            const endpoint = isEditing ? 'update.php' : 'create.php';

            try {
                const response = await fetch(`${INST_API_BASE}/${endpoint}`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(data)
                });
                const result = await response.json();
                
                if (result.success) {
                    showToast(isEditing ? 'Instruktur berhasil diupdate' : 'Instruktur berhasil ditambahkan', 'success');
                    closeModal();
                    setTimeout(() => location.reload(), 1000);
                } else {
                    showToast(result.message || 'Gagal menyimpan', 'error');
                }
            } catch (error) {
                showToast('Terjadi kesalahan saat menyimpan', 'error');
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
