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
        if ($mode === 'single') {
            $handler = new SingleplayerHandler();
            $category = $data['category'] ?? null;
            $this->response = $handler->getRandomQuestionResponse($category);
        } else {
            $this->response = ['success' => false, 'message' => '[GET] Ungültiger Modus oder veraltet'];
        }
    }
    
    private function handlePost(string $mode, array $data) {
        if (!isset($data['playerId']) || !is_numeric($data['playerId'])) {
            $this->response = [
                'success' => false, 
                'message' => '[POST] Spieler-ID fehlt oder ungültig'
            ];
            return;
        }
    
        if ($mode === 'multiplayer') {
            $handler = new MultiplayerHandler();
    
            switch ($data['action'] ?? null) {
                case 'joinOrCreateGame':
                    $result = $handler->joinOrCreateGame((int)$data['playerId']);
                    $this->response = $result;
                    break;
                
                case 'getNextQuestion':
                    if (!isset($data['gameId'], $data['questionNr'])) {
                        $this->response = ['success' => false, 'message' => 'Spiel-ID oder Frage-Nr fehlt'];
                        return;
                    }
                    $this->response = $handler->getNextQuestion(
                        (int)$data['gameId'], 
                        (int)$data['playerId'], 
                        (int)$data['questionNr']
                    );
                    break;
    
                default:
                    $this->response = $handler->saveAnswer($data);
            }
        } elseif ($mode === 'single') {
            $handler = new SingleplayerHandler();
            $this->response = $handler->saveAnswer($data);
        } else {
            $this->response = ['success' => false, 'message' => '[saveGameResultFromRequest] Ungültige Daten'];
        }
    }
}

// Hauptausführung
$handler = new QuizHandler();
$handler->handleRequest($_SERVER['REQUEST_METHOD']);
