<!-- admin/editar_producto.php -->
<?php
session_start();

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

// Obtener ID del producto
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id <= 0) {
    $_SESSION['flash_error'] = 'ID de producto no válido.';
    header('Location: listar_productos.php');
    exit();
}

// Consultar producto
try {
    $stmt = $pdo->prepare("SELECT * FROM productos WHERE id = ?");
    $stmt->execute([$id]);
    $producto = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$producto) {
        $_SESSION['flash_error'] = 'Producto no encontrado.';
        header('Location: listar_productos.php');
        exit();
    }

    // Obtener imágenes del producto
    $stmtImg = $pdo->prepare("SELECT * FROM producto_imagenes WHERE producto_id = ? ORDER BY orden ASC");
    $stmtImg->execute([$id]);
    $imagenes = $stmtImg->fetchAll(PDO::FETCH_ASSOC);

    // Obtener categorías asignadas
    $stmtCat = $pdo->prepare("SELECT categoria_id FROM producto_categoria WHERE producto_id = ?");
    $stmtCat->execute([$id]);
    $categorias_asignadas = $stmtCat->fetchAll(PDO::FETCH_COLUMN);

    // Obtener todas las categorías para el selector
    $query = "SELECT id, nombre, parent_id FROM categorias
              ORDER BY parent_id IS NULL DESC, parent_id ASC, nombre ASC";
    $categorias = $pdo->query($query)->fetchAll(PDO::FETCH_ASSOC);

    // Agrupar por padre
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

} catch (PDOException $e) {
    error_log('Error al cargar producto: ' . $e->getMessage());
    $_SESSION['flash_error'] = 'Error al cargar el producto.';
    header('Location: listar_productos.php');
    exit();
}

// Mensajes
$mensaje = '';
$tipoMensaje = '';

if (isset($_GET['success'])) {
    $mensaje = '✅ Producto actualizado exitosamente.';
    $tipoMensaje = 'success';
} elseif (isset($_GET['error'])) {
    $errores = [
        'invalid_input'     => 'Por favor completa todos los campos obligatorios correctamente.',
        'referencia_exists' => 'La referencia ya está en uso por otro producto.',
        'slug_exists'       => 'Ya existe un producto con ese identificador URL.',
        'invalid_price'     => 'El precio debe ser un valor numérico mayor a 0.',
        'no_categories'     => 'Debes seleccionar al menos una categoría.',
        'internal'          => 'Error del sistema. Intenta más tarde.'
    ];
    $mensaje = $errores[$_GET['error']] ?? 'Ocurrió un error inesperado.';
    $tipoMensaje = 'error';
}

// Helper
function e($str) {
    return htmlspecialchars($str ?? '', ENT_QUOTES, 'UTF-8');
}

$host = htmlspecialchars($_SERVER['HTTP_HOST'] ?? '', ENT_QUOTES, 'UTF-8');
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Editar Producto - MALEJA</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../assets/css/pages/registros.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        /* Estilos específicos para la edición */
        .current-images {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
            gap: 1rem;
            margin-top: 1rem;
        }
        .current-image-item {
            position: relative;
            background: var(--color-bg);
            border: 1px solid var(--color-line);
            border-radius: var(--radius-m);
            padding: 0.5rem;
            transition: var(--transition);
        }
        .current-image-item:hover {
            border-color: var(--color-primary);
            transform: translateY(-2px);
        }
        .current-image-item.principal {
            border-color: var(--color-primary);
            border-width: 2px;
            background: rgba(212, 175, 55, 0.05);
        }
        .current-image-item img {
            width: 100%;
            height: 150px;
            object-fit: cover;
            border-radius: var(--radius-s);
            margin-bottom: 0.5rem;
        }
        .current-image-item .principal-badge {
            position: absolute;
            top: 0.8rem;
            right: 0.8rem;
            background: var(--color-primary);
            color: #111;
            padding: 0.2rem 0.5rem;
            border-radius: var(--radius-s);
            font-size: 0.7rem;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 0.2rem;
        }
        .current-image-item .delete-img-btn {
            width: 100%;
            padding: 0.4rem;
            background: rgba(255, 71, 87, 0.1);
            border: 1px solid rgba(255, 71, 87, 0.3);
            color: #ff6b7a;
            border-radius: var(--radius-s);
            cursor: pointer;
            font-size: 0.75rem;
            font-weight: 600;
            transition: var(--transition);
        }
        .current-image-item .delete-img-btn:hover {
            background: rgba(255, 71, 87, 0.2);
            border-color: rgba(255, 71, 87, 0.5);
            color: #ff4757;
        }
        .info-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.3rem;
            padding: 0.4rem 0.7rem;
            background: rgba(212, 175, 55, 0.1);
            border: 1px solid rgba(212, 175, 55, 0.3);
            border-radius: var(--radius-m);
            font-size: 0.85rem;
            color: var(--color-primary);
            margin-bottom: 1rem;
        }
        .add-images-section {
            margin-top: 1.5rem;
            padding-top: 1.5rem;
            border-top: 1px solid var(--color-line);
        }
        .quick-actions {
            display: flex;
            gap: 0.8rem;
            padding: 1rem;
            background: rgba(212, 175, 55, 0.05);
            border: 1px solid rgba(212, 175, 55, 0.2);
            border-radius: var(--radius-m);
            margin-bottom: 1rem;
        }
    </style>
</head>
<body>
<div class="container">
    <header>
        <h1><i class="fas fa-edit"></i> Editar Producto</h1>
        <div class="header-actions">
            <a href="listar_productos.php" class="btn secondary"><i class="fas fa-arrow-left"></i> Volver a listado</a>
            <a class="logout-btn" href="#" onclick="logoutTo('login');return false;"><i class="fas fa-sign-out-alt"></i> Cerrar sesión</a>
        </div>
    </header>

    <?php if ($mensaje): ?>
        <div class="message <?= $tipoMensaje ?>" role="alert" aria-live="polite">
            <button class="close-btn" onclick="this.parentElement.style.display='none'" aria-label="Cerrar">&times;</button>
            <?= $mensaje ?>
            <?php if (isset($_SESSION['form_errors'])): ?>
                <?php if ($mensaje) echo '<br><br>'; ?>
                <ul class="error-list">
                    <?php foreach ($_SESSION['form_errors'] as $err): ?>
                        <li><?= e($err) ?></li>
                    <?php endforeach; ?>
                </ul>
                <?php unset($_SESSION['form_errors']); ?>
            <?php endif; ?>
        </div>
    <?php endif; ?>

    <form action="procesar_edicion_producto.php" method="POST" enctype="multipart/form-data" id="formulario-edicion" novalidate>
        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
        <input type="hidden" name="producto_id" value="<?= $id ?>">

        <!-- Acciones rápidas -->
        <div class="quick-actions">
            <div class="form-group inline" style="margin: 0;">
                <input type="checkbox" name="activo" id="activo" value="1" <?= $producto['activo'] ? 'checked' : '' ?>>
                <label for="activo" style="margin: 0;">Producto activo</label>
            </div>
            <div class="form-group inline" style="margin: 0;">
                <input type="checkbox" name="destacado" id="destacado" value="1" <?= $producto['destacado'] ? 'checked' : '' ?>>
                <label for="destacado" style="margin: 0;">Producto destacado</label>
            </div>
        </div>

        <!-- Información básica -->
        <fieldset>
            <legend><i class="fas fa-info-circle"></i> Información básica</legend>

            <div class="info-badge">
                <i class="fas fa-barcode"></i>
                <strong>Referencia:</strong> <?= e($producto['referencia']) ?>
                <span style="margin-left: 1rem;"><i class="fas fa-link"></i> <strong>Slug:</strong> <?= e($producto['slug']) ?></span>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="nombre">Nombre del producto *</label>
                    <input type="text" name="nombre" id="nombre" required
                           value="<?= e($producto['nombre']) ?>"
                           placeholder="Ej: Sandalia Dama Verano" maxlength="120">
                    <div class="help-text">Máximo 120 caracteres</div>
                </div>

                <div class="form-group">
                    <label for="precio">Precio (COP) *</label>
                    <div class="input-group">
                        <span class="input-prefix">$</span>
                        <input type="number" name="precio" id="precio" min="0" max="999999999" step="100" required
                               value="<?= e($producto['precio']) ?>" placeholder="Ej: 89000">
                    </div>
                    <div class="help-text">Valor numérico mayor a 0</div>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="stock">Stock disponible</label>
                    <input type="number" name="stock" id="stock" min="0"
                           value="<?= e($producto['stock']) ?>"
                           placeholder="Cantidad en inventario (opcional)">
                    <div class="help-text">Dejar vacío si no aplica</div>
                </div>

                <div class="form-group">
                    <label for="orden_destacado">Orden destacado</label>
                    <input type="number" name="orden_destacado" id="orden_destacado"
                           min="0" max="255" value="<?= e($producto['orden_destacado']) ?>"
                           placeholder="0" <?= $producto['destacado'] ? '' : 'disabled' ?>>
                    <div class="help-text">Posición en listados destacados (si está marcado como destacado)</div>
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
                          maxlength="255" rows="3"><?= e($producto['descripcion_corta']) ?></textarea>
                <div class="help-text">Máximo 255 caracteres - Se muestra en listados</div>
                <div class="char-counter" id="contador-corta"><?= strlen($producto['descripcion_corta'] ?? '') ?>/255</div>
            </div>

            <div class="form-group">
                <label for="descripcion_larga">Descripción detallada</label>
                <textarea name="descripcion_larga" id="descripcion_larga"
                          placeholder="Descripción completa del producto..."
                          rows="6"><?= e($producto['descripcion_larga']) ?></textarea>
                <div class="help-text">Información técnica, materiales, cuidados, etc.</div>
            </div>
        </fieldset>

        <!-- Imágenes actuales -->
        <fieldset>
            <legend><i class="fas fa-images"></i> Imágenes actuales</legend>

            <?php if (!empty($imagenes)): ?>
                <div class="current-images">
                    <?php foreach ($imagenes as $img): ?>
                        <div class="current-image-item <?= $img['principal'] ? 'principal' : '' ?>">
                            <?php if ($img['principal']): ?>
                                <span class="principal-badge"><i class="fas fa-star"></i> Principal</span>
                            <?php endif; ?>
                            <img src="../assets/images/productos/<?= e($img['filename']) ?>" alt="<?= e($img['alt_text']) ?>">
                            <input type="text" name="alt_text_existing[<?= $img['id'] ?>]"
                                   value="<?= e($img['alt_text']) ?>"
                                   placeholder="Texto alternativo (SEO)"
                                   style="width: 100%; margin-bottom: 0.5rem; font-size: 0.8rem;">
                            <button type="button" class="delete-img-btn" onclick="marcarParaEliminar(<?= $img['id'] ?>, this)">
                                <i class="fas fa-trash"></i> Eliminar
                            </button>
                            <input type="hidden" name="eliminar_imagenes[]" value="" id="eliminar-<?= $img['id'] ?>">
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <p style="color: #888; font-style: italic;">No hay imágenes asociadas a este producto.</p>
            <?php endif; ?>

            <!-- Agregar nuevas imágenes -->
            <div class="add-images-section">
                <h3 style="font-size: 1rem; color: var(--color-text); margin-bottom: 1rem;">
                    <i class="fas fa-plus-circle" style="color: var(--color-primary);"></i> Agregar nuevas imágenes
                </h3>
                <div class="form-group">
                    <label for="nuevas_imagenes">Seleccionar imágenes</label>
                    <div class="file-upload-wrapper">
                        <input type="file" name="nuevas_imagenes[]" id="nuevas_imagenes"
                               accept="image/jpeg,image/jpg,image/png,image/webp" multiple>
                        <span class="file-upload-button"><i class="fas fa-cloud-upload-alt"></i> Seleccionar</span>
                        <span class="file-upload-label">Ningún archivo seleccionado</span>
                    </div>
                    <div class="help-text">Formato: JPG, PNG o WEBP (máx. 5MB cada una, mínimo 300x300px). Puedes seleccionar múltiples imágenes.</div>
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
                            <strong><?= e($grupo['nombre']) ?></strong>
                            <div class="subcategorias">
                                <?php foreach ($grupo['subcategorias'] as $sub): ?>
                                    <label class="categoria-item">
                                        <input type="checkbox" name="categorias[]" value="<?= $sub['id'] ?>"
                                            <?= in_array($sub['id'], $categorias_asignadas) ? 'checked' : '' ?>>
                                        <?= e($sub['nombre']) ?>
                                    </label>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
                <div class="help-text">Selecciona al menos una categoría</div>
            </div>
        </fieldset>

        <div class="form-actions">
            <a href="listar_productos.php" class="btn secondary">
                <i class="fas fa-times"></i> Cancelar
            </a>
            <button type="submit" id="btn-submit" class="btn primary">
                <span class="btn-text"><i class="fas fa-save"></i> Actualizar Producto</span>
                <div class="loading" style="display: none;"><i class="fas fa-spinner fa-spin"></i></div>
            </button>
        </div>
    </form>
</div>

<!-- Formulario oculto para logout seguro -->
<form id="logoutForm" action="logout.php" method="POST" style="display:none;">
    <input type="hidden" name="csrf" value="<?= e($_SESSION['csrf_logout'] ?? $_SESSION['csrf_token']) ?>">
    <input type="hidden" name="redirect" value="">
</form>

<script>
function logoutTo(where) {
    const form = document.getElementById('logoutForm');
    form.redirect.value = (where === 'home') ? 'home' : 'login';
    form.submit();
}

// Marcar imagen para eliminar
function marcarParaEliminar(imgId, btn) {
    if (confirm('¿Estás seguro de eliminar esta imagen?')) {
        const item = btn.closest('.current-image-item');
        const input = document.getElementById('eliminar-' + imgId);

        if (item.classList.contains('marked-delete')) {
            // Desmarcar
            item.classList.remove('marked-delete');
            item.style.opacity = '1';
            input.value = '';
            btn.innerHTML = '<i class="fas fa-trash"></i> Eliminar';
            btn.style.background = 'rgba(255, 71, 87, 0.1)';
        } else {
            // Marcar
            item.classList.add('marked-delete');
            item.style.opacity = '0.5';
            input.value = imgId;
            btn.innerHTML = '<i class="fas fa-undo"></i> Deshacer';
            btn.style.background = 'rgba(255, 165, 2, 0.2)';
        }
    }
}

// Contador de caracteres
const textareaCorta = document.getElementById('descripcion_corta');
const contadorCorta = document.getElementById('contador-corta');

if (textareaCorta && contadorCorta) {
    textareaCorta.addEventListener('input', function() {
        const length = this.value.length;
        contadorCorta.textContent = length + '/255';

        if (length > 230) {
            contadorCorta.classList.add('warning');
        } else {
            contadorCorta.classList.remove('warning');
        }

        if (length >= 255) {
            contadorCorta.classList.add('danger');
        } else {
            contadorCorta.classList.remove('danger');
        }
    });
}

// Manejo del checkbox destacado
const destacadoCheck = document.getElementById('destacado');
const ordenDestacado = document.getElementById('orden_destacado');

if (destacadoCheck && ordenDestacado) {
    destacadoCheck.addEventListener('change', function() {
        ordenDestacado.disabled = !this.checked;
        if (!this.checked) {
            ordenDestacado.value = '0';
        }
    });
}

// Preview de archivos seleccionados
const fileInput = document.getElementById('nuevas_imagenes');
if (fileInput) {
    fileInput.addEventListener('change', function() {
        const label = this.parentElement.querySelector('.file-upload-label');
        if (this.files.length > 0) {
            label.textContent = this.files.length + ' archivo(s) seleccionado(s)';
        } else {
            label.textContent = 'Ningún archivo seleccionado';
        }
    });
}

// Validación de categorías antes de enviar
document.getElementById('formulario-edicion').addEventListener('submit', function(e) {
    const categorias = document.querySelectorAll('input[name="categorias[]"]:checked');
    if (categorias.length === 0) {
        e.preventDefault();
        alert('Debes seleccionar al menos una categoría');
        return false;
    }

    // Mostrar estado de carga
    const btnSubmit = document.getElementById('btn-submit');
    btnSubmit.classList.add('submitting');
    btnSubmit.disabled = true;
});
</script>

</body>
</html>
