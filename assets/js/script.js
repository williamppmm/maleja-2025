/**
 * MALEJA Calzado - script principal
 * - Modal de detalle de producto
 * - Filtros auto-submit
 * - Optimizaciones de performance
 */

document.addEventListener('DOMContentLoaded', () => {
  initProductModal();
  initAutoFilters();
  initPerformanceOptimizations();
});

/* ========== MODAL DETALLE PRODUCTO ========== */
function initProductModal() {
  const modal = document.getElementById('modal-producto');
  if (!modal) return;

  const imgEl = document.getElementById('modal-imagen');
  const tituloEl = document.getElementById('modal-titulo');
  const refEl = document.getElementById('modal-ref');
  const precioEl = document.getElementById('modal-precio');
  const descEl = document.getElementById('modal-descripcion');
  const btnWhats = document.getElementById('modal-btn-whatsapp');
  const btnMail = document.getElementById('modal-btn-mail');
  let lastFocus = null;

  const formatCOP = n => '$' + Number(n).toLocaleString('es-CO', { maximumFractionDigits: 0 });

  function abrir(card) {
    lastFocus = document.activeElement;
    
    const nombre = card.dataset.nombre || 'Producto';
    const ref = card.dataset.referencia || '';
    const precio = card.dataset.precio || '';
    const descr = card.dataset.descripcion || '';
    const img = card.dataset.img || card.querySelector('img')?.src || '';

    // Sanitizar datos
    tituloEl.textContent = nombre;
    refEl.textContent = ref ? 'Ref: ' + ref : '';
    precioEl.textContent = precio ? formatCOP(precio) : '';
    descEl.textContent = descr;
    
    // Configurar imagen con lazy loading
    if (img) {
      imgEl.src = img;
      imgEl.alt = nombre;
      imgEl.loading = 'eager'; // Cargar inmediatamente en modal
    }

    // Generar enlaces dinámicos
    const msg = encodeURIComponent(`Hola Maleja, me interesa el producto: ${nombre}${ref ? ' (ref '+ref+')' : ''}`);
    btnWhats.href = `https://wa.me/573172703742?text=${msg}`;

    const subject = encodeURIComponent(`Consulta: ${nombre}${ref ? ' - Ref '+ref : ''}`);
    const body = encodeURIComponent('Hola, me interesa este modelo. ¿Disponibilidad de tallas?');
    btnMail.href = `mailto:ventas@malejacalzado.co?subject=${subject}&body=${body}`;

    // Abrir modal con animación
    modal.classList.add('is-open');
    modal.setAttribute('aria-hidden', 'false');
    
    // Focus management
    requestAnimationFrame(() => {
      const closeBtn = modal.querySelector('.modal__close');
      if (closeBtn) closeBtn.focus();
    });
    
    // Prevenir scroll del body
    document.body.style.overflow = 'hidden';
    
    // Event listeners
    document.addEventListener('keydown', escClose);
    
    // Analytics (opcional)
    if (typeof gtag !== 'undefined') {
      gtag('event', 'view_item', {
        'item_id': card.dataset.id || '',
        'item_name': nombre,
        'item_category': 'Calzado'
      });
    }
  }

  function cerrar() {
    modal.classList.remove('is-open');
    modal.setAttribute('aria-hidden', 'true');
    document.removeEventListener('keydown', escClose);
    
    // Restaurar scroll del body
    document.body.style.overflow = '';
    
    // Restaurar focus
    if (lastFocus) {
      lastFocus.focus();
      lastFocus = null;
    }
  }
  
  function escClose(e) { 
    if (e.key === 'Escape') cerrar(); 
  }

  // Event delegation para mejor performance
  document.addEventListener('click', e => {
    const trigger = e.target.closest('[data-accion="info"]');
    if (trigger) {
      e.preventDefault();
      const card = trigger.closest('.producto-card');
      if (card) abrir(card);
      return;
    }
    
    if (e.target.hasAttribute('data-modal-cerrar') ||
        e.target === modal.querySelector('.modal__backdrop')) {
      cerrar();
    }
  });
}

/* ========== FILTROS AUTO-SUBMIT ========== */
function initAutoFilters() {
  const form = document.querySelector('.filtros-form');
  if (!form) return;

  const selOrden = form.querySelector('select[name="orden"]');
  const selCat = form.querySelector('select[name="cat"]');
  const inputQ = form.querySelector('input[name="q"]');
  const btnFiltrar = form.querySelector('.btn-filtrar');
  
  // Ocultar botón filtrar si JS está disponible
  if (btnFiltrar) btnFiltrar.style.display = 'none';

  // Auto-submit en cambio de orden
  if (selOrden) {
    selOrden.addEventListener('change', () => {
      showLoadingState();
      form.submit();
    });
  }
  
  // Auto-submit en cambio de categoría
  if (selCat) {
    selCat.addEventListener('change', () => {
      showLoadingState();
      form.submit();
    });
  }

  // Auto-submit con debounce en búsqueda
  if (inputQ) {
    let searchTimeout;
    
    inputQ.addEventListener('input', () => {
      clearTimeout(searchTimeout);
      searchTimeout = setTimeout(() => {
        showLoadingState();
        form.submit();
      }, 800);
    });
    
    inputQ.addEventListener('keypress', e => {
      if (e.key === 'Enter') {
        e.preventDefault();
        clearTimeout(searchTimeout);
        showLoadingState();
        form.submit();
      }
    });
  }
  
  function showLoadingState() {
    const container = document.querySelector('.grid-productos');
    if (container) {
      container.innerHTML = '<div class="loading-spinner"><div class="spinner"></div></div>';
    }
  }
}

/* ========== OPTIMIZACIONES DE PERFORMANCE ========== */
function initPerformanceOptimizations() {
  // Lazy loading para imágenes
  if ('IntersectionObserver' in window) {
    const imageObserver = new IntersectionObserver((entries, observer) => {
      entries.forEach(entry => {
        if (entry.isIntersecting) {
          const img = entry.target;
          if (img.dataset.src) {
            img.src = img.dataset.src;
            img.removeAttribute('data-src');
            observer.unobserve(img);
          }
        }
      });
    });

    // Observar imágenes con data-src
    document.querySelectorAll('img[data-src]').forEach(img => {
      imageObserver.observe(img);
    });
  }
  
  // Preload de imágenes críticas al hover
  const productCards = document.querySelectorAll('.producto-card');
  productCards.forEach(card => {
    card.addEventListener('mouseenter', preloadImage, { once: true });
  });
  
  function preloadImage(e) {
    const img = e.currentTarget.dataset.img;
    if (img && !document.querySelector(`link[href="${img}"]`)) {
      const link = document.createElement('link');
      link.rel = 'preload';
      link.as = 'image';
      link.href = img;
      document.head.appendChild(link);
    }
  }
  
  // Throttle para scroll events si es necesario
  let scrollTimeout;
  function throttleScroll(callback, limit = 100) {
    return function() {
      if (!scrollTimeout) {
        scrollTimeout = setTimeout(() => {
          callback.apply(this, arguments);
          scrollTimeout = null;
        }, limit);
      }
    };
  }
  
  // Detectar si el usuario prefiere menos movimiento
  const prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
  if (prefersReducedMotion) {
    document.documentElement.style.setProperty('--transition', '0.1s ease');
  }
}

/* ========== UTILIDADES ADICIONALES ========== */

// Función para validar email (si es necesaria)
function isValidEmail(email) {
  const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
  return emailRegex.test(email);
}

// Función para detectar dispositivo móvil
function isMobileDevice() {
  return window.innerWidth <= 768 || /Android|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent);
}

// Función para generar números de WhatsApp limpios
function cleanPhoneNumber(phone) {
  return phone.replace(/\D/g, '');
}

// Manejo de errores global
window.addEventListener('error', (e) => {
  console.error('Error capturado:', e.error);
  // Aquí podrías enviar el error a un servicio de tracking
});

// Manejo de promesas rechazadas
window.addEventListener('unhandledrejection', (e) => {
  console.error('Promesa rechazada:', e.reason);
  e.preventDefault();
});

/* ========== ANALYTICS Y TRACKING (OPCIONAL) ========== */
function trackEvent(eventName, parameters = {}) {
  // Google Analytics 4
  if (typeof gtag !== 'undefined') {
    gtag('event', eventName, parameters);
  }
  
  // Facebook Pixel
  if (typeof fbq !== 'undefined') {
    fbq('track', eventName, parameters);
  }
  
  // Console log para desarrollo
  console.log('Event tracked:', eventName, parameters);
}

// Exportar funciones útiles para uso global
window.MalejaCalzado = {
  trackEvent,
  isValidEmail,
  isMobileDevice,
  cleanPhoneNumber
};