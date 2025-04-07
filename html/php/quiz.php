<?php
require_once 'database.php';
header('Content-Type: application/json');

class QuizHandler extends Database {
    private array $response = [];

    public function handleRequest(string $method): void {
        try {
            $mode = 'single';
    
            if ($method === 'POST') {
                $rawData = file_get_contents("php://input");
                $data = json_decode($rawData, true);
                $mode = $data['mode'] ?? 'single';
            } elseif ($method === 'GET') {
                $mode = $_GET['mode'] ?? 'single';
            }
    
            if ($method === 'GET') {
                $this->handleGet($mode);
            } elseif ($method === 'POST') {
                $this->handlePost($mode);
            } else {
                $this->response = ['success' => false, 'message' => '[handleRequest] Methode nicht unterstützt.'];
            }
        } catch (Exception $e) {
            $this->response = ['error' => '[handleRequest] Interner Serverfehler: ' . $e->getMessage()];
        }
    
        echo json_encode($this->response);
    }
    
    private function handleGet(string $mode): void {
        if ($mode === 'multiplayer') {
            $gameId   = $_GET['gameId']   ?? null;
            $playerId = $_GET['playerId'] ?? null;

            if ($gameId && $playerId) {
                $frage = $this->getMultiplayerQuestion((int)$gameId, (int)$playerId);
                if ($frage) {
                    $this->response = [
                        'info' => [
                            'id'      => $frage['QuestionID'],
                            'frage'   => $frage['Question'],
                            'antwort' => [
                                '1' => $frage['Answer1'],
                                '2' => $frage['Answer2'],
                                '3' => $frage['Answer3'],
                                '4' => $frage['Answer4']
                            ],
                            'richtig' => $frage['correctAnswer'],
                            'nr'      => $frage['QuestionNumber']
                        ]
                    ];
                } else {
                    $this->response = [
                        'message' => '[handleGet] Keine neue Frage mehr verfügbar',
                        'debug' => [
                            'gameId'   => $gameId,
                            'playerId' => $playerId,
                            'frage'    => $frage
                        ]
                    ];
                }
            } else {
                $this->response = ['success' => false, 'message' => '[handleGet] Spiel-ID und Spieler-ID erforderlich.'];
            }
        } else {
            $category = $_GET['category'] ?? null;
            $this->response = $this->loadRandomQuestion($category);
        }
    }
    
    private function handlePost(string $mode): void {
        $data = json_decode(file_get_contents("php://input"), true);
    
        error_log("[quiz.php] POST-Daten: " . json_encode($data));
    
        if (!isset($data['playerId']) || !is_numeric($data['playerId'])) {
            $this->response = ['success' => false, 'message' => '[handlePost] Spieler-ID fehlt oder ungültig'];
            return;
        }
    
        if ($mode === 'multiplayer') {
            $this->handleMultiplayerPost($data);
        } else {
            $this->response = $this->handleSingleplayerAnswer($data);
        }
    }

    private function handleMultiplayerPost(array $data): void {
        $action = $data['action'] ?? null;
    
        if ($action === 'joinOrCreateGame') {
            $this->response = $this->handleJoinOrCreateGame($data);
            return;
        }
    
        // Standard: Antwort verarbeiten
        $this->response = $this->handleMultiplayerAnswer($data);
    }

    private function handleJoinOrCreateGame(array $data): array {
        $playerId = $data['playerId'] ?? null;

        if (!is_numeric($playerId)) {
            return ['success' => false, 'message' => '[handleJoinOrCreateGame] Ungültige Spieler-ID'];
        }

        $gameId = $this->joinOrCreateMultiplayerGame((int)$playerId);
        return ['success' => true, 'gameId' => $gameId];
    }
    
    private function handleMultiplayerAnswer(array $data): array {
        $gameId         = $data['gameId'] ?? null;
        $playerId       = $data['playerId'] ?? null;
        $questionId     = $data['questionId'] ?? null;
        $selectedAnswer = $data['selectedAnswer'] ?? null;
        $correctAnswer  = $data['correctAnswer'] ?? null;
    
        if ($gameId && $playerId && $questionId !== null && $selectedAnswer !== null && $correctAnswer !== null) {
            $isCorrect = parent::saveMultiplayerAnswer(
                (int)$gameId,
                (int)$playerId,
                (int)$questionId,
                (int)$selectedAnswer,
                (int)$correctAnswer
            );
            return ['success' => true, 'correct' => (bool)$isCorrect];
        }
    
        return ['success' => false, 'message' => '[handleMultiplayerAnswer] Ungültige oder fehlende Felder'];
    }

    private function handleSingleplayerAnswer(array $data): array {
        return $this->saveGameResultFromRequest($data);
    }
    
    private function loadRandomQuestion(string $category = null): array {
        try {
            $frage = $this->getRandomQuestion($category);
    
            if (!$frage || empty($frage['Question'])) {
                return ['error' => '[loadRandomQuestion] Keine gültige Frage gefunden.'];
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
            return ['error' => '[loadRandomQuestion] Datenbankfehler: ' . $e->getMessage()];
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
            return ['success' => false, 'message' => '[saveGameResultFromRequest] Ungültige Daten'];
        }
    }
}

// Hauptausführung
$handler = new QuizHandler();
$handler->handleRequest($_SERVER['REQUEST_METHOD']);


?>