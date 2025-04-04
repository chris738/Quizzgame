<?php

use PHPUnit\Framework\TestCase;

class QuizApiTest extends TestCase
{
    public function testQuizApiWithoutCategoryReturnsValidQuestion()
    {
        $url = 'https://chris.quizz.tuxchen.de/php/quiz.php';

        $response = @file_get_contents($url);
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