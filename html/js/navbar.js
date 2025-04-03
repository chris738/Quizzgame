document.addEventListener('DOMContentLoaded', () => {
    loadNavbar();
  });
  
  async function loadNavbar() {
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
    const playerId = parseInt(localStorage.getItem('playerId')) || 0;
    if (playerId <= 0) return;
  
    fetch(`php/login.php?playerId=${playerId}`)
      .then(res => res.json())
      .then(data => {
        if (data.success && data.username) {
          const userElement = document.getElementById('nav-username');
          if (userElement) {
            userElement.textContent = '🧑 ' + data.username;
          }
        }
      })
      .catch(err => console.error('Fehler beim Laden des Benutzernamens:', err));
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
  