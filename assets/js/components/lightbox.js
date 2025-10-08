/**
 * LIGHTBOX - Visor de imágenes con zoom
 * Permite ver imágenes en pantalla completa con navegación
 */

class Lightbox {
  constructor() {
    this.currentIndex = 0;
    this.images = [];
    this.isOpen = false;
    this.init();
  }

  init() {
    this.createLightboxHTML();
    this.cacheDOMElements();
    this.bindEvents();
  }

  createLightboxHTML() {
    const lightboxHTML = `
      <div class="lightbox" id="lightbox" role="dialog" aria-modal="true" aria-label="Visor de imagen ampliada">
        <div class="lightbox__loader" aria-label="Cargando imagen"></div>

        <div class="lightbox__content">
          <button class="lightbox__close"
                  type="button"
                  aria-label="Cerrar visor de imagen"
                  data-lightbox-close>×</button>

          <button class="lightbox__nav lightbox__nav--prev"
                  type="button"
                  aria-label="Imagen anterior"
                  data-lightbox-prev>‹</button>

          <img src=""
               alt=""
               class="lightbox__img"
               id="lightbox-img"
               data-lightbox-img>

          <button class="lightbox__nav lightbox__nav--next"
                  type="button"
                  aria-label="Imagen siguiente"
                  data-lightbox-next>›</button>

          <div class="lightbox__counter"
               id="lightbox-counter"
               aria-live="polite"></div>
        </div>
      </div>
    `;

    document.body.insertAdjacentHTML('beforeend', lightboxHTML);
  }

  cacheDOMElements() {
    this.lightbox = document.getElementById('lightbox');
    this.img = document.getElementById('lightbox-img');
    this.counter = document.getElementById('lightbox-counter');
    this.closeBtn = document.querySelector('[data-lightbox-close]');
    this.prevBtn = document.querySelector('[data-lightbox-prev]');
    this.nextBtn = document.querySelector('[data-lightbox-next]');
  }

  bindEvents() {
    // Cerrar
    this.closeBtn?.addEventListener('click', () => this.close());
    this.lightbox?.addEventListener('click', (e) => {
      if (e.target === this.lightbox || e.target === this.img) {
        this.close();
      }
    });

    // Navegación
    this.prevBtn?.addEventListener('click', () => this.prev());
    this.nextBtn?.addEventListener('click', () => this.next());

    // Teclado
    document.addEventListener('keydown', (e) => {
      if (!this.isOpen) return;

      switch(e.key) {
        case 'Escape':
          this.close();
          break;
        case 'ArrowLeft':
          this.prev();
          break;
        case 'ArrowRight':
          this.next();
          break;
      }
    });

    // Touch swipe (móvil)
    this.addSwipeSupport();
  }

  open(imageSrc, imageAlt = '', allImages = []) {
    if (!imageSrc) return;

    // Si hay múltiples imágenes, usar galería
    if (allImages.length > 0) {
      this.images = allImages;
      this.currentIndex = allImages.findIndex(img => img.src === imageSrc);
      if (this.currentIndex === -1) this.currentIndex = 0;
    } else {
      // Solo una imagen
      this.images = [{ src: imageSrc, alt: imageAlt }];
      this.currentIndex = 0;
    }

    this.showImage();
    this.lightbox.classList.add('is-open');
    this.isOpen = true;
    document.body.style.overflow = 'hidden'; // Bloquear scroll

    // Focus trap
    this.closeBtn?.focus();
  }

  close() {
    this.lightbox.classList.remove('is-open');
    this.isOpen = false;
    document.body.style.overflow = ''; // Restaurar scroll
    this.images = [];
    this.currentIndex = 0;
  }

  showImage() {
    const currentImage = this.images[this.currentIndex];
    if (!currentImage) return;

    // Mostrar loader
    this.lightbox.classList.add('is-loading');

    // Cargar imagen
    const imgLoader = new Image();
    imgLoader.onload = () => {
      this.img.src = currentImage.src;
      this.img.alt = currentImage.alt || 'Imagen ampliada';
      this.lightbox.classList.remove('is-loading');
      this.updateCounter();
      this.updateNavigation();
    };

    imgLoader.onerror = () => {
      this.lightbox.classList.remove('is-loading');
      if (window.MALEJA_DEBUG === true) {
        console.error('Error cargando imagen:', currentImage.src);
      }
      this.close();
    };

    imgLoader.src = currentImage.src;
  }

  updateCounter() {
    if (this.images.length > 1) {
      this.counter.textContent = `${this.currentIndex + 1} / ${this.images.length}`;
      this.counter.style.display = 'block';
    } else {
      this.counter.style.display = 'none';
    }
  }

  updateNavigation() {
    // Mostrar/ocultar botones de navegación
    if (this.images.length <= 1) {
      if (this.prevBtn) this.prevBtn.classList.add('is-hidden');
      if (this.nextBtn) this.nextBtn.classList.add('is-hidden');
    } else {
      if (this.prevBtn) this.prevBtn.classList.remove('is-hidden');
      if (this.nextBtn) this.nextBtn.classList.remove('is-hidden');

      // Deshabilitar si estamos en los extremos (opcional)
      if (this.currentIndex === 0) {
        if (this.prevBtn) this.prevBtn.style.opacity = '0.5';
      } else {
        if (this.prevBtn) this.prevBtn.style.opacity = '1';
      }

      if (this.currentIndex === this.images.length - 1) {
        if (this.nextBtn) this.nextBtn.style.opacity = '0.5';
      } else {
        if (this.nextBtn) this.nextBtn.style.opacity = '1';
      }
    }
  }

  prev() {
    if (this.images.length <= 1) return;
    this.currentIndex = (this.currentIndex - 1 + this.images.length) % this.images.length;
    this.showImage();
  }

  next() {
    if (this.images.length <= 1) return;
    this.currentIndex = (this.currentIndex + 1) % this.images.length;
    this.showImage();
  }

  addSwipeSupport() {
    let touchStartX = 0;
    let touchEndX = 0;

    this.lightbox?.addEventListener('touchstart', (e) => {
      touchStartX = e.changedTouches[0].screenX;
    }, { passive: true });

    this.lightbox?.addEventListener('touchend', (e) => {
      touchEndX = e.changedTouches[0].screenX;
      this.handleSwipe();
    }, { passive: true });

    const handleSwipe = () => {
      const swipeThreshold = 50;
      const diff = touchStartX - touchEndX;

      if (Math.abs(diff) > swipeThreshold) {
        if (diff > 0) {
          this.next(); // Swipe izquierda
        } else {
          this.prev(); // Swipe derecha
        }
      }
    };

    this.handleSwipe = handleSwipe;
  }
}

// Inicializar Lightbox global
const lightbox = new Lightbox();

// Función helper para abrir desde cualquier lugar
window.openLightbox = (imageSrc, imageAlt = '', allImages = []) => {
  lightbox.open(imageSrc, imageAlt, allImages);
};

// Auto-inicializar en imágenes con atributo data-lightbox
document.addEventListener('DOMContentLoaded', () => {
  // Click en imagen individual
  document.querySelectorAll('[data-lightbox]').forEach(img => {
    // Solo procesar imágenes que NO sean de producto-card
    // (esas las maneja product-image-zoom.js)
    if (img.classList.contains('producto-card__img')) {
      return;
    }

    img.style.cursor = 'zoom-in';

    img.addEventListener('click', (e) => {
      e.preventDefault();
      const src = img.dataset.lightbox || img.src;
      const alt = img.alt || '';

      // Buscar si pertenece a una galería
      const gallery = img.dataset.lightboxGallery;
      if (gallery) {
        const galleryImages = [...document.querySelectorAll(`[data-lightbox-gallery="${gallery}"]`)]
          .map(el => ({
            src: el.dataset.lightbox || el.src,
            alt: el.alt || ''
          }));
        lightbox.open(src, alt, galleryImages);
      } else {
        lightbox.open(src, alt);
      }
    });
  });

  // Click en enlaces con clase .lightbox-trigger
  document.querySelectorAll('.lightbox-trigger').forEach(link => {
    link.addEventListener('click', (e) => {
      e.preventDefault();
      const src = link.href || link.dataset.src;
      const alt = link.dataset.alt || link.textContent || '';
      lightbox.open(src, alt);
    });
  });
});

// Export para módulos ES6 (opcional)
if (typeof module !== 'undefined' && module.exports) {
  module.exports = Lightbox;
}
