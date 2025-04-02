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
    initAddUserForm(); // 👈 Benutzer-Formular aktivieren
});