// Globale Variablen
let currentQuestionID = null;
let correctAnswer = null;
let hasAnswered = false; // Merker, ob schon geantwortet wurde

// Frage vom Server holen
function getQuestions() {
    fetch('php/quiz.php')
        .then(response => response.json())
        .then(data => {
            if (data.info && !data.info.error) {
                // ID und richtige Antwort aus JSON merken
                currentQuestionID = data.info.id;
                correctAnswer = parseInt(data.info.richtig, 10);

                // Frage-Text und Antworten befüllen
                document.getElementById('Question').textContent = data.info.frage;
                document.getElementById('answer1').textContent = data.info.antwort["1"];
                document.getElementById('answer2').textContent = data.info.antwort["2"];
                document.getElementById('answer3').textContent = data.info.antwort["3"];
                document.getElementById('answer4').textContent = data.info.antwort["4"];

                // UI zurücksetzen
                resetUI();
            } else {
                console.error('Fehler:', data.info ? data.info.error : data);
            }
        })
        .catch(error => console.error('Fehler beim Fetch:', error));
}

function handleAnswerClick(spanID) {
    // Falls schon beantwortet, nichts tun
    if (hasAnswered) return;

    // "answer1" -> wir wollen die Zahl als Integer (1,2,3,4)
    const selectedAnswer = parseInt(spanID.replace('answer',''), 10);

    // Das Element für Feedback
    const feedbackDiv = document.getElementById('feedback');

    // Prüfung, ob richtig
    if (selectedAnswer === correctAnswer) {
        document.querySelector(`[data-color="answer${selectedAnswer}"]`)
                .classList.add('correct');
        feedbackDiv.textContent = "Richtig beantwortet!";
    } else {
        // Falsche Antwort rot markieren
        document.querySelector(`[data-color="answer${selectedAnswer}"]`)
                .classList.add('wrong');
        // Richtige Antwort grün markieren
        document.querySelector(`[data-color="answer${correctAnswer}"]`)
                .classList.add('correct');

        // Zusätzlich: Text zur richtigen Antwort aus dem DOM auslesen
        const correctText = document.getElementById(`answer${correctAnswer}`).textContent;
        feedbackDiv.textContent = "Leider falsch! Die richtige Antwort war: " + correctText;
    }

    // Merken, dass beantwortet wurde
    hasAnswered = true;

    // Button "Neue Frage" einblenden und fokussieren
    const newQuestionBtn = document.getElementById('newQuestionBtn');
    newQuestionBtn.style.display = 'inline-block';
    const feedback = document.getElementById('feedback');
    feedback.focus();
}


// Neue Frage laden
function loadNewQuestion() {
    getQuestions();
}

// Zurücksetzen, damit bei neuer Frage kein altes CSS hängenbleibt
function resetUI() {
    // CSS-Klassen entfernen
    document.querySelectorAll('.answer').forEach(answerDiv => {
        answerDiv.classList.remove('correct', 'wrong');
    });
    // Feedback löschen
    document.getElementById('feedback').textContent = '';
    // Merker zurücksetzen
    hasAnswered = false;
    // Button ausblenden
    document.getElementById('newQuestionBtn').style.display = 'none';

    //focus auf das erste element setzen
    const questionHeading = document.getElementById('Question');
    questionHeading.focus();
}

// Beim Laden der Seite die erste Frage holen
document.addEventListener('DOMContentLoaded', () => {
    getQuestions();
});
