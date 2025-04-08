<?php

use PHPUnit\Framework\TestCase;

class QuizApiTest extends TestCase
{
    public function testQuizApiWithCategoryReturnsValidJson()
    {
        // Die URL der neuen API mit der Kategorie 'Musik' als GET-Parameter
        $url = 'https://chris.quizz.tuxchen.de/php/quiz.php?category=Musik';

        // Initialisierung des cURL-Requests für die GET-Anfrage
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        // Senden der Anfrage
        $response = curl_exec($ch);
        
        // Check for cURL errors
        if(curl_errno($ch)) {
            echo 'cURL error: ' . curl_error($ch);
        }
        curl_close($ch);

        // Log the raw response for debugging
        echo "\nRaw API Response:\n" . $response . "\n";

        // Ensure the response is not empty or null
        $this->assertNotNull($response, "API response is null");

        // Decode JSON response
        $data = json_decode($response, true);

        // Ensure the response is valid JSON
        $this->assertNotNull($data, 'Antwort ist kein gültiges JSON');
        $this->assertIsArray($data, 'Antwort ist kein gültiges JSON');
    }
}

?>
