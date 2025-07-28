<!-- admin/formulario_producto.php -->
<?php
session_start();

// Configuración de errores (desactívala en producción)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Autenticación
if (!isset($_SESSION['admin']) || $_SESSION['admin'] !== true) {
    header('Location: login.php?error=session');
    exit();
}

// CSRF
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// DB
require_once '../config/db.php';

// Categorías
$query = "SELECT id, nombre, parent_id FROM categorias 
          ORDER BY parent_id IS NULL DESC, parent_id ASC, nombre ASC";
$categorias = $pdo->query($query)->fetchAll(PDO::FETCH_ASSOC);

// Agrupar por padre con verificación de existencia
$agrupadas = [];
foreach ($categorias as $cat) {
    if ($cat['parent_id'] === null) {
        $agrupadas[$cat['id']] = ['nombre' => $cat['nombre'], 'subcategorias' => []];
    }
}
foreach ($categorias as $cat) {
    if ($cat['parent_id'] && isset($agrupadas[$cat['parent_id']])) {
        $agrupadas[$cat['parent_id']]['subcategorias'][] = $cat;
    }
}

// Mensajes
$mensaje = '';
$tipoMensaje = '';
$valoresAnteriores = [];

if (isset($_GET['success'])) {
    $mensaje = '✅ Producto registrado exitosamente.';
    $tipoMensaje = 'success';
} elseif (isset($_GET['error'])) {
    $errores = [
        'invalid_input'     => 'Por favor completa todos los campos obligatorios correctamente.',
        'invalid_file'      => 'El archivo no es válido. Solo se permiten imágenes JPG, PNG o WEBP (máx. 5MB).',
        'upload_fail'       => 'Error al subir la imagen. Verifica los permisos o intenta con otra imagen.',
        'internal'          => 'Error del sistema. Intenta más tarde.',
        'invalid_method'    => 'Método de envío no permitido.',
        'session'           => 'Tu sesión ha expirado. Inicia sesión nuevamente.',
        'slug_exists'       => 'Ya existe un producto con ese identificador URL. Intenta con otro nombre.',
        'invalid_stock'     => 'El stock debe ser un número válido (0 o mayor).',
        'referencia_exists' => 'La referencia ya existe en el sistema. Usa un código único.',
        'no_categories'     => 'Debes seleccionar al menos una categoría.',
        'invalid_price'     => 'El precio debe ser un valor numérico mayor a 0.'
    ];
    $mensaje = $errores[$_GET['error']] ?? 'Ocurrió un error inesperado.';
    $tipoMensaje = 'error';

    if (isset($_SESSION['form_data'])) {
        $valoresAnteriores = $_SESSION['form_data'];
        unset($_SESSION['form_data']);
    }
}

// Helpers
function getValue($field, $default = '') {
    global $valoresAnteriores;
    return htmlspecialchars($valoresAnteriores[$field] ?? $default, ENT_QUOTES, 'UTF-8');
}

$host = htmlspecialchars($_SERVER['HTTP_HOST'] ?? '', ENT_QUOTES, 'UTF-8');
$alt0 = isset($valoresAnteriores['alt_texts'][0])
    ? htmlspecialchars($valoresAnteriores['alt_texts'][0], ENT_QUOTES, 'UTF-8')
    : '';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Registro de Productos - MALEJA</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../assets/css/pages/registros.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
<div class="container">
    <header>
        <h1><i class="fas fa-shoe-prints"></i> Registrar Producto</h1>
        <div class="header-actions">
            <a href="listar_productos.php" class="btn secondary"><i class="fas fa-list"></i> Ver Productos</a>
            <a href="listar_categorias.php" class="btn secondary"><i class="fas fa-tags"></i> Gestionar Categorías</a>
            <a class="logout-btn" href="#" onclick="logoutTo('login');return false;"><i class="fas fa-sign-out-alt"></i> Cerrar sesión</a>
        </div>
    </header>

    <?php if ($mensaje || isset($_SESSION['form_errors'])): ?>
        <div class="message <?= $tipoMensaje ?>" role="alert" aria-live="polite">
            <button class="close-btn" onclick="this.parentElement.style.display='none'" aria-label="Cerrar">&times;</button>
            <?= $mensaje ?>
            <?php if (isset($_SESSION['form_errors'])): ?>
                <?php if ($mensaje) echo '<br><br>'; ?>
                <ul class="error-list">
                    <?php foreach ($_SESSION['form_errors'] as $err): ?>
                        <li><?= htmlspecialchars($err, ENT_QUOTES, 'UTF-8') ?></li>
                    <?php endforeach; ?>
                </ul>
                <?php unset($_SESSION['form_errors']); ?>
            <?php endif; ?>
        </div>
    <?php endif; ?>

    <form action="procesar_producto.php" method="POST" enctype="multipart/form-data" id="formulario-producto" novalidate>
        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">

        <!-- Imágenes -->
        <fieldset>
            <legend><i class="fas fa-images"></i> Imágenes del producto</legend>
            <div class="image-upload-container">
                <div class="image-upload-item featured">
                    <label for="imagen_principal">Imagen principal *</label>
                    <div class="file-upload-wrapper">
                        <input type="file" name="imagenes[]" id="imagen_principal"
                               accept="image/jpeg,image/jpg,image/png,image/webp" required aria-describedby="help-imgp">
                        <span class="file-upload-button"><i class="fas fa-cloud-upload-alt"></i> Seleccionar</span>
                        <span class="file-upload-label">Ningún archivo seleccionado</span>
                    </div>
                    <input type="text" name="alt_texts[]" placeholder="Texto alternativo (SEO)"
                           value="<?= $alt0 ?>" maxlength="180">
                    <input type="hidden" name="principales[]" value="1">
                    <div class="help-text" id="help-imgp">Formato: JPG, PNG o WEBP (máx. 5MB, mínimo 300x300px)</div>
                    <div class="preview-container" id="preview-0">
                        <?php if (isset($valoresAnteriores['imagen_preview'][0])): ?>
                            <div class="image-preview">
                                <img src="<?= $valoresAnteriores['imagen_preview'][0] ?>" alt="Preview">
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </fieldset>

        <!-- Información básica -->
        <fieldset>
            <legend><i class="fas fa-info-circle"></i> Información básica</legend>

            <div class="form-row">
                <div class="form-group">
                    <label for="nombre">Nombre del producto *</label>
                    <input type="text" name="nombre" id="nombre" required
                           value="<?= getValue('nombre') ?>"
                           placeholder="Ej: Sandalia Dama Verano" maxlength="120"
                           aria-describedby="help-nombre">
                    <div class="help-text" id="help-nombre">Máximo 120 caracteres</div>
                </div>

                <div class="form-group">
                    <label for="referencia">Referencia *</label>
                    <div class="ref-wrapper">
                        <input type="text" name="referencia" id="referencia" required
                               value="<?= getValue('referencia') ?>"
                               placeholder="Ej: SD-001" maxlength="50" aria-describedby="help-ref">
                    </div>
                    <div class="help-text" id="help-ref">Código único (letras, números y guiones). Se genera automáticamente si no lo modificas.</div>
                </div>
            </div>

            <div class="form-group">
                <label for="slug">URL amigable (Slug)</label>
                <div class="slug-container">
                    <span class="slug-prefix"><?= $host ?>/productos/</span>
                    <input type="text" name="slug" id="slug"
                           value="<?= getValue('slug') ?>"
                           placeholder="Se genera automáticamente" maxlength="140" aria-describedby="help-slug">
                </div>
                <div class="help-text" id="help-slug">Identificador único para URLs. Se genera desde el nombre.</div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="precio">Precio (COP) *</label>
                    <div class="input-group">
                        <span class="input-prefix">$</span>
                        <input type="number" name="precio" id="precio" min="0" max="999999999" step="100" required
                               value="<?= getValue('precio', '0') ?>" placeholder="Ej: 89000" aria-describedby="help-precio">
                    </div>
                    <div class="help-text" id="help-precio">Valor numérico mayor a 0</div>
                </div>

                <div class="form-group">
                    <label for="stock">Stock disponible</label>
                    <input type="number" name="stock" id="stock" min="0"
                           value="<?= getValue('stock') ?>"
                           placeholder="Cantidad en inventario (opcional)" aria-describedby="help-stock">
                    <div class="help-text" id="help-stock">Dejar vacío si no aplica</div>
                </div>
            </div>
        </fieldset>

        <!-- Descripciones -->
        <fieldset>
            <legend><i class="fas fa-align-left"></i> Descripciones</legend>

            <div class="form-group">
                <label for="descripcion_corta">Descripción corta</label>
                <textarea name="descripcion_corta" id="descripcion_corta"
                          placeholder="Descripción breve para listados..."
                          maxlength="255" rows="3" aria-describedby="help-dc"><?= getValue('descripcion_corta') ?></textarea>
                <div class="help-text" id="help-dc">Máximo 255 caracteres - Se muestra en listados</div>
                <div class="char-counter" id="contador-corta"><?= strlen(getValue('descripcion_corta')) ?>/255</div>
            </div>

            <div class="form-group">
                <label for="descripcion_larga">Descripción detallada</label>
                <textarea name="descripcion_larga" id="descripcion_larga"
                          placeholder="Descripción completa del producto..."
                          rows="6" aria-describedby="help-dl"><?= getValue('descripcion_larga') ?></textarea>
                <div class="help-text" id="help-dl">Información técnica, materiales, cuidados, etc.</div>
            </div>
        </fieldset>

        <!-- Configuración -->
        <fieldset>
            <legend><i class="fas fa-cog"></i> Configuración</legend>

            <div class="form-row-triple">
                <div class="form-group inline">
                    <input type="checkbox" name="activo" id="activo" value="1" <?= getValue('activo', '1') === '1' ? 'checked' : '' ?>>
                    <label for="activo">Producto activo</label>
                    <div class="help-text">Visible en el catálogo</div>
                </div>

                <div class="form-group inline">
                    <input type="checkbox" name="destacado" id="destacado" value="1" <?= getValue('destacado') === '1' ? 'checked' : '' ?>>
                    <label for="destacado">Producto destacado</label>
                    <div class="help-text">Aparecerá en secciones especiales</div>
                </div>

                <div class="form-group">
                    <label for="orden_destacado">Orden destacado</label>
                    <input type="number" name="orden_destacado" id="orden_destacado"
                           min="0" max="255" value="<?= getValue('orden_destacado', '0') ?>"
                           placeholder="0" <?= getValue('destacado') === '1' ? '' : 'disabled' ?>
                           aria-describedby="help-od">
                    <div class="help-text" id="help-od">Posición en listados destacados</div>
                </div>
            </div>
        </fieldset>

        <!-- Categorías -->
        <fieldset>
            <legend><i class="fas fa-tags"></i> Categorías *</legend>

            <div class="form-group">
                <div class="checkboxes">
                    <?php foreach ($agrupadas as $idGrupo => $grupo): ?>
                        <div class="categoria-grupo">
                            <strong><?= htmlspecialchars($grupo['nombre'], ENT_QUOTES, 'UTF-8') ?></strong>
                            <div class="subcategorias">
                                <?php foreach ($grupo['subcategorias'] as $sub): ?>
                                    <label class="categoria-item">
                                        <input type="checkbox" name="categorias[]" value="<?= $sub['id'] ?>"
                                            <?= in_array($sub['id'], $valoresAnteriores['categorias'] ?? []) ? 'checked' : '' ?>>
                                        <?= htmlspecialchars($sub['nombre'], ENT_QUOTES, 'UTF-8') ?>
                                    </label>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
                <div class="help-text">Selecciona al menos una categoría</div>
                <div class="error-message" id="error-categorias" style="display: none;" aria-live="polite">
                    <i class="fas fa-exclamation-circle"></i> Debes seleccionar al menos una categoría
                </div>
            </div>
        </fieldset>

        <div class="form-actions">
            <button type="reset" class="btn secondary">
                <i class="fas fa-eraser"></i> Limpiar
            </button>
            <button type="submit" id="btn-submit" class="btn primary">
                <span class="btn-text"><i class="fas fa-save"></i> Guardar Producto</span>
                <div class="loading" style="display: none;"><i class="fas fa-spinner fa-spin"></i></div>
            </button>
        </div>
    </form>
</div>

<script src="../assets/js/pages/formulario_producto.js"></script>

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