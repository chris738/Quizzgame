<?php

use PHPUnit\Framework\TestCase;

class QuizApiTest extends TestCase
{
    public function testQuizApiWithCategoryReturnsValidQuestion()
    {
        // Die URL der neuen API
        $url = 'https://chris.quizz.tuxchen.de/php/quiz.php';

        // Daten, die als POST gesendet werden
        $postData = ['category' => 'Musik'];

        // Initialisierung des cURL-Requests für die POST-Anfrage
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postData));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        // Senden der Anfrage
        $response = curl_exec($ch);
        curl_close($ch);

        $this->assertNotFalse($response, "Fehler beim Abrufen von $url");

        $data = json_decode($response, true);
        $this->assertIsArray($data, 'Antwort ist kein gültiges JSON');

        $this->assertArrayHasKey('info', $data, 'Feld "info" fehlt');
        $info = $data['info'];

        echo "\n✅ Parsed JSON (info):\n" . json_encode($info, JSON_PRETTY_PRINT) . "\n";

        $this->assertArrayHasKey('id', $info);
        $this->assertArrayHasKey('richtig', $info);
        $this->assertArrayHasKey('frage', $info);
        $this->assertArrayHasKey('antwort', $info);

        $this->assertIsArray($info['antwort']);
        foreach (["1", "2", "3", "4"] as $key) {
            $this->assertArrayHasKey($key, $info['antwort']);
        }
    }
}

?>
