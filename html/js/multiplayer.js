let gameId = null;
let playerId = null;
let currentQuestionId = null;
//let correctAnswer = null;

async function initMultiplayer(currentPlayerId) {
    playerId = currentPlayerId;

    console.log('[initMultiplayer] Sende:', {
        mode: 'multiplayer',
        action: 'joinOrCreateGame',
        playerId
    });

    const response = await fetch('php/quiz.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
            mode: 'multiplayer',
            action: 'joinOrCreateGame',
            playerId: playerId
        })
    });

    const result = await response.json();
    if (result.success) {
        gameId = result.gameId;
        console.log(`[initMultiplayer] Multiplayer-Game beigetreten: ${gameId}`);
        document.getElementById('waitingRoom')?.style?.setProperty('display', 'none'); 
        document.getElementById('quizContainer')?.style.setProperty('display', 'block');
        await startGame();
    } else {
        console.error(`[initMultiplayer] Fehler beim Beitreten: ${result.message || 'Unbekannter Fehler'}`);
        console.error('[initMultiplayer] Fehler beim Beitreten:', result.message, result);
    }
}

async function startGame() {
    await loadNextQuestion();
}

async function loadNextQuestion() {
    const response = await fetch(`php/quiz.php?mode=multiplayer&gameId=${gameId}&playerId=${playerId}`);
    const result = await response.json();

    if (result.wait) {
        console.log('[loadNextQuestion] Warte auf anderen Spieler...');

        // Anzeigen: "Warte auf Mitspieler..."
        document.getElementById('quizContainer').style.display = 'none';
        document.getElementById('waitingRoom').style.display = 'block';
        document.getElementById('waitingRoom').innerHTML = `
          <h2>Mehrspieler-Modus</h2>
          <p>${result.message || 'Warte auf Mitspieler...'}</p>
        `;

        setAnswerButtonsEnabled(false);
        setTimeout(loadNextQuestion, 5000);
        return;
    }

    // Wenn Frage kommt: Quiz anzeigen, Wartebereich ausblenden
    document.getElementById('waitingRoom').style.display = 'none';
    document.getElementById('quizContainer').style.display = 'block';

    const q = result.info;
    currentQuestionId = q.id;
    correctAnswer = parseInt(q.richtig);

    document.getElementById('Question').textContent = q.frage;
    document.getElementById('answer1').textContent = q.antwort["1"];
    document.getElementById('answer2').textContent = q.antwort["2"];
    document.getElementById('answer3').textContent = q.antwort["3"];
    document.getElementById('answer4').textContent = q.antwort["4"];
    setAnswerButtonsEnabled(true);
}

function setAnswerButtonsEnabled(enabled) {
    for (let i = 1; i <= 4; i++) {
        document.getElementById('answer' + i).parentElement.style.pointerEvents = enabled ? 'auto' : 'none';
        document.getElementById('answer' + i).parentElement.style.opacity = enabled ? '1' : '0.5';
    }
}

async function submitAnswer(answerNumber) {
    const selectedAnswer = parseInt(answerNumber, 10);
    const isCorrect = (selectedAnswer === correctAnswer);
    const score = calculateScore(isCorrect);

    // Anzeige aktualisieren
    handleAnswerClick(selectedAnswer);

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
            correctAnswer: correctAnswer
        })
    });

    const result = await response.json();

    if (result.success) {
        console.log(`[submitAnswer] Antwort war ${result.correct ? 'richtig' : 'falsch'}`);
    } else {
        console.error(`[submitAnswer] Fehler beim Speichern der Antwort: ${result.message || 'Unbekannter Fehler'}`);
    }
}


// ✅ Automatisch starten beim Seitenladen
document.addEventListener('DOMContentLoaded', () => {
    /*
    const storedId = parseInt(localStorage.getItem('playerId')) || 0;
    console.log(`[Info] SotedID ist: ${storedId}`);
    if (storedId > 0) {
        initMultiplayer(storedId);
    } else {
        console.error('[DOMContentLoaded] Keine gültige Spieler-ID gefunden. Abbruch.');
        window.location.href = 'login.html';
    }
        */
});
