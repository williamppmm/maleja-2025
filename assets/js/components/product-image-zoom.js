// assets/js/components/product-image-zoom.js
// Hace zoom (abre el lightbox) al clicar en las imagenes de producto fuera del modal.
(function () {
  const MODAL_SELECTOR = '#modal-producto';
  const IMAGE_SELECTOR = '.producto-card__img';
  const ZOOMABLE_CLASS = 'producto-card__img--zoomable';

  const enhancedImages = new WeakSet();

  function getZoomSource (img) {
    if (img.dataset.zoomSrc) return img.dataset.zoomSrc;
    if (img.dataset.lightbox) return img.dataset.lightbox;

    const card = img.closest('.producto-card');
    if (card && card.dataset.img) return card.dataset.img;

    return img.currentSrc || img.src || '';
  }

  function getZoomAlt (img) {
    if (img.dataset.zoomAlt) return img.dataset.zoomAlt;
    if (img.alt && img.alt.trim().length > 0) return img.alt.trim();

    const card = img.closest('.producto-card');
    if (card && card.dataset.nombre) return 'Imagen de ' + card.dataset.nombre;

    return 'Imagen de producto';
  }

  function openZoom (img) {
    const src = getZoomSource(img);
    if (!src) return;

    const alt = getZoomAlt(img);

    if (typeof window.openLightbox === 'function') {
      window.openLightbox(src, alt);
    } else {
      window.open(src, '_blank', 'noopener');
    }
  }

  function enhanceImage (img) {
    if (!img || enhancedImages.has(img)) return;
    if (img.closest(MODAL_SELECTOR)) return;

    enhancedImages.add(img);
    img.classList.add(ZOOMABLE_CLASS);

    if (!img.hasAttribute('tabindex')) img.setAttribute('tabindex', '0');
    if (!img.hasAttribute('role')) img.setAttribute('role', 'button');
    if (!img.hasAttribute('aria-label')) {
      img.setAttribute('aria-label', getZoomAlt(img) + ' (ampliar)');
    }
  }

  function handleClick (event) {
    const img = event.target.closest(IMAGE_SELECTOR);
    if (!img || img.closest(MODAL_SELECTOR)) return;

    // Verificar que el click fue directamente en la imagen
    // y no en algún botón u otro elemento
    const target = event.target;
    if (target.tagName === 'BUTTON' || target.closest('button')) return;
    if (target.hasAttribute('data-accion')) return;

    enhanceImage(img);

    if (!enhancedImages.has(img)) return;

    event.preventDefault();
    event.stopPropagation();
    openZoom(img);
  }

  function handleKeydown (event) {
    if (event.key !== 'Enter' && event.key !== ' ') return;

    const target = event.target;
    if (!target || !target.matches(IMAGE_SELECTOR)) return;
    if (target.closest(MODAL_SELECTOR)) return;

    enhanceImage(target);
    if (!enhancedImages.has(target)) return;

    event.preventDefault();
    openZoom(target);
  }

  function init () {
    document.querySelectorAll(IMAGE_SELECTOR).forEach(enhanceImage);
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', init);
  } else {
    init();
  }

  document.addEventListener('click', handleClick);
  document.addEventListener('keydown', handleKeydown);

  // Observar tarjetas que se inyecten dinamicamente (paginacion, etc.)
  const observer = new MutationObserver(() => {
    document.querySelectorAll(IMAGE_SELECTOR).forEach(enhanceImage);
  });

  observer.observe(document.body, { childList: true, subtree: true });
})();
