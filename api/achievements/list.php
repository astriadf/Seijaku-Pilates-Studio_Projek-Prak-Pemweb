<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../config/static_data.php';

$database = new Database();
$db = $database->getConnection();

$result = [];

if ($db !== null) {

    require_once __DIR__ . '/../../classes/achievement.php';
    require_once __DIR__ . '/../../utils/jwt.php';

    $authUser = JWT::requireAuth();
    $achievement = new Achievement($db);

    $allAchievements = $achievement->getAll();
    $userAchievements = $achievement->getUserAchievements($authUser['user_id']);
    $userAchievementIds = array_column($userAchievements, 'achievement_id');

    foreach ($allAchievements as $a) {
        $a['earned'] = in_array($a['achievement_id'], $userAchievementIds);

        if ($a['earned']) {
            foreach ($userAchievements as $ua) {
                if ($ua['achievement_id'] == $a['achievement_id']) {
                    $a['earned_at'] = $ua['earned_at'];
                    break;
                }
            }
        }

        $result[] = $a;
    }

} else {
    $result = StaticData::getAchievements();
    foreach ($result as &$a) {
        $a['earned'] = false;
    }
}

echo json_encode([
    'success' => true,
    'data' => $result
]);
