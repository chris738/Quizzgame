let QuestionID = 1;  // Initialer Wert für die Frage-ID

// Funktion, um die Frage-ID zu aktualisieren
function updateQuestionID() {
    const input = document.getElementById('questionIdInput');
    QuestionID = parseInt(input.value, 10);  // Wert aus dem Eingabefeld holen und in eine Zahl umwandeln
    getQuestions(QuestionID);  // Frage mit der neuen QuestionID laden
}

// Initiale Frage laden
getQuestions(QuestionID);

document.querySelectorAll('.answer').forEach(answer => {
    answer.addEventListener('click', function() {
        if (!this.classList.contains('disabled')) {
            document.querySelectorAll('.answer').forEach(a => a.classList.remove('selected'));
            this.classList.add('selected');
        }
    });
});

function getQuestions(QuestionID) {
    fetch(`backend.php?frageID=${QuestionID}`)
        .then(response => response.json())
        .then(data => {
            if (data.info) {
                document.getElementById('Question').textContent = data.info.fragen.Question;
                document.getElementById('green').textContent = data.info.fragen.AnswerGreen;
                document.getElementById('red').textContent = data.info.fragen.AnswerRed;
                document.getElementById('yellow').textContent = data.info.fragen.AnswerYellow;
                document.getElementById('blue').textContent = data.info.fragen.AnswerBlue;
            }
        })
        .catch(error => console.error('Fehler beim Abrufen der Daten in backend.js:', error));
}

// Funktion, die aufgerufen wird, wenn eine Antwort ausgewählt wird
function handleAnswerClick(selectedAnswer) {
    // Anfrage an den Server, um zu prüfen, ob die Antwort korrekt ist
    checkAnswer(QuestionID, selectedAnswer);
}

// Funktion zum Überprüfen, ob die Antwort korrekt ist
function checkAnswer(QuestionID, selectedAnswer) {
    fetch(`backend.php?frageID=${QuestionID}&answer=${selectedAnswer}`)
        .then(response => response.json())
        .then(data => {
            if (data.info.isCorrect !== undefined) {
                const answers = document.querySelectorAll('.answer');
                answers.forEach(answer => answer.classList.add('disabled'));  // Alle Antworten deaktivieren

                if (data.info.isCorrect === true) {
                    document.querySelector(`#green`).classList.add('correct');
                } else {
                    document.querySelector(`#green`).classList.add('correct');
                    // Hier den Fehlern die Klasse `incorrect` zuweisen
                    document.querySelector(`#red`).classList.add('incorrect');
                    document.querySelector(`#yellow`).classList.add('incorrect');
                    document.querySelector(`#blue`).classList.add('incorrect');
                }

                // Wartet, bevor die Frage neu geladen wird (zur nächsten Frage)
                setTimeout(() => {
                    getQuestions(QuestionID);
                }, 1000); // Verzögerung von 1 Sekunde, bevor die nächste Frage geladen wird
            } else {
                console.error('Daten sind undefiniert.');
            }
        })
        .catch(error => console.error('Fehler beim Überprüfen der Antwort:', error));
}
