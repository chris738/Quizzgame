<?php
require_once 'database.php';
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents("php://input"), true);
    $name = trim($data['name'] ?? '');
    $password = trim($data['password'] ?? '');

    if (!$name || !$password) {
        echo json_encode(['success' => false, 'message' => 'Name oder Passwort fehlt']);
        exit;
    }

    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    try {
        $db = new Database();
        $stmt = $db->conn->prepare("INSERT INTO player (name, password) VALUES (:name, :password)");
        $stmt->execute([':name' => $name, ':password' => $hashedPassword]);

        echo json_encode(['success' => true]);
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Name bereits vergeben?']);
    }
}
?>