<?php
require_once 'database.php';
header('Content-Type: text/html; charset=utf-8');

function getFragen($frageID) {
    $database = new Database();
    $fragen = $database->getFragen($frageID);

    if (!$fragen) {
        return ['error' => 'getFragen konnten nicht abgerufen werden.'];
    }

    return [
    	'fragen' => [
	        'Question'     => $fragen['Question'],
            'AnswerGreen'  => $fragen['Answer1'],
            'AnswerRed'    => $fragen['Answer2'],
            'AnswerYellow' => $fragen['Answer3'],
            'AnswerBlue'   => $fragen['Answer4']
        ]
    ];
}

function getAnswer($frageID, $answer) {
    $database = new Database();
    $correctAnswer = $database->getCorrectAnswer($frageID);

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

try {
    if ($method === 'GET') {
        $response = ['info' => getFragen($frageID)];
        echo json_encode($response);

    } elseif ($method === 'POST') {
        $answer = json_decode(file_get_contents('php://input'), true);
        echo handleBuildingUpgrade($frageID, $answer);

    } else {
        echo json_encode(['success' => false, 'message' => 'Methode nicht unterstützt.']);
    }
} catch (Exception $e) {
    echo json_encode(['error' => 'Interner Serverfehler: ' . $e->getMessage()]);
}

?>
