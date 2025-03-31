<?php
require_once 'database.php';
header('Content-Type: application/json');

class QuestionManager {
    private $database;
    private $response = [];

    public function __construct(Database $database) {
        $this->database = $database;
    }

    /**
     * Validiert die Eingabedaten für eine neue Frage.
     *
     * @param string $question
     * @param string $category
     * @param string $answer1
     * @param string $answer2
     * @param string $answer3
     * @param string $answer4
     * @param string $correctAnswer
     * @return bool
     */
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
    

    /**
     * Fügt eine neue Frage zur Datenbank hinzu und speichert das Ergebnis in der Klassenvariablen $response.
     *
     * @param array $data Formulardaten ($_POST)
     */
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
            $this->database->addQuestion($question, $category, $answer1, $answer2, $answer3, $answer4, $correctAnswer);
            $this->response = ['success' => true, 'message' => 'Frage erfolgreich hinzugefügt'];
        } catch (Exception $e) {
            $this->response = ['success' => false, 'message' => 'Fehler: ' . $e->getMessage()];
        }
    }

    /**
     * Gibt die in der Klassenvariablen $response gespeicherte Antwort zurück.
     *
     * @return array
     */
    public function getResponse() {
        return $this->response;
    }
}

// Hauptausführung
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $database = new Database();
    $questionManager = new QuestionManager($database);
    $questionManager->addQuestion($_POST);
    echo json_encode($questionManager->getResponse());
}
?>


