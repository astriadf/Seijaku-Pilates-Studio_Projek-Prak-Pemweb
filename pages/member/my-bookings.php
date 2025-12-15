<?php
session_start();
require_once '../../config/database.php';
require_once '../../classes/user.php';
require_once '../../classes/booking.php';


$db = new Database();
$conn = $db->getConnection();
$user = new User($conn);
$booking = new Booking($conn);


$userId = $_SESSION['user_id'] ?? null;
$userData = null;


if ($userId) {
    $userData = $user->getById($userId);
}


$avatarEmojis = [
    'totoro' => '🌳', 'chihiro' => '👧', 'ponyo' => '🐟', 'kiki' => '🧹',
    'satsuki' => '👩', 'sophie' => '👱‍♀️', 'calcifer' => '🔥', 'jiji' => '🐱',
    'kodama' => '👻', 'catbus' => '🚌', 'howl' => '🏰', 'haku' => '🐉'
];
$userAvatar = $avatarEmojis[$userData['avatar'] ?? 'totoro'] ?? '🌳';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Bookings - Seijaku Studio Pilates</title>
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
                    <a href="my-bookings.php" class="nav-item active">
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
                    <a href="profile.php" class="nav-item">
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
                    <h1>My Bookings</h1>
                    <p>View and manage all your class bookings</p>
                </div>
                <div class="top-actions">
                    <a href="booking.php" class="btn btn-primary">
                        <i class="fas fa-plus"></i> New Booking
                    </a>
                </div>
            </div>


            <div class="tabs">
                <button class="tab active" data-tab="upcoming">Upcoming</button>
                <button class="tab" data-tab="pending">Pending</button>
                <button class="tab" data-tab="history">History</button>
            </div>


            <div class="bookings-list" id="bookingsList">
                <div class="empty-state">
                    <i class="fas fa-spinner fa-spin"></i>
                    <p>Loading data...</p>
                </div>
            </div>
        </main>
    </div>


    <div class="modal" id="cancelModal">
        <div class="modal-content">
            <div class="modal-header">
                <h3>Cancel Booking</h3>
                <button class="modal-close" onclick="closeModal()">&times;</button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to cancel this booking?</p>
                <p style="color: var(--text-light); font-size: 0.9rem; margin-top: 10px;">
                    Cancellation cannot be undone.
                </p>
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" onclick="closeModal()">No</button>
                <button class="btn btn-danger" onclick="confirmCancel()">Yes, Cancel</button>
            </div>
        </div>
    </div>


    <div class="toast-container" id="toastContainer"></div>


    <script>
        const API_BASE = '../../api';
        let currentTab = 'upcoming';
        let cancelBookingId = null;


        const monthNames = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 
            'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];


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
                currentTab = this.dataset.tab;
                loadBookings();
            });
        });


        async function loadBookings() {
            const token = localStorage.getItem('token');
            let status = '';
            
            if (currentTab === 'upcoming') status = 'approved';
            else if (currentTab === 'pending') status = 'pending';
            else status = 'completed,cancelled';


            try {
                const response = await fetch(`${API_BASE}/bookings/user.php?status=${status}`, {
                    headers: { 'Authorization': `Bearer ${token}` }
                });
                const data = await response.json();


                if (data.success) {
                    renderBookings(data.data);
                } else {
                    showEmptyState();
                }
            } catch (error) {
                console.error('Error:', error);
                showEmptyState();
            }
        }


        function renderBookings(bookings) {
            const container = document.getElementById('bookingsList');
            
            if (!bookings || bookings.length === 0) {
                showEmptyState();
                return;
            }


            container.innerHTML = bookings.map(b => {
                const date = new Date(b.class_date);
                const statusClass = b.booking_status === 'approved' ? 'confirmed' : 
                    b.booking_status === 'pending' ? 'pending' : 'cancelled';
                const statusText = b.booking_status === 'approved' ? 'Confirmed' :
                    b.booking_status === 'pending' ? 'Pending' :
                    b.booking_status === 'completed' ? 'Completed' : 'Cancelled';


                const canCancel = ['approved', 'pending'].includes(b.booking_status) && 
                    new Date(b.class_date) > new Date();


                return `
                    <div class="booking-card">
                        <div class="booking-card-date">
                            <div class="day">${date.getDate()}</div>
                            <div class="month">${monthNames[date.getMonth()]}</div>
                            <div class="year">${date.getFullYear()}</div>
                        </div>
                        <div class="booking-card-info">
                            <h4>${b.class_name}</h4>
                            <p><i class="fas fa-clock"></i> ${b.start_time.substring(0, 5)} - ${b.end_time.substring(0, 5)}</p>
                            <p><i class="fas fa-user"></i> ${b.instructor_name}</p>
                        </div>
                        <div class="booking-card-actions">
                            <span class="booking-status ${statusClass}">${statusText}</span>
                            ${canCancel ? `<button class="btn btn-secondary btn-sm" onclick="openCancelModal(${b.booking_id})">Cancel</button>` : ''}
                        </div>
                    </div>
                `;
            }).join('');
        }


        function showEmptyState() {
            const messages = {
                upcoming: { icon: 'calendar-check', title: 'No Bookings', text: 'You do not have any upcoming bookings' },
                pending: { icon: 'clock', title: 'No Pending Bookings', text: 'There are no bookings waiting for confirmation' },
                history: { icon: 'history', title: 'No History Yet', text: 'Your booking history will appear here' }
            };
            
            const msg = messages[currentTab];
            document.getElementById('bookingsList').innerHTML = `
                <div class="empty-state">
                    <i class="fas fa-${msg.icon}"></i>
                    <h3>${msg.title}</h3>
                    <p>${msg.text}</p>
                    <a href="booking.php" class="btn btn-primary">Book a Class</a>
                </div>
            `;
        }


        function openCancelModal(bookingId) {
            cancelBookingId = bookingId;
            document.getElementById('cancelModal').classList.add('active');
        }


        function closeModal() {
            document.getElementById('cancelModal').classList.remove('active');
        }


        async function confirmCancel() {
            const token = localStorage.getItem('token');
            
            try {
                const response = await fetch(`${API_BASE}/bookings/cancel.php`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Authorization': `Bearer ${token}`
                    },
                    body: JSON.stringify({ booking_id: cancelBookingId })
                });
                
                const data = await response.json();
                
                if (data.success) {
                    showToast('Booking has been successfully cancelled', 'success');
                    closeModal();
                    loadBookings();
                } else {
                    showToast(data.message || 'Failed to cancel booking', 'error');
                }
            } catch (error) {
                showToast('An error occurred', 'error');
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
