<?php
session_start();

// Sesión y rol
if (!isset($_SESSION['admin']) || $_SESSION['admin'] !== true) {
    header('Location: login.php?error=session');
    exit();
}

// Método
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: formulario_producto.php?error=invalid_method');
    exit();
}

// CSRF
if (empty($_POST['csrf_token']) || empty($_SESSION['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
    $_SESSION['form_errors'] = ['Token CSRF inválido o expirado.'];
    header('Location: formulario_producto.php?error=invalid_input');
    exit();
}

// DB
require_once '../config/db.php';

// Guardar datos en sesión para repoblar formulario si falla
$_SESSION['form_data'] = $_POST;
if (isset($_FILES['imagenes'])) {
    $_SESSION['form_data']['imagen_preview'] = [];
    foreach ($_FILES['imagenes']['tmp_name'] as $i => $tmp) {
        if (!empty($tmp) && file_exists($tmp)) {
            $info = getimagesize($tmp);
            if ($info !== false) {
                $data = file_get_contents($tmp);
                $_SESSION['form_data']['imagen_preview'][$i] = 'data:' . $info['mime'] . ';base64,' . base64_encode($data);
            }
        }
    }
}

// ------------ Funciones ------------
function sanitizarInput($data) {
    return htmlspecialchars(trim($data), ENT_QUOTES, 'UTF-8');
}
function generarSlug($texto, $pdo) {
    if (empty($texto)) return '';
    $slug = iconv('UTF-8', 'ASCII//TRANSLIT', $texto);
    $slug = strtolower(trim($slug));
    $slug = preg_replace('/[^a-z0-9\s-]/', '', $slug);
    $slug = preg_replace('/[\s-]+/', '-', $slug);
    $slug = trim($slug, '-');
    if (empty($slug)) $slug = 'producto-' . uniqid();
    $base = $slug;
    $n = 1;
    while (true) {
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM productos WHERE slug = ?");
        $stmt->execute([$slug]);
        if ($stmt->fetchColumn() == 0) break;
        $slug = $base . '-' . $n;
        $n++;
    }
    return $slug;
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

// ------------ Sanitización ------------
$nombre            = sanitizarInput($_POST['nombre'] ?? '');
$referencia        = strtoupper(sanitizarInput($_POST['referencia'] ?? ''));
$slug              = sanitizarInput($_POST['slug'] ?? '');
$descripcion_corta = $_POST['descripcion_corta'] !== '' ? sanitizarInput($_POST['descripcion_corta']) : null;
$descripcion_larga = $_POST['descripcion_larga'] !== '' ? sanitizarInput($_POST['descripcion_larga']) : null;
$precio            = filter_var($_POST['precio'] ?? 0, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
$stock             = isset($_POST['stock']) && $_POST['stock'] !== '' ? (int)$_POST['stock'] : null;
$destacado         = isset($_POST['destacado']) ? 1 : 0;
$orden_destacado   = $destacado ? (int)$_POST['orden_destacado'] : 0;
$activo            = isset($_POST['activo']) ? 1 : 0;
$categoria_ids     = array_map('intval', $_POST['categorias'] ?? []);

// ------------ Validaciones ------------
$errores = [];

if ($nombre === '') $errores[] = 'El nombre del producto es obligatorio';
elseif (strlen($nombre) > 120) $errores[] = 'El nombre no puede exceder los 120 caracteres';

if ($referencia === '') $errores[] = 'La referencia es obligatoria';
elseif (!preg_match('/^[A-Z0-9-]{2,50}$/', $referencia)) $errores[] = 'La referencia debe contener solo letras mayúsculas, números y guiones (2-50 caracteres)';

if ($precio <= 0) $errores[] = 'El precio debe ser mayor a 0';

if ($stock !== null && $stock < 0) $errores[] = 'El stock no puede ser negativo';

if ($destacado && ($orden_destacado < 1 || $orden_destacado > 255)) $errores[] = 'El orden de destacado debe estar entre 1 y 255';

if (empty($categoria_ids)) $errores[] = 'Debes seleccionar al menos una categoría';

// referencia única
try {
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM productos WHERE referencia = ?");
    $stmt->execute([$referencia]);
    if ($stmt->fetchColumn() > 0) $errores[] = 'La referencia ya está en uso';
} catch (PDOException $e) {
    error_log("Error al verificar referencia: " . $e->getMessage());
    $errores[] = 'Error al verificar la referencia';
}

// slug único
try {
    $slug = generarSlug($slug ?: $nombre, $pdo);
} catch (Exception $e) {
    $errores[] = 'Error al generar el slug';
}

// Directorio imágenes
$uploadDir = '../assets/images/productos/';
if (!is_dir($uploadDir) && !mkdir($uploadDir, 0755, true)) {
    $errores[] = 'No se pudo crear el directorio para imágenes';
}

// Imagen principal
if (empty($_FILES['imagenes']['name'][0])) {
    $errores[] = 'La imagen principal es obligatoria';
} else {
    $validacion = validarImagen([
        'name'     => $_FILES['imagenes']['name'][0],
        'type'     => $_FILES['imagenes']['type'][0],
        'tmp_name' => $_FILES['imagenes']['tmp_name'][0],
        'error'    => $_FILES['imagenes']['error'][0],
        'size'     => $_FILES['imagenes']['size'][0]
    ]);
    if (!$validacion['success']) $errores[] = $validacion['error'];
}

// Errores -> volver
if (!empty($errores)) {
    $_SESSION['form_errors'] = $errores;
    header('Location: formulario_producto.php?error=invalid_input');
    exit();
}

// ------------ Persistencia ------------
$imagenesGuardadas = [];

try {
    $pdo->beginTransaction();

    $sqlProducto = "INSERT INTO productos
        (nombre, referencia, slug, descripcion_corta, descripcion_larga, precio, stock, destacado, orden_destacado, activo)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmtProducto = $pdo->prepare($sqlProducto);
    $stmtProducto->execute([
        $nombre,
        $referencia,
        $slug,
        $descripcion_corta,
        $descripcion_larga,
        $precio,
        $stock,
        $destacado,
        $orden_destacado,
        $activo
    ]);
    $productoId = $pdo->lastInsertId();

    $sqlImagen = "INSERT INTO producto_imagenes (producto_id, filename, alt_text, orden, principal)
                  VALUES (?, ?, ?, ?, ?)";
    $stmtImagen = $pdo->prepare($sqlImagen);

    foreach ($_FILES['imagenes']['tmp_name'] as $i => $tmp) {
        if (empty($tmp)) continue;

        $img = [
            'name'     => $_FILES['imagenes']['name'][$i],
            'type'     => $_FILES['imagenes']['type'][$i],
            'tmp_name' => $_FILES['imagenes']['tmp_name'][$i],
            'error'    => $_FILES['imagenes']['error'][$i],
            'size'     => $_FILES['imagenes']['size'][$i]
        ];

        $proc = procesarImagen($img, $uploadDir);
        if (!$proc['success']) throw new Exception("Error al procesar imagen: " . $proc['error']);

        $alt   = sanitizarInput($_POST['alt_texts'][$i] ?? '');
        $prin  = isset($_POST['principales'][$i]) ? 1 : 0;
        $orden = $i + 1;

        $stmtImagen->execute([$productoId, $proc['filename'], $alt, $orden, $prin]);
        $imagenesGuardadas[] = $proc['path'];
    }

    $sqlCategoria = "INSERT INTO producto_categoria (producto_id, categoria_id) VALUES (?, ?)";
    $stmtCategoria = $pdo->prepare($sqlCategoria);
    foreach ($categoria_ids as $cid) {
        if ($cid > 0) $stmtCategoria->execute([$productoId, $cid]);
    }

    $pdo->commit();

    unset($_SESSION['form_data'], $_SESSION['form_errors']);

    error_log("Producto creado: ID $productoId - $nombre ($referencia)");

    header('Location: formulario_producto.php?success=true');
    exit();

} catch (Exception $e) {
    $pdo->rollBack();

    foreach ($imagenesGuardadas as $path) {
        if (file_exists($path)) @unlink($path);
    }
}
    if ($_ENV['APP_DEBUG'] === 'true') {
    // Entorno local: muestra el error directamente
    die("Error al procesar producto: " . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8'));
} else {
    // Producción: registra y muestra mensaje general
    error_log("Error al procesar producto: " . $e->getMessage());
    $_SESSION['form_errors'] = ['Ocurrió un error interno. Por favor, inténtalo nuevamente.'];
    header('Location: formulario_producto.php?error=internal');
    exit();
}