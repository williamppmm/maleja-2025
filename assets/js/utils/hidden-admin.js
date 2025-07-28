// assets/js/utils/hidden-admin.js
// Triple-clic en logo para acceso admin con gestión de sesión

(function () {
  'use strict';
  
  const el = document.getElementById('logo-maleja-footer');
  if (!el) return;

  const ADMIN_LOGIN = window.__ADMIN_LOGIN__ || '/admin/login.php';
  const ADMIN_DASHBOARD = window.__ADMIN_DASHBOARD__ || '/admin/dashboard.php';
  const ADMIN_LOGOUT = window.__ADMIN_LOGOUT__ || '/admin/logout.php';

  let clicks = 0, t = null;
  
  el.addEventListener('click', function (e) {
    clicks++;
    
    if (clicks === 3) {
      e.preventDefault();
      handleTripleClick();
    }
    
    clearTimeout(t);
    t = setTimeout(() => { clicks = 0; }, 700);
  }, { passive: false }); // Cambiar a false para poder preventDefault

  function handleTripleClick() {
    // Verificar si ya hay una sesión admin activa
    checkAdminSession()
      .then(isLoggedIn => {
        if (isLoggedIn) {
          // Ya está logueado, mostrar opciones
          showAdminMenu();
        } else {
          // No está logueado, ir al login
          window.location.href = ADMIN_LOGIN;
        }
      })
      .catch(error => {
        console.log('Error verificando sesión admin:', error);
        // En caso de error, ir al login
        window.location.href = ADMIN_LOGIN;
      });
  }

  function checkAdminSession() {
    return fetch(ADMIN_LOGOUT + '?action=check', {
      method: 'GET',
      credentials: 'same-origin',
      headers: {
        'X-Requested-With': 'XMLHttpRequest'
      }
    })
    .then(response => response.json())
    .then(data => data.logged_in === true)
    .catch(() => false);
  }

  function showAdminMenu() {
    // Crear modal con opciones
    const modal = createAdminModal();
    document.body.appendChild(modal);
    
    // Auto-remover después de 10 segundos
    setTimeout(() => {
      if (modal.parentNode) {
        modal.remove();
      }
    }, 10000);
  }

  function createAdminModal() {
    const modal = document.createElement('div');
    modal.className = 'admin-modal-overlay';
    modal.innerHTML = `
      <div class="admin-modal">
        <div class="admin-modal-header">
          <h3>Panel de Administración</h3>
          <button class="admin-modal-close" aria-label="Cerrar">&times;</button>
        </div>
        <div class="admin-modal-body">
          <p>Ya tienes una sesión activa. ¿Qué deseas hacer?</p>
          <div class="admin-modal-actions">
            <a href="${ADMIN_DASHBOARD}" class="admin-btn admin-btn-primary">
              📊 Ir al Dashboard
            </a>
            <button class="admin-btn admin-btn-danger" data-action="logout">
              🚪 Cerrar Sesión
            </button>
            <button class="admin-btn admin-btn-secondary" data-action="cancel">
              ❌ Cancelar
            </button>
          </div>
        </div>
      </div>
    `;

    // Event listeners
    modal.addEventListener('click', (e) => {
      const action = e.target.dataset.action;
      
      if (e.target.classList.contains('admin-modal-close') || action === 'cancel') {
        modal.remove();
      } else if (action === 'logout') {
        performLogout();
      } else if (e.target === modal) {
        modal.remove();
      }
    });

    return modal;
  }

  function performLogout() {
    // Mostrar loading
    const modal = document.querySelector('.admin-modal');
    if (modal) {
      modal.innerHTML = `
        <div style="padding: 40px; text-align: center; color: #ccc;">
          <div style="display: inline-block; width: 30px; height: 30px; border: 3px solid #333; border-top: 3px solid #d4af37; border-radius: 50%; animation: spin 1s linear infinite;"></div>
          <p style="margin: 15px 0 0; font-size: 14px;">Cerrando sesión...</p>
        </div>
      `;
    }

    // Realizar logout
    fetch(ADMIN_LOGOUT, {
      method: 'POST',
      credentials: 'same-origin',
      headers: {
        'Content-Type': 'application/x-www-form-urlencoded',
        'X-Requested-With': 'XMLHttpRequest'
      }
    })
    .then(() => {
      // Remover modal y mostrar confirmación
      if (modal) modal.remove();
      
      // Mostrar notificación temporal
      showLogoutNotification();
    })
    .catch(error => {
      console.error('Error al cerrar sesión:', error);
      if (modal) modal.remove();
    });
  }

  function showLogoutNotification() {
    const notification = document.createElement('div');
    notification.style.cssText = `
      position: fixed;
      top: 20px;
      right: 20px;
      background: #2d5016;
      color: #90ee90;
      padding: 15px 20px;
      border-radius: 8px;
      border: 1px solid #4caf50;
      box-shadow: 0 4px 12px rgba(0,0,0,0.3);
      z-index: 10001;
      font-size: 14px;
      animation: slideInRight 0.3s ease-out;
    `;
    notification.innerHTML = '✅ Sesión cerrada correctamente';
    
    // Agregar animación CSS
    const style = document.createElement('style');
    style.textContent = `
      @keyframes slideInRight {
        from { transform: translateX(100%); opacity: 0; }
        to { transform: translateX(0); opacity: 1; }
      }
      @keyframes spin {
        from { transform: rotate(0deg); }
        to { transform: rotate(360deg); }
      }
    `;
    document.head.appendChild(style);
    
    document.body.appendChild(notification);
    
    // Auto-remover después de 3 segundos
    setTimeout(() => {
      notification.style.animation = 'slideInRight 0.3s ease-out reverse';
      setTimeout(() => notification.remove(), 300);
    }, 3000);
  }
})();