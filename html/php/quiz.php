<?php
require_once 'database.php';
header('Content-Type: application/json');

class QuizHandler extends Database {
    private array $response = [];

    public function handleRequest(string $method): void {
        try {
            $mode = $_REQUEST['mode'] ?? 'single';
    
            if ($method === 'GET') {
                $this->handleGet($mode);
            } elseif ($method === 'POST') {
                $this->handlePost($mode);
            } else {
                $this->response = ['success' => false, 'message' => 'Methode nicht unterstützt.'];
            }
        } catch (Exception $e) {
            $this->response = ['error' => 'Interner Serverfehler: ' . $e->getMessage()];
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
                    $this->response = ['message' => 'Keine neue Frage mehr verfügbar'];
                }
            } else {
                $this->response = ['success' => false, 'message' => 'Spiel-ID und Spieler-ID erforderlich.'];
            }
        } else {
            $category = $_GET['category'] ?? null;
            $this->response = $this->loadRandomQuestion($category);
        }
    }
    
    private function handlePost(string $mode): void {
        $data = json_decode(file_get_contents("php://input"), true);
    
        $playerId = $data['playerId'] ?? null;
        if ($playerId) {
            if ($mode === 'multiplayer') {
                $action = $data['action'] ?? null;
        
                if ($action === 'joinOrCreateGame') {
                    $playerId = $data['playerId'] ?? null;
        
                    if ($playerId) {
                        $gameId = $this->joinOrCreateMultiplayerGame((int)$playerId);
                        $this->response = ['success' => true, 'gameId' => $gameId];
                    } else {
                        $this->response = ['success' => false, 'message' => 'Spieler-ID fehlt'];
                    }
                    return;
                }
        
                // Antwort übermitteln (Standard)
                $gameId         = $data['gameId'] ?? null;
                $playerId       = $data['playerId'] ?? null;
                $questionId     = $data['questionId'] ?? null;
                $selectedAnswer = $data['selectedAnswer'] ?? null;
                $correctAnswer  = $data['correctAnswer'] ?? null;
        
                if ($gameId && $playerId && $questionId !== null && $selectedAnswer !== null && $correctAnswer !== null) {
                    $isCorrect = $this->handleMultiplayerAnswer(
                        (int)$gameId,
                        (int)$playerId,
                        (int)$questionId,
                        (int)$selectedAnswer,
                        (int)$correctAnswer
                    );
        
                    $this->response = ['success' => true, 'correct' => (bool)$isCorrect];
                } else {
                    $this->response = ['success' => false, 'message' => 'Ungültige oder fehlende Felder'];
                }
            } else {
                $this->response = $this->saveGameResultFromRequest($data);
            }
        } else {
            $this->response = ['success' => false, 'message' => 'Spieler-ID fehlt'];
        }
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

    private function loadMultiplayerQuestion(int $gameId, int $playerId): array {
        $frage = $this->getMultiplayerQuestion($gameId, $playerId);
    
        if (!$frage) {
            return ['message' => 'Keine neue Frage mehr verfügbar'];
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
                'richtig' => $frage['correctAnswer'],
                'nr'      => $frage['QuestionNumber']
            ]
        ];
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
    
        return ['success' => false, 'message' => 'Ungültige oder fehlende Felder'];
    }
    
}

// Hauptausführung
$handler = new QuizHandler();
$handler->handleRequest($_SERVER['REQUEST_METHOD']);


?>