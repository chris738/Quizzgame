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
                'answer1' => $fragen['Answer1'],
                'answer2' => $fragen['Answer2'],
                'answer3' => $fragen['Answer3'],
                'answer4' => $fragen['Answer4']
            ],
            'richtig' => $fragen['correctAnswer']
        ];
    } catch (PDOException $e) {
        return ['error' => 'Datenbankfehler: ' . $e->getMessage()];
    } catch (Exception $e) {
        return ['error' => 'Unbekannter Fehler: ' . $e->getMessage()];
    }
}


function checkAnswer($frageID, $answer) {
    $database = new Database();
    $correctAnswer = $database->getAnswer($frageID);

    if (!$correctAnswer) {
        return ['error' => 'Antwort konnte nicht abgerufen werden.'];
    }

    // Überprüfen, ob die Antwort korrekt ist
    if ($correctAnswer['correctAnswer'] == $answer) {
        return ['isCorrect' => true];
    } else {
        return ['isCorrect' => false];
    }
}

// Eingehende Anfrage verarbeiten
$method = $_SERVER['REQUEST_METHOD'];
$frageID = $_GET['frageID'] ?? null;
$answer = $_GET['answer'] ?? null; // Antwort des Benutzers
$randomquestion = $_GET['randomquestion'] ?? null;

try {
    if ($method === 'GET') {
        if ($frageID && $answer !== null) {
            // Wenn frageID und UserAnswer angegeben sind, überprüfe die Antwort
            $response = ['info' => checkAnswer($frageID, $answer)];
            echo json_encode($response);
        } elseif ($randomquestion) {
            $response = RandomQuestion();
        } else {
            // Ansonsten rufe die Frage ab
            $response = ['info' => RandomQuestion()];
            echo json_encode($response);
        }
    } elseif ($method === 'POST') {
        // POST-Verarbeitung hier (falls nötig)
    } else {
        echo json_encode(['success' => false, 'message' => 'Methode nicht unterstützt.']);
    }
} catch (Exception $e) {
    echo json_encode(['error' => 'Interner Serverfehler: ' . $e->getMessage()]);
}

?>
