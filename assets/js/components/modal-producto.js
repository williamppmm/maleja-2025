// assets/js/components/modal-producto.js
(function () {
  /* ---------- Referencias DOM ------------------------------------ */
  const modal     = document.getElementById('modal-producto');
  if (!modal) return;

  const imgEl     = modal.querySelector('#modal-imagen');
  const titleEl   = modal.querySelector('#modal-titulo');
  const refEl     = modal.querySelector('#modal-ref');
  const priceEl   = modal.querySelector('#modal-precio');
  const descEl    = modal.querySelector('#modal-descripcion');
  const btnWhats  = modal.querySelector('#modal-btn-whatsapp');
  const btnMail   = modal.querySelector('#modal-btn-mail');
  const selectTalla = modal.querySelector('#modal-talla');

  let lastFocus   = null;
  let currentProductData = null;

  const COP = n => '$' + Number(n).toLocaleString('es-CO', { maximumFractionDigits: 0 });
  const WHATS_NUMBER = '573172703742';
  const IMAGE_BASE   = 'https://malejacalzado.com';

  function resolveImageUrl (imgPath = '') {
    if (!imgPath) return '';
    if (imgPath.startsWith('http')) return imgPath;
    const base = IMAGE_BASE.replace(/\/+$/, '');
    const path = imgPath.startsWith('/') ? imgPath : `/${imgPath}`;
    return `${base}${path}`;
  }

  function getSelectedTalla () {
    return (selectTalla && typeof selectTalla.value === 'string')
      ? selectTalla.value.trim()
      : '';
  }

  function buildContactMessage () {
    if (!currentProductData) return { message: '', talla: '' };

    const talla = getSelectedTalla();
    const { nombre, referencia, imagen } = currentProductData;

    let message = `Hola Maleja, me interesa el producto: ${nombre}`;
    if (referencia) message += ` (ref ${referencia})`;
    if (talla) message += ` - Talla: ${talla}`;

    const imgUrl = resolveImageUrl(imagen);
    if (imgUrl) message += `\n\nVer producto: ${imgUrl}`;

    return { message, talla };
  }

  function buildEmailSubject (talla) {
    if (!currentProductData) return 'Consulta de producto';

    const { nombre, referencia } = currentProductData;
    const detailParts = [];

    if (referencia) detailParts.push(`Ref ${referencia}`);
    if (talla) detailParts.push(`Talla ${talla}`);

    const detailText = detailParts.length ? ` (${detailParts.join(' - ')})` : '';
    return `Consulta: ${nombre}${detailText}`;
  }

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

    /* Guardar datos para uso posterior */
    currentProductData = {
      nombre,
      referencia: ref,
      imagen: img
    };

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

    /* Resetear selector de talla */
    if (selectTalla) selectTalla.value = '';

    /* Actualizar enlaces de contacto */
    updateWhatsAppLink();
    updateEmailLink();

    /* Mostrar modal */
    modal.classList.add('is-open');
    modal.setAttribute('aria-hidden', 'false');
    document.body.style.overflow = 'hidden';

    /* Enfocar botón cerrar */
    requestAnimationFrame(() => modal.querySelector('.modal__close')?.focus());

    /* Esc → cerrar */
    document.addEventListener('keydown', onEsc);
  }

  /* ---------- Actualizar enlace de WhatsApp ----------------------- */
  function updateWhatsAppLink () {
    if (!currentProductData || !btnWhats) return;
    const { message } = buildContactMessage();
    btnWhats.href = `https://wa.me/${WHATS_NUMBER}?text=${encodeURIComponent(message)}`;
  }

  function updateEmailLink () {
    if (!currentProductData || !btnMail) return;
    const { message, talla } = buildContactMessage();
    const subject = buildEmailSubject(talla);
    btnMail.href = `mailto:ventas@malejacalzado.com?subject=${encodeURIComponent(subject)}&body=${encodeURIComponent(message)}`;
  }

  /* ---------- Event listener para selector de talla --------------- */
  if (selectTalla) {
    selectTalla.addEventListener('change', () => {
      updateWhatsAppLink();
      updateEmailLink();
    });
  }

  /* ---------- Cerrar ---------------------------------------------- */
  function close () {
    modal.classList.remove('is-open');
    modal.setAttribute('aria-hidden', 'true');
    document.body.style.overflow = '';
    document.removeEventListener('keydown', onEsc);
    lastFocus?.focus();
    currentProductData = null;
  }

  function onEsc (e) { if (e.key === 'Escape') close(); }

  /* ---------- Delegación de eventos ------------------------------- */
  document.addEventListener('click', e => {
    const trigger = e.target.closest('[data-accion="info"]');
    if (trigger) {
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
