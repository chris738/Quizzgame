document.addEventListener('DOMContentLoaded', async () => {
    const response = await fetch('php/highscore.php');
    const data = await response.json();
    const tbody = document.querySelector('tbody');
    tbody.innerHTML = ''; // alte Beispielzeilen entfernen
  
    data.forEach((entry, index) => {
      const row = document.createElement('tr');
      row.innerHTML = `
        <td>${index + 1}</td>
        <td>${entry.username}</td>
        <td>${entry.totalScore}</td>
      `;
      tbody.appendChild(row);
    });
  });
  