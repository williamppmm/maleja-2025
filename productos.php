<?php
require_once __DIR__ . '/config/db.php';

/**
 * Configuración de la página
 */
$currentPage     = 'productos';
$pageTitle       = 'Productos | MALEJA Calzado';
$metaDescription = 'Catálogo de sandalias y calzado femenino: estilos, comodidad y actitud.';
$canonicalUrl    = 'https://calzadomaleja.co/productos.php';

/**
 * Parámetros de filtrado y paginación
 */
$porPagina = 20;
$pagina    = (isset($_GET['p']) && ctype_digit($_GET['p'])) ? max(1, (int)$_GET['p']) : 1;
$offset    = ($pagina - 1) * $porPagina;

$busqueda  = isset($_GET['q'])   ? trim($_GET['q']) : '';
$categoria = (isset($_GET['cat']) && ctype_digit($_GET['cat'])) ? (int)$_GET['cat'] : 0;
$ordenar   = isset($_GET['orden']) ? $_GET['orden'] : 'recientes';

/**
 * Construir WHERE y parámetros
 */
$whereClause = 'p.activo = 1';
$params      = [];

if ($busqueda !== '') {
    $whereClause .= ' AND (p.nombre LIKE ? OR p.referencia LIKE ? OR p.descripcion_larga LIKE ?)';
    $like = '%' . $busqueda . '%';
    $params[] = $like;
    $params[] = $like;
    $params[] = $like;
}

if ($categoria > 0) {
    $whereClause .= ' AND EXISTS (
        SELECT 1 FROM producto_categoria pc
        WHERE pc.producto_id = p.id AND pc.categoria_id = ?
    )';
    $params[] = $categoria;
}

/**
 * Orden
 */
$orderBy = match ($ordenar) {
    'precio_asc'  => 'p.precio ASC',
    'precio_desc' => 'p.precio DESC',
    'nombre'      => 'p.nombre ASC',
    'destacados'  => 'p.destacado DESC, p.orden_destacado IS NULL, p.orden_destacado ASC',
    default       => 'p.id DESC'
};

/**
 * SQL principal y total
 */
$sqlProductos = "
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
    WHERE $whereClause
    ORDER BY $orderBy
    LIMIT ? OFFSET ?
";

$sqlTotal = "SELECT COUNT(*) FROM productos p WHERE $whereClause";

/**
 * Ejecutar consultas
 */
$productos  = [];
$categorias = [];
$total      = 0;

try {
    // Total
    $stmtTotal = $pdo->prepare($sqlTotal);
    $stmtTotal->execute($params);
    $total = (int) $stmtTotal->fetchColumn();

    // Productos
    $paramsProductos = array_merge($params, [$porPagina, $offset]);
    $stmt = $pdo->prepare($sqlProductos);
    $stmt->execute($paramsProductos);
    $productos = $stmt->fetchAll();

    // Categorías (si existe la tabla)
    try {
        $categorias = $pdo->query("SELECT id, nombre FROM categorias ORDER BY nombre")->fetchAll();
    } catch (Throwable $ignorar) {
        $categorias = [];
    }
} catch (Throwable $e) {
    error_log('Error productos.php: ' . $e->getMessage());
}

/**
 * Paginación
 */
$totalPaginas = $total > 0 ? (int) ceil($total / $porPagina) : 1;

/**
 * Helper para generar enlaces de filtro/paginación
 */
function generarUrlFiltro(array $nuevosParams = []): string {
    global $busqueda, $categoria, $ordenar;
    $base = [
        'q'     => $busqueda,
        'cat'   => $categoria,
        'orden' => $ordenar
    ];
    $params = array_merge($base, $nuevosParams);

    // Eliminar valores vacíos / neutros
    $params = array_filter($params, fn($v) => !($v === '' || $v === 0 || $v === null));

    return 'productos.php' . (empty($params) ? '' : '?' . http_build_query($params));
}

include __DIR__ . '/includes/header.php';
?>

<section class="section productos-listado" aria-labelledby="titulo-productos">
  <div class="container">
    <div class="productos-header">
      <div class="productos-header__text">
        <h1 id="titulo-productos" class="section__title">Catálogo</h1>
        <p class="section__subtitle">Encuentra el estilo que va con tu flow.</p>
        <p class="productos-contador">
          <?= $total ?>
          <?= $total === 1 ? 'producto encontrado' : 'productos encontrados' ?>
          <?= $busqueda !== '' ? ' para "' . htmlspecialchars($busqueda) . '"' : '' ?>
        </p>
      </div>
    </div>

    <!-- Filtros -->
    <div class="filtros-bar">
      <form class="filtros-form" method="GET" action="productos.php">
        <div class="filtro-grupo">
          <input
            type="search"
            name="q"
            value="<?= htmlspecialchars($busqueda) ?>"
            placeholder="Buscar productos, referencia..."
            class="filtro-input"
            autocomplete="off">
        </div>

        <?php if (!empty($categorias)): ?>
          <div class="filtro-grupo">
            <select name="cat" class="filtro-select">
              <option value="">Todas las categorías</option>
              <?php foreach ($categorias as $cat): ?>
                <option value="<?= $cat['id'] ?>"
                  <?= $categoria === (int)$cat['id'] ? 'selected' : '' ?>>
                  <?= htmlspecialchars($cat['nombre']) ?>
                </option>
              <?php endforeach; ?>
            </select>
          </div>
        <?php endif; ?>

        <div class="filtro-grupo">
          <select name="orden" class="filtro-select">
            <option value="recientes"   <?= $ordenar === 'recientes'   ? 'selected' : '' ?>>Más recientes</option>
            <option value="destacados"  <?= $ordenar === 'destacados'  ? 'selected' : '' ?>>Destacados primero</option>
            <option value="precio_asc"  <?= $ordenar === 'precio_asc'  ? 'selected' : '' ?>>Precio: menor a mayor</option>
            <option value="precio_desc" <?= $ordenar === 'precio_desc' ? 'selected' : '' ?>>Precio: mayor a menor</option>
            <option value="nombre"      <?= $ordenar === 'nombre'      ? 'selected' : '' ?>>Nombre A-Z</option>
          </select>
        </div>

        <button type="submit" class="btn btn--outline btn-filtrar">Filtrar</button>

        <?php if ($busqueda !== '' || $categoria > 0 || $ordenar !== 'recientes'): ?>
          <a href="productos.php" class="btn btn--secondary">Limpiar</a>
        <?php endif; ?>
      </form>
    </div>

    <!-- Resultados -->
    <?php if (empty($productos)): ?>
      <div class="mensaje-vacio">
        <h3>No se encontraron productos</h3>
        <p>
          <?php if ($busqueda !== ''): ?>
            Intenta con otros términos o <a href="productos.php">ver todos</a>.
          <?php else: ?>
            Aún no hay productos disponibles.
          <?php endif; ?>
        </p>
      </div>
    <?php else: ?>
      <div class="grid-productos grid-productos--catalogo">
        <?php foreach ($productos as $p): ?>
          <article
            class="producto-card <?= $p['destacado'] ? 'producto-card--destacado' : '' ?>"
            data-id="<?= htmlspecialchars($p['id']) ?>"
            data-nombre="<?= htmlspecialchars($p['nombre']) ?>"
            data-referencia="<?= htmlspecialchars($p['referencia']) ?>"
            data-precio="<?= (int)$p['precio'] ?>"
            data-descripcion="<?= htmlspecialchars($p['descripcion_corta'] ?? '') ?>"
            data-descripcion-larga="<?= htmlspecialchars($p['descripcion_larga'] ?? '') ?>"
            data-img="<?= htmlspecialchars($p['imagen']) ?>">

            <?php if ($p['destacado']): ?>
              <div class="producto-badge">Destacado</div>
            <?php endif; ?>

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
    <?php endif; ?>

    <!-- Paginación -->
    <?php if ($totalPaginas > 1): ?>
      <nav class="paginacion" aria-label="Paginación de productos">
        <ul class="paginacion__list">
          <?php if ($pagina > 1): ?>
            <li>
              <a class="paginacion__link paginacion__link--prev"
                 href="<?= generarUrlFiltro(['p' => $pagina - 1]) ?>">← Anterior</a>
            </li>
          <?php endif; ?>

          <?php
          $inicio = max(1, $pagina - 2);
          $fin    = min($totalPaginas, $pagina + 2);

          if ($inicio > 1) {
              echo '<li><a class="paginacion__link" href="' . generarUrlFiltro(['p' => 1]) . '">1</a></li>';
              if ($inicio > 2) {
                  echo '<li><span class="paginacion__dots">...</span></li>';
              }
          }

          for ($i = $inicio; $i <= $fin; $i++) {
              $active = $i === $pagina ? ' is-active' : '';
              echo '<li><a class="paginacion__link' . $active . '" href="' . generarUrlFiltro(['p' => $i]) . '">' . $i . '</a></li>';
          }

          if ($fin < $totalPaginas) {
              if ($fin < $totalPaginas - 1) {
                  echo '<li><span class="paginacion__dots">...</span></li>';
              }
              echo '<li><a class="paginacion__link" href="' . generarUrlFiltro(['p' => $totalPaginas]) . '">' . $totalPaginas . '</a></li>';
          }

          if ($pagina < $totalPaginas) {
              echo '<li><a class="paginacion__link paginacion__link--next" href="' . generarUrlFiltro(['p' => $pagina + 1]) . '">Siguiente →</a></li>';
          }
          ?>
        </ul>
        <p class="paginacion__info">
          Página <?= $pagina ?> de <?= $totalPaginas ?> (<?= number_format($total) ?> productos)
        </p>
      </nav>
    <?php endif; ?>
  </div>
</section>

<?php
include __DIR__ . '/includes/footer.php';
?>
