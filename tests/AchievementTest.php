<?php

use PHPUnit\Framework\TestCase;

class AchievementTest extends TestCase
{
    public function testAchievementSystemClassesExist()
    {
        require_once __DIR__ . '/../html/php/achievements.php';
        require_once __DIR__ . '/../html/php/database.php';
        
        // Test that classes exist
        $this->assertTrue(class_exists('AchievementManager'), 'AchievementManager class should exist');
        $this->assertTrue(class_exists('Database'), 'Database class should exist');
        
        // Test that Database has achievement methods
        $db = new Database();
        $this->assertTrue(method_exists($db, 'getAchievements'), 'getAchievements method should exist');
        $this->assertTrue(method_exists($db, 'getPlayerAchievements'), 'getPlayerAchievements method should exist');
        $this->assertTrue(method_exists($db, 'checkAndUnlockAchievements'), 'checkAndUnlockAchievements method should exist');
        $this->assertTrue(method_exists($db, 'unlockAchievement'), 'unlockAchievement method should exist');
        $this->assertTrue(method_exists($db, 'getPlayerStats'), 'getPlayerStats method should exist');
    }
    
    public function testAchievementManagerMethods()
    {
        require_once __DIR__ . '/../html/php/achievements.php';
        
        $am = new AchievementManager();
        $this->assertTrue(method_exists($am, 'getPlayerAchievementData'), 'getPlayerAchievementData method should exist');
        $this->assertTrue(method_exists($am, 'checkNewAchievements'), 'checkNewAchievements method should exist');
    }
    
    public function testAchievementProgressCalculation()
    {
        require_once __DIR__ . '/../html/php/achievements.php';
        
        $am = new AchievementManager();
        
        // Test with a mock achievement structure
        $achievement = [
            'RequirementType' => 'QUESTIONS_ANSWERED',
            'RequirementValue' => 10
        ];
        
        $stats = [
            'totalQuestions' => 5,
            'correctAnswers' => 3,
            'gamesPlayed' => 2,
            'currentStreak' => 2,
            'multiplayerWins' => 0
        ];
        
        // Use reflection to test private method
        $reflection = new ReflectionClass($am);
        $method = $reflection->getMethod('getAchievementProgress');
        $method->setAccessible(true);
        
        $progress = $method->invoke($am, $stats, $achievement);
        
        $this->assertEquals(5, $progress['current'], 'Current progress should be 5');
        $this->assertEquals(10, $progress['target'], 'Target should be 10');
        $this->assertEquals(50, $progress['percentage'], 'Percentage should be 50%');
    }
}

?>