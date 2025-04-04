<?php
require_once 'database.php';
header('Content-Type: application/json');

class QuizHandler extends Database {
    private array $response = [];

    public function handleRequest(string $method): void {
        try {
            if ($method === 'GET') {
                $this->handleGet();
            } elseif ($method === 'POST') {
                $this->handlePost();
            } else {
                $this->response = ['success' => false, 'message' => 'Methode nicht unterstützt.'];
            }
        } catch (Exception $e) {
            $this->response = ['error' => 'Interner Serverfehler: ' . $e->getMessage()];
        }

        echo json_encode($this->response);
    }

    private function handleGet(): void {
        $category = $_GET['category'] ?? null;
        $this->response = $this->loadRandomQuestion($category);
    }

    private function handlePost(): void {
        $data = json_decode(file_get_contents("php://input"), true);
        $this->response = $this->saveGameResultFromRequest($data);
    }

    private function loadRandomQuestion(string $category = null): array {
        try {
            $frage = $this->getRandomQuestion($category);
    
            if (!$frage || empty($frage['Question'])) {
                return ['error' => 'Keine gültige Frage gefunden.'];
            }
    
            return [
                'info' => [
                    'id'      => $frage['QuestionID'],
                    'frage'   => $frage['Question'],
                    'antwort' => [
                        '1' => $frage['Answer1'],
                        '2' => $frage['Answer2'],
                        '3' => $frage['Answer3'],
                        '4' => $frage['Answer4']
                    ],
                    'richtig' => $frage['correctAnswer']
                ]
            ];
        } catch (PDOException $e) {
            return ['error' => 'Datenbankfehler: ' . $e->getMessage()];
        }
    }
    

    private function saveGameResultFromRequest(array $data): array {
        $playerId       = $data['playerId'] ?? null;
        $questionId     = $data['questionId'] ?? null;
        $selectedAnswer = $data['selectedAnswer'] ?? null;
        $correctAnswer  = $data['correctAnswer'] ?? null;
        $score          = $data['score'] ?? null;
    
        if ($playerId && $questionId && $selectedAnswer !== null && $correctAnswer !== null) {
            $this->saveGameResult(
                (int) $playerId,
                (int) $questionId,
                (int) $selectedAnswer,
                (int) $correctAnswer,
                is_numeric($score) ? (int) $score : null
            );
            return ['success' => true];
        } else {
            return ['success' => false, 'message' => 'Ungültige Daten'];
        }
    }
}

// Hauptausführung
$handler = new QuizHandler();
$handler->handleRequest($_SERVER['REQUEST_METHOD']);


?>