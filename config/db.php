<?php
// config/db.php (versión simple sin .env)

$host    = 'localhost';        // XAMPP
$db      = 'malejacalzado';    // nombre de la base que creaste en phpMyAdmin
$user    = 'root';             // usuario por defecto en XAMPP
$pass    = '';                 // contraseña vacía en XAMPP
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";

try {
    $pdo = new PDO($dsn, $user, $pass, [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ]);
} catch (Throwable $e) {
    die("Error de conexión: " . $e->getMessage());
}