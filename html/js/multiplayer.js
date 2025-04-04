let gameId = null;
let playerId = null;
let currentQuestionId = null;
let correctAnswer = null;

// ✅ Funktion global verfügbar machen
window.initMultiplayer = async function (currentPlayerId) {
    playerId = currentPlayerId;

    const response = await fetch('php/joinMultiplayer.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ playerId })
    });

    const result = await response.json();
    if (result.success) {
        gameId = result.gameId;
        console.log(`Multiplayer-Game beigetreten: ${gameId}`);
        document.getElementById('waitingRoom')?.remove();
        document.getElementById('quizContainer')?.style.setProperty('display', 'block');
        await startGame();
    } else {
        alert(result.message || 'Fehler beim Beitreten');
    }
}

async function startGame() {
    await loadNextQuestion();
}

async function loadNextQuestion() {
    const response = await fetch(`php/quiz.php?mode=multiplayer&gameId=${gameId}&playerId=${playerId}`);
    const result = await response.json();

    if (!result.info) {
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
        console.log(`Antwort war ${result.correct ? 'richtig' : 'falsch'}`);
        await loadNextQuestion();
    } else {
        alert(result.message || 'Antwort konnte nicht gespeichert werden');
    }
}
