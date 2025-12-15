<?php
session_start();
require_once '../../config/database.php';
require_once '../../config/static_data.php';

$db = new Database();
$conn = $db->getConnection();

$userId   = $_SESSION['user_id'] ?? null;
$userData = null;

if ($conn !== null) {
    require_once '../../classes/user.php';
    require_once '../../classes/classType.php';

    $userModel = new User($conn);
    $classType = new ClassType($conn);

    if ($userId) {
        $userData = $userModel->getById($userId);
    }

    $classes = $classType->getAll();
} else {
    // fallback demo mode
    $classes  = StaticData::getClassTypes();
    $userData = ['full_name' => 'Admin Demo', 'avatar' => 'totoro'];
}

// avatar di sidebar
$avatarEmojis = [
    'totoro'  => '🌳',
    'chihiro' => '👧',
    'ponyo'   => '🐟',
    'kiki'    => '🧹'
];
$userAvatar = $avatarEmojis[$userData['avatar'] ?? 'totoro'] ?? '🌳';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Class Types - Seijaku Studio Pilates</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../../public/css/dashboard.css">
    <style>
        .data-table { width: 100%; border-collapse: collapse; }
        .data-table th, .data-table td { padding: 15px; text-align: left; border-bottom: 1px solid #eee; }
        .data-table th { background: var(--bg); font-weight: 600; }
        .data-table tr:hover { background: var(--bg); }
        .action-btns { display: flex; gap: 8px; }
        .color-preview { width: 30px; height: 30px; border-radius: 50%; display: inline-block; }
    </style>
</head>
<body>
<button class="mobile-toggle" onclick="toggleSidebar()">
    <i class="fas fa-bars"></i>
</button>

<div class="dashboard-layout">
    <!-- SIDEBAR -->
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
                    <span>Manage Bookings</span>
                </a>
            </div>

            <div class="nav-section">
                <div class="nav-section-title">Master Data</div>
                <a href="classes.php" class="nav-item active">
                    <i class="fas fa-dumbbell"></i>
                    <span>Class Types</span>
                </a>
                <a href="videos.php" class="nav-item">
                    <i class="fas fa-video"></i>
                    <span>Video Tutorial</span>
                </a>
                <a href="instructors.php" class="nav-item">
                    <i class="fas fa-chalkboard-teacher"></i>
                    <span>Instruktur</span>
                </a>
                <a href="messages.php" class="nav-item">
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

    <!-- MAIN -->
    <main class="main-content">
        <div class="top-bar">
            <div class="page-title">
                <h1>Class Types</h1>
                <p>Organize Pilates Class Types</p>
            </div>
            <div class="top-actions">
                <button class="btn btn-primary" onclick="openModal()">
                    <i class="fas fa-plus"></i> Add Class
                </button>
            </div>
        </div>

        <div class="card">
            <div class="card-body" style="overflow-x: auto;">
                <table class="data-table">
                    <thead>
                    <tr>
                        <th>Name</th>
                        <th>Level</th>
                        <th>Timer</th>
                        <th>Price</th>
                        <th>Color</th>
                        <th>Picture</th>
                        <th>Action</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php
                    $levelText = [
                        'beginner'     => 'Pemula',
                        'intermediate' => 'Menengah',
                        'advanced'     => 'Lanjutan',
                        'all'          => 'Semua Level'
                    ];

                    // mapping gambar demo
                    $classImages = [
                        'Mat Pilates'          => '../../public/images/mat_pilates.jpg',
                        'Winsor Pilates'       => '../../public/images/winsor_pilates.png',
                        'Classical Pilates'    => '../../public/images/classical_pilates.jpg',
                        'STOTT Pilates'        => '../../public/images/stott_pilates.jpg',
                        'Reformer Pilates'     => '../../public/images/reformer-pilates.jpg',
                        'Clinical Pilates'     => '../../public/images/clinical_pilates.jpg',
                        'Contemporary Pilates' => '../../public/images/contemporary_pilates.jpg',
                        'Power Pilates'        => '../../public/images/power_pilates.jpg',
                        'Prenatal Pilates'     => '../../public/images/prenatal_pilates.jpg',
                        'Therapeutic Pilates'  => '../../public/images/therapeutic_pilates.jpg'
                    ];

                    foreach ($classes as $c):
                        $imgPath  = $classImages[$c['name']] ?? null;
                        $level    = $c['difficulty_level'] ?? $c['level'] ?? 'beginner';
                        $duration = $c['duration_minutes'] ?? $c['duration'] ?? 60;
                        $price    = $c['price'] ?? 150000;
                        $color    = $c['color'] ?? '#9FBF9E';
                        ?>
                        <tr>
                            <td><?= htmlspecialchars($c['name']) ?></td>
                            <td><?= $levelText[$level] ?? $level ?></td>
                            <td><?= (int)$duration ?> minute</td>
                            <td>Rp <?= number_format($price, 0, ',', '.') ?></td>
                            <td><span class="color-preview" style="background: <?= htmlspecialchars($color) ?>"></span></td>
                            <td>
                                <?php if ($imgPath): ?>
                                    <img src="<?= $imgPath ?>"
                                         alt="Gambar <?= htmlspecialchars($c['name']) ?>"
                                         style="width:60px;height:40px;object-fit:cover;border-radius:6px;">
                                <?php else: ?>
                                    <span style="font-size:0.8rem;color:#999;">(nothing's here)</span>
                                <?php endif; ?>
                            </td>
                            <td class="action-btns">
                                <button class="btn btn-secondary btn-sm"
                                        onclick="editClass(<?= (int)$c['class_type_id'] ?>)">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button class="btn btn-danger btn-sm"
                                        onclick="deleteClass(<?= (int)$c['class_type_id'] ?>)">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>
</div>

<!-- MODAL CLASS FORM -->
<div class="modal" id="classModal">
    <div class="modal-content">
        <div class="modal-header">
            <h3 id="modalTitle">Add Class</h3>
            <button class="modal-close" onclick="closeModal()">&times;</button>
        </div>
        <div class="modal-body">
            <form id="classForm">
                <input type="hidden" name="class_type_id" id="classTypeId">

                <div class="form-group">
                    <label>Class Name</label>
                    <input type="text" name="name" id="className" required>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>Level</label>
                        <select name="difficulty_level" id="classLevel" required>
                            <option value="beginner">Beginner</option>
                            <option value="intermediate">Intermediate</option>
                            <option value="advanced">Advanced</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Timer (minute)</label>
                        <input type="number" name="duration_minutes" id="classDuration" min="1" required>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>Price (Rp)</label>
                        <input type="number" name="price" id="classPrice" min="0" required>
                    </div>
                    <div class="form-group">
                        <label>Color</label>
                        <input type="color" name="color" id="classColor" value="#9FBF9E" style="height:45px;">
                    </div>
                </div>

                <div class="form-group">
                    <label>Description</label>
                    <textarea name="description" id="classDescription" rows="3"></textarea>
                </div>
            </form>
        </div>
        <div class="modal-footer">
            <button class="btn btn-secondary" onclick="closeModal()">Cancel</button>
            <button class="btn btn-primary" onclick="saveClass()">Save</button>
        </div>
    </div>
</div>

<div class="toast-container" id="toastContainer"></div>

<script>
    const CLASS_API_BASE = '../../api/classes';

    function toggleSidebar() {
        document.getElementById('sidebar').classList.toggle('active');
    }

    function logout() {
        localStorage.removeItem('token');
        localStorage.removeItem('user');
        window.location.href = '../../public/index.php';
    }

    function openModal(id = null) {
        document.getElementById('modalTitle').textContent = id ? 'Edit Class' : 'Add Class';
        document.getElementById('classForm').reset();
        document.getElementById('classTypeId').value = id || '';
        document.getElementById('classColor').value = '#9FBF9E';
        document.getElementById('classModal').classList.add('active');
    }

    function closeModal() {
        document.getElementById('classModal').classList.remove('active');
    }

    async function editClass(id) {
        try {
            const response = await fetch(`${CLASS_API_BASE}/get.php?id=${id}`);
            const result   = await response.json();

            if (result.success && result.data) {
                const data = result.data;
                document.getElementById('classTypeId').value      = data.class_type_id;
                document.getElementById('className').value        = data.name || '';
                document.getElementById('classLevel').value       = data.difficulty_level || 'beginner';
                document.getElementById('classDuration').value    = data.duration_minutes || 60;
                document.getElementById('classPrice').value       = data.price || 75000;
                document.getElementById('classColor').value       = data.color || '#9FBF9E';
                document.getElementById('classDescription').value = data.description || '';
                document.getElementById('modalTitle').textContent = 'Edit Class';
                document.getElementById('classModal').classList.add('active');
            } else {
                showToast('Failed to load class data', 'error');
            }
        } catch (error) {
            console.error('Error:', error);
            showToast('An error occurred while loading data', 'error');
        }
    }

    async function deleteClass(id) {
        if (!confirm('Are you sure you want to delete this class?')) return;

        try {
            const response = await fetch(`${CLASS_API_BASE}/delete.php`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ class_type_id: id })
            });
            const result = await response.json();

            if (result.success) {
                showToast('Class deleted successfully', 'success');
                setTimeout(() => location.reload(), 1000);
            } else {
                showToast(result.message || 'Failed to delete class', 'error');
            }
        } catch (error) {
            console.error('Error:', error);
            showToast('An error occurred while deleting', 'error');
        }
    }

    async function saveClass() {
        const id = document.getElementById('classTypeId').value;

        const data = {
            class_type_id: id || null,
            name: document.getElementById('className').value.trim(),
            difficulty_level: document.getElementById('classLevel').value,
            duration_minutes: parseInt(document.getElementById('classDuration').value, 10) || 60,
            price: parseFloat(document.getElementById('classPrice').value) || 75000,
            color: document.getElementById('classColor').value,
            description: document.getElementById('classDescription').value.trim()
        };

        if (!data.name) {
            showToast('Nama kelas harus diisi', 'error');
            return;
        }

        try {
            const url = id ? `${CLASS_API_BASE}/update.php` : `${CLASS_API_BASE}/create.php`;
            const response = await fetch(url, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(data)
            });
            const result = await response.json();

            if (result.success) {
                showToast(id ? 'Class updated successfully' : 'Class added successfully', 'success');
                closeModal();
                setTimeout(() => location.reload(), 1000);
            } else {
                showToast(result.message || 'Failed to save class', 'error');
            }
        } catch (error) {
            console.error('Error:', error);
            showToast('An error occurred while saving', 'error');
        }
    }

    function showToast(message, type = 'success') {
        const container = document.getElementById('toastContainer');
        const toast     = document.createElement('div');
        toast.className = `toast ${type}`;
        toast.innerHTML =
            `<i class="fas fa-${type === 'success' ? 'check-circle' : 'exclamation-circle'}"></i><span>${message}</span>`;
        container.appendChild(toast);
        setTimeout(() => toast.remove(), 3000);
    }
</script>
</body>
</html>
