<?php
require_once 'database.php';
header('Content-Type: application/json');

class AchievementManager extends Database {
    public function __construct() {
        parent::__construct();
    }

    public function getPlayerAchievementData($playerId) {
        try {
            $achievements = $this->getAchievements();
            $playerAchievements = $this->getPlayerAchievements($playerId);
            $stats = $this->getPlayerStats($playerId);

            // Mark which achievements are unlocked
            $unlockedIds = array_column($playerAchievements, 'AchievementID');
            
            foreach ($achievements as &$achievement) {
                $achievement['isUnlocked'] = in_array($achievement['AchievementID'], $unlockedIds);
                
                // Add unlock date if unlocked
                if ($achievement['isUnlocked']) {
                    $unlockedData = array_filter($playerAchievements, function($pa) use ($achievement) {
                        return $pa['AchievementID'] == $achievement['AchievementID'];
                    });
                    $achievement['unlockedAt'] = !empty($unlockedData) ? array_values($unlockedData)[0]['UnlockedAt'] : null;
                }

                // Add progress information
                $achievement['progress'] = $this->getAchievementProgress($stats, $achievement);
            }

            return [
                'success' => true,
                'achievements' => $achievements,
                'stats' => $stats,
                'totalUnlocked' => count($playerAchievements),
                'totalAchievements' => count($achievements)
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Fehler beim Laden der Achievements: ' . $e->getMessage()
            ];
        }
    }

    private function getAchievementProgress($stats, $achievement) {
        $current = 0;
        $target = $achievement['RequirementValue'];

        switch ($achievement['RequirementType']) {
            case 'QUESTIONS_ANSWERED':
                $current = $stats['totalQuestions'];
                break;
            case 'CORRECT_ANSWERS':
                $current = $stats['correctAnswers'];
                break;
            case 'GAMES_PLAYED':
                $current = $stats['gamesPlayed'];
                break;
            case 'MULTIPLAYER_WINS':
                $current = $stats['multiplayerWins'];
                break;
            case 'STREAK':
                $current = $stats['currentStreak'];
                break;
        }

        return [
            'current' => $current,
            'target' => $target,
            'percentage' => min(100, round(($current / $target) * 100))
        ];
    }

    public function checkNewAchievements($playerId) {
        try {
            $newlyUnlocked = $this->checkAndUnlockAchievements($playerId);
            return [
                'success' => true,
                'newAchievements' => $newlyUnlocked,
                'count' => count($newlyUnlocked)
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Fehler beim Überprüfen der Achievements: ' . $e->getMessage()
            ];
        }
    }
}

// Handle API requests
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $action = $_GET['action'] ?? '';
    $playerId = $_GET['playerId'] ?? null;

    if (!$playerId) {
        echo json_encode(['success' => false, 'message' => 'Player ID erforderlich']);
        exit;
    }

    $manager = new AchievementManager();

    switch ($action) {
        case 'getPlayerAchievements':
            $response = $manager->getPlayerAchievementData($playerId);
            break;
            
        case 'checkNewAchievements':
            $response = $manager->checkNewAchievements($playerId);
            break;
            
        default:
            $response = ['success' => false, 'message' => 'Ungültige Aktion'];
    }

    echo json_encode($response);
}
?>