// assets/js/components/modal-producto.js
(function () {
  /* ---------- Referencias DOM ------------------------------------ */
  const modal     = document.getElementById('modal-producto');
  if (!modal) return;                        // si no existe, salir

  const imgEl     = modal.querySelector('#modal-imagen');
  const titleEl   = modal.querySelector('#modal-titulo');
  const refEl     = modal.querySelector('#modal-ref');
  const priceEl   = modal.querySelector('#modal-precio');
  const descEl    = modal.querySelector('#modal-descripcion');
  const btnWhats  = modal.querySelector('#modal-btn-whatsapp');
  const btnMail   = modal.querySelector('#modal-btn-mail');

  let lastFocus   = null;                    // gestión de focus

  const COP = n => '$' + Number(n).toLocaleString('es-CO', { maximumFractionDigits: 0 });

  /* ---------- Abrir ------------------------------------------------ */
  function open (card) {
    lastFocus = document.activeElement;

    /* Datos desde data‑attributes */
    const nombre       = card.dataset.nombre           || 'Producto';
    const ref          = card.dataset.referencia       || '';
    const precio       = card.dataset.precio           || '';
    const descrLarga   = card.dataset.descripcionLarga || card.getAttribute('data-descripcion-larga') || '';
    const descrCorta   = card.dataset.descripcion      || '';
    const descr        = descrLarga || descrCorta;
    const img          = card.dataset.img              || card.querySelector('img')?.src || '';

    /* Pintar en el modal */
    titleEl.textContent = nombre;
    refEl.textContent   = ref    ? `Ref: ${ref}` : '';
    priceEl.textContent = precio ? COP(precio)  : '';
    descEl.textContent  = descr  || '';

    if (img) {
      imgEl.src     = img;
      imgEl.alt     = nombre;
      imgEl.loading = 'eager';
    }

    /* Enlaces dinámicos */
    const msg = encodeURIComponent(`Hola Maleja, me interesa el producto: ${nombre}${ref ? ' (ref ' + ref + ')' : ''}`);
    btnWhats.href = `https://wa.me/573135152530?text=${msg}`;

    const subject = encodeURIComponent(`Consulta: ${nombre}${ref ? ' - Ref ' + ref : ''}`);
    const body    = encodeURIComponent('Hola, me interesa este modelo. ¿Disponibilidad de tallas?');
    btnMail.href  = `mailto:ventas@malejacalzado.co?subject=${subject}&body=${body}`;

    /* Mostrar modal */
    modal.classList.add('is-open');
    modal.setAttribute('aria-hidden', 'false');
    document.body.style.overflow = 'hidden';

    /* Enfocar botón cerrar */
    requestAnimationFrame(() => modal.querySelector('.modal__close')?.focus());

    /* Esc → cerrar */
    document.addEventListener('keydown', onEsc);
  }

  /* ---------- Cerrar ---------------------------------------------- */
  function close () {
    modal.classList.remove('is-open');
    modal.setAttribute('aria-hidden', 'true');
    document.body.style.overflow = '';
    document.removeEventListener('keydown', onEsc);
    lastFocus?.focus();
  }

  function onEsc (e) { if (e.key === 'Escape') close(); }

  /* ---------- Delegación de eventos ------------------------------- */
  document.addEventListener('click', e => {
    const trigger = e.target.closest('[data-accion="info"]');
    if (trigger) {                            // abrir desde tarjeta
      const card = trigger.closest('.producto-card');
      if (card) open(card);
      return;
    }
    /* clic backdrop o botones con data-modal-cerrar */
    if (
      e.target.hasAttribute('data-modal-cerrar') ||
      e.target === modal.querySelector('.modal__backdrop')
    ) {
      close();
    }
  });
})();