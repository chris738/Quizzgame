<?php
// Datenbank-Verbindungsdetails
$servername = "localhost"; // Der Hostname (normalerweise 'localhost')
$username = "quizgame";    // Der Benutzername
$password = "sicheresPasswort";  // Das Passwort für den Benutzer
$dbname = "quizgame";      // Der Name der Datenbank

// Verbindung herstellen
$conn = new mysqli($servername, $username, $password, $dbname);

// Überprüfen, ob die Verbindung erfolgreich ist
if ($conn->connect_error) {
    die("Verbindung fehlgeschlagen: " . $conn->connect_error);
}

// Funktion zum Einfügen einer Frage
function insertQuestion($question, $answer1, $answer2, $answer3, $answer4) {
    global $conn;
    $stmt = $conn->prepare("INSERT INTO Fragen (Question, Answer1, Answer2, Answer3, Answer4) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sssss", $question, $answer1, $answer2, $answer3, $answer4);
    if ($stmt->execute()) {
        echo "Frage erfolgreich hinzugefügt!";
    } else {
        echo "Fehler beim Hinzufügen der Frage: " . $stmt->error;
    }
    $stmt->close();
}

// Funktion zum Abrufen aller Fragen
function getAllQuestions() {
    global $conn;
    $sql = "SELECT * FROM Fragen";
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            echo "Frage: " . $row["Question"] . "<br>";
            echo "Antwort 1: " . $row["Answer1"] . "<br>";
            echo "Antwort 2: " . $row["Answer2"] . "<br>";
            echo "Antwort 3: " . $row["Answer3"] . "<br>";
            echo "Antwort 4: " . $row["Answer4"] . "<br><br>";
        }
    } else {
        echo "Keine Fragen gefunden.";
    }
}

// Funktion zum Löschen einer Frage
function deleteQuestion($questionID) {
    global $conn;
    $stmt = $conn->prepare("DELETE FROM Fragen WHERE QuestionID = ?");
    $stmt->bind_param("i", $questionID);
    if ($stmt->execute()) {
        echo "Frage erfolgreich gelöscht!";
    } else {
        echo "Fehler beim Löschen der Frage: " . $stmt->error;
    }
    $stmt->close();
}


// Funktion zum Löschen eines Spielers
function deletePlayer($playerID) {
    global $conn;
    $stmt = $conn->prepare("DELETE FROM player WHERE PlayerID = ?");
    $stmt->bind_param("i", $playerID);
    if ($stmt->execute()) {
        echo "Spieler erfolgreich gelöscht!";
    } else {
        echo "Fehler beim Löschen des Spielers: " . $stmt->error;
    }
    $stmt->close();
}

// Beispielaufruf der Funktionen
// Frage hinzufügen
insertQuestion('Was ist die Hauptstadt von Deutschland?', 'Berlin', 'München', 'Hamburg', 'Köln');

// Alle Fragen abrufen
getAllQuestions();

// Spieler löschen
deletePlayer(1);

// Verbindung schließen
$conn->close();
?>

