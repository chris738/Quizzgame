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

            if ($gameId && $playerId) {
                $handler = new MultiplayerHandler();
                $this->response = $handler->getNextQuestion((int)$gameId, (int)$playerId);
            } else {
                $this->response = ['success' => false, 'message' => '[GET] Spiel-ID und Spieler-ID erforderlich.'];
            }
        } else {
            $handler = new SingleplayerHandler();
            $category = $data['category'] ?? null;
            $this->response = $handler->getRandomQuestionResponse($category);
        }
    }

    private function handlePost(string $mode, array $data): void {
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
        } else {
            $handler = new SingleplayerHandler();
            $this->response = $handler->saveAnswer($data);
        }
    }
}

// Hauptausführung
$handler = new QuizHandler();
$handler->handleRequest($_SERVER['REQUEST_METHOD']);
