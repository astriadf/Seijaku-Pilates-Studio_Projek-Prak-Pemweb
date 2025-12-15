<?php
session_start();
require_once '../../config/database.php';
require_once '../../classes/user.php';
require_once '../../classes/video.php';

$db = new Database();
$conn = $db->getConnection();
$userModel = new User($conn);
$video = new Video($conn);

$userId = $_SESSION['user_id'] ?? null;
$userData = null;

if ($userId) {
    $userData = $userModel->getById($userId);
}

$videos = $video->getAll();

$avatarEmojis = ['totoro' => '🌳', 'chihiro' => '👧', 'ponyo' => '🐟', 'kiki' => '🧹'];
$userAvatar = $avatarEmojis[$userData['avatar'] ?? 'totoro'] ?? '🌳';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Video Tutorial - Seijaku Studio Pilates</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../../public/css/dashboard.css">
    <style>
        .data-table { width: 100%; border-collapse: collapse; }
        .data-table th, .data-table td { padding: 15px; text-align: left; border-bottom: 1px solid #eee; }
        .data-table th { background: var(--bg); font-weight: 600; }
        .data-table tr:hover { background: var(--bg); }
        .action-btns { display: flex; gap: 8px; }
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
                    <a href="videos.php" class="nav-item active">
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

        <main class="main-content">
            <div class="top-bar">
                <div class="page-title">
                    <h1>Video Tutorial</h1>
                    <p>Kelola video tutorial pilates</p>
                </div>
                <div class="top-actions">
                    <button class="btn btn-primary" onclick="openModal()">
                        <i class="fas fa-plus"></i> Tambah Video
                    </button>
                </div>
            </div>

            <div class="card">
                <div class="card-body" style="overflow-x: auto;">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Judul</th>
                                <th>Level</th>
                                <th>Durasi</th>
                                <th>Views</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $levelText = ['beginner' => 'Pemula', 'intermediate' => 'Menengah', 'advanced' => 'Lanjutan'];
                            foreach ($videos as $v): 
                            ?>
                            <tr>
                                <td><?= htmlspecialchars($v['title']) ?></td>
                                <td><span class="video-level"><?= $levelText[$v['level']] ?? $v['level'] ?></span></td>
                                <td><?= $v['duration_minutes'] ?> menit</td>
                                <td><?= number_format($v['view_count']) ?></td>
                                <td class="action-btns">
                                    <button class="btn btn-secondary btn-sm" onclick="editVideo(<?= $v['video_id'] ?>)">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button class="btn btn-danger btn-sm" onclick="deleteVideo(<?= $v['video_id'] ?>)">
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

    <div class="modal" id="videoModal">
        <div class="modal-content">
            <div class="modal-header">
                <h3 id="modalTitle">Tambah Video</h3>
                <button class="modal-close" onclick="closeModal()">&times;</button>
            </div>
            <div class="modal-body">
                <form id="videoForm">
                    <input type="hidden" name="video_id" id="videoId">
                    <div class="form-group">
                        <label>Judul</label>
                        <input type="text" name="title" id="videoTitle" required>
                    </div>
                    <div class="form-group">
                        <label>URL Video (Youtube)</label>
                        <input type="url" name="video_url" id="videoUrl" required>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label>Level</label>
                            <select name="level" id="videoLevel" required>
                                <option value="beginner">Pemula</option>
                                <option value="intermediate">Menengah</option>
                                <option value="advanced">Lanjutan</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Durasi (menit)</label>
                            <input type="number" name="duration_minutes" id="videoDuration" required>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Kategori</label>
                        <select name="category" id="videoCategory">
                            <option value="Mat Pilates">Mat Pilates</option>
                            <option value="Reformer Pilates">Reformer Pilates</option>
                            <option value="Clinical Pilates">Clinical Pilates</option>
                            <option value="Contemporary Pilates">Contemporary Pilates</option>
                            <option value="STOTT Pilates">STOTT Pilates</option>
                            <option value="Winsor Pilates">Winsor Pilates</option>
                            <option value="Classical Pilates">Classical Pilates</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Deskripsi</label>
                        <textarea name="description" id="videoDescription" rows="3"></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" onclick="closeModal()">Batal</button>
                <button class="btn btn-primary" onclick="saveVideo()">Simpan</button>
            </div>
        </div>
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

        function openModal(id = null) {
            document.getElementById('modalTitle').textContent = id ? 'Edit Video' : 'Tambah Video';
            document.getElementById('videoForm').reset();
            document.getElementById('videoId').value = id || '';
            document.getElementById('videoModal').classList.add('active');
        }

        function closeModal() {
            document.getElementById('videoModal').classList.remove('active');
        }

        async function editVideo(id) {
            try {
                const response = await fetch(`../../api/videos/get.php?id=${id}`);
                const result = await response.json();
                
                if (result.success && result.data) {
                    const data = result.data;
                    document.getElementById('videoId').value = data.video_id;
                    document.getElementById('videoTitle').value = data.title || '';
                    document.getElementById('videoUrl').value = data.youtube_url || '';
                    document.getElementById('videoLevel').value = data.level || 'beginner';
                    document.getElementById('videoDuration').value = data.duration_minutes || 0;
                    document.getElementById('videoCategory').value = data.category || 'Mat Pilates';
                    document.getElementById('videoDescription').value = data.description || '';
                    document.getElementById('modalTitle').textContent = 'Edit Video';
                    document.getElementById('videoModal').classList.add('active');
                } else {
                    showToast('Gagal memuat data video', 'error');
                }
            } catch (error) {
                console.error('Error:', error);
                showToast('Terjadi kesalahan saat memuat data', 'error');
            }
        }
        async function deleteVideo(id) {
            if (confirm('Yakin ingin menghapus video ini?')) {
                try {
                    const response = await fetch('../../api/videos/delete.php', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify({ video_id: id })
                    });
                    const result = await response.json();
                    
                    if (result.success) {
                        showToast('Video berhasil dihapus', 'success');
                        setTimeout(() => location.reload(), 1000);
                    } else {
                        showToast(result.message || 'Gagal menghapus video', 'error');
                    }
                } catch (error) {
                    console.error('Error:', error);
                    showToast('Terjadi kesalahan saat menghapus', 'error');
                }
            }
        }

        async function saveVideo() {
            const id = document.getElementById('videoId').value;
            const data = {
                video_id: id || null,
                title: document.getElementById('videoTitle').value,
                video_url: document.getElementById('videoUrl').value,
                level: document.getElementById('videoLevel').value,
                duration_minutes: parseInt(document.getElementById('videoDuration').value) || 0,
                category: document.getElementById('videoCategory').value,
                description: document.getElementById('videoDescription').value
            };
            if (!data.title) {
                showToast('Judul video harus diisi', 'error');
                return;
            }
            try {
                const url = id ? '../../api/videos/update.php' : '../../api/videos/create.php';
                const response = await fetch(url, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(data)
                });
                const result = await response.json();
                
                if (result.success) {
                    showToast(id ? 'Video berhasil diperbarui' : 'Video berhasil ditambahkan', 'success');
                    closeModal();
                    setTimeout(() => location.reload(), 1000);
                } else {
                    showToast(result.message || 'Gagal menyimpan video', 'error');
                }
            } catch (error) {
                console.error('Error:', error);
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
