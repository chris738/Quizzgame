let QuestionID = 1;  // Initialer Wert für die Frage-ID

// Funktion, um die Frage-ID zu aktualisieren
function updateQuestionID() {
    const input = document.getElementById('questionIdInput');
    QuestionID = parseInt(input.value, 10);  // Wert aus dem Eingabefeld holen und in eine Zahl umwandeln
    getQuestions(QuestionID);  // Frage mit der neuen QuestionID laden
}


document.querySelectorAll('.answer').forEach(answer => {
    answer.addEventListener('click', function() {
        document.querySelectorAll('.answer').forEach(a => a.classList.remove('selected'));
        this.classList.add('selected');
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
function handleAnswerClick(id) {
    let selectedAnswer = 0;
    
    // Bestimmen, welche Antwort geklickt wurde
    if(id === 'answer1') {
        selectedAnswer = 1;
    } else if(id === 'answer2') {
        selectedAnswer = 2;
    } else if(id === 'answer3') {
        selectedAnswer = 3;
    } else if(id === 'answer4') {
        selectedAnswer = 4;
    }
    
    // Anfrage an den Server, um zu prüfen, ob die Antwort korrekt ist
    checkAnswer(QuestionID, selectedAnswer);
}

// Funktion zum Überprüfen, ob die Antwort korrekt ist
function checkAnswer(QuestionID, selectedAnswer) {
    fetch(`backend.php?frageID=${QuestionID}&answer=${selectedAnswer}`)
        .then(response => response.json())
        .then(data => {
            if (data.info.isCorrect !== undefined) {
                if (data.info.isCorrect == true) {
                    alert('Die Antwort ist korrekt!');
                } else {
                    alert('Leider falsch. Versuche es noch einmal.');
                }
            } else {
                alert('Data is undefined.');
            }
        })
        .catch(error => console.error('Fehler beim Überprüfen der Antwort:', error));
}

getQuestions(QuestionID);
