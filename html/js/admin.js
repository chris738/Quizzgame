function initAddQuestionForm() {
    const form = document.getElementById("addQuestionForm");

    form.addEventListener("submit", async (e) => {
        e.preventDefault();

        const formData = new FormData(form);

        try {
            const response = await fetch("php/admin.php", {
                method: "POST",
                body: formData
            });

            const result = await response.json();

            if (result.success) {
                alert("Frage hinzugefügt!");
                form.reset();
            } else {
                alert("Fehler: " + result.message);
            }
        } catch (err) {
            alert("Netzwerkfehler: " + err.message);
        }
    });
}

function initEditQuestionForm() {
    const form = document.getElementById("editQuestionForm");

    form.addEventListener("submit", async (e) => {
        e.preventDefault();

        const formData = new FormData(form);
        formData.append('action', 'updateQuestion'); 

        try {
            const response = await fetch("php/admin.php", {
                method: "POST",
                body: formData
            });

            const result = await response.json();

            if (result.success) {
                alert("Frage erfolgreich aktualisiert!");
                form.reset();
            } else {
                alert("Fehler: " + result.message);
            }
        } catch (err) {
            alert("Netzwerkfehler: " + err.message);
        }
    });
}

function initDeleteQuestionForm() {
    const form = document.getElementById("deleteQuestionForm");

    form.addEventListener("submit", async (e) => {
        e.preventDefault();

        const formData = new FormData(form);
        formData.append('action', 'deleteQuestion'); 

        try {
            const response = await fetch("php/admin.php", {
                method: "POST",
                body: formData
            });

            const result = await response.json();

            if (result.success) {
                alert("Frage erfolgreich gelöscht!");
                form.reset();
            } else {
                alert("Fehler: " + result.message);
            }
        } catch (err) {
            alert("Netzwerkfehler: " + err.message);
        }
    });
}

function loadQuestionData(questionId) {
    if (!questionId) {
        alert("Bitte gib eine Frage-ID ein.");
        return;
    }

    console.log("Lade Frage mit ID: ", questionId); 

    fetch(`php/admin.php?action=loadQuestion&id=${questionId}`)
        .then(response => response.json())
        .then(data => {
            if (data.error) {
                alert(data.error);
            } else {
                
                fillFormWithQuestionData(data.info);
            }
        })
        .catch(error => {
            console.error("Fehler beim Laden der Frage:", error);
            alert("Es gab ein Problem beim Laden der Frage.");
        });
}

function fillFormWithQuestionData(questionData) {
    document.getElementById("editquestion").value = questionData.frage;
    document.getElementById("editcategory").value = questionData.category;
    document.getElementById("editanswer1").value = questionData.antwort['1'];
    document.getElementById("editanswer2").value = questionData.antwort['2'];
    document.getElementById("editanswer3").value = questionData.antwort['3'];
    document.getElementById("editanswer4").value = questionData.antwort['4'];
    document.getElementById("editcorrectAnswer").value = questionData.richtig;
}


function initAddUserForm() {
    const form = document.getElementById("addUserForm");

    form.addEventListener("submit", async (e) => {
        e.preventDefault();

        const formData = new FormData(form);

        try {
            const response = await fetch("php/admin.php", {
                method: "POST",
                body: formData
            });

            const result = await response.json();

            if (result.success) {
                alert("Benutzer hinzugefügt!");
                form.reset();
            } else {
                alert("Fehler: " + result.message);
            }
        } catch (err) {
            alert("Netzwerkfehler: " + err.message);
        }
    });
}

function loadAllQuestions() {
    fetch('php/admin.php?action=listQuestions')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                displayQuestionsTable(data.questions);
            } else {
                alert("Fehler beim Laden der Fragen: " + data.message);
            }
        })
        .catch(error => {
            console.error("Fehler beim Laden der Fragen:", error);
            alert("Es gab ein Problem beim Laden der Fragen.");
        });
}

function displayQuestionsTable(questions) {
    const tableContainer = document.getElementById('questionsTable');
    
    if (questions.length === 0) {
        tableContainer.innerHTML = '<p>Keine Fragen gefunden.</p>';
        return;
    }

    let tableHTML = `
        <table style="width: 100%; border-collapse: collapse; margin-top: 10px;">
            <thead>
                <tr style="background-color: #f0f0f0;">
                    <th style="border: 1px solid #ddd; padding: 8px;">ID</th>
                    <th style="border: 1px solid #ddd; padding: 8px;">Frage</th>
                    <th style="border: 1px solid #ddd; padding: 8px;">Kategorie</th>
                    <th style="border: 1px solid #ddd; padding: 8px;">Korrekte Antwort</th>
                    <th style="border: 1px solid #ddd; padding: 8px;">Aktionen</th>
                </tr>
            </thead>
            <tbody>
    `;

    questions.forEach(question => {
        const correctAnswerText = question[`Answer${question.correctAnswer}`] || 'N/A';
        tableHTML += `
            <tr>
                <td style="border: 1px solid #ddd; padding: 8px;">${question.QuestionID}</td>
                <td style="border: 1px solid #ddd; padding: 8px; max-width: 300px; word-wrap: break-word;">${question.Question}</td>
                <td style="border: 1px solid #ddd; padding: 8px;">${question.Category}</td>
                <td style="border: 1px solid #ddd; padding: 8px;">${correctAnswerText}</td>
                <td style="border: 1px solid #ddd; padding: 8px;">
                    <button onclick="editQuestionFromTable(${question.QuestionID})" style="margin-right: 5px;">Bearbeiten</button>
                    <button onclick="deleteQuestionFromTable(${question.QuestionID})" style="background-color: #ff4444; color: white;">Löschen</button>
                </td>
            </tr>
        `;
    });

    tableHTML += `
            </tbody>
        </table>
    `;

    tableContainer.innerHTML = tableHTML;
}

function editQuestionFromTable(questionId) {
    // ID in das Bearbeitungsfeld eintragen
    document.getElementById("id").value = questionId;
    
    // Frage laden
    loadQuestionData(questionId);
    
    // Zum Bearbeitungsbereich scrollen
    document.getElementById("editQuestionForm").scrollIntoView({ behavior: 'smooth' });
}

function deleteQuestionFromTable(questionId) {
    if (confirm(`Möchten Sie die Frage mit ID ${questionId} wirklich löschen?`)) {
        const formData = new FormData();
        formData.append('action', 'deleteQuestion');
        formData.append('deleteQuestionID', questionId);
        
        fetch("php/admin.php", {
            method: "POST",
            body: formData
        })
        .then(response => response.json())
        .then(result => {
            if (result.success) {
                alert("Frage erfolgreich gelöscht!");
                loadAllQuestions(); // Liste neu laden
            } else {
                alert("Fehler: " + result.message);
            }
        })
        .catch(err => {
            alert("Netzwerkfehler: " + err.message);
        });
    }
}

document.addEventListener('DOMContentLoaded', function() {
    const loadButton = document.getElementById("loadQuestionBtn");
    if (loadButton) {
        loadButton.addEventListener('click', function() {
            const questionId = document.getElementById("id").value;
            loadQuestionData(questionId);
        });
    } else {
        console.log('Button nicht gefunden!');
    }

    const loadAllQuestionsButton = document.getElementById("loadAllQuestionsBtn");
    if (loadAllQuestionsButton) {
        loadAllQuestionsButton.addEventListener('click', function() {
            loadAllQuestions();
        });
    }
});


document.addEventListener("DOMContentLoaded", () => {
    initAddQuestionForm();
    initAddUserForm();
    initEditQuestionForm();
    initDeleteQuestionForm(); 
});

