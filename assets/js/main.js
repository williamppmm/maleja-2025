// assets/js/main.js
(function () {
  'use strict';

  /* ---------------------------------------------------------------
   * Arranque
   * ------------------------------------------------------------- */
  document.addEventListener('DOMContentLoaded', () => {
    initAutoFilters();
    initPerformanceOptimizations();
    initDevCredit();
  });

  /* ---------------------------------------------------------------
   * 1. Filtros auto‑submit (cat, orden, búsqueda)
   * ------------------------------------------------------------- */
  function initAutoFilters () {
    const form = document.querySelector('.filtros-form');
    if (!form) return;

    const selOrden   = form.querySelector('select[name="orden"]');
    const selCat     = form.querySelector('select[name="cat"]');
    const inputQ     = form.querySelector('input[name="q"]');
    const btnFiltrar = form.querySelector('.btn-filtrar');

    /* Ocultar botón cuando hay JS */
    if (btnFiltrar) btnFiltrar.style.display = 'none';

    const submitWithLoader = () => {
      const grid = document.querySelector('.grid-productos');
      if (grid) {
        grid.innerHTML =
          '<div class="loading-spinner"><div class="spinner"></div></div>';
      }
      form.submit();
    };

    /* select orden / categoría */
    selOrden && selOrden.addEventListener('change', submitWithLoader);
    selCat   && selCat.addEventListener('change', submitWithLoader);

    /* búsqueda con debounce */
    if (inputQ) {
      let t;
      inputQ.addEventListener('input', () => {
        clearTimeout(t);
        t = setTimeout(submitWithLoader, 800);
      });
      inputQ.addEventListener('keypress', e => {
        if (e.key === 'Enter') {
          e.preventDefault();
          clearTimeout(t);
          submitWithLoader();
        }
      });
    }
  }

  /* ---------------------------------------------------------------
   * 2. Optimizaciones de performance
   * ------------------------------------------------------------- */
  function initPerformanceOptimizations () {
    /* Lazy‑load de imágenes con data‑src */
    if ('IntersectionObserver' in window) {
      const io = new IntersectionObserver((entries, obs) => {
        entries.forEach(entry => {
          if (entry.isIntersecting) {
            const img = entry.target;
            img.src = img.dataset.src;
            img.removeAttribute('data-src');
            obs.unobserve(img);
          }
        });
      }, { rootMargin: '0px 0px 100px 0px' });

      document
        .querySelectorAll('img[data-src]')
        .forEach(img => io.observe(img));
    }

    /* Preload de imagen en tarjetas al hover */
    document
      .querySelectorAll('.producto-card[data-img]')
      .forEach(card => {
        card.addEventListener(
          'mouseenter',
          () => {
            const url = card.dataset.img;
            if (
              url &&
              !document.querySelector(`link[rel="preload"][href="${url}"]`)
            ) {
              const l   = document.createElement('link');
              l.rel     = 'preload';
              l.as      = 'image';
              l.href    = url;
              document.head.appendChild(l);
            }
          },
          { once: true }
        );
      });

    /* Respetar "prefers‑reduced‑motion" */
    if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
      document.documentElement.style.setProperty('--transition', '0.1s ease');
    }
  }

  /* ---------------------------------------------------------------
   * 3. Crédito del desarrollador / Tooltip
   * ------------------------------------------------------------- */
  function initDevCredit () {
    const trigger = document.getElementById('dev-credit-trigger');
    const info    = document.getElementById('dev-info');
    
    if (!trigger || !info) {
      console.log('[dev-credit] Elementos no encontrados:', { trigger: !!trigger, info: !!info });
      return;
    }

    console.log('[dev-credit] Inicializando tooltip del desarrollador');

    let visible  = false;
    let autoHide = null;

    const show = () => {
      clearTimeout(autoHide);
      
      // Mostrar el tooltip usando la clase CSS
      info.classList.add('show');
      visible = true;
      
      // Auto-ocultar después de 6 segundos
      autoHide = setTimeout(hide, 6000);
      console.log('[dev-credit] Tooltip mostrado');
    };

    const hide = () => {
      clearTimeout(autoHide);
      
      // Ocultar el tooltip removiendo la clase
      info.classList.remove('show');
      visible = false;
      
      console.log('[dev-credit] Tooltip ocultado');
    };

    // Event listener para el click en el trigger
    trigger.addEventListener('click', e => { 
      e.preventDefault(); 
      console.log('[dev-credit] Click en trigger, visible:', visible);
      visible ? hide() : show(); 
    });
    
    // Event listener para cerrar al hacer click fuera
    document.addEventListener('click', e => {
      if (visible && !trigger.contains(e.target) && !info.contains(e.target)) {
        hide();
      }
    });
  }

  /* ---------------------------------------------------------------
   * 4. Utilidades globales
   * ------------------------------------------------------------- */
  function isValidEmail (mail) {
    return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(mail);
  }

  function isMobileDevice () {
    return (
      window.innerWidth <= 768 ||
      /Android|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(
        navigator.userAgent
      )
    );
  }

  const cleanPhoneNumber = phone => phone.replace(/\D/g, '');

  function trackEvent (name, params = {}) {
    /* GA4 */
    if (typeof gtag === 'function') gtag('event', name, params);
    /* Meta Pixel */
    if (typeof fbq === 'function') fbq('track', name, params);
    /* Consola para desarrollo */
    console.log('[track]', name, params);
  }

  /* Exponer utilidades si hiciera falta */
  window.MalejaCalzado = {
    trackEvent,
    isValidEmail,
    isMobileDevice,
    cleanPhoneNumber
  };

  /* ---------------------------------------------------------------
   * 5. Manejo básico de errores
   * ------------------------------------------------------------- */
  window.addEventListener('error', e =>
    console.error('💥 JS error:', e.error || e.message)
  );
  window.addEventListener('unhandledrejection', e => {
    console.error('💥 Promise rejection:', e.reason);
    e.preventDefault();
  });
})();