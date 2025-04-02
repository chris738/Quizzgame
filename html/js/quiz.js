// --------------------
// Globale Variablen
// --------------------
let currentQuestionID = null;
let correctAnswer = null;
let hasAnswered = false;
let currentPlayerId = parseInt(localStorage.getItem('playerId')) || 0;
let questionStartTime = null;

let selectedCategory = null;    // gewählte Kategorie
let questionCount = 0;          // wie viele Fragen wurden schon gestellt?
const maxQuestions = 4;         // wie viele Fragen pro Spiel?
let totalScore = 0;             // gesamter Score über alle Fragen

function selectCategory(categoryName) {
    // Speichere die Auswahl
    selectedCategory = categoryName;

    // Kategorieauswahl ausblenden
    document.getElementById('categorySelection').style.display = 'none';

    // Quiz-Bereich einblenden
    document.getElementById('quizContainer').style.display = 'block';

    // Erste Frage laden
    questionCount = 0;
    totalScore = 0;      // Score zurücksetzen, falls noch was vom letzten Spiel war
    loadNewQuestion();
    setNavVisibility(false);
}

function loadNewQuestion() {
    let url = 'php/quiz.php';
    if (selectedCategory) {
        url += '?category=' + encodeURIComponent(selectedCategory);
    }

    fetch(url)
        .then(response => response.json())
        .then(data => {
            if (data.info && !data.info.error) {
                currentQuestionID = data.info.id;
                correctAnswer = parseInt(data.info.richtig, 10);

                // Frage-Text und Antworten befüllen
                document.getElementById('Question').textContent = data.info.frage;
                document.getElementById('answer1').textContent = data.info.antwort["1"];
                document.getElementById('answer2').textContent = data.info.antwort["2"];
                document.getElementById('answer3').textContent = data.info.antwort["3"];
                document.getElementById('answer4').textContent = data.info.antwort["4"];

                questionStartTime = Date.now();

                // Frageanzahl hochzählen
                questionCount++;

                // UI zurücksetzen
                resetUI();
            } else {
                console.error('Fehler:', data.info ? data.info.error : data);
            }
        })
        .catch(error => console.error('Fehler beim Fetch:', error));
}

function handleAnswerClick(spanID) {
    if (hasAnswered) return;

    const selectedAnswer = parseInt(spanID.replace('answer',''), 10);
    const feedbackDiv = document.getElementById('feedback');

    const isCorrect = (selectedAnswer === correctAnswer);
    const score = calculateScore(isCorrect);
    if (isCorrect) {
        document.querySelector(`[data-color="answer${selectedAnswer}"]`)
                .classList.add('correct');
        feedbackDiv.textContent = `Richtig! +${score} Punkte`;
    } else {
        document.querySelector(`[data-color="answer${selectedAnswer}"]`)
                .classList.add('wrong');
        document.querySelector(`[data-color="answer${correctAnswer}"]`)
                .classList.add('correct');
        const correctText = document.getElementById(`answer${correctAnswer}`).textContent;
        feedbackDiv.textContent = "Falsch! Richtig wäre: " + correctText;
    }

    // Score zum Gesamtscore addieren
    totalScore += score;

    hasAnswered = true;

    // "Nächste Frage"-Button einblenden
    const newQuestionBtn = document.getElementById('newQuestionBtn');
    newQuestionBtn.style.display = 'inline-block';

    // Barrierefreiheit
    feedbackDiv.setAttribute('tabindex', '0');
    newQuestionBtn.setAttribute('tabindex', '1');
    feedbackDiv.focus();

    // Spielstand speichern (Backend-Logik)
    saveGameResult(currentPlayerId, currentQuestionID, selectedAnswer, correctAnswer, score);
}

function nextQuestion() {
    // Wenn wir unsere max. Anzahl an Fragen erreicht haben, dann Endergebnis anzeigen
    if (questionCount >= maxQuestions) {
        showFinalScore();
    } else {
        loadNewQuestion();
    }
}

function showFinalScore() {
    // Quiz-Bereich ausblenden
    document.getElementById('quizContainer').style.display = 'none';

    // Ergebnis-Bereich sichtbar machen
    document.getElementById('gameResult').style.display = 'block';
    document.getElementById('restartBtn').style.display = 'block';
    document.getElementById('finalScore').textContent = 
        `Du hast ${maxQuestions} Fragen beantwortet und insgesamt ${totalScore} Punkte erreicht!`;

    //lade den hightscore aus der js/hightscore.js
    loadHighscore();

    const section = document.getElementById('highscoreSection');
    section.style.display = 'block';
    
    // Optional: Scroll zum Highscore-Bereich
    section.scrollIntoView({ behavior: 'smooth' });

    setTimeout(() => {
        document.getElementById('finalScore').focus();
    }, 400);
}

function resetGame() {
    // Navigations bereich wieder einblenden
    setNavVisibility(true);
    // Ergebnis-Bereich ausblenden
    document.getElementById('gameResult').style.display = 'none';
    // Kategorieauswahl wieder anzeigen
    document.getElementById('categorySelection').style.display = 'block';
    // Highscore wieder ausblenden
    document.getElementById('highscoreSection').style.display = 'none';;
    // Focus auf die Kategorien
    document.getElementById('categorySelection').focus();
}

function calculateScore(isCorrect) {
    if (!isCorrect) return 0;
    const responseTime = Math.floor((Date.now() - questionStartTime) / 1000);
    const basePoints = 100;
    // z.B. Bonus abhängig von der Schnelligkeit
    const bonusPoints = Math.max(0, 100 - responseTime * (100 / 60));
    return Math.round(basePoints + bonusPoints);
}

function saveGameResult(playerId, questionID, selectedAnswer, correctAnswer, score) {
    fetch('php/quiz.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({
            playerId: playerId,
            questionId: questionID,
            selectedAnswer: selectedAnswer,
            correctAnswer: correctAnswer,
            score: score
        })
    })
    .then(response => response.json())
    .then(data => {
        if (!data.success) {
            console.error("Fehler beim Speichern des Spiels:", data.message);
        }
    })
    .catch(error => {
        console.error("Netzwerkfehler:", error);
    });
}

function resetUI() {
    // CSS-Klassen entfernen
    document.querySelectorAll('.answer').forEach(answerDiv => {
        answerDiv.classList.remove('correct', 'wrong');
    });
    document.getElementById('feedback').textContent = '';
    hasAnswered = false;

    // "Nächste Frage" Button ausblenden
    document.getElementById('newQuestionBtn').style.display = 'none';

    // Fokus auf Frage setzen
    const questionHeading = document.getElementById('Question');


    setTimeout(() => {
        questionHeading.focus();
    }, 250);
}

function setNavVisibility(visible) {
    const hiddenLinks = document.querySelectorAll('nav[aria-label="Hauptnavigation"] a[aria-hidden="true"]');
    hiddenLinks.forEach(link => {
      if (visible) {
        link.removeAttribute('aria-hidden');
      } else {
        link.setAttribute('aria-hidden', 'true');
      }
    });
  }

document.addEventListener('DOMContentLoaded', () => {
    setNavVisibility(false);
    // Zu Beginn: erst mal nur Kategorie-Auswahl anzeigen.
    // => categorySelection ist schon sichtbar (per CSS),
    //    quizContainer & gameResult sind hidden.
    // => Mach also hier nichts weiter, außer falls du
    //    beim Laden direkt etwas debuggen willst.
});
