<?php
require_once 'database.php';
session_start();
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents("php://input"), true);
    $name = trim($data['name'] ?? '');
    $password = trim($data['password'] ?? '');

    try {
        $db = new Database();
        $stmt = $db->conn->prepare("SELECT PlayerID, password FROM player WHERE name = :name");
        $stmt->execute([':name' => $name]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['playerId'] = $user['PlayerID'];
            echo json_encode(['success' => true, 'playerId' => $user['PlayerID']]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Login fehlgeschlagen']);
        }
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => 'Fehler: ' . $e->getMessage()]);
    }
}
?>