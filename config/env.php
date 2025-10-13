<?php
/**
 * Carga variables desde un archivo .env (si existe) y las expone en $_ENV
 * Formato: CLAVE=valor (sin espacios alrededor del "=")
 */
$envFile = __DIR__ . '/../.env'; // ../ para ir a la raíz del proyecto

if (is_readable($envFile)) {
    $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        // Ignorar comentarios
        if (str_starts_with(trim($line), '#')) continue;
        if (!str_contains($line, '=')) continue;

        [$key, $value] = explode('=', $line, 2);
        $key   = trim($key);
        $value = trim($value);

        // Quitar comillas envolventes si las hay
        $value = preg_replace('/^([\'"])(.*)\1$/', '$2', $value);

        if ($key !== '') {
            $_ENV[$key] = $value;
            putenv("$key=$value");
        }
    }
}

// Definir constantes de aplicación convenientes
if (!isset($APP_URL) || !$APP_URL) {
    $envAppUrl = $_ENV['APP_URL'] ?? getenv('APP_URL') ?: '';
    if ($envAppUrl !== '') {
        $APP_URL = rtrim($envAppUrl, '/');
    } else {
        $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https://' : 'http://';
        $host   = $_SERVER['HTTP_HOST'] ?? 'localhost';
        $APP_URL = rtrim($scheme . $host, '/');
    }
}
