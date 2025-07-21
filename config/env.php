<!-- config/env.php -->
 
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