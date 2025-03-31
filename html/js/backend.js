// Globale Variablen
let currentQuestionID = null;
let correctAnswer = null;
let hasAnswered = false; // Merker, ob bereits geantwortet wurde

// Frage vom Server holen
function getQuestions() {
    fetch(`php/backend.php`)
        .then(response => response.json())
        .then(data => {
            if (data.info) {
                // ID und richtige Antwort aus JSON merken
                currentQuestionID = data.info.id;
                correctAnswer = parseInt(data.info.richtig, 10);

                // Frage-Text und Antworten befüllen
                document.getElementById('Question').textContent = data.info.frage;
                document.getElementById('answer1').textContent = data.info.antwort["1"];
                document.getElementById('answer2').textContent = data.info.antwort["2"];
                document.getElementById('answer3').textContent = data.info.antwort["3"];
                document.getElementById('answer4').textContent = data.info.antwort["4"];

                // Alles zurücksetzen
                resetUI();
            }
        })
        .catch(error => console.error('Fehler beim Abrufen der Daten:', error));
}

// Klick auf eine Antwort
function handleAnswerClick(spanID) {
    // Wenn schon beantwortet, nichts mehr tun
    if (hasAnswered) return;

    // "answer1" -> wir wollen die Zahl am Ende als Number
    const selectedAnswer = parseInt(spanID.replace('answer',''), 10);

    // Antwort auswerten
    checkAnswer(currentQuestionID, selectedAnswer);
}

// Antwort prüfen und einfärben
function checkAnswer(questionID, selectedAnswer) {
    // Merken: Es wurde geantwortet, keine zweite Auswahl mehr
    hasAnswered = true;

    // Zuerst alle "selected"-Markierungen entfernen (optional, falls du sie verwendest)
    document.querySelectorAll('.answer').forEach(answerDiv => {
        answerDiv.classList.remove('selected');
    });

    // Richtig oder falsch?
    if (selectedAnswer === correctAnswer) {
        document.querySelector(`[data-color="answer${selectedAnswer}"]`)
                .classList.add('correct');
    } else {
        // Falsche Antwort -> rot
        document.querySelector(`[data-color="answer${selectedAnswer}"]`)
                .classList.add('wrong');

        // Richtige Antwort -> grün
        document.querySelector(`[data-color="answer${correctAnswer}"]`)
                .classList.add('correct');
    }

    // Button "Neue Frage" sichtbar machen
    document.getElementById('newQuestionBtn').style.display = 'inline-block';
}

// Neue Frage laden (wird vom Button aufgerufen)
function loadNewQuestion() {
    getQuestions();
}

// Zurücksetzen, damit bei neuer Frage kein altes CSS hängenbleibt
function resetUI() {
    // Falls wir alle Klasseneinträge (correct, wrong) entfernen möchten:
    document.querySelectorAll('.answer').forEach(answerDiv => {
        answerDiv.classList.remove('correct', 'wrong', 'selected');
    });
    // Merker zurücksetzen
    hasAnswered = false;
    // Button ausblenden
    document.getElementById('newQuestionBtn').style.display = 'none';
}

// Beim Laden der Seite: direkt die erste Frage holen
getQuestions();
