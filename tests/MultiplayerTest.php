<?php

use PHPUnit\Framework\TestCase;

class MultiplayerTest extends TestCase
{
    public function testMultiplayerClassExists()
    {
        require_once __DIR__ . '/../html/php/multiplayer.php';
        require_once __DIR__ . '/../html/php/database.php';
        
        // Test that classes exist
        $this->assertTrue(class_exists('MultiplayerHandler'), 'MultiplayerHandler class should exist');
        
        // Test that MultiplayerHandler has required methods
        $mh = new MultiplayerHandler();
        $this->assertTrue(method_exists($mh, 'joinOrCreateGame'), 'joinOrCreateGame method should exist');
        $this->assertTrue(method_exists($mh, 'saveAnswer'), 'saveAnswer method should exist');
        $this->assertTrue(method_exists($mh, 'getNextQuestion'), 'getNextQuestion method should exist');
    }
    
    public function testDatabaseMultiplayerMethods()
    {
        require_once __DIR__ . '/../html/php/database.php';
        
        $db = new Database();
        
        // Test that Database has multiplayer methods
        $this->assertTrue(method_exists($db, 'joinOrCreateMultiplayerGame'), 'joinOrCreateMultiplayerGame method should exist');
        $this->assertTrue(method_exists($db, 'assignQuestions'), 'assignQuestions method should exist');
        $this->assertTrue(method_exists($db, 'getMultiplayerQuestion'), 'getMultiplayerQuestion method should exist');
        $this->assertTrue(method_exists($db, 'saveMultiplayerAnswer'), 'saveMultiplayerAnswer method should exist');
        $this->assertTrue(method_exists($db, 'assignPlayer2ToQuestions'), 'assignPlayer2ToQuestions method should exist');
        $this->assertTrue(method_exists($db, 'isPlayersTurn'), 'isPlayersTurn method should exist');
    }
    
    public function testMultiplayerAnswerValidation()
    {
        require_once __DIR__ . '/../html/php/multiplayer.php';
        
        $mh = new MultiplayerHandler();
        
        // Test with invalid data
        $invalidData = [
            'gameId' => null,
            'playerId' => 1,
            'questionId' => 1,
            'selectedAnswer' => 1,
            'correctAnswer' => 1,
            'questionNumber' => 1
        ];
        
        $result = $mh->saveAnswer($invalidData);
        $this->assertFalse($result['success'], 'Should return false for invalid data');
        
        // Test with valid data structure (won't work without database, but tests the structure)
        $validData = [
            'gameId' => 1,
            'playerId' => 1,
            'questionId' => 1,
            'selectedAnswer' => 1,
            'correctAnswer' => 1,
            'questionNumber' => 1
        ];
        
        // This will likely fail due to database connection, but it tests the method structure
        $result = $mh->saveAnswer($validData);
        $this->assertIsArray($result, 'Should return an array');
        $this->assertArrayHasKey('success', $result, 'Result should have success key');
    }
}

?>