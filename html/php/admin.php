<?php
require_once 'database.php';
header('Content-Type: application/json');

/**
 * Prüft, ob alle Eingabefelder gültig sind.
 */
function isValidInput($question, $category, $a1, $a2, $a3, $a4, $correct) {
    return $question && $category && $a1 && $a2 && $a3 && $a4 && in_array($correct, ['1', '2', '3', '4']);
}

/**
 * Fügt eine neue Frage zur Datenbank hinzu.
 *
 * @param array $data Formulardaten ($_POST)
 * @return array Antwort für JSON-Response
 */

function addQuestion($data) {
    $question      = trim($data['question'] ?? '');
    $category      = trim($data['category'] ?? '');
    $answer1       = trim($data['answer1'] ?? '');
    $answer2       = trim($data['answer2'] ?? '');
    $answer3       = trim($data['answer3'] ?? '');
    $answer4       = trim($data['answer4'] ?? '');
    $correctAnswer = trim($data['correctAnswer'] ?? '');

    if (!isValidInput($question, $category, $answer1, $answer2, $answer3, $answer4, $correctAnswer)) {
        return ['success' => false, 'message' => 'Ungültige Eingabedaten'];
    }

    try {
        $database = new Database();
        $database->addQuestion($question, $category, $a1, $a2, $a3, $a4, $correctAnswer);
        return ['success' => true, 'message' => 'Frage erfolgreich hinzugefügt'];
    } catch (Exception $e) {
        return ['success' => false, 'message' => 'Fehler: ' . $e->getMessage()];
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = $_POST;
    $response = addQuestion($data);
    ob_clean();
    echo json_encode($response);
}

?>