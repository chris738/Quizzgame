let gameId = null;
let playerId = null;
let currentQuestionId = null;
let correctAnswer = null;

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
        document.getElementById('waitingRoom')?.remove();
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

    if (!result.info) {
        console.warn(`[loadNextQuestion] Keine neue Frage: ${result.message || 'Spiel beendet.'}`);
        document.getElementById('Question').textContent = result.message || 'Spiel beendet.';
        return;
    }

    const q = result.info;
    currentQuestionId = q.id;
    correctAnswer = parseInt(q.richtig);

    document.getElementById('Question').textContent = q.frage;
    document.getElementById('answer1').textContent = q.antwort["1"];
    document.getElementById('answer2').textContent = q.antwort["2"];
    document.getElementById('answer3').textContent = q.antwort["3"];
    document.getElementById('answer4').textContent = q.antwort["4"];
}

async function submitAnswer(answerNumber) {
    const response = await fetch('php/quiz.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
            mode: 'multiplayer',
            gameId: gameId,
            playerId: playerId,
            questionId: currentQuestionId,
            selectedAnswer: answerNumber,
            correctAnswer: correctAnswer
        })
    });

    const result = await response.json();
    if (result.success) {
        console.log(`[submitAnswer] Antwort war ${result.correct ? 'richtig' : 'falsch'}`);
        await loadNextQuestion();
    } else {
        console.error(`[submitAnswer] Fehler beim Speichern der Antwort: ${result.message || 'Unbekannter Fehler'}`);
    }
}

// ✅ Automatisch starten beim Seitenladen
document.addEventListener('DOMContentLoaded', () => {
    const storedId = parseInt(localStorage.getItem('playerId')) || 0;
    if (storedId > 0) {
        initMultiplayer(storedId);
    } else {
        console.error('[DOMContentLoaded] Keine gültige Spieler-ID gefunden. Abbruch.');
        window.location.href = 'login.html';
    }
});
