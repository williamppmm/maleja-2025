/**
 * MALEJA Calzado - script principal
 * - Modal de detalle de producto
 * - Filtros auto-submit
 */

document.addEventListener('DOMContentLoaded', () => {
  initProductModal();
  initAutoFilters();
});

/* ========== MODAL DETALLE PRODUCTO (botón Ver detalle) ========== */
function initProductModal() {
  const modal = document.getElementById('modal-producto');
  if (!modal) return;

  const imgEl    = document.getElementById('modal-imagen');
  const tituloEl = document.getElementById('modal-titulo');
  const refEl    = document.getElementById('modal-ref');
  const precioEl = document.getElementById('modal-precio');
  const descEl   = document.getElementById('modal-descripcion');
  const btnWhats = document.getElementById('modal-btn-whatsapp');
  const btnMail  = document.getElementById('modal-btn-mail');
  let lastFocus  = null;

  const formatCOP = n => '$' + Number(n).toLocaleString('es-CO', { maximumFractionDigits: 0 });

  function abrir(card) {
    lastFocus = document.activeElement;
    const nombre = card.dataset.nombre || 'Producto';
    const ref    = card.dataset.referencia || '';
    const precio = card.dataset.precio || '';
    const descr  = card.dataset.descripcion || '';
    const img    = card.dataset.img || card.querySelector('img')?.src || '';

    tituloEl.textContent = nombre;
    refEl.textContent    = ref ? 'Ref: ' + ref : '';
    precioEl.textContent = precio ? formatCOP(precio) : '';
    descEl.textContent   = descr;
    imgEl.src = img;
    imgEl.alt = nombre;

    const msg = encodeURIComponent(`Hola Maleja, me interesa el producto: ${nombre}${ref ? ' (ref '+ref+')' : ''}`);
    btnWhats.href = `https://wa.me/573172703742?text=${msg}`;

    const subject = encodeURIComponent(`Consulta: ${nombre}${ref ? ' - Ref '+ref : ''}`);
    const body    = encodeURIComponent('Hola, me interesa este modelo. ¿Disponibilidad de tallas?');
    btnMail.href  = `mailto:ventas@malejacalzado.co?subject=${subject}&body=${body}`;

    modal.classList.add('is-open');
    modal.setAttribute('aria-hidden', 'false');
    setTimeout(() => modal.querySelector('.modal__close')?.focus(), 30);
    document.addEventListener('keydown', escClose);
  }

  function cerrar() {
    modal.classList.remove('is-open');
    modal.setAttribute('aria-hidden', 'true');
    document.removeEventListener('keydown', escClose);
    if (lastFocus) lastFocus.focus();
  }
  
  function escClose(e) { 
    if (e.key === 'Escape') cerrar(); 
  }

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
  const selCat   = form.querySelector('select[name="cat"]');
  const inputQ   = form.querySelector('input[name="q"]');
  const btnFiltrar = form.querySelector('.btn-filtrar');
  
  if (btnFiltrar) btnFiltrar.style.display = 'none';

  if (selOrden) selOrden.addEventListener('change', () => form.submit());
  if (selCat)   selCat.addEventListener('change', () => form.submit());

  if (inputQ) {
    let t;
    inputQ.addEventListener('input', () => {
      clearTimeout(t);
      t = setTimeout(() => form.submit(), 800);
    });
    inputQ.addEventListener('keypress', e => {
      if (e.key === 'Enter') {
        e.preventDefault();
        clearTimeout(t);
        form.submit();
      }
    });
  }
}