// Navbar automatisch einfügen
document.addEventListener('DOMContentLoaded', async () => {
    const container = document.getElementById('navbar-container');
    if (!container) return;
  
    try {
      const response = await fetch('navbar.html');
      const html = await response.text();
      container.innerHTML = html;
  
      // Nach dem Einfügen: aktiven Link markieren
      const links = container.querySelectorAll('.navbar a');
      const current = location.pathname.split('/').pop();
  
      links.forEach(link => {
        if (link.getAttribute('href') === current) {
          link.classList.add('active');
        }
      });
    } catch (err) {
      console.error('Fehler beim Laden der Navbar:', err);
    }
  });
  