<?php
session_start();

// Sesión y rol
if (!isset($_SESSION['admin']) || $_SESSION['admin'] !== true) {
    header('Location: login.php?error=session');
    exit();
}

// Método
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: listar_productos.php?error=invalid_method');
    exit();
}

// CSRF
if (empty($_POST['csrf_token']) || empty($_SESSION['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
    $_SESSION['form_errors'] = ['Token CSRF inválido o expirado.'];
    header('Location: listar_productos.php?error=invalid_input');
    exit();
}

// DB
require_once '../config/db.php';

// ------------ Funciones ------------
function sanitizarInput($data) {
    return htmlspecialchars(trim($data), ENT_QUOTES, 'UTF-8');
}

function validarImagen($img) {
    $allowed = ['image/jpeg', 'image/png', 'image/webp'];
    $max = 5 * 1024 * 1024;
    if ($img['error'] !== UPLOAD_ERR_OK) {
        return ['success' => false, 'error' => 'Error al subir la imagen: ' . $img['name']];
    }
    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mime = $finfo->file($img['tmp_name']);
    if (!in_array($mime, $allowed)) {
        return ['success' => false, 'error' => 'Tipo de archivo no permitido: ' . $img['name']];
    }
    if ($img['size'] > $max) {
        return ['success' => false, 'error' => 'Imagen demasiado grande (máx. 5MB): ' . $img['name']];
    }
    $info = getimagesize($img['tmp_name']);
    if ($info[0] < 300 || $info[1] < 300) {
        return ['success' => false, 'error' => 'La imagen debe tener al menos 300x300px: ' . $img['name']];
    }
    return ['success' => true];
}

function procesarImagen($img, $dir) {
    $ext = strtolower(pathinfo($img['name'], PATHINFO_EXTENSION));
    $filename = 'prod_' . uniqid() . '_' . time() . '.' . $ext;
    $dest = $dir . $filename;
    if (!move_uploaded_file($img['tmp_name'], $dest)) {
        return ['success' => false, 'error' => 'Error al mover la imagen: ' . $img['name']];
    }
    return ['success' => true, 'filename' => $filename, 'path' => $dest];
}

// ------------ Obtener ID del producto ------------
$productoId = isset($_POST['producto_id']) ? (int)$_POST['producto_id'] : 0;

if ($productoId <= 0) {
    $_SESSION['flash_error'] = 'ID de producto no válido.';
    header('Location: listar_productos.php');
    exit();
}

// Verificar que el producto existe
try {
    $stmt = $pdo->prepare("SELECT id FROM productos WHERE id = ?");
    $stmt->execute([$productoId]);
    if (!$stmt->fetch()) {
        $_SESSION['flash_error'] = 'Producto no encontrado.';
        header('Location: listar_productos.php');
        exit();
    }
} catch (PDOException $e) {
    error_log('Error al verificar producto: ' . $e->getMessage());
    $_SESSION['flash_error'] = 'Error al verificar el producto.';
    header('Location: listar_productos.php');
    exit();
}

// ------------ Sanitización ------------
$nombre            = sanitizarInput($_POST['nombre'] ?? '');
$descripcion_corta = $_POST['descripcion_corta'] !== '' ? sanitizarInput($_POST['descripcion_corta']) : null;
$descripcion_larga = $_POST['descripcion_larga'] !== '' ? sanitizarInput($_POST['descripcion_larga']) : null;
$precio            = filter_var($_POST['precio'] ?? 0, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
$stock             = isset($_POST['stock']) && $_POST['stock'] !== '' ? (int)$_POST['stock'] : null;
$destacado         = isset($_POST['destacado']) ? 1 : 0;
$orden_destacado   = $destacado ? (int)$_POST['orden_destacado'] : 0;
$activo            = isset($_POST['activo']) ? 1 : 0;
$categoria_ids     = array_map('intval', $_POST['categorias'] ?? []);

// Imágenes a eliminar
$eliminar_imagenes = array_filter(array_map('intval', $_POST['eliminar_imagenes'] ?? []));

// Alt text para imágenes existentes
$alt_text_existing = $_POST['alt_text_existing'] ?? [];

// ------------ Validaciones ------------
$errores = [];

if ($nombre === '') {
    $errores[] = 'El nombre del producto es obligatorio';
} elseif (strlen($nombre) > 120) {
    $errores[] = 'El nombre no puede exceder los 120 caracteres';
}

if ($precio <= 0) {
    $errores[] = 'El precio debe ser mayor a 0';
}

if ($stock !== null && $stock < 0) {
    $errores[] = 'El stock no puede ser negativo';
}

if ($destacado && ($orden_destacado < 0 || $orden_destacado > 255)) {
    $errores[] = 'El orden de destacado debe estar entre 0 y 255';
}

if (empty($categoria_ids)) {
    $errores[] = 'Debes seleccionar al menos una categoría';
}

// Validar nuevas imágenes si se subieron
$nuevasImagenes = [];
if (!empty($_FILES['nuevas_imagenes']['name'][0])) {
    foreach ($_FILES['nuevas_imagenes']['tmp_name'] as $i => $tmp) {
        if (empty($tmp)) continue;

        $img = [
            'name'     => $_FILES['nuevas_imagenes']['name'][$i],
            'type'     => $_FILES['nuevas_imagenes']['type'][$i],
            'tmp_name' => $_FILES['nuevas_imagenes']['tmp_name'][$i],
            'error'    => $_FILES['nuevas_imagenes']['error'][$i],
            'size'     => $_FILES['nuevas_imagenes']['size'][$i]
        ];

        $validacion = validarImagen($img);
        if (!$validacion['success']) {
            $errores[] = $validacion['error'];
        } else {
            $nuevasImagenes[] = $img;
        }
    }
}

// Errores -> volver
if (!empty($errores)) {
    $_SESSION['form_errors'] = $errores;
    header('Location: editar_producto.php?id=' . $productoId . '&error=invalid_input');
    exit();
}

// ------------ Persistencia ------------
$imagenesGuardadas = [];
$uploadDir = '../assets/images/productos/';

try {
    $pdo->beginTransaction();

    // Actualizar producto
    $sqlProducto = "UPDATE productos SET
        nombre = ?,
        descripcion_corta = ?,
        descripcion_larga = ?,
        precio = ?,
        stock = ?,
        destacado = ?,
        orden_destacado = ?,
        activo = ?,
        updated_at = NOW()
        WHERE id = ?";

    $stmtProducto = $pdo->prepare($sqlProducto);
    $stmtProducto->execute([
        $nombre,
        $descripcion_corta,
        $descripcion_larga,
        $precio,
        $stock,
        $destacado,
        $orden_destacado,
        $activo,
        $productoId
    ]);

    // Actualizar alt_text de imágenes existentes
    foreach ($alt_text_existing as $imgId => $altText) {
        $imgId = (int)$imgId;
        $altText = sanitizarInput($altText);
        $stmt = $pdo->prepare("UPDATE producto_imagenes SET alt_text = ? WHERE id = ? AND producto_id = ?");
        $stmt->execute([$altText, $imgId, $productoId]);
    }

    // Eliminar imágenes marcadas
    if (!empty($eliminar_imagenes)) {
        // Obtener filenames antes de eliminar
        $placeholders = implode(',', array_fill(0, count($eliminar_imagenes), '?'));
        $stmt = $pdo->prepare("SELECT filename FROM producto_imagenes WHERE id IN ($placeholders) AND producto_id = ?");
        $stmt->execute(array_merge($eliminar_imagenes, [$productoId]));
        $imagenesAEliminar = $stmt->fetchAll(PDO::FETCH_COLUMN);

        // Eliminar de la base de datos
        $stmt = $pdo->prepare("DELETE FROM producto_imagenes WHERE id IN ($placeholders) AND producto_id = ?");
        $stmt->execute(array_merge($eliminar_imagenes, [$productoId]));

        // Eliminar archivos físicos
        foreach ($imagenesAEliminar as $filename) {
            $filePath = $uploadDir . $filename;
            if (file_exists($filePath)) {
                @unlink($filePath);
            }
        }
    }

    // Procesar y agregar nuevas imágenes
    if (!empty($nuevasImagenes)) {
        // Obtener el último orden
        $stmt = $pdo->prepare("SELECT COALESCE(MAX(orden), 0) FROM producto_imagenes WHERE producto_id = ?");
        $stmt->execute([$productoId]);
        $ultimoOrden = (int)$stmt->fetchColumn();

        $sqlImagen = "INSERT INTO producto_imagenes (producto_id, filename, alt_text, orden, principal)
                      VALUES (?, ?, ?, ?, ?)";
        $stmtImagen = $pdo->prepare($sqlImagen);

        foreach ($nuevasImagenes as $i => $img) {
            $proc = procesarImagen($img, $uploadDir);
            if (!$proc['success']) {
                throw new Exception("Error al procesar imagen: " . $proc['error']);
            }

            $orden = $ultimoOrden + $i + 1;
            $principal = 0; // Las nuevas imágenes no son principales por defecto

            $stmtImagen->execute([$productoId, $proc['filename'], '', $orden, $principal]);
            $imagenesGuardadas[] = $proc['path'];
        }
    }

    // Actualizar categorías
    // Primero eliminar todas las categorías actuales
    $pdo->prepare("DELETE FROM producto_categoria WHERE producto_id = ?")->execute([$productoId]);

    // Luego insertar las nuevas
    $sqlCategoria = "INSERT INTO producto_categoria (producto_id, categoria_id) VALUES (?, ?)";
    $stmtCategoria = $pdo->prepare($sqlCategoria);
    foreach ($categoria_ids as $cid) {
        if ($cid > 0) {
            $stmtCategoria->execute([$productoId, $cid]);
        }
    }

    $pdo->commit();

    unset($_SESSION['form_errors']);

    error_log("Producto actualizado: ID $productoId - $nombre");

    $_SESSION['flash_success'] = 'Producto actualizado exitosamente.';
    header('Location: editar_producto.php?id=' . $productoId . '&success=true');
    exit();

} catch (Exception $e) {
    $pdo->rollBack();

    // Eliminar imágenes guardadas en caso de error
    foreach ($imagenesGuardadas as $path) {
        if (file_exists($path)) {
            @unlink($path);
        }
    }

    error_log("Error al actualizar producto: " . $e->getMessage());
    $_SESSION['form_errors'] = ['Ocurrió un error interno. Por favor, inténtalo nuevamente.'];
    header('Location: editar_producto.php?id=' . $productoId . '&error=internal');
    exit();
}
