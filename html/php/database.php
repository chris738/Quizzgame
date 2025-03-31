<?php

interface DatabaseInterface {
    public function getRandomQuestion();
    public function saveGameResult($playerId, $questionId, $selectedAnswer, $correctAnswer);
    public function addQuestion($question, $category, $a1, $a2, $a3, $a4, $correctAnswer);
    public function insertUser($username, $hashedPassword); 
}

class Database implements DatabaseInterface {
    private $host = '172.17.0.7';
    private $dbname = 'quizgame';
    private $username = 'quizgame';
    private $password = 'sicheresPasswort';
    private $conn;

    public function __construct() {
        try {
            $this->conn = new PDO("mysql:host={$this->host};dbname={$this->dbname}", $this->username, $this->password);
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            die("Connection failed: " . $e->getMessage());
        }
    }

    public function getRandomQuestion() {
        $sql = "
        SELECT 
            QuestionID, Question, Answer1, Answer2, Answer3, Answer4, correctAnswer
        FROM 
            Question
        ORDER BY 
            RAND() 
        LIMIT 1";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function saveGameResult($playerId, $questionId, $selectedAnswer, $correctAnswer) {
        $isCorrect = ($selectedAnswer === $correctAnswer) ? 1 : 0;
    
        $sql = "
                INSERT INTO Game (PlayerID, QuestionID, SelectedAnswer, CorrectAnswer, IsCorrect)
                VALUES (:playerId, :questionId, :selectedAnswer, :correctAnswer, :isCorrect)";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([
            ':playerId' => $playerId,
            ':questionId' => $questionId,
            ':selectedAnswer' => $selectedAnswer,
            ':correctAnswer' => $correctAnswer,
            ':isCorrect' => $isCorrect
        ]);
    }

    public function addQuestion($question, $category, $a1, $a2, $a3, $a4, $correctAnswer) {
        $stmt = $this->conn->prepare("
            INSERT INTO Question (Question, Category, Answer1, Answer2, Answer3, Answer4, correctAnswer)
            VALUES (:question, :category, :a1, :a2, :a3, :a4, :correct)
        ");
        $stmt->execute([
            ':question' => $question,
            ':category' => $category,
            ':a1' => $a1,
            ':a2' => $a2,
            ':a3' => $a3,
            ':a4' => $a4,
            ':correct' => $correctAnswer
        ]);
    }

    public function insertUser($username, $hashedPassword) {
        $stmt = $this->conn->prepare("
            INSERT INTO player (username, password)
            VALUES (:username, :password)
        ");
        $stmt->execute([
            ':username' => $username,
            ':password' => $hashedPassword
        ]);
    }
    
    
}

?>
