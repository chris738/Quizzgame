<?php

interface DatabaseInterface {
    public function getRandomQuestions($limit, $category);
    public function saveGameResult($playerId, $questionId, $selectedAnswer, $correctAnswer, $score);
    public function insertQuestion($question, $category, $a1, $a2, $a3, $a4, $correctAnswer);
    public function insertUser($username, $hashedPassword);
    public function getUserByName($name);
    public function getTopHighscores($limit);
    public function getUserById($id);
    public function assignQuestions($gameId, $player1Id, $player2Id);
    public function joinOrCreateMultiplayerGame($playerId);
    public function getMultiplayerQuestion($gameId, $playerId);
    public function saveMultiplayerAnswer($gameId, $playerId, $questionId, $selectedAnswer, $correctAnswer);
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

    public function getRandomQuestions(int $limit = 1, string $category = null): array {
        // versehentliche Übergabe abfangen
        if (is_string($limit) && $category === null) {
            $category = $limit;
            $limit = 1;
        }
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
                LIMIT :limit";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':category', $category);
            $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        } else {
            $sql = "
                SELECT 
                    QuestionID, Question, Answer1, Answer2, Answer3, Answer4, correctAnswer
                FROM 
                    Question
                ORDER BY 
                    RAND()
                LIMIT :limit";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        }
    
        $stmt->execute();
        return $limit === 1
            ? $stmt->fetch(PDO::FETCH_ASSOC)
            : $stmt->fetchAll(PDO::FETCH_ASSOC);
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

    public function joinOrCreateMultiplayerGame($playerId) {
        // Schritt 1: Nach offenem Raum suchen
        $stmt = $this->conn->prepare("
            SELECT * FROM MultiplayerGame 
            WHERE Player2ID IS NULL AND IsStarted = FALSE
            LIMIT 1
        ");
        $stmt->execute();
        $game = $stmt->fetch(PDO::FETCH_ASSOC);
    
        if ($game) {
            // Beitreten
            $stmt = $this->conn->prepare("
                UPDATE MultiplayerGame 
                SET Player2ID = :pid 
                WHERE GameID = :gid
            ");
            $stmt->execute([':pid' => $playerId, ':gid' => $game['GameID']]);
            return $game['GameID'];
        } else {
            // Neuen Raum erstellen
            $roomCode = substr(md5(uniqid()), 0, 6);
            $stmt = $this->conn->prepare("
                INSERT INTO MultiplayerGame (RoomCode, Player1ID) 
                VALUES (:code, :pid)
            ");
            $stmt->execute([':code' => $roomCode, ':pid' => $playerId]);
            return $this->conn->lastInsertId();
        }
    }

    // 1–4 beantwortet von Player1, 5–8 von Player2
    public function assignQuestions($gameId, $player1Id, $player2Id) {
        $questions = $this->getRandomQuestions(8); // gib 8 zufällige Fragen

        foreach ($questions as $index => $q) {
            $answeredBy = ($index < 4) ? $player1Id : $player2Id;
            $stmt = $this->conn->prepare("
                INSERT INTO MultiplayerQuestion (GameID, QuestionNumber, QuestionID, AnsweredBy)
                VALUES (:game, :num, :qid, :answeredBy)
            ");
            $stmt->execute([
                ':game' => $gameId,
                ':num' => $index + 1,
                ':qid' => $q['QuestionID'],
                ':answeredBy' => $answeredBy
            ]);
        }
    }

    public function getMultiplayerQuestion($gameId, $playerId) {
        $stmt = $this->conn->prepare("
            SELECT 
            q.QuestionID AS QuestionID,
            q.Question AS Question,
            q.Answer1 AS Answer1,
            q.Answer2 AS Answer2,
            q.Answer3 AS Answer3,
            q.Answer4 AS Answer4,
            q.correctAnswer AS correctAnswer,
            mq.QuestionNumber AS QuestionNumber
            FROM MultiplayerQuestion mq
            JOIN Question q ON mq.QuestionID = q.QuestionID
            WHERE mq.GameID = :gameId AND mq.AnsweredBy = :playerId
            AND NOT EXISTS (
                SELECT 1 FROM MultiplayerAnswer a
                WHERE a.GameID = mq.GameID AND a.PlayerID = :playerId AND a.QuestionID = mq.QuestionID
            )
            ORDER BY mq.QuestionNumber ASC
            LIMIT 1
        ");
        $stmt->execute([':gameId' => $gameId, ':playerId' => $playerId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function saveMultiplayerAnswer($gameId, $playerId, $questionId, $selectedAnswer, $correctAnswer) {
        $isCorrect = ((int)$selectedAnswer === (int)$correctAnswer) ? 1 : 0;
    
        $stmt = $this->conn->prepare("
            INSERT INTO MultiplayerAnswer (GameID, PlayerID, QuestionID, SelectedAnswer, IsCorrect)
            VALUES (:game, :player, :question, :selected, :isCorrect)
        ");
        $stmt->execute([
            ':game' => $gameId,
            ':player' => $playerId,
            ':question' => $questionId,
            ':selected' => $selectedAnswer,
            ':isCorrect' => $isCorrect
        ]);
    
        return $isCorrect;
    }
    
}

?>
