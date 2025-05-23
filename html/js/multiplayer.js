let gameId = null;
let playerId = null;
let currentQuestionId = null;
let questionNumber = 1;
//let correctAnswer = null;

// Initialisiert den Multiplayer-Modus für den aktuellen Spieler
async function initMultiplayer(currentPlayerId) {
    playerId = currentPlayerId; // Setze die globale playerId

    // Debug-Ausgabe der gesendeten Daten
    console.log('[initMultiplayer] Sende:', {
        mode: 'multiplayer',
        action: 'joinOrCreateGame',
        playerId
    });

    // Anfrage an den Server senden, um Spiel beizutreten oder zu erstellen
    const response = await fetch('php/quiz.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
            mode: 'multiplayer',
            action: 'joinOrCreateGame',
            playerId: playerId
        })
    });

    // Antwort parsen
    const result = await response.json();

    // Erfolgreiche Antwort vom Server
    if (result.success) {
        gameId = result.gameId; // Globale gameId setzen

        // Status des Spiels auswerten und entsprechende Nachricht ausgeben
        if (result.status === 'joined') {
            console.log(`[initMultiplayer] Multiplayer-Game beigetreten: ${gameId}`);
        } else if (result.status === 'created') {
            console.log(`[initMultiplayer] Multiplayer-Game erstellt: ${gameId}`);
        } else {
            console.log(`[initMultiplayer] Multiplayer-Game gestartet: ${gameId}`);
        }

        // Warteraum ausblenden und Quiz anzeigen
        document.getElementById('waitingRoom')?.style?.setProperty('display', 'none'); 
        document.getElementById('quizContainer')?.style.setProperty('display', 'block');

        // Spiel starten
        await startGame();

    } else {
        // Fehlerfall: Ausgabe der Fehlermeldung
        console.error(`[initMultiplayer] Fehler beim Beitreten: ${result.message || 'Unbekannter Fehler'}`);
        console.error('[initMultiplayer] Fehler beim Beitreten:', result.message, result);
    }
}

async function startGame() {
    await loadNextQuestion();
}

function waitForOtherPlayers(message = 'Warte auf Mitspieler...') {
    console.log('[waitForOtherPlayers]', message);

    // Warteanzeige im UI
    document.getElementById('quizContainer').style.display = 'none';
    document.getElementById('waitingRoom').style.display = 'block';
    document.getElementById('waitingRoom').innerHTML = `
      <h2>Mehrspieler-Modus</h2>
      <p>${message}</p>
    `;

    // Nach 5 Sekunden erneut versuchen
    setTimeout(loadNextQuestion, 5000);
}

async function loadNextQuestion() {

    //Holen der Frage und Antworten vom Server
    const response = await fetch('php/quiz.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
            mode: 'multiplayer',
            action: 'getNextQuestion',
            gameId: gameId,
            playerId: playerId,
            questionNr: questionNumber
        })
    });

    const result = await response.json();

    //Prüfen, ob die Frage angezeigt werden kann
    if (result.wait) {
        if (result.skipped) {
            console.log(`[loadNextQuestion] Frage ${questionNumber} wurde vom Gegner gespielt. Überspringe.`);
            if (questionNumber < 16) {
                questionNumber++;
                loadNextQuestion();
            } else {
                console.log('[loadNextQuestion] Alle 16 Fragen beantwortet.');
                showFinalScore();
                return;
            }
            return;
        }

        //Warten auf andere Spieler
        waitForOtherPlayers(result.message);
        return;
    }

    //Anzeie der Frage
    displayQuestion(result.info);
}

async function submitAnswer(answerNumber) {
    const selectedAnswer = parseInt(answerNumber, 10);
    const isCorrect = (selectedAnswer === correctAnswer);
    const score = calculateScore(isCorrect);

    // Anzeige aktualisieren
    showAnswerFeedback(isCorrect, selectedAnswer, score);

    // An Server senden
    const response = await fetch('php/quiz.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
            mode: 'multiplayer',
            gameId: gameId,
            playerId: playerId,
            questionId: currentQuestionId,
            selectedAnswer: selectedAnswer,
            correctAnswer: correctAnswer,
            questionNumber: questionNumber
        })
    });

    const result = await response.json();

    if (result.success) {
        console.log(`[submitAnswer] Antwort war ${result.correct ? 'richtig' : 'falsch'} - Frage Nr: ${result.nr}`);
        
        if (questionNumber < 16) {
            questionNumber++;
            document.getElementById('newQuestionBtn').onclick = loadNextQuestion;

        } else {
            console.log('Spiel beendet. Alle 16 Fragen beantwortet.');
            // Optional: Endbildschirm oder Highscore
        }
    }


}


// ✅ Automatisch starten beim Seitenladen
document.addEventListener('DOMContentLoaded', () => {
    /*
    const storedId = parseInt(localStorage.getItem('playerId')) || 0;
    console.log(`[Info] Stored Player ID ist: ${storedId}`);
    if (storedId > 0) {
        initMultiplayer(storedId);
    } else {
        console.error('[DOMContentLoaded] Keine gültige Spieler-ID gefunden. Abbruch.');
        window.location.href = 'login.html';
    }
        */
});
