<?php

interface DatabaseInterface {
    public function getFragen($frageID);
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
            Fragen
        WHERE QuestionID= :frageID";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':frageID', $frageID, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}

?>
