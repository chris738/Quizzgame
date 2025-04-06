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
        formData.append('action', 'deleteQuestion'); // Action zum Löschen der Frage

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

document.addEventListener("DOMContentLoaded", () => {
    initDeleteQuestionForm(); // Initialisiert das Löschen-Formular
});


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
});