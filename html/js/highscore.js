// Globale Highscore-Ladefunktion
window.loadHighscore = async function () {
  try {
    const response = await fetch('php/highscore.php');
    const data = await response.json();

    const tbody = document.querySelector('tbody');
    if (!tbody) return;

    tbody.innerHTML = ''; // Alte Einträge entfernen

    data.forEach((entry, index) => {
      const row = document.createElement('tr');
      row.innerHTML = `
        <td>${index + 1}</td>
        <td>${entry.username}</td>
        <td>${entry.totalScore}</td>
      `;
      tbody.appendChild(row);
    });
  } catch (error) {
    console.error('Fehler beim Laden der Highscores:', error);
  }
};

// Automatischer Aufruf beim Laden der Seite (nur wenn eine Tabelle da ist)
document.addEventListener('DOMContentLoaded', () => {
  const tbody = document.querySelector('tbody');
  if (tbody) {
    loadHighscore();
  }
});
