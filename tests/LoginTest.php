<?php
use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../html/php/login.php';

class LoginTest extends TestCase
{
    protected function setUp(): void {
        $_SERVER['REQUEST_METHOD'] = 'POST';
    }

    public function testEmptyLoginData() {
        $this->mockInput(['name' => '', 'password' => '']);

        ob_start();
        handleLogin();
        $output = ob_get_clean();

        $json = json_decode($output, true);

        $this->assertFalse($json['success']);
        $this->assertEquals('Name und Passwort erforderlich', $json['message']);
    }

    public function testInvalidCredentials() {
        $this->mockInput(['name' => 'testuser', 'password' => 'wrongpass']);

        $dbMock = $this->createMock(Database::class);
        $dbMock->method('getUserByName')->willReturn([
            'PlayerID' => 1,
            'password' => password_hash('correctpass', PASSWORD_DEFAULT)
        ]);

        $this->overrideDatabaseInstance($dbMock);

        ob_start();
        handleLogin();
        $output = ob_get_clean();

        $json = json_decode($output, true);

        $this->assertFalse($json['success']);
        $this->assertEquals('Falsche Zugangsdaten', $json['message']);
    }

    public function testSuccessfulLogin() {
        $this->mockInput(['name' => 'testuser', 'password' => 'correctpass']);

        $dbMock = $this->createMock(Database::class);
        $dbMock->method('getUserByName')->willReturn([
            'PlayerID' => 42,
            'password' => password_hash('correctpass', PASSWORD_DEFAULT)
        ]);

        $this->overrideDatabaseInstance($dbMock);

        ob_start();
        handleLogin();
        $output = ob_get_clean();

        $json = json_decode($output, true);

        $this->assertTrue($json['success']);
        $this->assertEquals(42, $json['playerId']);
    }

    private function mockInput(array $data): void {
        // php://input kann nicht direkt überschrieben werden, also tricksen wir:
        file_put_contents('php://temp', json_encode($data));
        stream_wrapper_unregister('php');
        stream_wrapper_register('php', MockPhpStream::class);
        MockPhpStream::$content = json_encode($data);
    }

    private function overrideDatabaseInstance($mock): void {
        // Dazu musst du `new Database()` in handleLogin() z.B. durch `getDatabase()` ersetzen
        // und hier eine globale Hilfsfunktion mocken oder überschreiben
        // Beispiel siehe unten
    }
}


?>