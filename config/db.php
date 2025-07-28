<?php
// config/db.php

// 1. Cargar variables de entorno
require_once __DIR__ . '/env.php';

// 2. Leer variables con fallback por si falta alguna
$host    = $_ENV['DB_HOST']     ?? 'localhost';
$db      = $_ENV['DB_NAME']     ?? 'malejacalzado';
$user    = $_ENV['DB_USER']     ?? 'root';
$pass    = $_ENV['DB_PASS']     ?? '';
$charset = $_ENV['DB_CHARSET']  ?? 'utf8mb4';

// 3. Construcción del DSN para conexión PDO
$dsn = "mysql:host=$host;dbname=$db;charset=$charset";

try {
    // 4. Crear instancia PDO con configuración segura
    $pdo = new PDO(
        $dsn,
        $user,
        $pass,
        [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ]
    );

} catch (Throwable $e) {
    // 5. Manejo de errores diferenciado según entorno
    if (isset($_ENV['APP_DEBUG']) && $_ENV['APP_DEBUG'] === 'true') {
        die("Error de conexión BD: " . $e->getMessage());
    } else {
        error_log("Error de conexión BD: " . $e->getMessage());
        die("No se pudo conectar a la base de datos.");
    }
}