async function login(name, password) {
    const response = await fetch('php/login.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ name, password })
    });
    const result = await response.json();
    if (result.success) {
      localStorage.setItem('playerId', result.playerId);
      alert("Eingeloggt!");
    } else {
      alert(result.message || "Login fehlgeschlagen");
    }
  }
  
function handleLogin() {
    const name = document.getElementById('name').value;
    const password = document.getElementById('password').value;
    login(name, password).then(() => {
      if (localStorage.getItem('playerId')) {
        window.location.href = 'quiz.html';
      }
    });
  }
  