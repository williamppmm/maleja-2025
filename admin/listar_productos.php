<!-- admin/listar_productos.php -->

<?php
session_start();

// === Config básica (ajusta display_errors en prod) ===
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if (!isset($_SESSION['admin']) || $_SESSION['admin'] !== true) {
    header('Location: login.php?error=session');
    exit();
}

// CSRF simple para acciones POST
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
$csrf = $_SESSION['csrf_token'];

require_once '../config/db.php';

// --------- Helpers ---------
function e($str){ return htmlspecialchars($str, ENT_QUOTES, 'UTF-8'); }

// Parámetros de filtro/orden/paginación
$q          = isset($_GET['q']) ? trim($_GET['q']) : '';
$estado     = isset($_GET['estado']) ? $_GET['estado'] : 'todos'; // todos|activos|inactivos
$destacado  = isset($_GET['destacado']) ? $_GET['destacado'] : 'todos'; // todos|1|0
$sort       = isset($_GET['sort']) ? $_GET['sort'] : 'created_at'; // nombre|precio|created_at
$dir        = isset($_GET['dir']) && strtolower($_GET['dir']) === 'asc' ? 'ASC' : 'DESC';
$page       = max(1, (int)($_GET['page'] ?? 1));
$perPage    = 15;
$offset     = ($page - 1) * $perPage;

$allowedSort = ['nombre','precio','created_at'];
if (!in_array($sort, $allowedSort)) $sort = 'created_at';

// --------- Acciones (toggle activo / eliminar) ---------
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['accion']) && isset($_POST['id'])) {
    if (empty($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        $_SESSION['flash_error'] = 'Token inválido.';
        header('Location: listar_productos.php');
        exit();
    }

    $id = (int)$_POST['id'];
    $accion = $_POST['accion'];

    try {
        if ($accion === 'toggle_activo') {
            $pdo->prepare("UPDATE productos SET activo = IF(activo=1,0,1) WHERE id = ?")
                ->execute([$id]);
            $_SESSION['flash_success'] = 'Estado actualizado.';
        } elseif ($accion === 'eliminar_definitivo') {
            // Borrado duro
            $pdo->beginTransaction();
            $pdo->prepare("DELETE FROM producto_imagenes WHERE producto_id = ?")->execute([$id]);
            $pdo->prepare("DELETE FROM producto_categoria WHERE producto_id = ?")->execute([$id]);
            $pdo->prepare("DELETE FROM productos WHERE id = ? LIMIT 1")->execute([$id]);
            $pdo->commit();
            $_SESSION['flash_success'] = 'Producto eliminado.';
        }
    } catch (Exception $e) {
        if ($pdo->inTransaction()) $pdo->rollBack();
        error_log('Error acción productos: '.$e->getMessage());
        $_SESSION['flash_error'] = 'No se pudo completar la acción.';
    }

    header('Location: listar_productos.php');
    exit();
}

// --------- Construir WHERE dinámico ---------
$where = [];
$params = [];

if ($q !== '') {
    $where[] = '(p.nombre LIKE ? OR p.referencia LIKE ? OR p.slug LIKE ?)';
    $like = '%'.$q.'%';
    $params[] = $like; $params[] = $like; $params[] = $like;
}
if ($estado === 'activos') {
    $where[] = 'p.activo = 1';
} elseif ($estado === 'inactivos') {
    $where[] = 'p.activo = 0';
}
if ($destacado === '1') {
    $where[] = 'p.destacado = 1';
} elseif ($destacado === '0') {
    $where[] = 'p.destacado = 0';
}

$whereSql = $where ? 'WHERE '.implode(' AND ', $where) : '';

// Conteo total
$sqlCount = "SELECT COUNT(*) FROM productos p $whereSql";
$stmt = $pdo->prepare($sqlCount);
$stmt->execute($params);
$total = (int)$stmt->fetchColumn();

// Datos
$sql = "SELECT p.*, 
        (SELECT filename FROM producto_imagenes pi WHERE pi.producto_id = p.id AND pi.principal = 1 ORDER BY pi.orden ASC LIMIT 1) AS imagen
        FROM productos p
        $whereSql
        ORDER BY $sort $dir
        LIMIT $perPage OFFSET $offset";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$productos = $stmt->fetchAll(PDO::FETCH_ASSOC);

$totalPages = (int)ceil($total / $perPage);

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Productos - MALEJA</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../assets/css/pages/registros.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

</head>
<body>
<div class="container">
    <header>
        <h1><i class="fas fa-list"></i> Productos</h1>
        <div class="header-actions">
            <a href="listar_categorias.php" class="btn secondary"><i class="fas fa-tags"></i> Gestionar Categorías</a>
            <a href="formulario_producto.php" class="btn primary"><i class="fas fa-plus"></i> Nuevo producto</a>
            <a href="#" class="logout-btn" onclick="logoutTo('login');return false;"><i class="fas fa-sign-out-alt"></i> Cerrar sesión</a>
        </div>
    </header>

    <?php if (!empty($_SESSION['flash_success'])): ?>
        <div class="flash success"><?= e($_SESSION['flash_success']); unset($_SESSION['flash_success']); ?></div>
    <?php endif; ?>
    <?php if (!empty($_SESSION['flash_error'])): ?>
        <div class="flash error"><?= e($_SESSION['flash_error']); unset($_SESSION['flash_error']); ?></div>
    <?php endif; ?>

    <form class="filters" method="get" action="listar_productos.php">
        <div>
            <label>Buscar</label><br>
            <input type="text" name="q" value="<?= e($q) ?>" placeholder="Nombre, referencia, slug">
        </div>
        <div>
            <label>Estado</label><br>
            <select name="estado">
                <option value="todos"     <?= $estado==='todos'?'selected':''; ?>>Todos</option>
                <option value="activos"   <?= $estado==='activos'?'selected':''; ?>>Activos</option>
                <option value="inactivos" <?= $estado==='inactivos'?'selected':''; ?>>Inactivos</option>
            </select>
        </div>
        <div>
            <label>Destacado</label><br>
            <select name="destacado">
                <option value="todos" <?= $destacado==='todos'?'selected':''; ?>>Todos</option>
                <option value="1"     <?= $destacado==='1'?'selected':''; ?>>Solo destacados</option>
                <option value="0"     <?= $destacado==='0'?'selected':''; ?>>No destacados</option>
            </select>
        </div>
        <div>
            <label>Ordenar por</label><br>
            <select name="sort">
                <option value="created_at" <?= $sort==='created_at'?'selected':''; ?>>Fecha</option>
                <option value="nombre"     <?= $sort==='nombre'?'selected':''; ?>>Nombre</option>
                <option value="precio"     <?= $sort==='precio'?'selected':''; ?>>Precio</option>
            </select>
            <select name="dir">
                <option value="desc" <?= $dir==='DESC'?'selected':''; ?>>Desc</option>
                <option value="asc"  <?= $dir==='ASC'?'selected':''; ?>>Asc</option>
            </select>
        </div>
        <div style="align-self:flex-end;">
            <button class="btn primary" type="submit">
                <i class="fas fa-search"></i> Filtrar
            </button>
            <a href="listar_productos.php" class="btn secondary">
                <i class="fas fa-eraser"></i> Limpiar
            </a>
        </div>
    </form>

    <table class="table">
        <thead>
            <tr>
                <th>Img</th>
                <th>Nombre</th>
                <th>Referencia</th>
                <th>Precio</th>
                <th>Estado</th>
                <th>Destacado</th>
                <th>Creado</th>
                <th style="width:160px;">Acciones</th>
            </tr>
        </thead>
        <tbody>
        <?php if (!$productos): ?>
            <tr>
                <td colspan="8" style="text-align:center; padding:1rem;">
                    No hay resultados para tu búsqueda.
                    <a href="listar_productos.php" class="btn secondary small">
                        <i class="fas fa-eraser"></i> Limpiar filtros
                    </a>
                </td>
            </tr>
        <?php else: foreach ($productos as $p): ?>
            <tr>
                <td>
                    <?php if ($p['imagen']): ?>
                        <img src="../assets/images/productos/<?= e($p['imagen']) ?>" alt="thumb" class="thumb">
                    <?php else: ?>
                        <span style="font-size:.8rem;color:#999">Sin imagen</span>
                    <?php endif; ?>
                </td>
                <td><?= e($p['nombre']) ?></td>
                <td><?= e($p['referencia']) ?></td>
                <td>$ <?= number_format($p['precio'], 0, ',', '.') ?></td>
                <td>
                    <span class="estado-badge <?= $p['activo']? 'estado-activo':'estado-inactivo' ?>">
                        <?= $p['activo']? 'Activo':'Inactivo' ?>
                    </span>
                </td>
                <td>
                    <?php if ($p['destacado']): ?>
                        <span class="destacado-badge">Destacado (<?= (int)$p['orden_destacado'] ?>)</span>
                    <?php else: ?>
                        <span style="font-size:.75rem;color:#666">—</span>
                    <?php endif; ?>
                </td>
                <td><?= e(date('Y-m-d H:i', strtotime($p['created_at']))) ?></td>
                <td class="actions">
                    <a href="editar_producto.php?id=<?= (int)$p['id'] ?>" class="btn secondary" title="Editar"><i class="fas fa-edit"></i></a>

                    <form method="post" onsubmit="return confirm('¿Cambiar estado activo/inactivo?');">
                        <input type="hidden" name="csrf_token" value="<?= $csrf ?>">
                        <input type="hidden" name="accion" value="toggle_activo">
                        <input type="hidden" name="id" value="<?= (int)$p['id'] ?>">
                        <button class="btn" type="submit" title="Activar/Desactivar"><i class="fas fa-toggle-on"></i></button>
                    </form>

                    <form method="post" onsubmit="return confirm('Esto borrará el producto y sus imágenes. ¿Seguro?');">
                        <input type="hidden" name="csrf_token" value="<?= $csrf ?>">
                        <input type="hidden" name="accion" value="eliminar_definitivo">
                        <input type="hidden" name="id" value="<?= (int)$p['id'] ?>">
                        <button class="btn danger" type="submit" title="Eliminar"><i class="fas fa-trash"></i></button>
                    </form>
                </td>
            </tr>
        <?php endforeach; endif; ?>
        </tbody>
    </table>

    <?php if ($totalPages > 1): ?>
        <div class="pagination">
            <?php
            $baseQs = $_GET;
            for ($i=1; $i<=$totalPages; $i++):
                $baseQs['page'] = $i;
                $url = 'listar_productos.php?'.http_build_query($baseQs);
                if ($i == $page): ?>
                    <span class="current"><?= $i ?></span>
                <?php else: ?>
                    <a href="<?= e($url) ?>"><?= $i ?></a>
                <?php endif; endfor; ?>
        </div>
    <?php endif; ?>
</div>

<!-- Formulario oculto para logout seguro -->
<form id="logoutForm" action="logout.php" method="POST" style="display:none;">
    <input type="hidden" name="csrf" value="<?= htmlspecialchars($_SESSION['csrf_logout'] ?? $_SESSION['csrf_token']) ?>">
    <input type="hidden" name="redirect" value="">
</form>

<script>
function logoutTo(where) {
    const form = document.getElementById('logoutForm');
    form.redirect.value = (where === 'home') ? 'home' : 'login';
    form.submit();
}
</script>
</body>
</html>
