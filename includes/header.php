<?php
/**
 * Header global reutilizable.
 *
 * VARIABLES esperadas (definir antes de incluir):
 *   $pageTitle        (string)
 *   $metaDescription  (string)
 *   $currentPage      (string)  one of: home | productos | nosotras | contacto
 *
 * OPCIONALES:
 *   $canonicalUrl     (string URL absoluta)  -> añade <link rel="canonical">
 *   $noIndex          (bool)                 -> añade meta robots noindex
 *   $extraHead        (string)               -> inyecta HTML adicional dentro de <head>
 *
 * EJEMPLO antes de incluir:
 *   $currentPage = 'productos';
 *   $pageTitle = 'Productos | MALEJA Calzado';
 *   include __DIR__ . '/header.php';
 */

// Defaults (si no vienen definidas)
$pageTitle        = $pageTitle        ?? 'MALEJA Calzado | Sandalias y calzado femenino en Cali';
$metaDescription  = $metaDescription  ?? 'Sandalias y calzado femenino con estilo y comodidad.';
$currentPage      = $currentPage      ?? '';
$canonicalUrl     = $canonicalUrl     ?? null;
$noIndex          = !empty($noIndex);
$extraHead        = $extraHead        ?? '';
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <!-- Charset & Viewport -->
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width,initial-scale=1.0">

  <!-- SEO principal -->
  <title><?= htmlspecialchars($pageTitle) ?></title>
  <meta name="description" content="<?= htmlspecialchars($metaDescription) ?>">
  <meta name="author" content="MALEJA Calzado">
  <?php if ($noIndex): ?>
    <meta name="robots" content="noindex,nofollow">
  <?php else: ?>
    <meta name="robots" content="index,follow">
  <?php endif; ?>
  <?php if ($canonicalUrl): ?>
    <link rel="canonical" href="<?= htmlspecialchars($canonicalUrl) ?>">
  <?php endif; ?>

  <!-- Open Graph / Social -->
  <meta property="og:site_name" content="MALEJA Calzado">
  <meta property="og:type" content="website">
  <meta property="og:title" content="<?= htmlspecialchars($pageTitle) ?>">
  <meta property="og:description" content="<?= htmlspecialchars($metaDescription) ?>">
  <meta property="og:url" content="<?= htmlspecialchars($canonicalUrl ?? 'https://calzadomaleja.co/') ?>">
  <meta property="og:image" content="https://calzadomaleja.co/assets/banners/banner.png">
  <meta name="twitter:card" content="summary_large_image">

  <!-- Favicon -->
  <link rel="icon" href="assets/images/logos/logo-basic.png" type="image/png">

  <!-- CSS global -->
  <link rel="stylesheet" href="assets/css/base.css">
  <link rel="stylesheet" href="assets/css/layout.css">
  <link rel="stylesheet" href="assets/css/components.css">
  <link rel="stylesheet" href="assets/css/components/dev-credit.css">
  <link rel="stylesheet" href="assets/css/components/modal.css">

  <!-- CSS condicional por página -->
  <?php if ($currentPage === 'home'): ?>
    <link rel="stylesheet" href="assets/css/pages/home.css">
  <?php elseif ($currentPage === 'productos'): ?>
    <link rel="stylesheet" href="assets/css/pages/productos.css">
  <?php elseif ($currentPage === 'nosotras'): ?>
    <link rel="stylesheet" href="assets/css/pages/nosotras.css">
  <?php elseif ($currentPage === 'contacto'): ?>
    <link rel="stylesheet" href="assets/css/pages/contacto.css">
  <?php endif; ?>

  <!-- Inyección adicional (scripts / estilos específicos) -->
  <?= $extraHead ?>
</head>
<body class="page-<?= htmlspecialchars($currentPage) ?>">
  <!-- Link de accesibilidad -->
  <a class="skip-link" href="#contenido-principal">Saltar al contenido</a>

  <!-- Header principal -->
  <header class="header" role="banner">
    <div class="container header__inner">
      <!-- Logo -->
      <a href="index.php"
         class="header__brand"
         aria-label="Inicio MALEJA Calzado">
        <img
          src="assets/images/logos/logo.png"
          alt="MALEJA Calzado - Logo"
          class="logo"
          width="150"
          height="auto"
          decoding="async">
      </a>

      <!-- Navegación principal -->
      <nav class="nav" aria-label="Navegación principal">
        <ul class="nav__list">
          <li>
            <a href="index.php"
               class="nav__link <?= $currentPage==='home' ? 'nav__link--active':'' ?>"
               aria-current="<?= $currentPage==='home' ? 'page':'false' ?>">
              Inicio
            </a>
          </li>
          <li>
            <a href="nosotras.php"
               class="nav__link <?= $currentPage==='nosotras' ? 'nav__link--active':'' ?>"
               aria-current="<?= $currentPage==='nosotras' ? 'page':'false' ?>">
              Nosotras
            </a>
          </li>
            <li>
            <a href="productos.php"
               class="nav__link <?= $currentPage==='productos' ? 'nav__link--active':'' ?>"
               aria-current="<?= $currentPage==='productos' ? 'page':'false' ?>">
              Productos
            </a>
          </li>
          <li>
            <a href="contacto.php"
               class="nav__link <?= $currentPage==='contacto' ? 'nav__link--active':'' ?>"
               aria-current="<?= $currentPage==='contacto' ? 'page':'false' ?>">
              Contacto
            </a>
          </li>
        </ul>
      </nav>

      <!-- Redes sociales -->
      <div class="social social--header" aria-label="Redes sociales">
        <a href="https://www.instagram.com/malejacalzado/"
           target="_blank"
           rel="noopener"
           aria-label="Instagram MALEJA Calzado">
          <img src="assets/icons/instagram.png" alt="" width="24" height="24" loading="lazy">
        </a>
        <a href="https://wa.me/573135152530"
           target="_blank"
           rel="noopener"
           aria-label="WhatsApp MALEJA Calzado">
          <img src="assets/icons/whatsapp.png" alt="" width="24" height="24" loading="lazy">
        </a>
      </div>
    </div>
  </header>

  <!-- Inicio contenido principal -->
  <main id="contenido-principal">