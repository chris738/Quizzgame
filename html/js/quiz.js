// Globale Variablen
let currentQuestionID = null;
let correctAnswerKey = null;  // speichert den korrekten Key (1, 2, 3, oder 4)
let hasAnswered = false;      // Merker, ob bereits geantwortet wurde

// Frage vom Server holen
function getQuestions() {
    fetch('php/quiz.php')  // Pfad ggf. anpassen
        .then(response => response.json())
        .then(data => {
            if (data.info && !data.info.error) {
                // ID und richtige Antwort aus JSON merken
                currentQuestionID = data.info.id;
                correctAnswerKey = data.info.richtig;  // z.B. "1", "2", "3" oder "4"

                // Frage-Text und Antworten befüllen
                document.getElementById('Question').textContent = data.info.frage;
                document.getElementById('answer1').textContent = data.info.antwort["1"];
                document.getElementById('answer2').textContent = data.info.antwort["2"];
                document.getElementById('answer3').textContent = data.info.antwort["3"];
                document.getElementById('answer4').textContent = data.info.antwort["4"];

                // UI zurücksetzen
                resetUI();
            } else {
                console.error('Fehler im JSON oder keine Frage gefunden:', data.info.error);
            }
        })
        .catch(error => console.error('Fehler beim Abrufen der Daten:', error));
}

// Klick auf eine Antwort
function handleAnswerClick(spanID) {
    // Wenn schon beantwortet, nicht erneut prüfen
    if (hasAnswered) return;

    // Bsp: "answer1" -> wir wollen die Zahl am Ende
    const selectedAnswerKey = spanID.replace('answer', ''); // "1", "2", ...

    // Antwort auswerten (clientseitig)
    checkClientSideAnswer(selectedAnswerKey);
}

// Clientseitige Prüfung
function checkClientSideAnswer(selectedAnswerKey) {
    hasAnswered = true;

    if (selectedAnswerKey === correctAnswerKey) {
        // Richtige Antwort
        document.querySelector(`[data-color="answer${selectedAnswerKey}"]`)
                .classList.add('correct');
    } else {
        // Falsche Antwort
        document.querySelector(`[data-color="answer${selectedAnswerKey}"]`)
                .classList.add('wrong');

        // Richtige Antwort hervorheben
        document.querySelector(`[data-color="answer${correctAnswerKey}"]`)
                .classList.add('correct');
    }

    // Button "Neue Frage" einblenden
    document.getElementById('newQuestionBtn').style.display = 'inline-block';
}

// Neue Frage laden (Button klick)
function loadNewQuestion() {
    getQuestions();
}

// UI zurücksetzen, damit bei neuer Frage kein altes CSS hängenbleibt
function resetUI() {
    document.querySelectorAll('.answer').forEach(answerDiv => {
        answerDiv.classList.remove('correct', 'wrong', 'selected');
    });
    hasAnswered = false;
    document.getElementById('newQuestionBtn').style.display = 'none';
}

// Beim Laden der Seite gleich die erste Frage holen
getQuestions();
