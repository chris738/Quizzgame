async function login(name, password) {
  const response = await fetch('php/login.php', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({ name, password })
  });

  const result = await response.json();

  if (result.success) {
    const isPlayer2 = document.getElementById('isPlayer2').checked;
    if (isPlayer2) {
      localStorage.setItem('player2Id', result.playerId);
      alert("Spieler 2 eingeloggt!");
    } else {
      localStorage.setItem('playerId', result.playerId);
      alert("Spieler 1 eingeloggt!");
    }
  } else {
    alert(result.message || "Login fehlgeschlagen");
  }
}

function handleLogin() {
  const name = document.getElementById('name').value;
  const password = document.getElementById('password').value;
  login(name, password).then(() => {
    if (localStorage.getItem('playerId') || localStorage.getItem('player2Id')) {
      window.location.href = 'quiz.html';
    }
  });
}
