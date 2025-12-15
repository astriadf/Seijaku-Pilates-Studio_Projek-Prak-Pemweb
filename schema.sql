SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+07:00";

-- Create database
CREATE DATABASE IF NOT EXISTS `seijaku_pilates` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `seijaku_pilates`;

-- =====================================================
-- TABLE: users
-- =====================================================
DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
    `user_id` INT AUTO_INCREMENT PRIMARY KEY,
    `email` VARCHAR(255) NOT NULL UNIQUE,
    `password_hash` VARCHAR(255) NOT NULL,
    `full_name` VARCHAR(100) NOT NULL,
    `phone` VARCHAR(20),
    `role` ENUM('admin', 'instructor', 'member') DEFAULT 'member',
    `status` ENUM('active', 'inactive', 'suspended') DEFAULT 'active',
    `avatar` VARCHAR(50) DEFAULT 'totoro',
    `profile_picture` VARCHAR(255),
    `total_bookings` INT DEFAULT 0,
    `total_classes_attended` INT DEFAULT 0,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    `last_login` TIMESTAMP NULL,
    INDEX `idx_email` (`email`),
    INDEX `idx_role` (`role`),
    INDEX `idx_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- TABLE: class_types 
-- =====================================================
DROP TABLE IF EXISTS `class_types`;
CREATE TABLE `class_types` (
    `class_type_id` INT AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(100) NOT NULL,
    `slug` VARCHAR(100) NOT NULL UNIQUE,
    `description` TEXT,
    `difficulty_level` ENUM('beginner', 'intermediate', 'advanced') NOT NULL,
    `duration_minutes` INT DEFAULT 60,
    `icon` VARCHAR(50) DEFAULT 'yoga',
    `color` VARCHAR(20) DEFAULT '#9FBF9E',
    `price` DECIMAL(10,2) DEFAULT 75000.00,
    `is_active` BOOLEAN DEFAULT TRUE,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX `idx_difficulty` (`difficulty_level`),
    INDEX `idx_active` (`is_active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- TABLE: instructors
-- =====================================================
DROP TABLE IF EXISTS `instructors`;
CREATE TABLE `instructors` (
    `instructor_id` INT AUTO_INCREMENT PRIMARY KEY,
    `user_id` INT NOT NULL,
    `specialization` VARCHAR(255),
    `bio` TEXT,
    `experience_years` INT DEFAULT 0,
    `certification` VARCHAR(255),
    `instagram` VARCHAR(100),
    `rating` DECIMAL(2,1) DEFAULT 5.0,
    `total_classes` INT DEFAULT 0,
    `is_active` BOOLEAN DEFAULT TRUE,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`user_id`) REFERENCES `users`(`user_id`) ON DELETE CASCADE,
    INDEX `idx_active` (`is_active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- TABLE: class_schedules 
-- =====================================================
DROP TABLE IF EXISTS `class_schedules`;
CREATE TABLE `class_schedules` (
    `schedule_id` INT AUTO_INCREMENT PRIMARY KEY,
    `class_type_id` INT NOT NULL,
    `instructor_id` INT NOT NULL,
    `day_of_week` ENUM('friday', 'saturday', 'sunday') NOT NULL,
    `start_time` TIME NOT NULL,
    `end_time` TIME NOT NULL,
    `capacity` INT DEFAULT 10,
    `level` ENUM('beginner', 'intermediate', 'advanced') NOT NULL,
    `is_active` BOOLEAN DEFAULT TRUE,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`class_type_id`) REFERENCES `class_types`(`class_type_id`) ON DELETE CASCADE,
    FOREIGN KEY (`instructor_id`) REFERENCES `instructors`(`instructor_id`) ON DELETE CASCADE,
    INDEX `idx_day` (`day_of_week`),
    INDEX `idx_active` (`is_active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- TABLE: class_instances 
-- =====================================================
DROP TABLE IF EXISTS `class_instances`;
CREATE TABLE `class_instances` (
    `instance_id` INT AUTO_INCREMENT PRIMARY KEY,
    `schedule_id` INT NOT NULL,
    `class_date` DATE NOT NULL,
    `start_time` TIME NOT NULL,
    `end_time` TIME NOT NULL,
    `capacity` INT DEFAULT 10,
    `current_bookings` INT DEFAULT 0,
    `status` ENUM('scheduled', 'ongoing', 'completed', 'cancelled') DEFAULT 'scheduled',
    `notes` TEXT,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`schedule_id`) REFERENCES `class_schedules`(`schedule_id`) ON DELETE CASCADE,
    UNIQUE KEY `unique_schedule_date` (`schedule_id`, `class_date`),
    INDEX `idx_date` (`class_date`),
    INDEX `idx_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- TABLE: bookings
-- =====================================================
DROP TABLE IF EXISTS `bookings`;
CREATE TABLE `bookings` (
    `booking_id` INT AUTO_INCREMENT PRIMARY KEY,
    `user_id` INT NOT NULL,
    `instance_id` INT NOT NULL,
    `booking_status` ENUM('pending', 'approved', 'rejected', 'cancelled', 'completed') DEFAULT 'pending',
    `booking_date` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `approved_by` INT NULL,
    `approved_at` TIMESTAMP NULL,
    `cancelled_at` TIMESTAMP NULL,
    `cancellation_reason` TEXT,
    `notes` TEXT,
    `attended` BOOLEAN DEFAULT FALSE,
    FOREIGN KEY (`user_id`) REFERENCES `users`(`user_id`) ON DELETE CASCADE,
    FOREIGN KEY (`instance_id`) REFERENCES `class_instances`(`instance_id`) ON DELETE CASCADE,
    FOREIGN KEY (`approved_by`) REFERENCES `users`(`user_id`) ON DELETE SET NULL,
    INDEX `idx_user` (`user_id`),
    INDEX `idx_status` (`booking_status`),
    INDEX `idx_date` (`booking_date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- TABLE: videos
-- =====================================================
DROP TABLE IF EXISTS `videos`;
CREATE TABLE `videos` (
    `video_id` INT AUTO_INCREMENT PRIMARY KEY,
    `title` VARCHAR(255) NOT NULL,
    `description` TEXT,
    `youtube_url` VARCHAR(500),
    `thumbnail_url` VARCHAR(500),
    `duration_minutes` INT DEFAULT 0,
    `level` ENUM('beginner', 'intermediate', 'advanced') DEFAULT 'beginner',
    `category` VARCHAR(100),
    `view_count` INT DEFAULT 0,
    `is_featured` BOOLEAN DEFAULT FALSE,
    `is_active` BOOLEAN DEFAULT TRUE,
    `created_by` INT,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`created_by`) REFERENCES `users`(`user_id`) ON DELETE SET NULL,
    INDEX `idx_level` (`level`),
    INDEX `idx_featured` (`is_featured`),
    INDEX `idx_active` (`is_active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- TABLE: reviews (testimonials)
-- =====================================================
DROP TABLE IF EXISTS `reviews`;
CREATE TABLE `reviews` (
    `review_id` INT AUTO_INCREMENT PRIMARY KEY,
    `user_id` INT NULL,
    `guest_name` VARCHAR(100),
    `rating` INT NOT NULL CHECK (`rating` >= 1 AND `rating` <= 5),
    `review_text` TEXT NOT NULL,
    `is_approved` BOOLEAN DEFAULT TRUE,
    `is_featured` BOOLEAN DEFAULT FALSE,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`user_id`) REFERENCES `users`(`user_id`) ON DELETE SET NULL,
    INDEX `idx_approved` (`is_approved`),
    INDEX `idx_featured` (`is_featured`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- TABLE: achievements
-- =====================================================
DROP TABLE IF EXISTS `achievements`;
CREATE TABLE `achievements` (
    `achievement_id` INT AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(100) NOT NULL,
    `description` TEXT,
    `icon` VARCHAR(50),
    `badge_image` VARCHAR(255),
    `character_name` VARCHAR(50),
    `required_classes` INT DEFAULT 0,
    `affirmation` TEXT,
    `appreciation` TEXT,
    `color` VARCHAR(20) DEFAULT '#9FBF9E',
    `sort_order` INT DEFAULT 0,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- TABLE: user_achievements
-- =====================================================
DROP TABLE IF EXISTS `user_achievements`;
CREATE TABLE `user_achievements` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `user_id` INT NOT NULL,
    `achievement_id` INT NOT NULL,
    `earned_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`user_id`) REFERENCES `users`(`user_id`) ON DELETE CASCADE,
    FOREIGN KEY (`achievement_id`) REFERENCES `achievements`(`achievement_id`) ON DELETE CASCADE,
    UNIQUE KEY `unique_user_achievement` (`user_id`, `achievement_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- TABLE: notifications
-- =====================================================
DROP TABLE IF EXISTS `notifications`;
CREATE TABLE `notifications` (
    `notification_id` INT AUTO_INCREMENT PRIMARY KEY,
    `user_id` INT NOT NULL,
    `type` VARCHAR(50) NOT NULL,
    `title` VARCHAR(255) NOT NULL,
    `message` TEXT,
    `is_read` BOOLEAN DEFAULT FALSE,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`user_id`) REFERENCES `users`(`user_id`) ON DELETE CASCADE,
    INDEX `idx_user_read` (`user_id`, `is_read`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- TABLE: contact_messages
-- =====================================================
DROP TABLE IF EXISTS `contact_messages`;
CREATE TABLE `contact_messages` (
    `message_id` INT AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(100) NOT NULL,
    `email` VARCHAR(255) NOT NULL,
    `message` TEXT NOT NULL,
    `is_read` BOOLEAN DEFAULT FALSE,
    `replied_at` TIMESTAMP NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX `idx_read` (`is_read`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- INSERT DEFAULT DATA
-- =====================================================

-- Default Users (admin1, instructor1-10, member1)
INSERT INTO `users` (`email`, `password_hash`, `full_name`, `phone`, `role`, `status`, `avatar`) VALUES
('admin1@seijaku.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Admin Seijaku', '+62 812 3456 7890', 'admin', 'active', 'totoro'),
('instructor1@seijaku.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Sakura Tanaka', '+62 812 1111 0001', 'instructor', 'active', 'chihiro'),
('instructor2@seijaku.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Yuki Yamamoto', '+62 812 1111 0002', 'instructor', 'active', 'ponyo'),
('instructor3@seijaku.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Hana Suzuki', '+62 812 1111 0003', 'instructor', 'active', 'kiki'),
('instructor4@seijaku.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Mei Nakamura', '+62 812 1111 0004', 'instructor', 'active', 'satsuki'),
('instructor5@seijaku.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Aoi Watanabe', '+62 812 1111 0005', 'instructor', 'active', 'sophie'),
('instructor6@seijaku.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Rin Kobayashi', '+62 812 1111 0006', 'instructor', 'active', 'sheeta'),
('instructor7@seijaku.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Kaede Ito', '+62 812 1111 0007', 'instructor', 'active', 'nausicaa'),
('instructor8@seijaku.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Momiji Sato', '+62 812 1111 0008', 'instructor', 'active', 'san'),
('instructor9@seijaku.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Tsubaki Honda', '+62 812 1111 0009', 'instructor', 'active', 'arrietty'),
('instructor10@seijaku.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Ayame Kimura', '+62 812 1111 0010', 'instructor', 'active', 'marnie'),
('instructor11@seijaku.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Hiroshi Tanaka', '+62 812 1111 0011', 'instructor', 'active', 'haku'),
('member1@seijaku.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Astria Dina Fitri', '+62 812 9999 0001', 'member', 'active', 'totoro');

-- Instructors data
INSERT INTO `instructors` (`user_id`, `specialization`, `bio`, `experience_years`, `certification`, `instagram`, `rating`) VALUES
(2, 'Mat Pilates, Classical Pilates', 'Instruktur bersertifikat dengan pengalaman 8 tahun dalam pilates klasik dan mat. Fokus pada teknik pernapasan dan keselarasan tubuh.', 8, 'Balanced Body Mat Instructor', '@sakura.pilates', 4.9),
(3, 'Reformer Pilates, STOTT Pilates', 'Spesialis reformer dengan pendekatan STOTT yang modern. Membantu klien mencapai kekuatan inti optimal.', 6, 'STOTT Pilates Certified', '@yuki.reformer', 4.8),
(4, 'Clinical Pilates, Rehabilitasi', 'Fisioterapis bersertifikat dengan keahlian pilates klinis untuk rehabilitasi dan pemulihan cedera.', 10, 'Clinical Pilates Certification', '@hana.clinical', 5.0),
(5, 'Contemporary Pilates, Winsor Pilates', 'Menggabungkan teknik kontemporer dengan pendekatan Winsor untuk hasil yang dinamis dan efektif.', 5, 'Winsor Pilates Certified', '@mei.contemporary', 4.7),
(6, 'Mat Pilates, Pilates untuk Pemula', 'Ahli dalam mengajar pemula dengan pendekatan yang sabar dan mendukung. Ideal untuk yang baru memulai.', 4, 'Comprehensive Pilates Instructor', '@aoi.beginner', 4.9),
(7, 'Reformer Pilates, Advanced Training', 'Instruktur level advanced dengan fokus pada gerakan kompleks dan tantangan lanjutan.', 7, 'Peak Pilates Certified', '@rin.advanced', 4.8),
(8, 'STOTT Pilates, Posture Correction', 'Spesialis koreksi postur menggunakan metode STOTT. Membantu memperbaiki alignment tubuh.', 6, 'STOTT Full Certification', '@kaede.posture', 4.9),
(9, 'Classical Pilates, Core Strength', 'Fokus pada penguatan inti dengan metode pilates klasik autentik dari Joseph Pilates.', 9, 'Classical Pilates Master', '@momiji.core', 5.0),
(10, 'Winsor Pilates, Weight Management', 'Membantu klien mencapai berat badan ideal melalui program Winsor Pilates yang dinamis.', 5, 'Winsor Pilates Instructor', '@tsubaki.winsor', 4.6),
(11, 'Clinical Pilates, Senior Fitness', 'Spesialis pilates untuk usia 50+ dengan fokus pada mobilitas dan kesehatan tulang.', 8, 'Senior Fitness Specialist', '@ayame.senior', 4.8),
(12, 'Power Pilates, Athletic Training', 'Instruktur pilates pria pertama di Seijaku dengan spesialisasi Power Pilates untuk atlet dan fitness enthusiast.', 7, 'Power Pilates Master Trainer', '@hiroshi.power', 4.9);

-- Class Types (7 jenis)
INSERT INTO `class_types` (`name`, `slug`, `description`, `difficulty_level`, `duration_minutes`, `icon`, `color`, `price`) VALUES
('Mat Pilates', 'mat-pilates', 'Latihan pilates di atas matras dengan fokus pada kekuatan inti dan fleksibilitas. Cocok untuk semua level.', 'beginner', 60, 'mat', '#9FBF9E', 75000.00),
('Winsor Pilates', 'winsor-pilates', 'Fokus pada pembentukan tubuh dan penurunan berat badan melalui gerakan dinamis dan efektif.', 'beginner', 60, 'winsor', '#A8DADC', 75000.00),
('Classical Pilates', 'classical-pilates', 'Metode pilates tradisional yang dikembangkan oleh Joseph Pilates dengan fokus pada kontrol, presisi, dan pernapasan.', 'intermediate', 60, 'classical', '#D4C5E2', 85000.00),
('STOTT Pilates', 'stott-pilates', 'Pendekatan modern dengan penekanan pada penyelarasan tulang belakang dan stabilitas.', 'intermediate', 60, 'stott', '#F4D9C6', 85000.00),
('Reformer Pilates', 'reformer-pilates', 'Latihan menggunakan mesin reformer untuk resistensi dan gerakan yang lebih terkontrol.', 'intermediate', 60, 'reformer', '#FFB7C5', 100000.00),
('Clinical Pilates', 'clinical-pilates', 'Pilates yang dirancang untuk rehabilitasi dan pemulihan cedera dengan pengawasan khusus.', 'advanced', 60, 'clinical', '#87CEEB', 120000.00),
('Contemporary Pilates', 'contemporary-pilates', 'Evolusi modern dari pilates klasik dengan penggabungan teknik-teknik terbaru dan inovatif.', 'advanced', 60, 'contemporary', '#DDA0DD', 100000.00),
('Power Pilates', 'power-pilates', 'High intensity pilates for maximum strength and endurance. Latihan pilates intensitas tinggi untuk kekuatan dan daya tahan maksimal.', 'advanced', 60, 'power', '#FF6B35', 100000.00),
('Prenatal Pilates', 'prenatal-pilates', 'Safe and gentle pilates for pregnant women. Pilates yang aman dan lembut untuk ibu hamil.', 'beginner', 45, 'prenatal', '#FFB6C1', 85000.00),
('Therapeutic Pilates', 'therapeutic-pilates', 'Gentle rehabilitation pilates for recovery. Pilates rehabilitasi lembut untuk pemulihan.', 'beginner', 50, 'therapeutic', '#98D8C8', 90000.00);

-- Class Schedules (Jumat, Sabtu, Minggu - 07:00-11:00, 14:00-17:00, 19:00-21:00)
-- Friday schedules
INSERT INTO `class_schedules` (`class_type_id`, `instructor_id`, `day_of_week`, `start_time`, `end_time`, `capacity`, `level`) VALUES
-- Morning sessions Friday
(1, 6, 'friday', '07:00:00', '08:00:00', 10, 'beginner'),
(2, 10, 'friday', '08:00:00', '09:00:00', 10, 'beginner'),
(3, 1, 'friday', '09:00:00', '10:00:00', 10, 'intermediate'),
(5, 2, 'friday', '10:00:00', '11:00:00', 8, 'intermediate'),
-- Afternoon sessions Friday
(4, 8, 'friday', '14:00:00', '15:00:00', 10, 'intermediate'),
(1, 6, 'friday', '15:00:00', '16:00:00', 10, 'beginner'),
(6, 4, 'friday', '16:00:00', '17:00:00', 6, 'advanced'),
-- Evening sessions Friday
(7, 5, 'friday', '19:00:00', '20:00:00', 8, 'advanced'),
(3, 9, 'friday', '20:00:00', '21:00:00', 10, 'intermediate'),

-- Saturday schedules
(1, 6, 'saturday', '07:00:00', '08:00:00', 10, 'beginner'),
(2, 10, 'saturday', '08:00:00', '09:00:00', 10, 'beginner'),
(5, 2, 'saturday', '09:00:00', '10:00:00', 8, 'intermediate'),
(3, 1, 'saturday', '10:00:00', '11:00:00', 10, 'intermediate'),
-- Afternoon Saturday
(4, 8, 'saturday', '14:00:00', '15:00:00', 10, 'intermediate'),
(6, 4, 'saturday', '15:00:00', '16:00:00', 6, 'advanced'),
(7, 5, 'saturday', '16:00:00', '17:00:00', 8, 'advanced'),
-- Evening Saturday
(1, 6, 'saturday', '19:00:00', '20:00:00', 10, 'beginner'),
(5, 7, 'saturday', '20:00:00', '21:00:00', 8, 'intermediate'),

-- Sunday schedules
(2, 10, 'sunday', '07:00:00', '08:00:00', 10, 'beginner'),
(1, 6, 'sunday', '08:00:00', '09:00:00', 10, 'beginner'),
(4, 8, 'sunday', '09:00:00', '10:00:00', 10, 'intermediate'),
(3, 9, 'sunday', '10:00:00', '11:00:00', 10, 'intermediate'),
-- Afternoon Sunday
(5, 2, 'sunday', '14:00:00', '15:00:00', 8, 'intermediate'),
(6, 4, 'sunday', '15:00:00', '16:00:00', 6, 'advanced'),
(7, 5, 'sunday', '16:00:00', '17:00:00', 8, 'advanced'),
-- Evening Sunday
(3, 1, 'sunday', '19:00:00', '20:00:00', 10, 'intermediate'),
(1, 6, 'sunday', '20:00:00', '21:00:00', 10, 'beginner');

-- Videos
INSERT INTO `videos` (`title`, `description`, `youtube_url`, `thumbnail_url`, `duration_minutes`, `level`, `category`, `view_count`, `is_featured`, `created_by`) VALUES
('Pengenalan Pilates untuk Pemula', 'Pelajari dasar-dasar pilates dengan gerakan sederhana yang aman untuk pemula.', 'https://youtu.be/jRFpuiIhKAQ', NULL, 15, 'beginner', 'Mat Pilates', 1250, TRUE, 1),
('Mat Pilates: Kekuatan Inti', 'Fokus pada penguatan otot inti dengan serangkaian gerakan mat pilates yang efektif.', 'https://youtu.be/Zma_7kh-FGA', NULL, 20, 'intermediate', 'Mat Pilates', 890, TRUE, 1),
('Reformer Basics', 'Pengenalan penggunaan mesin reformer untuk latihan pilates yang lebih intens.', 'https://youtu.be/WnSr8w4QEWo', NULL, 25, 'beginner', 'Reformer Pilates', 650, FALSE, 1),
('Clinical Pilates untuk Nyeri Punggung', 'Gerakan therapeutic untuk membantu mengurangi nyeri punggung bawah.', 'https://youtu.be/ypkrDdd61wk', NULL, 18, 'beginner', 'Clinical Pilates', 2100, TRUE, 1),
('Contemporary Flow', 'Aliran gerakan kontemporer yang menggabungkan pilates modern dengan elemen yoga.', 'https://youtu.be/WnSr8w4QEWo', NULL, 30, 'advanced', 'Contemporary Pilates', 450, FALSE, 1),
('STOTT Pilates: Postur Sempurna', 'Latihan untuk memperbaiki postur tubuh dengan metode STOTT.', 'https://youtu.be/Zma_7kh-FGA', NULL, 22, 'intermediate', 'STOTT Pilates', 780, FALSE, 1),
('Winsor Pilates: Fat Burning', 'Sesi pembakaran kalori intensif dengan teknik Winsor Pilates.', 'https://youtu.be/nIHtsrP15ik', NULL, 35, 'intermediate', 'Winsor Pilates', 1500, TRUE, 1),
('Classical Pilates Fundamentals', 'Gerakan dasar pilates klasik yang autentik dari Joseph Pilates.', 'https://youtu.be/jRFpuiIhKAQ', NULL, 28, 'beginner', 'Classical Pilates', 920, FALSE, 1),
('Advanced Core Challenge', 'Tantangan inti level lanjutan untuk yang sudah berpengalaman.', 'https://youtu.be/Zma_7kh-FGA', NULL, 40, 'advanced', 'Mat Pilates', 380, FALSE, 1),
('Pilates untuk Fleksibilitas', 'Tingkatkan fleksibilitas tubuh dengan gerakan peregangan pilates.', 'https://youtu.be/WnSr8w4QEWo', NULL, 20, 'beginner', 'Mat Pilates', 1100, TRUE, 1);

-- Achievements (Badges Ghibli)
INSERT INTO `achievements` (`name`, `description`, `icon`, `character_name`, `required_classes`, `affirmation`, `appreciation`, `color`, `sort_order`) VALUES
('Langkah Pertama Totoro', 'Selamat telah menghadiri kelas pertama Anda! Seperti Totoro yang menjaga hutan, Anda telah memulai perjalanan menjaga kesehatan tubuh.', '🌱', 'Totoro', 1, 'Setiap perjalanan dimulai dengan satu langkah. Anda sudah melakukannya!', 'Terima kasih telah mempercayakan langkah pertama Anda kepada Seijaku. Totoro bangga padamu!', '#9FBF9E', 1),
('Perjalanan Chihiro', 'Anda telah menghadiri 10 kelas! Seperti Chihiro yang berani menghadapi dunia roh, Anda menunjukkan keberanian dalam perjalanan fitness.', '🌸', 'Chihiro', 10, 'Keberanian adalah melakukan hal yang benar meski takut. Anda sudah membuktikannya!', 'Seperti Chihiro yang menemukan kekuatannya, Anda juga menemukan potensi dalam diri. Luar biasa!', '#FFB7C5', 2),
('Semangat Calcifer', 'Anda telah menghadiri 25 kelas! Api semangat Anda menyala seperti Calcifer - penuh energi dan dedikasi!', '🔥', 'Calcifer', 25, 'Api dalam diri Anda tidak pernah padam. Terus jaga semangat itu!', 'Dedikasi Anda menginspirasi seperti api Calcifer yang menjaga kastil. Terus bersinar!', '#FF6B35', 3),
('Ketekunan Ponyo', 'Anda telah menghadiri 40 kelas! Seperti Ponyo yang pantang menyerah untuk menjadi manusia, ketekunan Anda luar biasa!', '🐟', 'Ponyo', 40, 'Ketekunan mengalahkan bakat ketika bakat tidak tekun. Anda adalah buktinya!', 'Ponyo akan sangat bangga dengan ketekunan Anda. Terus berenang menuju tujuan!', '#4ECDC4', 4),
('Penguasa Howl', 'Anda telah menghadiri 60 kelas! Seperti Howl yang menguasai sihir, Anda telah menguasai seni pilates!', '🏰', 'Howl', 60, 'Penguasaan datang dari latihan yang konsisten. Anda adalah master sejati!', 'Howl mengakui kehebatan Anda. Kastil bergerak untuk merayakan pencapaian ini!', '#8B5CF6', 5),
('Penjaga Hutan Kodama', 'Anda telah menghadiri 100 kelas! Seperti Kodama yang menjaga keseimbangan alam, Anda telah mencapai keseimbangan sempurna.', '🌲', 'Kodama', 100, 'Anda adalah legenda hidup. Keseimbangan tubuh dan pikiran telah tercapai.', 'Para Kodama berbisik tentang kehebatan Anda di seluruh hutan. Terima kasih telah menjadi inspirasi!', '#228B22', 6);

-- Default Reviews
INSERT INTO `reviews` (`user_id`, `guest_name`, `rating`, `review_text`, `is_approved`, `is_featured`) VALUES
(12, NULL, 5, 'Studio yang sangat nyaman dengan instruktur yang ramah dan profesional. Sangat direkomendasikan!', TRUE, TRUE),
(NULL, 'Dewi Lestari', 5, 'Setelah 3 bulan latihan di Seijaku, nyeri punggung saya berkurang drastis. Terima kasih!', TRUE, TRUE),
(NULL, 'Budi Santoso', 4, 'Jadwalnya sangat fleksibel dan cocok dengan kesibukan saya. Kelas pilates terbaik di Purbalingga!', TRUE, TRUE),
(NULL, 'Rina Wijaya', 5, 'Suasana studio seperti di film Ghibli, sangat menenangkan. Instruktur Sakura sangat sabar!', TRUE, FALSE),
(NULL, 'Ahmad Hidayat', 5, 'Reformer pilates di sini luar biasa! Mesin berkualitas dan instruktur yang ahli.', TRUE, FALSE),
(NULL, 'Maya Kusuma', 4, 'Saya pemula dan merasa sangat nyaman belajar di sini. Tidak ada tekanan, hanya dukungan!', TRUE, TRUE);

COMMIT;
