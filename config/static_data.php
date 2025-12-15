<?php
class StaticData {
    public static function getClassTypes() {
        return [
            ['class_type_id' => 1, 'name' => 'Mat Pilates', 'description' => 'Classic floor-based exercises using body weight for resistance. Perfect for beginners to build core strength and flexibility.', 'level' => 'beginner', 'duration' => 60, 'image_url' => 'images/classes/mat-pilates.jpg'],
            ['class_type_id' => 2, 'name' => 'Reformer Pilates', 'description' => 'Equipment-based training using the reformer machine for controlled resistance movements.', 'level' => 'intermediate', 'duration' => 55, 'image_url' => 'images/classes/reformer-pilates.jpg'],
            ['class_type_id' => 3, 'name' => 'Clinical Pilates', 'description' => 'Rehabilitation-focused sessions designed for injury recovery and prevention.', 'level' => 'all', 'duration' => 45, 'image_url' => 'images/classes/clinical-pilates.jpg'],
            ['class_type_id' => 4, 'name' => 'Barre Pilates', 'description' => 'Ballet-inspired workout combining Pilates principles with barre exercises.', 'level' => 'intermediate', 'duration' => 50, 'image_url' => 'images/classes/barre-pilates.jpg'],
            ['class_type_id' => 5, 'name' => 'Hot Pilates', 'description' => 'Intense Pilates session in a heated room for deeper stretching and detoxification.', 'level' => 'advanced', 'duration' => 60, 'image_url' => 'images/classes/hot-pilates.jpg'],
            ['class_type_id' => 6, 'name' => 'Aerial Pilates', 'description' => 'Suspended Pilates using silk hammocks for decompression and core strengthening.', 'level' => 'advanced', 'duration' => 55, 'image_url' => 'images/classes/aerial-pilates.jpg'],
            ['class_type_id' => 7, 'name' => 'Cadillac Pilates', 'description' => 'Full-body workout using the Cadillac trapeze table for advanced movements.', 'level' => 'advanced', 'duration' => 60, 'image_url' => 'images/classes/cadillac-pilates.jpg'],
            ['class_type_id' => 8, 'name' => 'Power Pilates', 'description' => 'High-intensity Pilates combining cardio elements for maximum calorie burn.', 'level' => 'intermediate', 'duration' => 45, 'image_url' => 'images/classes/power-pilates.jpg'],
            ['class_type_id' => 9, 'name' => 'Prenatal Pilates', 'description' => 'Gentle, safe exercises designed specifically for expecting mothers.', 'level' => 'beginner', 'duration' => 45, 'image_url' => 'images/classes/prenatal-pilates.jpg'],
            ['class_type_id' => 10, 'name' => 'Therapeutic Pilates', 'description' => 'Targeted sessions for chronic pain management and postural correction.', 'level' => 'all', 'duration' => 50, 'image_url' => 'images/classes/therapeutic-pilates.jpg']
        ];
    }

    public static function getSchedules() {
        $schedules = [];
        $classNames = ['Mat Pilates', 'Reformer Pilates', 'Clinical Pilates', 'Barre Pilates', 'Hot Pilates'];
        $instructors = ['Yuki Sakamoto', 'Kenji Yamamoto', 'Aiko Tanaka', 'Mei Suzuki', 'Haruto Sato'];
        $times = ['09:00:00', '10:30:00', '14:00:00', '16:00:00', '18:00:00'];
        
        $baseDate = date('Y-m-d');
        for ($i = 0; $i < 7; $i++) {
            $date = date('Y-m-d', strtotime($baseDate . " +$i days"));
            foreach ([0, 2, 4] as $j => $timeIdx) {
                $schedules[] = [
                    'schedule_id' => count($schedules) + 1,
                    'class_type_id' => ($i % 5) + 1,
                    'class_name' => $classNames[$i % 5],
                    'instructor_name' => $instructors[$i % 5],
                    'schedule_date' => $date,
                    'start_time' => $times[$timeIdx],
                    'end_time' => date('H:i:s', strtotime($times[$timeIdx]) + 3600),
                    'max_capacity' => 12,
                    'current_bookings' => rand(3, 10),
                    'room' => 'Studio ' . chr(65 + ($i % 3))
                ];
            }
        }
        return $schedules;
    }

    public static function getInstructors() {
        return [
            ['instructor_id' => 1, 'user_id' => 2, 'first_name' => 'Yuki', 'last_name' => 'Sakamoto', 'email' => 'instructor1@seijaku.com', 'bio' => 'Certified Pilates instructor with over 10 years of experience in mat and reformer techniques.', 'specialization' => 'Mat Pilates, Reformer', 'certification' => 'BASI Pilates Certified', 'years_experience' => 10, 'profile_image' => 'images/instructors/yuki-sakamoto.jpg'],
            ['instructor_id' => 2, 'user_id' => 3, 'first_name' => 'Kenji', 'last_name' => 'Yamamoto', 'email' => 'instructor2@seijaku.com', 'bio' => 'Former professional dancer turned Pilates specialist focusing on barre and aerial techniques.', 'specialization' => 'Barre Pilates, Aerial', 'certification' => 'Stott Pilates Certified', 'years_experience' => 8, 'profile_image' => 'images/instructors/kenji-yamamoto.jpg'],
            ['instructor_id' => 3, 'user_id' => 4, 'first_name' => 'Aiko', 'last_name' => 'Tanaka', 'email' => 'instructor3@seijaku.com', 'bio' => 'Clinical Pilates specialist with a background in physical therapy and rehabilitation.', 'specialization' => 'Clinical Pilates, Therapeutic', 'certification' => 'DMA Clinical Pilates', 'years_experience' => 12, 'profile_image' => 'images/instructors/aiko-tanaka.jpg'],
            ['instructor_id' => 4, 'user_id' => 5, 'first_name' => 'Mei', 'last_name' => 'Suzuki', 'email' => 'instructor4@seijaku.com', 'bio' => 'Hot Pilates enthusiast bringing energy and intensity to every class.', 'specialization' => 'Hot Pilates, Power Pilates', 'certification' => 'PMA Certified', 'years_experience' => 6, 'profile_image' => 'images/instructors/mei-suzuki.jpg'],
            ['instructor_id' => 5, 'user_id' => 6, 'first_name' => 'Haruto', 'last_name' => 'Sato', 'email' => 'instructor5@seijaku.com', 'bio' => 'Cadillac and equipment specialist with expertise in advanced Pilates techniques.', 'specialization' => 'Cadillac, Reformer', 'certification' => 'Peak Pilates Certified', 'years_experience' => 9, 'profile_image' => 'images/instructors/haruto-sato.jpg'],
            ['instructor_id' => 6, 'user_id' => 7, 'first_name' => 'Sakura', 'last_name' => 'Watanabe', 'email' => 'instructor6@seijaku.com', 'bio' => 'Prenatal and postnatal Pilates expert helping mothers stay fit and healthy.', 'specialization' => 'Prenatal Pilates, Mat', 'certification' => 'Pre/Postnatal Pilates Certified', 'years_experience' => 7, 'profile_image' => 'images/instructors/sakura-watanabe.jpg'],
            ['instructor_id' => 7, 'user_id' => 8, 'first_name' => 'Ren', 'last_name' => 'Nakamura', 'email' => 'instructor7@seijaku.com', 'bio' => 'Young and dynamic instructor specializing in power and athletic Pilates.', 'specialization' => 'Power Pilates, Athletic', 'certification' => 'Balanced Body Certified', 'years_experience' => 4, 'profile_image' => 'images/instructors/ren-nakamura.jpg'],
            ['instructor_id' => 8, 'user_id' => 9, 'first_name' => 'Hana', 'last_name' => 'Kobayashi', 'email' => 'instructor8@seijaku.com', 'bio' => 'Mindfulness-focused instructor combining Pilates with meditation practices.', 'specialization' => 'Therapeutic, Clinical', 'certification' => 'Polestar Pilates Certified', 'years_experience' => 11, 'profile_image' => 'images/instructors/hana-kobayashi.jpg'],
            ['instructor_id' => 9, 'user_id' => 10, 'first_name' => 'Tsubaki', 'last_name' => 'Honda', 'email' => 'instructor9@seijaku.com', 'bio' => 'Aerial and suspension training specialist with circus arts background.', 'specialization' => 'Aerial, Barre', 'certification' => 'Aerial Arts Certified', 'years_experience' => 5, 'profile_image' => 'images/instructors/tsubaki-honda.jpg'],
            ['instructor_id' => 10, 'user_id' => 11, 'first_name' => 'Hiroshi', 'last_name' => 'Tanaka', 'email' => 'instructor10@seijaku.com', 'bio' => 'Senior instructor with comprehensive knowledge of all Pilates disciplines.', 'specialization' => 'All Disciplines', 'certification' => 'Master Pilates Instructor', 'years_experience' => 15, 'profile_image' => 'images/instructors/hiroshi-tanaka.jpg']
        ];
    }

    public static function getVideos() {
        return [
            ['video_id' => 1, 'title' => 'Beginner Mat Pilates - 30 Min Full Body', 'description' => 'Perfect introduction to mat Pilates for beginners.', 'youtube_url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ', 'thumbnail_url' => 'images/videos/beginner-mat.jpg', 'duration' => 30, 'level' => 'beginner', 'category' => 'mat', 'instructor_name' => 'Yuki Sakamoto', 'views' => 15420, 'is_featured' => 1],
            ['video_id' => 2, 'title' => 'Core Strengthening Reformer Workout', 'description' => 'Intermediate reformer session focusing on core stability.', 'youtube_url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ', 'thumbnail_url' => 'images/videos/core-reformer.jpg', 'duration' => 45, 'level' => 'intermediate', 'category' => 'reformer', 'instructor_name' => 'Kenji Yamamoto', 'views' => 12350, 'is_featured' => 1],
            ['video_id' => 3, 'title' => 'Gentle Clinical Pilates for Back Pain', 'description' => 'Therapeutic exercises for lower back pain relief.', 'youtube_url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ', 'thumbnail_url' => 'images/videos/clinical-back.jpg', 'duration' => 25, 'level' => 'beginner', 'category' => 'clinical', 'instructor_name' => 'Aiko Tanaka', 'views' => 18900, 'is_featured' => 1],
            ['video_id' => 4, 'title' => 'Barre Pilates Leg Sculpting', 'description' => 'Ballet-inspired workout for toned legs.', 'youtube_url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ', 'thumbnail_url' => 'images/videos/barre-legs.jpg', 'duration' => 35, 'level' => 'intermediate', 'category' => 'barre', 'instructor_name' => 'Mei Suzuki', 'views' => 9870, 'is_featured' => 0],
            ['video_id' => 5, 'title' => 'Hot Pilates HIIT Burn', 'description' => 'High-intensity Pilates for maximum calorie burn.', 'youtube_url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ', 'thumbnail_url' => 'images/videos/hot-hiit.jpg', 'duration' => 40, 'level' => 'advanced', 'category' => 'hot', 'instructor_name' => 'Haruto Sato', 'views' => 11200, 'is_featured' => 1],
            ['video_id' => 6, 'title' => 'Aerial Pilates Introduction', 'description' => 'Learn the basics of suspended Pilates movements.', 'youtube_url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ', 'thumbnail_url' => 'images/videos/aerial-intro.jpg', 'duration' => 50, 'level' => 'intermediate', 'category' => 'aerial', 'instructor_name' => 'Tsubaki Honda', 'views' => 7650, 'is_featured' => 0],
            ['video_id' => 7, 'title' => 'Cadillac Tower Workout', 'description' => 'Advanced full-body workout using the Cadillac.', 'youtube_url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ', 'thumbnail_url' => 'images/videos/cadillac-tower.jpg', 'duration' => 55, 'level' => 'advanced', 'category' => 'cadillac', 'instructor_name' => 'Hiroshi Tanaka', 'views' => 6430, 'is_featured' => 0],
            ['video_id' => 8, 'title' => 'Prenatal Pilates - Second Trimester', 'description' => 'Safe exercises for expecting mothers.', 'youtube_url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ', 'thumbnail_url' => 'images/videos/prenatal-2nd.jpg', 'duration' => 30, 'level' => 'beginner', 'category' => 'prenatal', 'instructor_name' => 'Sakura Watanabe', 'views' => 14200, 'is_featured' => 1],
            ['video_id' => 9, 'title' => 'Power Pilates Athletic Flow', 'description' => 'Dynamic workout for athletes and fitness enthusiasts.', 'youtube_url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ', 'thumbnail_url' => 'images/videos/power-athletic.jpg', 'duration' => 45, 'level' => 'advanced', 'category' => 'power', 'instructor_name' => 'Ren Nakamura', 'views' => 8900, 'is_featured' => 0],
            ['video_id' => 10, 'title' => 'Therapeutic Pilates for Posture', 'description' => 'Correct your posture with targeted exercises.', 'youtube_url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ', 'thumbnail_url' => 'images/videos/therapeutic-posture.jpg', 'duration' => 35, 'level' => 'all', 'category' => 'therapeutic', 'instructor_name' => 'Hana Kobayashi', 'views' => 16780, 'is_featured' => 1]
        ];
    }

    public static function getReviews() {
        return [
            ['review_id' => 1, 'user_id' => 12, 'first_name' => 'Akira', 'last_name' => 'Yoshida', 'rating' => 5, 'review_text' => 'Seijaku has transformed my life! The instructors are amazing and the facilities are world-class.', 'created_at' => '2024-11-15', 'is_featured' => 1],
            ['review_id' => 2, 'user_id' => 13, 'first_name' => 'Mika', 'last_name' => 'Ito', 'rating' => 5, 'review_text' => 'I have been coming here for 6 months and my back pain is completely gone. Highly recommend the clinical Pilates!', 'created_at' => '2024-11-10', 'is_featured' => 1],
            ['review_id' => 3, 'user_id' => 14, 'first_name' => 'Takeshi', 'last_name' => 'Mori', 'rating' => 4, 'review_text' => 'Great variety of classes and flexible scheduling. The reformer classes are my favorite.', 'created_at' => '2024-11-05', 'is_featured' => 1],
            ['review_id' => 4, 'user_id' => 15, 'first_name' => 'Yui', 'last_name' => 'Kimura', 'rating' => 5, 'review_text' => 'The prenatal classes helped me stay fit throughout my pregnancy. Sakura-sensei is wonderful!', 'created_at' => '2024-10-28', 'is_featured' => 1],
            ['review_id' => 5, 'user_id' => 16, 'first_name' => 'Ryo', 'last_name' => 'Hayashi', 'rating' => 5, 'review_text' => 'As an athlete, the power Pilates classes have improved my performance significantly.', 'created_at' => '2024-10-20', 'is_featured' => 1]
        ];
    }

    public static function getAchievements() {
        return [
            ['achievement_id' => 1, 'name' => 'First Class', 'description' => 'Complete your first Pilates class', 'icon' => 'fa-star', 'points' => 10, 'requirement_type' => 'classes', 'requirement_value' => 1],
            ['achievement_id' => 2, 'name' => 'Week Warrior', 'description' => 'Attend 5 classes in one week', 'icon' => 'fa-fire', 'points' => 50, 'requirement_type' => 'weekly_classes', 'requirement_value' => 5],
            ['achievement_id' => 3, 'name' => 'Month Master', 'description' => 'Complete 20 classes in a month', 'icon' => 'fa-crown', 'points' => 100, 'requirement_type' => 'monthly_classes', 'requirement_value' => 20],
            ['achievement_id' => 4, 'name' => 'Core Champion', 'description' => 'Complete 50 mat Pilates classes', 'icon' => 'fa-trophy', 'points' => 200, 'requirement_type' => 'class_type', 'requirement_value' => 50],
            ['achievement_id' => 5, 'name' => 'Reformer Regular', 'description' => 'Complete 30 reformer classes', 'icon' => 'fa-medal', 'points' => 150, 'requirement_type' => 'class_type', 'requirement_value' => 30],
            ['achievement_id' => 6, 'name' => 'Early Bird', 'description' => 'Attend 10 morning classes before 9 AM', 'icon' => 'fa-sun', 'points' => 75, 'requirement_type' => 'time_based', 'requirement_value' => 10],
            ['achievement_id' => 7, 'name' => 'Variety Seeker', 'description' => 'Try all 10 types of Pilates', 'icon' => 'fa-gem', 'points' => 250, 'requirement_type' => 'variety', 'requirement_value' => 10],
            ['achievement_id' => 8, 'name' => 'Century Club', 'description' => 'Complete 100 classes total', 'icon' => 'fa-award', 'points' => 500, 'requirement_type' => 'classes', 'requirement_value' => 100]
        ];
    }
}
