<?php
require_once 'database.php';
session_start();
header('Content-Type: application/json');

// --- Hauptsteuerung ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    handleLogin();
} elseif ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['playerId'])) {
    handleGetUsername();
} else {
    echo json_encode(['success' => false, 'message' => 'Ungültige Anfrage']);
    exit;
}

// --- Login-Logik ---
function handleLogin(): void {
    $data = json_decode(file_get_contents("php://input"), true);
    $name = trim($data['name'] ?? '');
    $password = trim($data['password'] ?? '');

    if ($name === '' || $password === '') {
        echo json_encode(['success' => false, 'message' => 'Name und Passwort erforderlich']);
        return;
    }

    try {
        $db = new Database();
        $user = $db->getUserByName($name);

        if ($user && password_verify($password, $user['password'])) {
            echo json_encode(['success' => true, 'playerId' => $user['PlayerID']]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Falsche Zugangsdaten']);
        }
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => 'Fehler: ' . $e->getMessage()]);
    }
}

// --- Benutzername abrufen ---
function handleGetUsername(): void {
    $playerId = (int) $_GET['playerId'];

    if ($playerId <= 0) {
        echo json_encode(['success' => false, 'message' => 'Ungültige ID']);
        return;
    }

    try {
        $db = new Database();
        $user = $db->getUserById($playerId);

        if ($user) {
            echo json_encode(['success' => true, 'username' => $user['username']]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Benutzer nicht gefunden']);
        }
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => 'Fehler: ' . $e->getMessage()]);
    }
}
?>
