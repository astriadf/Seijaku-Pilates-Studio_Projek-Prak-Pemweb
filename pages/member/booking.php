<?php
session_start();
require_once '../../config/database.php';
require_once '../../classes/user.php';
require_once '../../classes/classSchedule.php';
require_once '../../classes/classType.php';

$db = new Database();
$conn = $db->getConnection();
$user = new User($conn);
$schedule = new ClassSchedule($conn);
$classType = new ClassType($conn);

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

$classTypes = $classType->getAll();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Class Booking - Seijaku Studio Pilates</title>
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
                    <a href="booking.php" class="nav-item active">
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
                    <h1>Class Booking</h1>
                    <p>Select available date and class</p>
                </div>
            </div>

            <div class="content-grid">
                <div class="calendar-container">
                    <div class="calendar-header">
                        <h3 id="currentMonth"></h3>
                        <div class="calendar-nav">
                            <button onclick="changeMonth(-1)"><i class="fas fa-chevron-left"></i></button>
                            <button onclick="changeMonth(1)"><i class="fas fa-chevron-right"></i></button>
                        </div>
                    </div>
                    <div class="calendar-days">
                        <span>Sun</span>
                        <span>Mon</span>
                        <span>Tue</span>
                        <span>Wed</span>
                        <span>Thu</span>
                        <span>Fri</span>
                        <span>Sat</span>
                    </div>
                    <div class="calendar-grid" id="calendarGrid"></div>
                    <p style="margin-top: 15px; font-size: 0.85rem; color: var(--text-light);">
                        <i class="fas fa-info-circle"></i> Classes are available every Friday, Saturday & Sunday
                    </p>
                </div>

                <div class="available-classes">
                    <h3 style="margin-bottom: 20px;">Available Classes</h3>
                    <p id="selectedDateText" style="color: var(--text-light); margin-bottom: 20px;">Select a date to see available classes</p>
                    <div id="availableClasses">
                        <div class="empty-state">
                            <i class="fas fa-calendar-day"></i>
                            <h3>Select a Date</h3>
                            <p>Click a date on the calendar to see available classes</p>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <div class="modal" id="bookingModal">
        <div class="modal-content">
            <div class="modal-header">
                <h3>Confirm Booking</h3>
                <button class="modal-close" onclick="closeModal()">&times;</button>
            </div>
            <div class="modal-body">
                <p style="margin-bottom: 20px;">Are you sure you want to book this class?</p>
                <div class="booking-item" id="bookingPreview"></div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" onclick="closeModal()">Cancel</button>
                <button class="btn btn-primary" onclick="confirmBooking()">Confirm Booking</button>
            </div>
        </div>
    </div>

    <div class="toast-container" id="toastContainer"></div>

    <script>
        const API_BASE = '../../api';
        let currentDate = new Date();
        let selectedDate = null;
        let selectedScheduleId = null;

        const classIcons = {
            'mat': '🧘', 'winsor': '💪', 'classical': '✨', 'stott': '🎯',
            'reformer': '🏋️', 'clinical': '🩺', 'contemporary': '🌟'
        };

        const monthNames = ['January', 'February', 'March', 'April', 'May', 'June',
            'July', 'August', 'September', 'October', 'November', 'December'];

        function toggleSidebar() {
            document.getElementById('sidebar').classList.toggle('active');
        }

        function logout() {
            localStorage.removeItem('token');
            localStorage.removeItem('user');
            window.location.href = '../../public/index.php';
        }

        function renderCalendar() {
            const year = currentDate.getFullYear();
            const month = currentDate.getMonth();
            
            document.getElementById('currentMonth').textContent = `${monthNames[month]} ${year}`;
            
            const firstDay = new Date(year, month, 1).getDay();
            const daysInMonth = new Date(year, month + 1, 0).getDate();
            const today = new Date();
            
            let html = '';
            
            for (let i = 0; i < firstDay; i++) {
                html += '<div class="calendar-day disabled"></div>';
            }
            
            for (let day = 1; day <= daysInMonth; day++) {
                const date = new Date(year, month, day);
                const dayOfWeek = date.getDay();
                const isWeekend = dayOfWeek === 0 || dayOfWeek === 5 || dayOfWeek === 6;
                const isPast = date < new Date(today.getFullYear(), today.getMonth(), today.getDate());
                const isToday = date.toDateString() === today.toDateString();
                const isSelected = selectedDate && date.toDateString() === selectedDate.toDateString();
                
                let classes = 'calendar-day';
                if (isPast || !isWeekend) classes += ' disabled';
                if (isToday) classes += ' today';
                if (isSelected) classes += ' selected';
                if (isWeekend && !isPast) classes += ' has-class';
                
                const clickHandler = (!isPast && isWeekend) ? `onclick="selectDate(${year}, ${month}, ${day})"` : '';
                
                html += `<div class="${classes}" ${clickHandler}>${day}</div>`;
            }
            
            document.getElementById('calendarGrid').innerHTML = html;
        }

        function changeMonth(delta) {
            currentDate.setMonth(currentDate.getMonth() + delta);
            renderCalendar();
        }

        async function selectDate(year, month, day) {
            selectedDate = new Date(year, month, day);
            renderCalendar();
            
            const dateStr = `${year}-${String(month + 1).padStart(2, '0')}-${String(day).padStart(2, '0')}`;
            document.getElementById('selectedDateText').textContent = 
                `Classes on ${day} ${monthNames[month]} ${year}`;
            
            try {
                const response = await fetch(`${API_BASE}/classes/available.php?date=${dateStr}`);
                const data = await response.json();
                
                if (data.success && data.data.length > 0) {
                    renderAvailableClasses(data.data);
                } else {
                    document.getElementById('availableClasses').innerHTML = `
                        <div class="empty-state">
                            <i class="fas fa-calendar-times"></i>
                            <h3>No Classes</h3>
                            <p>No classes are available on this date</p>
                        </div>
                    `;
                }
            } catch (error) {
                console.error('Error:', error);
                showToast('Failed to load classes', 'error');
            }
        }

        function renderAvailableClasses(classes) {
            const html = classes.map(cls => `
                <div class="class-slot">
                    <div class="class-slot-info">
                        <div class="class-slot-icon">${classIcons[cls.icon] || '🧘'}</div>
                        <div class="class-slot-details">
                            <h4>${cls.class_name}</h4>
                            <p><i class="fas fa-user"></i> ${cls.instructor_name}</p>
                        </div>
                    </div>
                    <div class="class-slot-meta">
                        <div class="class-slot-time">${cls.start_time.substring(0, 5)}</div>
                        <div class="class-slot-spots">${cls.available_spots} spots left</div>
                        <button class="btn btn-primary btn-sm" style="margin-top: 10px;" 
                            onclick="openBookingModal(${cls.schedule_id}, '${cls.class_name}', '${cls.instructor_name}', '${cls.start_time}', '${cls.end_time}')"
                            ${cls.available_spots <= 0 ? 'disabled' : ''}>
                            ${cls.available_spots <= 0 ? 'Full' : 'Book'}
                        </button>
                    </div>
                </div>
            `).join('');
            
            document.getElementById('availableClasses').innerHTML = html;
        }

        function openBookingModal(scheduleId, className, instructor, startTime, endTime) {
            selectedScheduleId = scheduleId;
            
            const dateStr = selectedDate.getDate() + ' ' + monthNames[selectedDate.getMonth()];
            document.getElementById('bookingPreview').innerHTML = `
                <div class="booking-date">
                    <div class="day">${selectedDate.getDate()}</div>
                    <div class="month">${monthNames[selectedDate.getMonth()].substring(0, 3)}</div>
                </div>
                <div class="booking-details">
                    <h4>${className}</h4>
                    <p><i class="fas fa-clock"></i> ${startTime.substring(0, 5)} - ${endTime.substring(0, 5)}</p>
                    <p><i class="fas fa-user"></i> ${instructor}</p>
                </div>
            `;
            
            document.getElementById('bookingModal').classList.add('active');
        }

        function closeModal() {
            document.getElementById('bookingModal').classList.remove('active');
        }

        async function confirmBooking() {
            if (!selectedDate) {
                showToast('Please select a date first', 'error');
                closeModal();
                return;
            }
            
            const token = localStorage.getItem('token');
            
            const year = selectedDate.getFullYear();
            const month = String(selectedDate.getMonth() + 1).padStart(2, '0');
            const day = String(selectedDate.getDate()).padStart(2, '0');
            const dateStr = `${year}-${month}-${day}`;

            try {
                const response = await fetch(`${API_BASE}/bookings/create.php`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Authorization': `Bearer ${token}`
                    },
                    body: JSON.stringify({ schedule_id: selectedScheduleId, date: dateStr })
                });
                
                const data = await response.json();
                
                if (data.success) {
                    showToast('Booking successful! Waiting for admin confirmation.', 'success');
                    closeModal();
                    selectDate(selectedDate.getFullYear(), selectedDate.getMonth(), selectedDate.getDate());
                } else {
                    showToast(data.message || 'Failed to create booking', 'error');
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

        document.addEventListener('DOMContentLoaded', renderCalendar);
    </script>
</body>
</html>
