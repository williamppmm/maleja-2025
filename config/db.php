<!-- config/b.php -->
<?php
// config/db.php

// 1. Cargar variables de entorno
require_once __DIR__ . '/env.php';

// 2. Leer con fallback (por si algo falta)
$host    = $_ENV['DB_HOST']     ?? 'localhost';
$db      = $_ENV['DB_NAME']     ?? 'malejacalzado';
$user    = $_ENV['DB_USER']     ?? 'root';
$pass    = $_ENV['DB_PASS']     ?? '';
$charset = $_ENV['DB_CHARSET']  ?? 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";

try {
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
    // En producción quita el mensaje detallado
    die("Error de conexión BD: " . $e->getMessage());
}