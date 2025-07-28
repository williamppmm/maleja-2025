// assets/js/utils/auto-logout.js
// Auto-logout cuando navegas del admin al sitio público

(function () {
  'use strict';

  // Solo ejecutar en páginas públicas (no en admin)
  if (window.location.pathname.includes('/admin/')) {
    return;
  }

  const ADMIN_LOGOUT = window.__ADMIN_LOGOUT__ || '/admin/logout.php';

  // Verificar si hay sesión admin activa al cargar la página
  document.addEventListener('DOMContentLoaded', () => {
    checkAndLogoutAdmin();
  });

  function checkAndLogoutAdmin() {
    // Verificar si hay sesión admin activa
    fetch(ADMIN_LOGOUT + '?action=check', {
      method: 'GET',
      credentials: 'same-origin',
      headers: {
        'X-Requested-With': 'XMLHttpRequest'
      }
    })
    .then(response => response.json())
    .then(data => {
      if (data.logged_in === true) {
        // Hay sesión activa, obtener token CSRF y cerrarla
        getCsrfTokenAndLogout();
      }
    })
    .catch(error => {
      // Si hay error en la verificación, no hacer nada
      console.debug('No se pudo verificar sesión admin:', error);
    });
  }

  function getCsrfTokenAndLogout() {
    // Primero necesitamos obtener el token CSRF
    // Hacemos una petición a dashboard.php para obtenerlo
    fetch('/admin/dashboard.php', {
      method: 'GET',
      credentials: 'same-origin',
      headers: {
        'X-Requested-With': 'XMLHttpRequest'
      }
    })
    .then(response => response.text())
    .then(html => {
      // Extraer el token CSRF del HTML del dashboard
      const parser = new DOMParser();
      const doc = parser.parseFromString(html, 'text/html');
      const csrfInput = doc.querySelector('input[name="csrf"]');
      
      if (csrfInput && csrfInput.value) {
        performSilentLogout(csrfInput.value);
      } else {
        console.debug('No se pudo obtener token CSRF para auto-logout');
      }
    })
    .catch(error => {
      console.debug('Error obteniendo token CSRF:', error);
    });
  }

  function performSilentLogout(csrfToken) {
    // Logout silencioso con token CSRF válido
    const formData = new FormData();
    formData.append('csrf', csrfToken);
    formData.append('redirect', 'silent'); // Nuevo parámetro para logout silencioso

    fetch(ADMIN_LOGOUT, {
      method: 'POST',
      credentials: 'same-origin',
      headers: {
        'X-Requested-With': 'XMLHttpRequest'
      },
      body: formData
    })
    .then(() => {
      // Logout exitoso, pero silencioso
      console.debug('Sesión admin cerrada automáticamente por seguridad');
    })
    .catch(error => {
      // Si hay error, no mostrar nada al usuario
      console.debug('Error en auto-logout:', error);
    });
  }

})();