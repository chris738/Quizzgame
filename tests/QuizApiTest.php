<?php

use PHPUnit\Framework\TestCase;

class QuizApiTest extends TestCase
{
    public function testQuizApiWithCategoryReturnsValidJson()
    {
        $url = 'https://quizz.tuxchen.de/php/quiz.php';

        $response = @file_get_contents($url);
        $this->assertNotFalse($response, "Fehler beim Abrufen von $url");

        $data = json_decode($response, true);

        // Ensure the response is valid JSON
        $this->assertNotNull($data, 'Antwort ist kein gültiges JSON');
        $this->assertIsArray($data, 'Antwort ist kein gültiges JSON');
    }
}

?>
