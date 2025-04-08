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
  }
};

// Endbildschirm für lokalen Modus anzeigen
function showLocalFinalScore() {
  setNavVisibility(true);
  document.getElementById('quizContainer').style.display = 'none';
  document.getElementById('gameResult').style.display = 'block';
  document.getElementById('restartBtn').style.display = 'block';

  document.getElementById('finalScore').innerHTML =
  `<strong>${player1Name}</strong>: ${localScore[1]} Punkte<br>` +
  `<strong>${player2Name}</strong>: ${localScore[2]} Punkte`;

  const section = document.getElementById('highscoreSection');
  section.style.display = 'block';
  section.scrollIntoView({ behavior: 'smooth' });

  setTimeout(() => {
    document.getElementById('finalScore').focus();
  }, 400);
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
      });
    }
  
    // Kategorieauswahl erstmal ausblenden
    document.getElementById('categorySelection').style.display = 'none';
    setNavVisibility(false);
  });