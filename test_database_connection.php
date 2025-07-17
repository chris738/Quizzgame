<?php
// Test script to verify database connection and UpgradeBuilding procedure

require_once 'html/php/database.php';

try {
    echo "Testing database connection and UpgradeBuilding procedure...\n";
    
    // Test connection to the Database class
    $db = new Database();
    echo "✓ Database connection successful\n";
    
    // Test basic database operations
    $questions = $db->getRandomQuestions(1);
    if ($questions) {
        echo "✓ Can fetch questions from database\n";
    } else {
        echo "⚠ No questions found in database (this is expected if database is empty)\n";
    }
    
    // Test if we can connect directly to check for UpgradeBuilding procedure
    $host = '172.17.0.7';
    $dbname = 'quizgame';
    $username = 'quizgame';
    $password = 'sicheresPasswort';
    
    $pdo = new PDO("mysql:host={$host};dbname={$dbname}", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Check if UpgradeBuilding procedure exists in quizgame database
    $stmt = $pdo->prepare("SHOW PROCEDURE STATUS WHERE Name = 'UpgradeBuilding' AND Db = 'quizgame'");
    $stmt->execute();
    $result = $stmt->fetch();
    
    if ($result) {
        echo "✓ UpgradeBuilding procedure exists in quizgame database\n";
    } else {
        echo "⚠ UpgradeBuilding procedure not found in quizgame database\n";
    }
    
    // Check if UpgradeBuilding procedure exists in browsergame database
    $pdo_browser = new PDO("mysql:host={$host};dbname=browsergame", $username, $password);
    $pdo_browser->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    $stmt = $pdo_browser->prepare("SHOW PROCEDURE STATUS WHERE Name = 'UpgradeBuilding' AND Db = 'browsergame'");
    $stmt->execute();
    $result = $stmt->fetch();
    
    if ($result) {
        echo "✓ UpgradeBuilding procedure exists in browsergame database\n";
    } else {
        echo "⚠ UpgradeBuilding procedure not found in browsergame database\n";
    }
    
    echo "\nDatabase setup appears to be working correctly!\n";
    
} catch (Exception $e) {
    echo "✗ Error: " . $e->getMessage() . "\n";
    echo "This indicates the database needs to be initialized with the SQL file.\n";
    echo "Run: mysql -u root -p < QuizgameSQL.sql\n";
}
?>