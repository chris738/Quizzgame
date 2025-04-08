<?php

use PHPUnit\Framework\TestCase;

class LoginTest extends TestCase
{
    private string $url = 'https://quizz.tuxchen.de/php/login.php';

    public function testEmptyLoginData()
    {
        $response = $this->postJson([]);
        $this->assertFalse($response['success']);
        $this->assertEquals('Name und Passwort erforderlich', $response['message']);
    }

    public function testInvalidCredentials()
    {
        $response = $this->postJson([
            'name' => 'testuser',
            'password' => 'wrongpass'
        ]);
        $this->assertFalse($response['success']);
        $this->assertEquals('Falsche Zugangsdaten', $response['message']);
    }

    public function testValidLogin()
    {
        $response = $this->postJson([
            'name' => 'testuser',
            'password' => 'correctpass'
        ]);
        $this->assertTrue($response['success']);
        $this->assertArrayHasKey('playerId', $response);
    }

    private function postJson(array $data): array
    {
        $options = [
            'http' => [
                'method'  => 'POST',
                'header'  => "Content-Type: application/json",
                'content' => json_encode($data),
                'ignore_errors' => true
            ]
        ];

        $context  = stream_context_create($options);
        $result = file_get_contents($this->url, false, $context);

        $this->assertNotFalse($result, 'Fehler beim Abrufen der Login-API');
        $decoded = json_decode($result, true);
        $this->assertIsArray($decoded, 'Antwort ist kein gültiges JSON');

        echo "\nAntwort:\n" . json_encode($decoded, JSON_PRETTY_PRINT) . "\n";

        return $decoded;
    }
}

?>