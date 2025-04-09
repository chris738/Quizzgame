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
      localStorage.setItem('player2Name', result.username);
      alert("Spieler 2 eingeloggt!");
    } else {
      localStorage.setItem('playerId', result.playerId);
      localStorage.setItem('playerName', result.username);
      alert("Spieler 1 eingeloggt!");
    }

    if (typeof loadNavbar === 'function') loadNavbar();
  } else {
    alert(result.message || "Login fehlgeschlagen");
  }
}

function handleLogin() {
  const name = document.getElementById('name').value;
  const password = document.getElementById('password').value;
  login(name, password);
}

function logout() {
  const isPlayer2 = document.getElementById('isPlayer2').checked;
  if (isPlayer2) {
    localStorage.removeItem('player2Id');
    alert("Spieler 2 ausgeloggt!");
  } else {
    localStorage.removeItem('playerId');
    alert("Spieler 1 ausgeloggt!");
  }
  if (typeof loadNavbar === 'function') loadNavbar();
}
