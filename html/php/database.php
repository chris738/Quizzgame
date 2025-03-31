<?php

interface DatabaseInterface {
    public function getRandomQuestion();
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
}

?>
