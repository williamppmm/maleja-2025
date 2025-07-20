<?php
/**
 * Página de inicio (Home)
 * - Muestra hero
 * - Carga hasta 4 productos destacados
 */

require_once __DIR__ . '/config/db.php';

// Metadatos / contexto para el header
$currentPage      = 'home';
$pageTitle        = 'MALEJA Calzado | Sandalias y calzado femenino en Cali';
$metaDescription  = 'Sandalias y calzado femenino con estilo y comodidad.';

// --- Consulta productos destacados ---
$sqlDestacados = "
  SELECT p.id,
         p.nombre,
         p.referencia,
         p.slug,
         p.precio,
         p.descripcion_corta,
         COALESCE(i.filename, 'assets/images/productos/_placeholder.png') AS imagen
  FROM productos p
  LEFT JOIN producto_imagenes i
         ON i.producto_id = p.id AND i.principal = 1
  WHERE p.destacado = 1
    AND p.activo = 1
  ORDER BY p.orden_destacado IS NULL, p.orden_destacado, p.id
  LIMIT 4
";

try {
    $stmt = $pdo->query($sqlDestacados);
    $destacados = $stmt->fetchAll();
} catch (Throwable $e) {
    // Podrías registrar el error en un log:
    // error_log($e->getMessage());
    $destacados = [];
}

include __DIR__ . '/includes/header.php';
?>

<!-- HERO -->
<section class="hero" aria-label="Presentación de la marca">
  <picture class="hero__media">
    <img
      src="assets/images/banners/banner.png"
      alt="Sandalias elegantes MALEJA sobre fondo contrastado"
      class="hero__img"
      width="1600" height="900"
      decoding="async" fetchpriority="high">
  </picture>
  <div class="hero__overlay">
    <div class="hero__content container">
      <h1 class="hero__title">Sandalias y calzado femenino.</h1>
      <p class="hero__slogan">¡Tu flow, tu ritmo, tu estilo... pa' vos!</p>
      <div class="hero__actions">
        <a href="productos.php" class="btn btn--primary">Ver Productos</a>
        <a href="https://wa.me/573172703742"
           class="btn btn--whatsapp"
           target="_blank" rel="noopener">Pedir por WhatsApp</a>
      </div>
    </div>
  </div>
</section>

<!-- Productos destacados -->
<section class="section productos-preview" aria-labelledby="productos-destacados-title">
  <div class="container">
    <h2 id="productos-destacados-title" class="section__title">¡Lo más chévere! 😎</h2>
    <p class="section__subtitle">Las sandalias que están causando furor</p>

    <div class="grid-productos grid-productos--2">
      <?php if ($destacados): ?>
        <?php foreach ($destacados as $p): ?>
          <?php
            // Sanitización previa a impresión
            $pid    = (int)$p['id'];
            $nombre = htmlspecialchars($p['nombre']);
            $ref    = htmlspecialchars($p['referencia'] ?? '');
            $precio = (float)$p['precio'];
            $desc   = htmlspecialchars($p['descripcion_corta'] ?? '');
            $img    = htmlspecialchars($p['imagen']);
            $alt    = $desc ?: $nombre;
          ?>
          <article class="producto-card"
                   data-id="<?= $pid ?>"
                   data-nombre="<?= $nombre ?>"
                   data-referencia="<?= $ref ?>"
                   data-precio="<?= (int)$precio ?>"
                   data-descripcion="<?= $desc ?>"
                   data-img="<?= $img ?>">
            <picture class="producto-card__media">
              <img
                src="<?= $img ?>"
                alt="<?= $alt ?>"
                class="producto-card__img"
                loading="lazy"
                width="400" height="500">
            </picture>
            <div class="producto-card__body">
              <h3 class="producto-card__title"><?= $nombre ?></h3>
              <p class="producto-card__precio">$<?= number_format($precio, 0, ',', '.') ?></p>
              <button class="btn btn--outline btn-info"
                      type="button"
                      data-accion="info"
                      aria-label="Más información <?= $nombre ?>">
                Más información
              </button>
            </div>
          </article>
        <?php endforeach; ?>
      <?php else: ?>
        <p class="mensaje--vacio">No hay productos destacados todavía.</p>
      <?php endif; ?>
    </div>

    <div class="section__cta">
      <a href="productos.php" class="btn btn--secondary">Ver catálogo completo</a>
    </div>
  </div>
</section>

<!-- About corto -->
<section class="about-short section--alt" aria-labelledby="about-short-title">
  <div class="container about-short__inner">
    <div class="about-short__text">
      <h2 id="about-short-title" class="section__title">Diseño y comodidad con calidad</h2>
      <p>Cada par de MALEJA es seleccionado pensando en ti, para acompañar tu día, tu flow y tu estilo. Calidad, suavidad y la actitud que te representa.</p>
      <a href="nosotras.php" class="btn btn--secondary">Conoce nuestra historia</a>
    </div>
  </div>
</section>

<?php
include __DIR__ . '/includes/footer.php';