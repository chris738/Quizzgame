<?php
require_once 'database.php';

class SingleplayerHandler extends Database {
    public function getRandomQuestionResponse(?string $category): array {
        $frage = $this->getRandomQuestions($category);
        if (!$frage || empty($frage['Question'])) {
            return ['error' => '[Singleplayer] Keine gültige Frage gefunden.'];
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
    }

    public function saveAnswer(array $data): array {
        $playerId = $data['playerId'] ?? null;
        $questionId = $data['questionId'] ?? null;
        $selectedAnswer = $data['selectedAnswer'] ?? null;
        $correctAnswer = $data['correctAnswer'] ?? null;
        $score = $data['score'] ?? null;

        if ($playerId && $questionId && $selectedAnswer !== null && $correctAnswer !== null) {
            $this->saveGameResult((int)$playerId, (int)$questionId, (int)$selectedAnswer, (int)$correctAnswer, is_numeric($score) ? (int)$score : null);
            return ['success' => true];
        }

        return ['success' => false, 'message' => '[Singleplayer] Ungültige Daten'];
    }
}
