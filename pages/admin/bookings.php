<?php
session_start();
require_once '../../config/database.php';
require_once '../../classes/user.php';

$db = new Database();
$conn = $db->getConnection();
$userModel = new User($conn);

$userId = $_SESSION['user_id'] ?? null;
$userData = null;

if ($userId) {
    $userData = $userModel->getById($userId);
}

$avatarEmojis = [
    'totoro' => '🌳', 'chihiro' => '👧', 'ponyo' => '🐟', 'kiki' => '🧹'
];
$userAvatar = $avatarEmojis[$userData['avatar'] ?? 'totoro'] ?? '🌳';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Bookings - Seijaku Studio Pilates</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../../public/css/dashboard.css">
    <style>
        .data-table { width: 100%; border-collapse: collapse; }
        .data-table th, .data-table td { padding: 15px; text-align: left; border-bottom: 1px solid #eee; }
        .data-table th { background: var(--bg); font-weight: 600; color: var(--text); }
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
                    <div class="nav-section-title">Home</div>
                    <a href="dashboard.php" class="nav-item">
                        <i class="fas fa-home"></i>
                        <span>Dashboard</span>
                    </a>
                    <a href="bookings.php" class="nav-item active">
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
                        <span>Incoming Message</span>
                    </a>
                </div>

                <div class="nav-section">
                    <div class="nav-section-title">Acoount</div>
                    <a href="../../public/index.php" class="nav-item">
                        <i class="fas fa-arrow-left"></i>
                        <span>Back to Home Page</span>
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
                    <h1>Manage Bookings</h1>
                    <p>Approve or reject booking requests.</p>
                </div>
            </div>

            <div class="tabs">
                <button class="tab active" data-status="pending">Pending</button>
                <button class="tab" data-status="confirmed">Confirmed</button>
                <button class="tab" data-status="completed">Completed</button>
                <button class="tab" data-status="cancelled">Cancelled</button>
            </div>

            <div class="card">
                <div class="card-body" style="overflow-x: auto;">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Member</th>
                                <th>Class</th>
                                <th>Data</th>
                                <th>Time</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody id="bookingsTable">
                            <tr><td colspan="6" style="text-align: center;">Load Data...</td></tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
    </div>

    <div class="toast-container" id="toastContainer"></div>

    <script>
        const API_BASE = '../../api';
        let currentStatus = 'pending';

        const monthNames = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Des'];

        function toggleSidebar() {
            document.getElementById('sidebar').classList.toggle('active');
        }

        function logout() {
            localStorage.removeItem('token');
            localStorage.removeItem('user');
            window.location.href = '../../public/index.php';
        }

        document.querySelectorAll('.tab').forEach(tab => {
            tab.addEventListener('click', function() {
                document.querySelectorAll('.tab').forEach(t => t.classList.remove('active'));
                this.classList.add('active');
                currentStatus = this.dataset.status;
                loadBookings();
            });
        });

        async function loadBookings() {
            const token = localStorage.getItem('token');
            try {
                const response = await fetch(`${API_BASE}/bookings/admin.php?status=${currentStatus}`, {
                    headers: { 'Authorization': `Bearer ${token}` }
                });
                const data = await response.json();
                
                if (data.success) {
                    renderBookings(data.data);
                }
            } catch (error) {
                console.error('Error:', error);
            }
        }

        function renderBookings(bookings) {
            const tbody = document.getElementById('bookingsTable');
            
            if (!bookings || bookings.length === 0) {
                tbody.innerHTML = '<tr><td colspan="6" style="text-align: center; padding: 40px;">No file</td></tr>';
                return;
            }

            tbody.innerHTML = bookings.map(b => {
                const date = new Date(b.schedule_date);
                const statusClass = b.status === 'confirmed' ? 'confirmed' : b.status === 'pending' ? 'pending' : 'cancelled';
                const statusText = b.status === 'confirmed' ? 'Confirmed' : b.status === 'pending' ? 'Pending' : b.status === 'completed' ? 'Completed' : 'Cancelled';

                let actions = '';
                if (b.status === 'pending') {
                    actions = `
                        <button class="btn btn-primary btn-sm" onclick="approveBooking(${b.booking_id})"><i class="fas fa-check"></i></button>
                        <button class="btn btn-danger btn-sm" onclick="rejectBooking(${b.booking_id})"><i class="fas fa-times"></i></button>
                    `;
                }

                return `
                    <tr>
                        <td>${b.member_name}</td>
                        <td>${b.class_name}</td>
                        <td>${date.getDate()} ${monthNames[date.getMonth()]} ${date.getFullYear()}</td>
                        <td>${b.start_time.substring(0, 5)} - ${b.end_time.substring(0, 5)}</td>
                        <td><span class="booking-status ${statusClass}">${statusText}</span></td>
                        <td class="action-btns">${actions}</td>
                    </tr>
                `;
            }).join('');
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
                    showToast('Booking accepted', 'success');
                    loadBookings();
                } else {
                    showToast(data.message || 'Failed', 'error');
                }
            } catch (error) {
                showToast('There is something wrong.', 'error');
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
                    showToast('Booking succesfully rejected', 'success');
                    loadBookings();
                } else {
                    showToast(data.message || 'Failed', 'error');
                }
            } catch (error) {
                showToast('An error occured', 'error');
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

        document.addEventListener('DOMContentLoaded', loadBookings);
    </script>
</body>
</html>
