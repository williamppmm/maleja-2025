<?php
/**
 * Página de inicio - MALEJA Calzado
 */

require_once __DIR__ . '/config/db.php';

/**
 * Configuración de la página
 */
$currentPage     = 'home';
$pageTitle       = 'MALEJA Calzado | Calzado femenino en Cali';
$metaDescription = 'Sandalias y calzado femenino con estilo y comodidad.';
$canonicalUrl    = 'https://calzadomaleja.co/';

/**
 * Consulta de productos destacados con imágenes desde la base de datos
 */
$sqlDestacados = "
    SELECT p.id,
           p.nombre,
           p.referencia,
           p.slug,
           p.precio,
           p.descripcion_corta,
           p.descripcion_larga,
           p.destacado,
           COALESCE(CONCAT('assets/images/productos/', i.filename), 'assets/images/productos/_placeholder.png') AS imagen
    FROM productos p
    LEFT JOIN producto_imagenes i
           ON i.producto_id = p.id AND i.principal = 1
    WHERE p.destacado = 1 AND p.activo = 1
    ORDER BY p.orden_destacado IS NULL, p.orden_destacado ASC, p.id DESC
    LIMIT 4
";

/**
 * Ejecutar consulta de productos destacados
 */
$destacados = [];

try {
    $stmt = $pdo->query($sqlDestacados);
    $destacados = $stmt->fetchAll();
} catch (Throwable $e) {
    error_log('Error al cargar productos destacados: ' . $e->getMessage());
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
      <h1 class="hero__title">Calzado femenino.</h1>
      <p class="hero__slogan">¡Tu flow, tu ritmo, tu estilo... pa' vos!</p>
      <div class="hero__actions">
        <a href="productos.php" class="btn btn--primary">Ver Productos</a>
        <a href="https://wa.me/573135152530?text=Hola%20MALEJA%2C%20me%20gustaría%20saber%20más%20sobre%20sus%20productos."
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

    <?php if (empty($destacados)): ?>
      <div class="mensaje-vacio">
        <h3>Próximamente</h3>
        <p>Estamos preparando nuestros productos destacados para ti.</p>
        <a href="productos.php" class="btn btn--secondary">Ver todos los productos</a>
      </div>
    <?php else: ?>
      <div class="grid-productos grid-productos--2">
        <?php foreach ($destacados as $p): ?>
          <article
            class="producto-card producto-card--destacado"
            data-id="<?= htmlspecialchars($p['id']) ?>"
            data-nombre="<?= htmlspecialchars($p['nombre']) ?>"
            data-referencia="<?= htmlspecialchars($p['referencia']) ?>"
            data-precio="<?= (int)$p['precio'] ?>"
            data-descripcion="<?= htmlspecialchars($p['descripcion_corta'] ?? '') ?>"
            data-descripcion-larga="<?= htmlspecialchars($p['descripcion_larga'] ?? '') ?>"
            data-img="<?= htmlspecialchars($p['imagen']) ?>">

            <div class="producto-badge">Destacado</div>

            <picture class="producto-card__media">
              <img
                src="<?= htmlspecialchars($p['imagen']) ?>"
                alt="<?= htmlspecialchars($p['descripcion_corta'] ?: $p['nombre']) ?>"
                class="producto-card__img"
                loading="lazy"
                width="400"
                height="350">
            </picture>

            <div class="producto-card__body">
              <h3 class="producto-card__title"><?= htmlspecialchars($p['nombre']) ?></h3>
              <?php if (!empty($p['referencia'])): ?>
                <p class="producto-card__ref">Ref: <?= htmlspecialchars($p['referencia']) ?></p>
              <?php endif; ?>
              <p class="producto-card__precio">
                $<?= number_format($p['precio'], 0, ',', '.') ?>
              </p>
              <button
                class="btn btn--outline btn-info"
                type="button"
                data-accion="info"
                aria-label="Más información <?= htmlspecialchars($p['nombre']) ?>">
                Ver detalle
              </button>
            </div>
          </article>
        <?php endforeach; ?>
      </div>

      <div class="section__cta">
        <a href="productos.php" class="btn btn--secondary">Ver catálogo completo</a>
      </div>
    <?php endif; ?>
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

<?php include __DIR__ . '/includes/footer.php'; ?>