<?php

interface DatabaseInterface {
    public function getRandomQuestion($category);
    public function saveGameResult($playerId, $questionId, $selectedAnswer, $correctAnswer, $score);
    public function insertQuestion($question, $category, $a1, $a2, $a3, $a4, $correctAnswer);
    public function insertUser($username, $hashedPassword);
    public function getUserByName($name);
    public function getTopHighscores($limit);
    public function getUserById($id);
    public function editQuestion($id, $question, $category, $a1, $a2, $a3, $a4, $correctAnswer);
    public function dbdeleteQuestion($id);
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

    public function getRandomQuestion($category = null) {
        if ($category) {
            $sql = "
            SELECT 
                QuestionID, Question, Answer1, Answer2, Answer3, Answer4, correctAnswer
            FROM 
                Question
            WHERE 
                Category = :category
            ORDER BY 
                RAND() 
            LIMIT 1";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':category', $category);
        } else {
            $sql = "
            SELECT 
                QuestionID, Question, Answer1, Answer2, Answer3, Answer4, correctAnswer
            FROM 
                Question
            ORDER BY 
                RAND() 
            LIMIT 1";
            $stmt = $this->conn->prepare($sql);
        }
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function saveGameResult($playerId, $questionId, $selectedAnswer, $correctAnswer, $score = null) {
        $isCorrect = ($selectedAnswer === $correctAnswer) ? 1 : 0;
    
        $sql = "
            INSERT INTO Game (PlayerID, QuestionID, SelectedAnswer, CorrectAnswer, IsCorrect, Score)
            VALUES (:playerId, :questionId, :selectedAnswer, :correctAnswer, :isCorrect, :score)";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([
            ':playerId' => $playerId,
            ':questionId' => $questionId,
            ':selectedAnswer' => $selectedAnswer,
            ':correctAnswer' => $correctAnswer,
            ':isCorrect' => $isCorrect,
            ':score' => $score
        ]);
    }

    public function insertQuestion($question, $category, $a1, $a2, $a3, $a4, $correctAnswer) {
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

    public function editQuestion($id, $question, $category, $a1, $a2, $a3, $a4, $correctAnswer) {
        $stmt = $this->conn->prepare("
            UPDATE Question
            SET 
                Question = :question,
                Category = :category,
                Answer1 = :a1,
                Answer2 = :a2,
                Answer3 = :a3,
                Answer4 = :a4,
                correctAnswer = :correct
            WHERE 
                QuestionID = :id
        ");
        $stmt->execute([
            ':id' => $id,
            ':question' => $question,
            ':category' => $category,
            ':a1' => $a1,
            ':a2' => $a2,
            ':a3' => $a3,
            ':a4' => $a4,
            ':correct' => $correctAnswer
        ]);
    }

    public function getQuestionById($id) {
    $stmt = $this->conn->prepare("
    SELECT * FROM Question WHERE QuestionID = :id"
    );
    $stmt->execute([':id' => $id]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
    }


    public function dbdeleteQuestion($id) {
        $stmt = $this->conn->prepare("UPDATE MultiplayerAnswer SET QuestionID = NULL WHERE QuestionID = :id");
        $stmt->execute([':id' => $id]);
        $stmt = $this->conn->prepare("UPDATE MultiplayerQuestion SET QuestionID = NULL WHERE QuestionID = :id");
        $stmt->execute([':id' => $id]);


        $stmt = $this->conn->prepare("
        DELETE FROM Question WHERE QuestionID = :id"
        );
        $stmt->execute([':id' => $id]);
    }


    public function insertUser($username, $hashedPassword) {
        $stmt = $this->conn->prepare("
            INSERT INTO player (name, password)
            VALUES (:username, :password)
        ");
        $stmt->execute([
            ':username' => $username,
            ':password' => $hashedPassword
        ]);
    }
    
    public function getUserByName($name) {
        $stmt = $this->conn->prepare("SELECT PlayerID, password FROM player WHERE name = :name");
        $stmt->execute([':name' => $name]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getTopHighscores($limit = 20) {
        $stmt = $this->conn->prepare("
            SELECT username, totalScore
            FROM TopHighscores
            LIMIT :limit
        ");
        $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getUserById($id) {
        $stmt = $this->conn->prepare("SELECT PlayerID, name FROM player WHERE PlayerID = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
}

?>
