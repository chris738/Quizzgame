// Spieler-IDs aus localStorage laden
let player1Id = localStorage.getItem('playerId');
let player2Id = localStorage.getItem('player2Id');
let player1Name = localStorage.getItem('playerName') || 'Spieler 1';
let player2Name = localStorage.getItem('player2Name') || 'Spieler 2';

// Aktueller Spieler (1 oder 2)
let currentPlayer = 1;

// Punkte pro Spieler
let localScore = {
  1: 0,
  2: 0
};

// Fragenzähler & Maximalanzahl
let localQuestionCount = 0;
const localMaxQuestions = 8;

document.addEventListener('DOMContentLoaded', () => {
  // Prüfen, ob beide Spieler eingeloggt sind
  if (!player1Id || !player2Id) {
    alert('Beide Spieler müssen eingeloggt sein!');
    window.location.href = 'login.html';
    return;
  }

  // Navigation ausblenden beim Start
  setNavVisibility(false);
});

// Antwortauswahl-Handler für lokalen Modus
window.handleLocalAnswer = function(spanID) {
  if (hasAnswered) return;

  const selectedAnswer = parseInt(spanID.replace('answer', ''), 10);
  const isCorrect = (selectedAnswer === correctAnswer);
  const score = calculateScore(isCorrect);

  showAnswerFeedback(isCorrect, selectedAnswer, score);
  localScore[currentPlayer] += score;
  hasAnswered = true;
};

// Nächste Frage für lokalen Modus laden
window.nextLocalQuestion = function() {
  localQuestionCount++;

  if (localQuestionCount >= localMaxQuestions) {
    showLocalFinalScore();
  } else {
    currentPlayer = currentPlayer === 1 ? 2 : 1;
    loadNewQuestion(); // kommt aus quiz.js
    updateActivePlayerDisplay();
  }
};

// Endbildschirm für lokalen Modus anzeigen
function showLocalFinalScore() {
    setNavVisibility(true);
    document.getElementById('quizContainer').style.display = 'none';
    document.getElementById('gameResult').style.display = 'block';
    document.getElementById('restartBtn').style.display = 'block';
  
    // Sortiere Spieler nach Score
    const players = [
      { name: player1Name, score: localScore[1] },
      { name: player2Name, score: localScore[2] }
    ].sort((a, b) => b.score - a.score);
  
    const winner = players[0];
  
    document.getElementById('finalScore').innerHTML = `
      <h2 style="font-size: 2.5rem;">🏆 ${winner.name} gewinnt!</h2>
      <p style="margin-top: 2rem;">
        <strong>${players[0].name}</strong>: ${players[0].score} Punkte<br>
        <strong>${players[1].name}</strong>: ${players[1].score} Punkte
      </p>
    `;
  
    const section = document.getElementById('highscoreSection');
    section.style.display = 'block';
    section.scrollIntoView({ behavior: 'smooth' });
  
    setTimeout(() => {
      document.getElementById('finalScore').focus();
    }, 400);
  }

function updateActivePlayerDisplay() {
    const activeDiv = document.getElementById('activePlayer');
    const name = currentPlayer === 1 ? player1Name : player2Name;
    activeDiv.textContent = `🔹 ${name} ist dran!`;
    activeDiv.style.display = 'block';
  }

window.resetGameMultiplayer = function resetGameMultiplayer() {
    // Navigations bereich wieder einblenden
    setNavVisibility(false);

    //Seite neu Laden
    location.reload();
}

document.addEventListener('DOMContentLoaded', () => {
    const name1 = localStorage.getItem('playerName');
    const name2 = localStorage.getItem('player2Name');
  
    const vsStatus = document.getElementById('vsStatus');
    const startBtn = document.getElementById('startLocalGameBtn');
  
    if (!name1 || !name2) {
      vsStatus.textContent = "❌ Nicht genügend Spieler eingeloggt.";
      startBtn.style.display = 'none';
    } else {
      vsStatus.textContent = `${name1} 🆚 ${name2}`;
      startBtn.style.display = 'inline-block';
  
      startBtn.addEventListener('click', () => {
        document.getElementById('vsScreen').style.display = 'none';
        document.getElementById('categorySelection').style.display = 'block';
        updateActivePlayerDisplay();
      });
    }
  
    // Kategorieauswahl erstmal ausblenden
    document.getElementById('categorySelection').style.display = 'none';
    setNavVisibility(false);
  });