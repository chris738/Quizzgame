<?php
require_once 'database.php';
header('Content-Type: text/html; charset=utf-8');

function getFragen() {
    $database = new Database();
    $fragen = $database->getFragen();

    if (!$fragen) {
        return ['error' => 'getFragen konnten nicht abgerufen werden.'];
    }

    return [
        'map' => array_map(function($fragen) {
            return [
                'Question' => $fragen['Question'],
                'Answer1' => $fragen['Answer1'],
                'Answer1' => $fragen['Answer2'],
                'Answer1' => $fragen['Answer3'],
                'Answer1' => $fragen['Answer4']
            ];
        }, $fragen),
    ];
}

// Eingehende Anfrage verarbeiten
$method = $_SERVER['REQUEST_METHOD'];
$FrageID = $_GET['FrageID'] ?? null;

try {
    if ($method === 'GET') {

        $response = ['info' => getFrage()];

        echo json_encode($response);

    } else {
        echo json_encode(['success' => false, 'message' => 'Methode nicht unterstützt.']);
    }
} catch (Exception $e) {
    echo json_encode(['error' => 'Interner Serverfehler: ' . $e->getMessage()]);
}

?>
