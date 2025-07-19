<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>MALEJA Calzado | Sandalias y calzado femenino en Cali</title>
  <meta name="description" content="Sandalias y calzado femenino con estilo y comodidad.">
  <meta name="author" content="MALEJA Calzado">
  <meta name="robots" content="index,follow">

  <!-- Metadatos para redes sociales -->
  <meta property="og:title" content="MALEJA Calzado">
  <meta property="og:description" content="Sandalias y calzado femenino con estilo. ¡Calzado que enamora... como vos!">
  <meta property="og:image" content="https://calzadomaleja.co/assets/banners/banner.png">
  <meta property="og:url" content="https://calzadomaleja.co/">
  <meta property="og:type" content="website">
  <!-- Twitter -->
  <meta name="twitter:card" content="summary_large_image">

  <!-- Icono de la pestaña -->
  <link rel="icon" href="assets/images/logos/logo-basic.png" type="image/png">
  <!-- Hoja de estilos principal -->
  <link rel="stylesheet" href="assets/css/style.css">
</head>

<script>
(function() {
  const modal = document.getElementById('modal-producto');
  if (!modal) return;
  const dialog = modal.querySelector('.modal__dialog');
  const imgEl = document.getElementById('modal-imagen');
  const tituloEl = document.getElementById('modal-titulo');
  const refEl = document.getElementById('modal-ref');
  const precioEl = document.getElementById('modal-precio');
  const descEl = document.getElementById('modal-descripcion');
  const btnWhats = document.getElementById('modal-btn-whatsapp');
  const btnMail  = document.getElementById('modal-btn-mail');
  let lastFocus = null;

  const formatCOP = n =>
    '$' + Number(n).toLocaleString('es-CO', { maximumFractionDigits: 0 });

  function abrir(card){
    lastFocus = document.activeElement;
    const nombre = card.dataset.nombre || 'Producto';
    const ref = card.dataset.referencia || '';
    const precio = card.dataset.precio || '';
    const descr = card.dataset.descripcion || '';
    const img = card.dataset.img || '';

    tituloEl.textContent = nombre;
    refEl.textContent = ref ? 'Ref: ' + ref : '';
    precioEl.textContent = precio ? formatCOP(precio) : '';
    descEl.textContent = descr;
    imgEl.src = img;
    imgEl.alt = nombre;

    const msg = encodeURIComponent(`Hola Maleja, me interesa el producto: ${nombre}${ref ? ' (ref '+ref+')' : ''}`);
    btnWhats.href = `https://wa.me/573172703742?text=${msg}`;

    const subject = encodeURIComponent(`Consulta: ${nombre}${ref ? ' - Ref '+ref : ''}`);
    const body = encodeURIComponent('Hola, me interesa este modelo. ¿Disponibilidad de tallas?');
    btnMail.href = `mailto:ventas@malejacalzado.co?subject=${subject}&body=${body}`;

    modal.classList.add('is-open');
    modal.setAttribute('aria-hidden','false');
    setTimeout(()=> {
      const focusBtn = modal.querySelector('.modal__close');
      if (focusBtn) focusBtn.focus();
    },40);
    document.addEventListener('keydown', escClose);
  }

  function cerrar(){
    modal.classList.remove('is-open');
    modal.setAttribute('aria-hidden','true');
    document.removeEventListener('keydown', escClose);
    if (lastFocus) lastFocus.focus();
  }

  function escClose(e){ if (e.key === 'Escape') cerrar(); }

  document.addEventListener('click', e=>{
    const trigger = e.target.closest('[data-accion="info"]');
    if (trigger) {
      const card = trigger.closest('.producto-card');
      if (card) abrir(card);
    }
    if (e.target.hasAttribute('data-modal-cerrar') || e.target === modal.querySelector('.modal__backdrop')) {
      cerrar();
    }
  });
})();
</script>

<body>
  <!-- Acceso rápido para lectores de pantalla -->
  <a class="skip-link" href="#contenido-principal">Saltar al contenido</a>

  <!-- Encabezado principal -->
  <header class="header" role="banner">
    <div class="container header__inner">
      <!-- Logo de la marca -->
      <a href="index.html" class="header__brand" aria-label="Inicio MALEJA Calzado">
        <img src="assets/images/logos/logo.png" alt="MALEJA Calzado - Logo principal" class="logo" width="150" height="auto">
      </a>
      <!-- Menú de navegación principal -->
      <nav class="nav" aria-label="Navegación principal">
        <ul class="nav__list">
          <li><a href="index.html" class="nav__link nav__link--active">Inicio</a></li>
          <li><a href="nosotras.html" class="nav__link">Nosotras</a></li>
          <li><a href="productos.html" class="nav__link">Productos</a></li>
          <li><a href="contacto.html" class="nav__link">Contacto</a></li>
        </ul>
      </nav>
      <!-- Redes sociales en el header -->
      <div class="social social--header" aria-label="Redes sociales">
        <a href="https://www.instagram.com/malejacalzado/" target="_blank" rel="noopener" aria-label="Instagram MALEJA Calzado">
          <img src="assets/icons/instagram.png" alt="" width="24" height="24">
        </a>
        <a href="https://wa.me/573172703742" target="_blank" rel="noopener" aria-label="WhatsApp MALEJA Calzado">
          <img src="assets/icons/whatsapp.png" alt="" width="24" height="24">
        </a>
      </div>
    </div>
  </header>

  <!-- Contenido principal -->
  <main id="contenido-principal">
    <!-- Sección HERO: Presentación de la marca -->
    <section class="hero" aria-label="Presentación de la marca">
      <picture class="hero__media">
        <!-- Imagen principal del banner -->
        <img src="assets/images/banners/banner.png" alt="Sandalias elegantes MALEJA sobre fondo contrastado" class="hero__img" width="1600" height="900">
      </picture>
      <div class="hero__overlay">
        <div class="hero__content container">
            <h1 class="hero__title">Sandalias y calzado femenino.</h1>
            <p class="hero__slogan">¡Tu flow, tu ritmo, tu estilo... para vos!</p>
          <div class="hero__actions">
            <a href="productos.html" class="btn btn--primary">Ver Productos</a>
            <a href="https://wa.me/573172703742" class="btn btn--whatsapp" target="_blank" rel="noopener">Pedir por WhatsApp</a>
          </div>
        </div>
      </div>
    </section>

    <!-- Sección de productos destacados -->
    <section class="section productos-preview" aria-labelledby="productos-destacados-title">
      <div class="container">
        <h2 id="productos-destacados-title" class="section__title">¡Lo más chévere! 😎</h2>
        <p class="section__subtitle">Las sandalias que están causando furor</p>
      <?php
      require_once __DIR__ . '/config/db.php';

      $sql = "
        SELECT p.id, p.nombre, p.referencia, p.slug, p.precio, p.descripcion_corta,
              COALESCE(i.filename, 'assets/images/placeholders/placeholder-producto.png') AS imagen
        FROM productos p
        LEFT JOIN producto_imagenes i
          ON i.producto_id = p.id AND i.principal = 1
        WHERE p.destacado = 1 AND p.activo = 1
        ORDER BY p.orden_destacado, p.id
        LIMIT 4
      ";
      $destacados = [];
      try {
        $destacados = $pdo->query($sql)->fetchAll();
      } catch (Throwable $e) {
        // error_log($e->getMessage());
      }
      ?>
      <div class="grid-productos grid-productos--2">
        <!-- Tarjeta de producto 1 -->
        <article class="producto-card" data-id="prod-8330" data-categoria="sandalia-plana">
        <picture class="producto-card__media">
          <img src="assets/images/productos/_JAP8330.JPG"
          alt="Sandalia plana beige con flores bordadas"
          class="producto-card__img"
          loading="lazy" width="400" height="500">
        </picture>
        <div class="producto-card__body">
          <h3 class="producto-card__title">Sandalia plana Bucaramanga</h3>
          <p class="producto-card__precio" data-precio="75990">$75.990</p>
          <button class="btn btn--outline btn-info" type="button" data-accion="info" aria-label="Más información Sandalia plana Bucaramanga">
          Más información
          </button>
        </div>
        </article>
        <!-- Tarjeta de producto 2 -->
        <article class="producto-card" data-id="prod-8331" data-categoria="tacon">
        <picture class="producto-card__media">
          <img src="assets/images/productos/_JAP8331.JPG"
          alt="Tacón elegante negro brillante"
          class="producto-card__img"
          loading="lazy" width="400" height="500">
        </picture>
        <div class="producto-card__body">
          <h3 class="producto-card__title">Tacón elegante Bucaramanga</h3>
          <p class="producto-card__precio" data-precio="119990">$119.990</p>
          <button class="btn btn--outline btn-info" type="button" data-accion="info" aria-label="Más información Tacón elegante Bucaramanga">
          Más información
          </button>
        </div>
        </article>
        <!-- Tarjeta de producto 3 -->
        <article class="producto-card" data-id="prod-8337" data-categoria="canoa">
        <picture class="producto-card__media">
          <img src="assets/images/productos/_JAP8337.JPG"
          alt="Canoa elegante con diseño limpio"
          class="producto-card__img"
          loading="lazy" width="400" height="500">
        </picture>
        <div class="producto-card__body">
          <h3 class="producto-card__title">Canoa elegante Bucaramanga</h3>
          <p class="producto-card__precio" data-precio="89990">$89.990</p>
          <button class="btn btn--outline btn-info" type="button" data-accion="info" aria-label="Más información Canoa elegante Bucaramanga">
          Más información
          </button>
        </div>
        </article>
        <!-- Tarjeta de producto 4 -->
        <article class="producto-card" data-id="prod-8341" data-categoria="tacon">
        <picture class="producto-card__media">
          <img src="assets/images/productos/_JAP8341.JPG"
          alt="Tacón plataforma textura tipo yute"
          class="producto-card__img"
          loading="lazy" width="400" height="500">
        </picture>
        <div class="producto-card__body">
          <h3 class="producto-card__title">Tacón Yute Bucaramanga</h3>
          <p class="producto-card__precio" data-precio="99990">$99.990</p>
          <button class="btn btn--outline btn-info" type="button" data-accion="info" aria-label="Más información Tacón Yute Bucaramanga">
          Más información
          </button>
        </div>
        </article>
      </div>
      <!-- Botón para ver el catálogo completo -->
      <div class="section__cta">
        <a href="productos.html" class="btn btn--secondary">Ver catálogo completo</a>
      </div>
      </div>
    </section>

    <!-- Sección breve sobre la marca -->
    <section class="about-short section--alt" aria-labelledby="about-short-title">
      <div class="container about-short__inner">
        <div class="about-short__text">
          <h2 id="about-short-title" class="section__title">Diseño y comodidad con calidad</h2>
          <p>Cada par de MALEJA es seleccionado pensando en ti, para acompañar tu día, tu flow y tu estilo. Calidad, suavidad y la actitud que te representa.</p>
          <a href="nosotras.html" class="btn btn--secondary">Conoce nuestra historia</a>
        </div>
      </div>
    </section>
  </main>

  <!-- Pie de página -->
  <footer class="footer" role="contentinfo">
    <div class="container footer__grid">
      <!-- Columna de marca en el footer -->
      <div class="footer__col footer__brand">
        <img src="assets/images/logos/logo-white.png" alt="MALEJA Calzado - logo claro" width="140" height="auto" loading="lazy">
        <p class="footer__tagline">Estilo que camina contigo desde 2025.</p>
      </div>
      <!-- Columna de contacto en el footer -->
      <div class="footer__col footer__contacto">
        <h3 class="footer__heading">Contacto</h3>
        <ul class="footer__list">
          <li><a href="mailto:ventas@malejacalzado.co">ventas@malejacalzado.co</a></li>
          <li><a href="https://wa.me/573172703742" target="_blank" rel="noopener">+57 317 270 3742</a></li>
        </ul>
      </div>
      <!-- Columna de redes sociales en el footer -->
      <div class="footer__col footer__social">
        <h3 class="footer__heading">Síguenos</h3>
        <div class="social social--footer">
          <a href="https://www.instagram.com/malejacalzado/" target="_blank" rel="noopener" aria-label="Instagram">
            <img src="assets/icons/instagram.png" alt="" width="26" height="26" loading="lazy">
          </a>
          <a href="#" aria-label="Facebook (próximamente)">
            <img src="assets/icons/facebook.png" alt="" width="26" height="26" loading="lazy">
          </a>
        </div>
      </div>
      <!-- Derechos de autor -->
      <div class="copyright">
        <p>© 2025 MALEJA Calzado · Todos los derechos reservados; Hecho con ❤️ en Cali</p>
      </div>
    </div>
  </footer>
  <!-- MODAL PRODUCTO -->
  <div class="modal" id="modal-producto" aria-hidden="true" aria-labelledby="modal-titulo" role="dialog">
    <div class="modal__backdrop" data-modal-cerrar></div>
    <div class="modal__dialog" role="document">
      <button class="modal__close" type="button" aria-label="Cerrar" data-modal-cerrar>&times;</button>
      <div class="modal__content">
        <div class="modal__img-wrap">
          <img src="" alt="" id="modal-imagen">
        </div>
        <div class="modal__info">
          <h3 id="modal-titulo" class="modal__title"></h3>
          <p class="modal__ref" id="modal-ref"></p>
          <p class="modal__precio" id="modal-precio"></p>
          <p class="modal__descripcion" id="modal-descripcion"></p>
          <div class="modal__actions">
            <a id="modal-btn-whatsapp" class="btn btn--whatsapp" target="_blank" rel="noopener" href="#">
              Pedir por WhatsApp
            </a>
            <a id="modal-btn-mail" class="btn btn--secondary" href="#">
              Escribir por correo
            </a>
            <a id="modal-btn-ver" class="btn btn--outline" href="productos.html">
              Ver más modelos
            </a>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Script para interacción futura con modales de productos -->
  <script src="assets/js/script.js" defer></script>
</body>
</html>
