async function login(name, password) {
    const response = await fetch('php/login.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ name, password })
    });
    const result = await response.json();
    if (result.success) {
        localStorage.setItem('playerId', result.playerId);  // Für spätere Spiele
        alert("Eingeloggt!");
    } else {
        alert(result.message || "Login fehlgeschlagen");
    }
}
