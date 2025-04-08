document.addEventListener('DOMContentLoaded', () => {
    loadNavbar();
  });
  
  window.loadNavbar = async function loadNavbar() {
    const container = document.getElementById('navbar-container');
    if (!container) return;
  
    try {
      const response = await fetch('navbar.html');
      const html = await response.text();
      container.innerHTML = html;
  
      markActiveLink(container);
      loadUsername();
    } catch (err) {
      console.error('Fehler beim Laden der Navbar:', err);
    }
  }
  
  function markActiveLink(container) {
    const links = container.querySelectorAll('.navbar a');
    const current = location.pathname.split('/').pop();
  
    links.forEach(link => {
      if (link.getAttribute('href') === current) {
        link.classList.add('active');
      }
    });
  }
  
  function loadUsername() {
    const player1Id = parseInt(localStorage.getItem('playerId')) || 0;
    const player2Id = parseInt(localStorage.getItem('player2Id')) || 0;
  
    const userElement = document.getElementById('nav-username');
    if (!userElement) return;
  
    const promises = [];
  
    if (player1Id > 0) {
      promises.push(fetch(`php/login.php?playerId=${player1Id}`).then(res => res.json()));
    } else {
      promises.push(Promise.resolve(null));
    }
  
    if (player2Id > 0) {
      promises.push(fetch(`php/login.php?playerId=${player2Id}`).then(res => res.json()));
    } else {
      promises.push(Promise.resolve(null));
    }
  
    Promise.all(promises).then(([p1, p2]) => {
      const lines = [];
  
      if (p1 && p1.success && p1.username) {
        lines.push(`🧑 Spieler 1: ${p1.username}`);
        localStorage.setItem('playerName', p1.username);
      }
  
      if (p2 && p2.success && p2.username) {
        lines.push(`🧑 Spieler 2: ${p2.username}`);
        localStorage.setItem('player2Name', p2.username);
      }
  
      if (lines.length > 0) {
        userElement.innerHTML = lines.join('<br>');
      }
    }).catch(err => {
      console.error('Fehler beim Laden der Spielernamen:', err);
    });
  }
  
  window.setNavVisibility = function (visible) {
    const toggleLinks = document.querySelectorAll(
      'nav[aria-label="Hauptnavigation"] a[href="index.html"],' +
      'nav[aria-label="Hauptnavigation"] a[href="quiz.html"],' +
      'nav[aria-label="Hauptnavigation"] a[href="highscore.html"]'
    );
  
    toggleLinks.forEach(link => {
      if (visible) {
        link.removeAttribute('aria-hidden');
      } else {
        link.setAttribute('aria-hidden', 'true');
      }
    });
  };
  