<?php
require_once 'database.php';
header('Content-Type: application/json');

class QuestionManager extends Database {
    private $response = [];

    public function __construct() {
        parent::__construct();
    }

    private function isValidInput($question, $category, $answer1, $answer2, $answer3, $answer4, $correctAnswer) {
        $allFieldsFilled = 
            !empty($question) &&
            !empty($category) &&
            !empty($answer1) &&
            !empty($answer2) &&
            !empty($answer3) &&
            !empty($answer4);
    
        $correctIsValid = in_array($correctAnswer, ['1', '2', '3', '4']);
    
        return $allFieldsFilled && $correctIsValid;
    }

    public function addQuestion($data) {
        $question      = trim($data['question'] ?? '');
        $category      = trim($data['category'] ?? '');
        $answer1       = trim($data['answer1'] ?? '');
        $answer2       = trim($data['answer2'] ?? '');
        $answer3       = trim($data['answer3'] ?? '');
        $answer4       = trim($data['answer4'] ?? '');
        $correctAnswer = trim($data['correctAnswer'] ?? '');

        if (!$this->isValidInput($question, $category, $answer1, $answer2, $answer3, $answer4, $correctAnswer)) {
            $this->response = ['success' => false, 'message' => 'Ungültige Eingabedaten'];
            return;
        }

        try {
            parent::insertQuestion($question, $category, $answer1, $answer2, $answer3, $answer4, $correctAnswer);
            $this->response = ['success' => true, 'message' => 'Frage erfolgreich hinzugefügt'];
        } catch (Exception $e) {
            $this->response = ['success' => false, 'message' => 'Fehler: ' . $e->getMessage()];
        }
    }

    public function getResponse() {
        return $this->response;
    }
}

class UserManager extends Database {
    private $response = [];

    public function __construct() {
        parent::__construct();
    }

    private function isValid($username, $password): bool {
        if (strlen($username) < 3 || strlen($password) < 6) {
            $this->response = ['success' => false, 'message' => 'Ungültige Eingabedaten'];
            return false;
        }
        return true;
    }

    public function addUser($data) {
        $username = trim($data['username'] ?? '');
        $password = $data['password'] ?? '';

        if (!$this->isValid($username, $password)) {
            return;
        }

        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        try {
            parent::insertUser($username, $hashedPassword); // 👈 nur noch 2 Parameter
            $this->response = ['success' => true, 'message' => 'Benutzer erfolgreich hinzugefügt'];
        } catch (Exception $e) {
            $this->response = ['success' => false, 'message' => 'Fehler: ' . $e->getMessage()];
        }
    }

    public function getResponse() {
        return $this->response;
    }
}


// Hauptausführung
// Prüfe, ob eine POST-Anfrage gesendet wurde
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if (isset($_POST['question'])) {
        $questionManager = new QuestionManager();
        $questionManager->addQuestion($_POST);
        echo json_encode($questionManager->getResponse());

    } elseif (isset($_POST['username'])) {
        $adminManager = new UserManager();
        $adminManager->addUser($_POST);
        echo json_encode($adminManager->getResponse());

    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Ungültige Anfrage'
        ]);
    }
}



?>
