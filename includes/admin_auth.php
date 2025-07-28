<?php
// /includes/admin_auth.php
session_start();

// Si no hay sesión iniciada como admin, redirige al login
if (!isset($_SESSION['admin']) || $_SESSION['admin'] !== true) {
    header("Location: ../admin/login.php");
    exit();
}