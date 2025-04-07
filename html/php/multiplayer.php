<?php
require_once 'database.php';

class MultiplayerHandler extends Database {
    public function joinOrCreateGame(int $playerId): array {
        // Aufruf der vererbten Funktion aus Database
        return $this->joinOrCreateMultiplayerGame($playerId);
    }
    
    public function saveAnswer(array $data): array {
        $gameId = $data['gameId'] ?? null;
        $playerId = $data['playerId'] ?? null;
        $questionId = $data['questionId'] ?? null;
        $selectedAnswer = $data['selectedAnswer'] ?? null;
        $correctAnswer = $data['correctAnswer'] ?? null;
        $questionNumber = $data['questionNumber'] ?? null;
    
        if (
            $gameId !== null &&
            $playerId !== null &&
            $questionId !== null &&
            $selectedAnswer !== null &&
            $correctAnswer !== null &&
            $questionNumber !== null
        ) {
            $isCorrect = $this->saveMultiplayerAnswer(
                (int)$gameId,
                (int)$playerId,
                (int)$questionId,
                (int)$selectedAnswer,
                (int)$correctAnswer,
                (int)$questionNumber
            );
            return ['success' => true, 'correct' => (bool)$isCorrect];
        }
    
        return ['success' => false, 'message' => '[Multiplayer] Ungültige oder fehlende Felder'];
    }
    

    public function getNextQuestion(int $gameId, int $playerId, int $questionNr): array {
        // Prüfen, ob dieser Spieler an der Reihe ist
        if (!$this->isPlayersTurn($gameId, $playerId, $questionNr)) {
            return [
                'wait' => true,
                'skipped' => true,
                'message' => "Frage $questionNr wurde bereits beantwortet oder ist nicht deine Runde."
            ];
        }
        
        
        // Frage laden
        $frage = $this->getMultiplayerQuestion($gameId, $playerId, $questionNr);
    
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
