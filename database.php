<?php

interface DatabaseInterface {
    public function getFragen($frageID);
    public function getAnswer($frageID, $answer);
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

    public function getFragen($frageID) {
        $sql = "
        SELECT
            Question, Answer1, Answer2, Answer3, Answer4
        FROM 
            Question
        WHERE QuestionID= :frageID";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':frageID', $frageID, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getAnswer($frageID, $answer) {
        $sql = "
        SELECT
            correctAnswer
        FROM 
            Questions
        WHERE QuestionID = :frageID";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':frageID', $frageID, PDO::PARAM_INT);
        $stmt->execute();
        return = $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
}

?>
