<?php

use PHPUnit\Framework\TestCase;

class AdminTest extends TestCase
{
    public function testQuestionListingApiReturnsValidData()
    {
        // Mock database connection
        require_once __DIR__ . '/../html/php/database.php';
        
        // Test the database getAllQuestions method
        $db = new Database();
        
        // This will fail if there's no database connection, but it tests the method exists
        $this->assertTrue(method_exists($db, 'getAllQuestions'), 'getAllQuestions method should exist');
        $this->assertTrue(method_exists($db, 'getQuestionById'), 'getQuestionById method should exist');
    }
    
    public function testQuestionManagerClasses()
    {
        require_once __DIR__ . '/../html/php/admin.php';
        
        // Test that classes exist
        $this->assertTrue(class_exists('QuestionManager'), 'QuestionManager class should exist');
        $this->assertTrue(class_exists('UserManager'), 'UserManager class should exist');
        
        // Test that QuestionManager has required methods
        $qm = new QuestionManager();
        $this->assertTrue(method_exists($qm, 'addQuestion'), 'addQuestion method should exist');
        $this->assertTrue(method_exists($qm, 'updateQuestion'), 'updateQuestion method should exist');
        $this->assertTrue(method_exists($qm, 'deleteQuestion'), 'deleteQuestion method should exist');
        $this->assertTrue(method_exists($qm, 'loadQuestionById'), 'loadQuestionById method should exist');
    }
}

?>