<?php
require_once 'database.php';

class MultiplayerHandler extends Database {
    public function joinOrCreateGame(int $playerId): array {
        $gameId = $this->joinOrCreateMultiplayerGame($playerId);
        return ['success' => true, 'gameId' => $gameId];
    }

    public function saveAnswer(array $data): array {
        $gameId = $data['gameId'] ?? null;
        $playerId = $data['playerId'] ?? null;
        $questionId = $data['questionId'] ?? null;
        $selectedAnswer = $data['selectedAnswer'] ?? null;
        $correctAnswer = $data['correctAnswer'] ?? null;

        if ($gameId && $playerId && $questionId !== null && $selectedAnswer !== null && $correctAnswer !== null) {
            $isCorrect = $this->saveMultiplayerAnswer((int)$gameId, (int)$playerId, (int)$questionId, (int)$selectedAnswer, (int)$correctAnswer);
            return ['success' => true, 'correct' => (bool)$isCorrect];
        }

        return ['success' => false, 'message' => '[Multiplayer] Ungültige oder fehlende Felder'];
    }

    public function getNextQuestion(int $gameId, int $playerId): array {
        // Prüfen, ob dieser Spieler an der Reihe ist
        if (!$this->isPlayersTurn($gameId, $playerId)) {
            return [
                'wait' => true,
                'message' => 'Der andere Spieler ist an der Reihe.'
            ];
        }
    
        // Frage laden
        $frage = $this->getMultiplayerQuestion($gameId, $playerId);
    
        if (!$frage || !is_array($frage)) {
            return ['message' => '[Multiplayer] Keine neue Frage mehr verfügbar'];
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
    
}
