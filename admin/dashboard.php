<?php
session_start();
if (empty($_SESSION['admin']) || $_SESSION['admin'] !== true) {
    header('Location: login.php?error=session');
    exit;
}

/* CSRF para logout */
if (empty($_SESSION['csrf_logout'])) {
    $_SESSION['csrf_logout'] = bin2hex(random_bytes(32));
}

/* Construir paths */
$baseAdmin = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/');   // /maleja/admin
$logoutPhp = 'logout.php';
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Dashboard · MALEJA Admin</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="../assets/css/base.css">
  <link rel="stylesheet" href="../assets/css/components.css">
  <link rel="stylesheet" href="../assets/css/pages/dashboard.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body>
  <div class="dashboard-container">
    
    <!-- ========== SIDEBAR NAVEGACIÓN ========== -->
    <aside class="dash" role="navigation" aria-label="Navegación principal del panel administrativo">
      <h1>Panel MALEJA</h1>
      
      <nav>
        <ul>
          <li>
            <a href="formulario_producto.php" aria-label="Registrar nuevo producto">
              <i class="fas fa-plus" aria-hidden="true"></i> 
              <span>Registrar producto</span>
            </a>
          </li>
          <li>
            <a href="listar_productos.php" aria-label="Ver y editar productos existentes">
              <i class="fas fa-shoe-prints" aria-hidden="true"></i> 
              <span>Listar / editar productos</span>
            </a>
          </li>
          <li>
            <a href="listar_categorias.php" aria-label="Ver y editar categorías">
              <i class="fas fa-tags" aria-hidden="true"></i> 
              <span>Listar / editar categorías</span>
            </a>
          </li>
        </ul>
      </nav>

      <!-- ========== ÁREA DE ACCIONES ========== -->
      <div class="dash-actions">
        <!-- Enlace con confirmación de logout + home -->
        <a href="#" class="home-link" onclick="logoutTo('home');return false;" aria-label="Volver al sitio público">
          Volver a los zapatos más <strong>melos</strong> 😎
        </a>

        <div class="security-warning" role="alert">
          <i class="fas fa-shield-alt" aria-hidden="true"></i>
          <span>Al volver al sitio público, tu sesión se cerrará automáticamente por seguridad.</span>
        </div>

        <!-- Botón salir -->
        <a class="logout" href="#" onclick="logoutTo('login');return false;" aria-label="Cerrar sesión">
          <i class="fas fa-sign-out-alt" aria-hidden="true"></i> 
          <span>Salir</span>
        </a>
      </div>
    </aside>

    <!-- ========== CONTENIDO PRINCIPAL ========== -->
    <main class="main-content" role="main">
      <header class="content-header">
        <h2>Bienvenido al Panel Administrativo</h2>
        <p>Gestiona los productos y categorías de MALEJA Calzado desde aquí.</p>
      </header>



      <!-- ========== ACCIONES RÁPIDAS ========== -->
      <section class="quick-actions" aria-label="Acciones principales">
        <div class="actions-grid">
          <a href="formulario_producto.php" class="action-card action-card--primary">
            <div class="action-icon">
              <i class="fas fa-plus-circle"></i>
            </div>
            <div class="action-content">
              <h3>Registrar Producto</h3>
              <p>Agregar un nuevo producto al catálogo de calzado</p>
            </div>
            <div class="action-arrow">
              <i class="fas fa-chevron-right"></i>
            </div>
          </a>
          
          <a href="listar_productos.php" class="action-card action-card--secondary">
            <div class="action-icon">
              <i class="fas fa-shoe-prints"></i>
            </div>
            <div class="action-content">
              <h3>Gestionar Productos</h3>
              <p>Ver, editar y administrar todos los productos</p>
            </div>
            <div class="action-arrow">
              <i class="fas fa-chevron-right"></i>
            </div>
          </a>
          
          <a href="listar_categorias.php" class="action-card action-card--accent">
            <div class="action-icon">
              <i class="fas fa-tags"></i>
            </div>
            <div class="action-content">
              <h3>Categorías</h3>
              <p>Organizar y gestionar las categorías del catálogo</p>
            </div>
            <div class="action-arrow">
              <i class="fas fa-chevron-right"></i>
            </div>
          </a>
        </div>
        
        <!-- Info adicional -->
        <div class="dashboard-info">
          <div class="info-card">
            <i class="fas fa-info-circle"></i>
            <div>
              <h4>Sistema de Gestión MALEJA</h4>
              <p>Administra eficientemente tu catálogo de calzado desde este panel centralizado.</p>
            </div>
          </div>
        </div>
      </section>
    </main>

  </div>

  <!-- ========== FORMULARIO LOGOUT OCULTO ========== -->
  <form id="logoutForm" action="<?= htmlspecialchars($logoutPhp) ?>" method="POST" style="display:none;">
    <input type="hidden" name="csrf" value="<?= htmlspecialchars($_SESSION['csrf_logout']) ?>">
    <input type="hidden" name="redirect" value="">
  </form>

  <!-- ========== JAVASCRIPT ========== -->
  <script>
    function logoutTo(where) {
      if (where === 'home') {
        if (!confirm('¿Volver al sitio público? Tu sesión de administrador se cerrará por seguridad.')) {
          return;
        }
      }
      const form = document.getElementById('logoutForm');
      form.querySelector('input[name="redirect"]').value = (where === 'home') ? 'home' : 'login';
      form.submit();
    }

    // Mejorar accesibilidad con teclado
    document.addEventListener('keydown', function(e) {
      // Escape para foco en navegación principal
      if (e.key === 'Escape') {
        const firstNavLink = document.querySelector('.dash nav ul li:first-child a');
        if (firstNavLink) {
          firstNavLink.focus();
        }
      }
    });

    // Log de desarrollo
    if (window.location.hostname === 'localhost' || window.location.hostname.includes('dev')) {
      console.log('🎛️ Dashboard administrativo MALEJA inicializado');
    }
  </script>
</body>
</html>