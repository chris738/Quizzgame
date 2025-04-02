<?php
require_once 'database.php';

header('Content-Type: application/json');

$db = new Database();
$highscores = $db->getTopHighscores();
echo json_encode($highscores);
?>
