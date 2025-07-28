<?php
session_start();

// Limpiar cualquier salida previa (comentarios HTML, espacios, etc.)
ob_start();

header('Content-Type: application/json');

// Configuración de errores para desarrollo (comentar en producción)
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Verificar que sea un admin autenticado
if (!isset($_SESSION['admin']) || $_SESSION['admin'] !== true) {
    http_response_code(403);
    echo json_encode(['error' => 'Acceso no autorizado']);
    exit;
}

require_once '../config/db.php';

try {
    // Validar y sanitizar prefijo
    $prefijo = strtoupper(trim($_GET['prefijo'] ?? ''));

    // Validación del prefijo: solo letras mayúsculas, entre 1 y 5 caracteres
    if (!preg_match('/^[A-Z]{1,5}$/', $prefijo)) {
        http_response_code(400);
        echo json_encode([
            'error' => 'Prefijo inválido. Solo se permiten letras mayúsculas (1-5 caracteres).'
        ]);
        exit;
    }

    // Buscar la última referencia con este prefijo
    $stmt = $pdo->prepare("
        SELECT referencia 
        FROM productos 
        WHERE referencia LIKE ? 
        ORDER BY referencia DESC 
        LIMIT 1
    ");
    
    $stmt->execute([$prefijo . '-%']);
    $ultimaReferencia = $stmt->fetchColumn();

    // Determinar el siguiente número
    $siguienteNumero = 1;

    if ($ultimaReferencia) {
        // Extraer el número de la referencia (formato: PREFIJO-NNNN)
        $partes = explode('-', $ultimaReferencia);
        
        if (count($partes) >= 2 && is_numeric($partes[1])) {
            $siguienteNumero = intval($partes[1]) + 1;
        }
        
        // Validación de seguridad: máximo 9999 productos por prefijo
        if ($siguienteNumero > 9999) {
            http_response_code(400);
            echo json_encode([
                'error' => 'Se ha alcanzado el límite máximo de productos para el prefijo ' . $prefijo
            ]);
            exit;
        }
    }

    // Generar la nueva referencia con formato PREFIJO-NNNN
    $nuevaReferencia = $prefijo . '-' . str_pad($siguienteNumero, 4, '0', STR_PAD_LEFT);

    // Verificación adicional: asegurarse de que la referencia no exista
    // (por si acaso hay concurrencia o datos inconsistentes)
    $stmtVerificar = $pdo->prepare("SELECT COUNT(*) FROM productos WHERE referencia = ?");
    $stmtVerificar->execute([$nuevaReferencia]);
    
    if ($stmtVerificar->fetchColumn() > 0) {
        // Si por alguna razón ya existe, intentar con el siguiente número
        $siguienteNumero++;
        $nuevaReferencia = $prefijo . '-' . str_pad($siguienteNumero, 4, '0', STR_PAD_LEFT);
        
        // Verificar nuevamente
        $stmtVerificar->execute([$nuevaReferencia]);
        if ($stmtVerificar->fetchColumn() > 0) {
            throw new Exception('No se pudo generar una referencia única');
        }
    }

    // Limpiar cualquier salida previa y enviar solo JSON
    ob_clean();
    
    // Respuesta exitosa
    echo json_encode([
        'referencia' => $nuevaReferencia,
        'prefijo' => $prefijo,
        'numero' => $siguienteNumero,
        'success' => true
    ]);

} catch (PDOException $e) {
    // Error de base de datos
    error_log("Error de BD en generar_referencia.php: " . $e->getMessage());
    ob_clean(); // Limpiar salida antes del JSON
    http_response_code(500);
    echo json_encode([
        'error' => 'Error de base de datos. Inténtalo más tarde.'
    ]);
    
} catch (Exception $e) {
    // Otros errores
    error_log("Error en generar_referencia.php: " . $e->getMessage());
    ob_clean(); // Limpiar salida antes del JSON
    http_response_code(500);
    echo json_encode([
        'error' => 'Error interno del servidor.'
    ]);
}
?>