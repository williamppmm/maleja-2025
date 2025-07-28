<?php
declare(strict_types=1);

// Endurecer cookies ANTES de iniciar sesión
ini_set('session.cookie_httponly', '1');
ini_set('session.cookie_secure', isset($_SERVER['HTTPS']) ? '1' : '0');
ini_set('session.cookie_samesite', 'Strict');

session_start();

/* -------------------- CONFIG INICIAL ---------------------------- */
require_once __DIR__ . '/../config/db.php'; // $pdo disponible

// Evitar cachear esta página
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
header('Pragma: no-cache');

/* -------------------- SESIÓN YA ACTIVA -------------------------- */
if (!empty($_SESSION['admin']) && $_SESSION['admin'] === true) {
  header('Location: dashboard.php', true, 303);
  exit;
}

/* -------------------- CSRF TOKEN (GET) -------------------------- */
if (empty($_SESSION['csrf_login'])) {
  $_SESSION['csrf_login'] = bin2hex(random_bytes(32));
}

/* -------------------- RATE LIMIT SIMPLE ------------------------- */
if (!isset($_SESSION['login_attempts'])) {
  $_SESSION['login_attempts'] = 0;
  $_SESSION['login_last_try'] = 0;
}
$LOCK_SECONDS = 15;
$MAX_ATTEMPTS = 10;

/* -------------------- MANEJO POST (LOGIN) ----------------------- */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $now = time();

  if ($_SESSION['login_attempts'] >= $MAX_ATTEMPTS && ($now - $_SESSION['login_last_try']) < $LOCK_SECONDS) {
    header('Location: login.php?error=locked', true, 303);
    exit;
  }

  $csrf = $_POST['csrf'] ?? '';
  if (!hash_equals($_SESSION['csrf_login'], $csrf)) {
    header('Location: login.php?error=csrf', true, 303);
    exit;
  }

  $username = trim($_POST['username'] ?? '');
  $password = $_POST['password'] ?? '';

  if ($username === '' || $password === '') {
    header('Location: login.php?error=empty', true, 303);
    exit;
  }

  try {
    $stmt = $pdo->prepare('SELECT password_hash FROM admin_users WHERE username = ? LIMIT 1');
    $stmt->execute([$username]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    $ok = $user && password_verify($password, $user['password_hash']);

    if ($ok) {
      $_SESSION['login_attempts'] = 0;
      $_SESSION['login_last_try'] = 0;

      session_regenerate_id(true);
      $_SESSION['admin'] = true;

      unset($_SESSION['csrf_login']);

      header('Location: dashboard.php', true, 303);
      exit;
    }

    $_SESSION['login_attempts']++;
    $_SESSION['login_last_try'] = $now;
    header('Location: login.php?error=invalid', true, 303);
    exit;
  } catch (Throwable $e) {
    if ($_ENV['APP_DEBUG'] === 'true') {
      die("Error interno en login: " . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8'));
    } else {
      error_log('Error en login: ' . $e->getMessage());
      header('Location: login.php?error=system', true, 303);
      exit;
    }
  }
}

/* -------------------- MENSAJES DE ERROR ------------------------- */
$errors = [
  'empty'   => 'Por favor completa todos los campos.',
  'invalid' => 'Usuario o contraseña incorrectos.',
  'system'  => 'Error del sistema. Intenta más tarde.',
  'session' => 'Tu sesión ha expirado. Inicia sesión de nuevo.',
  'csrf'    => 'Token inválido, recarga la página e inténtalo de nuevo.',
  'locked'  => 'Demasiados intentos. Espera unos segundos y vuelve a intentar.'
];
$errorMessage = $errors[$_GET['error'] ?? ''] ?? '';

/* -------------------- URL HOME PÚBLICO -------------------------- */
$scheme   = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https://' : 'http://';
$host     = $_SERVER['HTTP_HOST'] ?? 'localhost';
$basePath = rtrim(dirname(dirname($_SERVER['SCRIPT_NAME'] ?? '')), '/');
$homeLink = $scheme . $host . $basePath . '/index.php';

$usernamePost = htmlspecialchars($_POST['username'] ?? '', ENT_QUOTES, 'UTF-8');
$csrfToken    = $_SESSION['csrf_login'];
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Panel Administrativo | MALEJA Calzado</title>
  <link rel="stylesheet" href="../assets/css/pages/login.css">
</head>
<body>
  <div class="login-container">

    <header class="login-header">
      <h1>Panel Administrativo</h1>
      <p>MALEJA&nbsp;Calzado</p>
    </header>

    <?php if ($errorMessage): ?>
      <div class="error-message" role="alert"><?= htmlspecialchars($errorMessage, ENT_QUOTES, 'UTF-8') ?></div>
    <?php endif; ?>

    <form action="login.php" method="POST" id="loginForm" novalidate>
      <input type="hidden" name="csrf" value="<?= $csrfToken ?>">

      <div class="form-group">
        <label for="username">Usuario</label>
        <input
          type="text"
          id="username"
          name="username"
          required
          autocomplete="username"
          value="<?= $usernamePost ?>"
        >
      </div>

      <div class="form-group">
        <label for="password">Contraseña</label>
        <div class="password-wrapper">
          <input
            type="password"
            id="password"
            name="password"
            required
            autocomplete="current-password"
          >
          <button type="button" id="togglePassword" class="password-toggle" aria-label="Mostrar contraseña">👁️</button>
        </div>
      </div>

      <button type="submit" id="submitBtn" class="submit-btn">
        <span class="btn-text">Iniciar Sesión</span>
        <div class="loading"></div>
      </button>
    </form>

    <p class="back-home">
      <a href="<?= $homeLink ?>">Volver a los zapatos más <strong>melos</strong> 😎</a>
    </p>

    <footer class="brand-footer">
      Sistema de gestión de productos
    </footer>
  </div>

  <script src="../assets/js/pages/login.js" defer></script>
</body>
</html>

