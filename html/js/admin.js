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


document.addEventListener("DOMContentLoaded", () => {
    initAddQuestionForm();
    initAddUserForm();
    initEditQuestionForm();
    initDeleteQuestionForm(); 
});

document.getElementById("loadQuestionBtn").addEventListener("click", function () {
    const questionId = document.getElementById("id").value;
    loadQuestionData(questionId);  
});