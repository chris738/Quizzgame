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

    public function updateQuestion($data) {
        $questionID    = $data['id'] ?? '';
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
            parent::editQuestion($questionID, $question, $category, $answer1, $answer2, $answer3, $answer4, $correctAnswer);
            $this->response = ['success' => true, 'message' => 'Frage erfolgreich aktualisiert'];
        } catch (Exception $e) {
            $this->response = ['success' => false, 'message' => 'Fehler: ' . $e->getMessage()];
        }
    }

    public function deleteQuestion($data) {
        $questionID = $data['deleteQuestionID'] ?? '';
    
        if (empty($questionID)) {
            $this->response = ['success' => false, 'message' => 'Frage ID ist erforderlich'];
            return;
        }
    
        try {
            parent::dbdeleteQuestion($questionID); 
            $this->response = ['success' => true, 'message' => 'Frage erfolgreich gelöscht'];
        } catch (Exception $e) {
            $this->response = ['success' => false, 'message' => 'Fehler: ' . $e->getMessage()];
        }
    }

    function loadQuestionById($questionId) {
        if (!$questionId) {
            return ['error' => 'Keine Frage-ID angegeben.'];
        }
    
        try {
            $db = new Database();
            $question = $db->getQuestionById($questionId); 
            
            if ($question) {
                return [
                    'info' => [
                        'frage' => $question['Question'],
                        'category' => $question['Category'],
                        'antwort' => [
                            '1' => $question['Answer1'],
                            '2' => $question['Answer2'],
                            '3' => $question['Answer3'],
                            '4' => $question['Answer4']
                        ],
                        'richtig' => $question['correctAnswer']
                    ]
                ];
            } else {
                return ['error' => 'Frage nicht gefunden.'];
            }
        } catch (PDOException $e) {
            return ['error' => 'Datenbankfehler: ' . $e->getMessage()];
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

    if (isset($_POST['action']) && $_POST['action'] === 'deleteQuestion') {
        $questionManager = new QuestionManager();
        $questionManager->deleteQuestion($_POST);
        echo json_encode($questionManager->getResponse());

    } elseif (isset($_POST['action']) && $_POST['action'] === 'updateQuestion') {
        $questionManager = new QuestionManager();
        $questionManager->updateQuestion($_POST);
        echo json_encode($questionManager->getResponse());

    } elseif (isset($_POST['question'])) {
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

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['action']) && $_GET['action'] === 'loadQuestion') {
    $questionId = $_GET['id'] ?? null;
    $response = loadQuestionById($questionId);
    echo json_encode($response);
    exit;
}


?>
