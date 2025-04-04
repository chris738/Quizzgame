<?php
use PHPUnit\Framework\TestCase;

class HighscoreApiTest extends TestCase
{
    public function testHighscoreApiReturnsValidJson()
    {
        $url = 'https://quizz.tuxchen.de/php/highscore.php';

        $response = @file_get_contents($url);
        $this->assertNotFalse($response, "Fehler beim Abrufen von $url");

        $data = json_decode($response, true);
        $this->assertIsArray($data, 'Antwort ist kein gültiges JSON');

        foreach ($data as $entry) {
            $this->assertArrayHasKey('username', $entry);
            $this->assertArrayHasKey('totalScore', $entry);
        }
    }
}


?>