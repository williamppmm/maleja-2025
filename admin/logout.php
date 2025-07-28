<?php
declare(strict_types=1);
session_start();

/* ------- AJAX check de estado de sesión (opcional) --------------- */
if (isset($_GET['action']) && $_GET['action'] === 'check') {
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode([
        'logged_in' => !empty($_SESSION['admin']) && $_SESSION['admin'] === true
    ]);
    exit;
}

/* ------- Solo permitir POST para cerrar sesión ------------------- */
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    // Si alguien entra directo, lo mandamos al login
    header('Location: login.php?error=session', true, 303);
    exit;
}

/* ------- Validar CSRF (simple) ----------------------------------- */
$token = $_POST['csrf'] ?? '';
if (empty($_SESSION['csrf_logout']) || !hash_equals($_SESSION['csrf_logout'], $token)) {
    // Token inválido
    header('Location: login.php?error=csrf', true, 303);
    exit;
}

/* ------- Destruir sesión ----------------------------------------- */
$_SESSION = [];
if (ini_get('session.use_cookies')) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params['path'], $params['domain'],
        $params['secure'], $params['httponly']
    );
}
session_destroy();

/* ------- Redirigir según parámetro redirect ---------------------- */
$to = $_POST['redirect'] ?? 'login';

// Si es logout silencioso (desde auto-logout.js), no redirigir
if ($to === 'silent') {
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(['status' => 'logged_out']);
    exit;
}

// Redirecciones normales
$scheme    = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https://' : 'http://';
$host      = $_SERVER['HTTP_HOST'] ?? 'localhost';
$basePath  = rtrim(dirname(dirname($_SERVER['SCRIPT_NAME'] ?? '')), '/'); // sube un nivel
$homeUrl   = $scheme . $host . $basePath . '/index.php';
$loginUrl  = $scheme . $host . dirname($_SERVER['SCRIPT_NAME']) . '/login.php';

if ($to === 'home') {
    header('Location: ' . $homeUrl, true, 303);
} else {
    header('Location: ' . $loginUrl, true, 303);
}
exit;