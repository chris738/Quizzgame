<?php
require_once 'database.php';
header('Content-Type: text/html; charset=utf-8');

function RandomQuestion() {
    try {
        $database = new Database();
        $fragen = $database->getRandomQuestion();

        if (!$fragen || empty($fragen['Question'])) {
            return ['error' => 'Keine gültige Frage gefunden.'];
        }

        return [
            'id'      => $fragen['QuestionID'],
            'frage'   => $fragen['Question'],
            'antwort' => [
                '1' => $fragen['Answer1'],
                '2' => $fragen['Answer2'],
                '3' => $fragen['Answer3'],
                '4' => $fragen['Answer4']
            ],
            'richtig' => $fragen['correctAnswer']
        ];
    } catch (PDOException $e) {
        return ['error' => 'Datenbankfehler: ' . $e->getMessage()];
    } catch (Exception $e) {
        return ['error' => 'Unbekannter Fehler: ' . $e->getMessage()];
    }
}

// Eingehende Anfrage verarbeiten
$method = $_SERVER['REQUEST_METHOD'];
//$random = $_GET['random'] ?? null;

try {
    if ($method === 'GET') {
        $response = ['info' => RandomQuestion()];
        echo json_encode($response);
    } elseif ($method === 'POST') {
        $data = json_decode(file_get_contents("php://input"), true);
        
        $playerId = $data['playerId'] ?? null;
        $questionId = $data['questionId'] ?? null;
        $selectedAnswer = $data['selectedAnswer'] ?? null;
        $correctAnswer = $data['correctAnswer'] ?? null;
    
        if ($playerId && $questionId && $selectedAnswer && $correctAnswer !== null) {
            $database = new Database();
            $database->saveGameResult((int)$playerId, (int)$questionId, (int)$selectedAnswer, (int)$correctAnswer);
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Ungültige Daten']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Methode nicht unterstützt.']);
    }
} catch (Exception $e) {
    echo json_encode(['error' => 'Interner Serverfehler: ' . $e->getMessage()]);
}

?>
