<?php
require_once 'singleplayer.php';
require_once 'multiplayer.php';

class QuizHandler {
    private array $response = [];

    public function handleRequest(string $method): void {
        try {
            $mode = $this->detectMode($method);
            $data = $method === 'POST' ? json_decode(file_get_contents("php://input"), true) : $_GET;

            if ($method === 'GET') {
                $this->handleGet($mode, $data);
            } elseif ($method === 'POST') {
                $this->handlePost($mode, $data);
            } else {
                $this->response = ['success' => false, 'message' => '[QuizHandler] Methode nicht unterstützt.'];
            }
        } catch (Exception $e) {
            $this->response = ['error' => '[QuizHandler] Serverfehler: ' . $e->getMessage()];
        }

        echo json_encode($this->response);
    }

    private function detectMode(string $method): string {
        if ($method === 'POST') {
            $data = json_decode(file_get_contents("php://input"), true);
            return $data['mode'] ?? 'single';
        } elseif ($method === 'GET') {
            return $_GET['mode'] ?? 'single';
        }
        return 'single';
    }

    private function handleGet(string $mode, array $data): void {
        if ($mode === 'multiplayer') {
            $gameId = $data['gameId'] ?? null;
            $playerId = $data['playerId'] ?? null;
            $questionNr = $data['questionNr'] ?? null;

            if ($gameId && $playerId) {
                $handler = new MultiplayerHandler();
                $this->response = $handler->getNextQuestion((int)$gameId, (int)$playerId, (int)$questionNr);
            } else {
                $this->response = ['success' => false, 'message' => '[GET] Spiel-ID und Spieler-ID erforderlich.'];
            }
        } else {
            $handler = new SingleplayerHandler();
            $category = $data['category'] ?? null;
            $this->response = $handler->getRandomQuestionResponse($category);
        }
    }

    private function handlePost(string $mode, array $data) {
        if (!isset($data['playerId']) || !is_numeric($data['playerId'])) {
            $this->response = ['success' => false, 'message' => '[POST] Spieler-ID fehlt oder ungültig'];
            return;
        }

        if ($mode === 'multiplayer') {
            $handler = new MultiplayerHandler();

            if (($data['action'] ?? null) === 'joinOrCreateGame') {
                $this->response = $handler->joinOrCreateGame((int)$data['playerId']);
            } else {
                $this->response = $handler->saveAnswer($data);
            }
        } elseif ($mode === 'single') {
            $handler = new SingleplayerHandler();
            $this->response = $handler->saveAnswer($data);
        } else {
            $this->response = ['success' => false, 'message' => '[saveGameResultFromRequest] Ungültige Daten'];
        }
    }
    
    private function loadMultiplayerQuestion(int $gameId, int $playerId): array {
        $frage = $this->getMultiplayerQuestion($gameId, $playerId);
    
        if (!$frage || !is_array($frage)) {
            return [
                'message' => '[loadMultiplayerQuestion] Keine neue Frage mehr verfügbar',
                'debug' => [
                    'gameId' => $gameId,
                    'playerId' => $playerId,
                    'frage' => $frage
                ]
            ];
        }
    
        return [
            'info' => [
                'id'      => $frage['QuestionID'] ?? null,
                'frage'   => $frage['Question'] ?? null,
                'antwort' => [
                    '1' => $frage['Answer1'] ?? null,
                    '2' => $frage['Answer2'] ?? null,
                    '3' => $frage['Answer3'] ?? null,
                    '4' => $frage['Answer4'] ?? null
                ],
                'richtig' => $frage['correctAnswer'] ?? null,
                'nr'      => $frage['QuestionNumber'] ?? null
            ]
        ];
    }
    
}

// Hauptausführung
$handler = new QuizHandler();
$handler->handleRequest($_SERVER['REQUEST_METHOD']);
