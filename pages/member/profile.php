<?php
session_start();
require_once '../../config/database.php';
require_once '../../classes/user.php';


$db   = new Database();
$conn = $db->getConnection();
$user = new User($conn);


$userId   = $_SESSION['user_id'] ?? null;
$userData = null;


if ($userId) {
    $userData = $user->getById($userId);
}


// mapping kode avatar -> file gambar
$avatarImages = [
    '1'  => 'avatar1.jpg',
    '2' => 'avatar2.png',
    '3'   => 'avatar3.png',
    '4'    => 'avatar4.png',
    '5' => 'avatar5.png',
    '6'  => 'avatar6.png',
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
    <title>My Profile - Seijaku Studio Pilates</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../../public/css/dashboard.css">
    <style>
        .user-avatar img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            border-radius: 50%;
        }
        .profile-avatar-large img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            border-radius: 50%;
        }
        .avatar-option img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            border-radius: 12px;
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
                    <a href="profile.php" class="nav-item active">
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
                <div class="user-profile">
                    <div class="user-avatar" id="sidebarAvatar">
                        <img src="<?= htmlspecialchars($avatarUrl) ?>" alt="Avatar">
                    </div>
                    <div class="user-info">
                        <h4 id="sidebarName"><?= htmlspecialchars($userData['full_name'] ?? 'Member') ?></h4>
                        <span>Member</span>
                    </div>
                </div>
            </div>
        </aside>


        <main class="main-content">
            <div class="top-bar">
                <div class="page-title">
                    <h1>My Profile</h1>
                    <p>Manage your account information</p>
                </div>
            </div>


            <div class="profile-container">
                <div class="profile-header">
                    <div class="profile-avatar-large" id="mainAvatar" onclick="openAvatarModal()">
                        <img src="<?= htmlspecialchars($avatarUrl) ?>" alt="Avatar" id="mainAvatarImg">
                        <div class="avatar-edit">
                            <i class="fas fa-camera"></i>
                        </div>
                    </div>
                    <h2 id="displayName"><?= htmlspecialchars($userData['full_name'] ?? 'Member') ?></h2>
                    <p><?= htmlspecialchars($userData['email'] ?? '') ?></p>
                </div>


                <div class="profile-form">
                    <form id="profileForm">
                        <div class="form-section">
                            <h3>Personal Information</h3>
                            <div class="form-row">
                                <div class="form-group">
                                    <label>Full Name</label>
                                    <input type="text" name="full_name" id="fullName" value="<?= htmlspecialchars($userData['full_name'] ?? '') ?>" required>
                                </div>
                                <div class="form-group">
                                    <label>Phone Number</label>
                                    <input type="tel" name="phone" id="phone" value="<?= htmlspecialchars($userData['phone'] ?? '') ?>">
                                </div>
                            </div>
                            <div class="form-group">
                                <label>Email</label>
                                <input type="email" name="email" id="email" value="<?= htmlspecialchars($userData['email'] ?? '') ?>" required>
                            </div>
                        </div>


                        <div class="form-section">
                            <h3>Change Password</h3>
                            <p style="color: var(--text-light); font-size: 0.9rem; margin-bottom: 20px;">Leave empty if you do not want to change your password</p>
                            <div class="form-row">
                                <div class="form-group">
                                    <label>New Password</label>
                                    <input type="password" name="password" id="password" placeholder="Minimum 6 characters">
                                </div>
                                <div class="form-group">
                                    <label>Confirm Password</label>
                                    <input type="password" name="password_confirm" id="passwordConfirm" placeholder="Repeat new password">
                                </div>
                            </div>
                        </div>


                        <input type="hidden" name="avatar" id="avatarInput" value="<?= htmlspecialchars($currentAvatarKey) ?>">


                        <button type="submit" class="btn btn-primary btn-lg">
                            <i class="fas fa-save"></i> Save Changes
                        </button>
                    </form>
                </div>
            </div>
        </main>
    </div>


    <div class="modal" id="avatarModal">
        <div class="modal-content">
            <div class="modal-header">
                <h3>Select Avatar</h3>
                <button class="modal-close" onclick="closeAvatarModal()">&times;</button>
            </div>
            <div class="modal-body">
                <p style="margin-bottom: 20px; color: var(--text-light);">Choose your favorite Ghibli character</p>
                <div class="avatar-selector">
                    <div class="avatar-option" data-avatar="1">
                        <img src="../../public/images/avatars/avatar1.jpg" alt="1">
                    </div>
                    <div class="avatar-option" data-avatar="2">
                        <img src="../../public/images/avatars/avatar2.png" alt="2">
                    </div>
                    <div class="avatar-option" data-avatar="3">
                        <img src="../../public/images/avatars/avatar3.png" alt="3">
                    </div>
                    <div class="avatar-option" data-avatar="4">
                        <img src="../../public/images/avatars/avatar4.png" alt="4">
                    </div>
                    <div class="avatar-option" data-avatar="5">
                        <img src="../../public/images/avatars/avatar5.png" alt="5">
                    </div>
                    <div class="avatar-option" data-avatar="6">
                        <img src="../../public/images/avatars/avatar6.png" alt="6">
                    </div>
                </div>
            </div>
        </div>
    </div>


    <div class="toast-container" id="toastContainer"></div>


    <script>
        const API_BASE     = '../../api';
        const AVATAR_BASE  = '../../public/images/avatars/';
        const avatarImages = {
            1:  'avatar1.jpg',
            2:'avatar2.png',
            3:  'avatar3.png',
            4:   'avatar4.png',
            5:'avatar5.png',
            6: 'avatar6.png'
        };


        function toggleSidebar() {
            document.getElementById('sidebar').classList.toggle('active');
        }


        function logout() {
            localStorage.removeItem('token');
            localStorage.removeItem('user');
            window.location.href = '../../public/index.php';
        }


        function openAvatarModal() {
            const current = document.getElementById('avatarInput').value;
            document.querySelectorAll('.avatar-option').forEach(opt => {
                opt.classList.toggle('selected', opt.dataset.avatar === current);
            });
            document.getElementById('avatarModal').classList.add('active');
        }


        function closeAvatarModal() {
            document.getElementById('avatarModal').classList.remove('active');
        }


        document.querySelectorAll('.avatar-option').forEach(opt => {
            opt.addEventListener('click', function() {
                const avatar = this.dataset.avatar;
                const file   = avatarImages[avatar];


                document.querySelectorAll('.avatar-option').forEach(o => o.classList.remove('selected'));
                this.classList.add('selected');


                document.getElementById('avatarInput').value = avatar;


                // main avatar
                const mainImg = document.getElementById('mainAvatarImg');
                if (mainImg && file) {
                    mainImg.src = AVATAR_BASE + file;
                }


                // sidebar avatar
                const sidebarAvatar = document.getElementById('sidebarAvatar');
                if (sidebarAvatar && file) {
                    sidebarAvatar.innerHTML = `<img src="${AVATAR_BASE + file}" alt="Avatar">`;
                }


                closeAvatarModal();
            });
        });


        document.getElementById('profileForm').addEventListener('submit', async function(e) {
            e.preventDefault();


            const password        = document.getElementById('password').value;
            const passwordConfirm = document.getElementById('passwordConfirm').value;


            if (password && password !== passwordConfirm) {
                showToast('Passwords do not match', 'error');
                return;
            }


            if (password && password.length < 6) {
                showToast('Password must be at least 6 characters', 'error');
                return;
            }


            const token = localStorage.getItem('token');
            const formData = {
                full_name: document.getElementById('fullName').value,
                email:     document.getElementById('email').value,
                phone:     document.getElementById('phone').value,
                avatar:    document.getElementById('avatarInput').value
            };


            if (password) {
                formData.password = password;
            }


            try {
                const response = await fetch(`${API_BASE}/users/update.php`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Authorization': `Bearer ${token}`
                    },
                    body: JSON.stringify(formData)
                });


                const data = await response.json();


                if (data.success) {
                    showToast('Profile updated successfully!', 'success');
                    document.getElementById('displayName').textContent = formData.full_name;
                    document.getElementById('sidebarName').textContent  = formData.full_name;


                    document.getElementById('password').value        = '';
                    document.getElementById('passwordConfirm').value = '';


                    const user = JSON.parse(localStorage.getItem('user') || '{}');
                    user.full_name = formData.full_name;
                    user.avatar    = formData.avatar;
                    localStorage.setItem('user', JSON.stringify(user));
                } else {
                    showToast(data.message || 'Failed to update profile', 'error');
                }
            } catch (error) {
                showToast('An error occurred', 'error');
            }
        });


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
