<!-- listar_categorias.php -->

<?php
session_start();

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if (!isset($_SESSION['admin']) || $_SESSION['admin'] !== true) {
    header('Location: login.php?error=session');
    exit();
}

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
$csrf = $_SESSION['csrf_token'];

require_once '../config/db.php';

function e($str){ return htmlspecialchars($str, ENT_QUOTES, 'UTF-8'); }

// ---- Helpers ----
function slugify($text){
    $text = iconv('UTF-8', 'ASCII//TRANSLIT', $text);
    $text = strtolower(trim($text));
    $text = preg_replace('/[^a-z0-9\s-]/', '', $text);
    $text = preg_replace('/[\s-]+/', '-', $text);
    return trim($text, '-');
}

function uniqueSlug($raw, PDO $pdo, $excludeId = null){
    $slug = $raw ?: 'cat-'.uniqid();
    $base = $slug;
    $i = 1;
    while (true){
        $sql = "SELECT COUNT(*) FROM categorias WHERE slug = ?" . ($excludeId? " AND id <> ?" : "");
        $params = [$slug];
        if ($excludeId) $params[] = $excludeId;
        $st = $pdo->prepare($sql);
        $st->execute($params);
        if ($st->fetchColumn() == 0) break;
        $slug = $base.'-'.$i;
        $i++;
    }
    return $slug;
}

function buildTree(array $cats){
    $tree = [];
    $refs = [];
    foreach ($cats as $c){
        $c['children'] = [];
        $refs[$c['id']] = $c;
    }
    foreach ($refs as $id => &$node){
        if ($node['parent_id']){
            $refs[$node['parent_id']]['children'][] = &$node;
        } else {
            $tree[] = &$node;
        }
    }
    return $tree;
}

function renderTreeRows($nodes, $depth = 0){
    $html = '';
    foreach ($nodes as $n){
        $indent = str_repeat('&mdash; ', $depth);
        $estadoHijos = count($n['children']);
        $html .= '<tr>';
        $html .= '<td>'.(int)$n['id'].'</td>';
        $html .= '<td>'.$indent.e($n['nombre']).'</td>';
        $html .= '<td>'.e($n['slug']).'</td>';
        $html .= '<td>'.($n['parent_id'] ? (int)$n['parent_id'] : '—').'</td>';
        $html .= '<td>'.e(date('Y-m-d H:i', strtotime($n['created_at']))).'</td>';
        $html .= '<td class="actions">';
        $html .= '<a class="btn secondary" href="listar_categorias.php?edit='.(int)$n['id'].'"><i class="fas fa-edit"></i></a> ';
        $html .= '<form method="post" style="display:inline-block" onsubmit="return confirm(\'¿Eliminar esta categoría? (Los productos quedarán sin esa categoría)\');">';
        $html .= '<input type="hidden" name="csrf_token" value="'.$_SESSION['csrf_token'].'">';
        $html .= '<input type="hidden" name="accion" value="delete">';
        $html .= '<input type="hidden" name="id" value="'.(int)$n['id'].'">';
        $html .= '<button class="btn danger" type="submit"><i class="fas fa-trash"></i></button>';
        $html .= '</form>';
        $html .= '</td>';
        $html .= '</tr>';
        if ($estadoHijos){
            $html .= renderTreeRows($n['children'], $depth + 1);
        }
    }
    return $html;
}

function buildOptions($nodes, $level = 0, $currentId = null){
    $out = '';
    foreach ($nodes as $n){
        if ($currentId && $n['id'] == $currentId) {
            // no permitir ser su propio padre
        } else {
            $out .= '<option value="'.$n['id'].'">'.str_repeat('— ', $level).e($n['nombre']).'</option>';
            if (!empty($n['children'])){
                $out .= buildOptions($n['children'], $level+1, $currentId);
            }
        }
    }
    return $out;
}

// ---- POST Actions ----
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['accion'])){
    if (empty($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])){
        $_SESSION['flash_error'] = 'Token inválido.';
        header('Location: listar_categorias.php');
        exit();
    }

    $accion = $_POST['accion'];
    $nombre = trim($_POST['nombre'] ?? '');
    $slug   = trim($_POST['slug'] ?? '');
    $desc   = trim($_POST['descripcion'] ?? '');
    $parent = isset($_POST['parent_id']) && $_POST['parent_id'] !== '' ? (int)$_POST['parent_id'] : null;
    $id     = isset($_POST['id']) ? (int)$_POST['id'] : null;

    $errores = [];

    if ($accion === 'create' || $accion === 'update'){
        if ($nombre === '') $errores[] = 'El nombre es obligatorio';
        if (strlen($nombre) > 80) $errores[] = 'El nombre no puede exceder 80 caracteres';
        if ($desc !== '' && strlen($desc) > 255) $errores[] = 'La descripción no puede exceder 255 caracteres';
        if ($parent !== null && $parent <= 0) $parent = null;
        if ($id && $parent === $id) $errores[] = 'Una categoría no puede ser su propia padre';

        // generar slug si vacío
        if ($slug === '') $slug = slugify($nombre);
        $slug = substr($slug, 0, 100);
        $slug = uniqueSlug($slug, $pdo, $accion==='update' ? $id : null);
    }

    if (!empty($errores)){
        $_SESSION['flash_error'] = implode('\n', $errores);
        header('Location: categorias.php'.($accion==='update' && $id? '?edit='.$id : ''));
        exit();
    }

    try {
        if ($accion === 'create'){
            $sql = "INSERT INTO categorias (nombre, slug, descripcion, parent_id) VALUES (?,?,?,?)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$nombre, $slug, $desc ?: null, $parent]);
            $_SESSION['flash_success'] = 'Categoría creada.';
        } elseif ($accion === 'update' && $id){
            $sql = "UPDATE categorias SET nombre=?, slug=?, descripcion=?, parent_id=? WHERE id=?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$nombre, $slug, $desc ?: null, $parent, $id]);
            $_SESSION['flash_success'] = 'Categoría actualizada.';
        } elseif ($accion === 'delete' && $id){
            // Borrado duro
            $pdo->beginTransaction();
            $pdo->prepare("DELETE FROM producto_categoria WHERE categoria_id = ?")->execute([$id]);
            $pdo->prepare("UPDATE categorias SET parent_id = NULL WHERE parent_id = ?")->execute([$id]);
            $pdo->prepare("DELETE FROM categorias WHERE id = ? LIMIT 1")->execute([$id]);
            $pdo->commit();
            $_SESSION['flash_success'] = 'Categoría eliminada.';
        }
    } catch (Exception $ex){
        if ($pdo->inTransaction()) $pdo->rollBack();
        error_log('Error categorías: '.$ex->getMessage());
        $_SESSION['flash_error'] = 'No se pudo completar la acción.';
    }

    header('Location: listar_categorias.php');
    exit();
}

// ---- GET for edit ----
$editCat = null;
if (isset($_GET['edit'])){
    $id = (int)$_GET['edit'];
    $st = $pdo->prepare('SELECT * FROM categorias WHERE id = ? LIMIT 1');
    $st->execute([$id]);
    $editCat = $st->fetch(PDO::FETCH_ASSOC) ?: null;
}

// ---- Fetch all categories ----
$catsStmt = $pdo->query('SELECT * FROM categorias ORDER BY nombre ASC');
$cats = $catsStmt->fetchAll(PDO::FETCH_ASSOC);
$tree = buildTree($cats);

// For select options
$optionsTree = buildTree($cats);

$host = e($_SERVER['HTTP_HOST'] ?? '');
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Categorías - MALEJA</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../assets/css/pages/registros.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
<div class="container">
    <header>
        <h1><i class="fas fa-sitemap"></i> Categorías</h1>
        <div class="header-actions">
            <a href="listar_productos.php" class="btn secondary"><i class="fas fa-box"></i> Productos</a>
            <a href="formulario_producto.php" class="btn secondary"><i class="fas fa-plus"></i> Nuevo producto</a>
            <a href="#" class="logout-btn" onclick="logoutTo('login');return false;"><i class="fas fa-sign-out-alt"></i> Cerrar sesión</a>
        </div>
    </header>

    <?php if (!empty($_SESSION['flash_success'])): ?>
        <div class="flash success"><?= e($_SESSION['flash_success']); unset($_SESSION['flash_success']); ?></div>
    <?php endif; ?>
    <?php if (!empty($_SESSION['flash_error'])): ?>
        <div class="flash error"><?= e($_SESSION['flash_error']); unset($_SESSION['flash_error']); ?></div>
    <?php endif; ?>

    <div class="container-flex">
        <div class="panel" style="flex:2 1 500px;">
            <h2><i class="fas fa-stream"></i> Árbol de categorías</h2>
            <table class="table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nombre</th>
                        <th>Slug</th>
                        <th>Padre</th>
                        <th>Creado</th>
                        <th style="width:120px;">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?= renderTreeRows($tree) ?>
                </tbody>
            </table>
        </div>

        <div class="panel" style="flex:1 1 360px;">
            <h2><?= $editCat ? '<i class="fas fa-edit"></i> Editar categoría' : '<i class="fas fa-plus"></i> Nueva categoría' ?></h2>
            <form method="post" action="listar_categorias.php" id="form-categoria" novalidate>
                <input type="hidden" name="csrf_token" value="<?= $csrf ?>">
                <input type="hidden" name="accion" value="<?= $editCat? 'update':'create' ?>">
                <?php if ($editCat): ?>
                    <input type="hidden" name="id" value="<?= (int)$editCat['id'] ?>">
                <?php endif; ?>

                <div class="form-group">
                    <label for="nombre">Nombre *</label>
                    <input type="text" name="nombre" id="nombre" maxlength="80" required value="<?= $editCat? e($editCat['nombre']):'' ?>">
                </div>

                <div class="form-group">
                    <label for="slug">Slug</label>
                    <div class="slug-input-wrap">
                        <span class="slug-prefix"><?= $host ?>/categorias/</span>
                        <input type="text" name="slug" id="slug" maxlength="100" value="<?= $editCat? e($editCat['slug']):'' ?>">
                    </div>
                    <div class="help-text">Se genera desde el nombre si lo dejas vacío.</div>
                </div>

                <div class="form-group">
                    <label for="descripcion">Descripción</label>
                    <textarea name="descripcion" id="descripcion" maxlength="255" rows="3"><?= $editCat? e($editCat['descripcion']):'' ?></textarea>
                </div>

                <div class="form-group">
                    <label for="parent_id">Padre</label>
                    <select name="parent_id" id="parent_id">
                        <option value="">— Sin padre (raíz) —</option>
                        <?= buildOptions($optionsTree, 0, $editCat['id'] ?? null) ?>
                    </select>
                </div>

                <div class="form-actions">
                    <button type="reset" class="btn secondary" id="btn-clear-cat"><i class="fas fa-eraser"></i> Limpiar</button>
                    <button type="submit" class="btn primary"><i class="fas fa-save"></i> <?= $editCat? 'Actualizar':'Crear' ?></button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
(function(){
    const nombre = document.getElementById('nombre');
    const slug   = document.getElementById('slug');
    const form   = document.getElementById('form-categoria');
    const btnClr = document.getElementById('btn-clear-cat');

    function generarSlug(text){
        return text.toLowerCase()
            .normalize('NFD').replace(/[\u0300-\u036f]/g, '')
            .replace(/[^a-z0-9\s-]/g, '')
            .replace(/\s+/g, '-')
            .replace(/-+/g, '-')
            .replace(/^-+|-+$/g, '')
            .substring(0, 100);
    }

    if (nombre && slug){
        if (!slug.dataset.generated){ slug.dataset.generated = slug.value ? 'false' : 'true'; }
        nombre.addEventListener('input', function(){
            if (!slug.value || slug.dataset.generated === 'true'){
                slug.value = generarSlug(this.value);
                slug.dataset.generated = 'true';
            }
        });
        slug.addEventListener('input', function(){ this.dataset.generated = 'false'; });
    }

    if (btnClr){
        btnClr.addEventListener('click', function(){
            form.reset();
            // volver a modo crear
            window.location.href = 'listar_categorias.php';
        });
    }
})();
</script>

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
