👠 MALEJA Calzado
Sitio web de catálogo para tienda de calzado femenino - MVP funcional
Página web minimalista y responsive para mostrar el catálogo de sandalias y calzado femenino de MALEJA, micro-marca caleña de calidad.
Estado actual: MVP funcional (catálogo público, filtros, modal de detalle, light-box de imágenes)
Próximo hito: Panel privado para CRUD de productos
🎯 Propósito y visión
MALEJA Calzado es una tienda online minimalista enfocada en:
ObjetivoEstado actualPróximos pasosMostrar catálogo con buena UX✅ productos.php con filtros, orden, paginación, modal y light-boxPermitir variaciones (tallas/colores)Mantener sitio ligero✅ Imágenes lazy-loaded y optimizadas (< 200 imágenes)Compresión automática en panelDiseño coherente✅ Sistema de diseño modular (oscuro + dorado)Tokens CSS en variables :rootGestión sencilla🔄 Pendiente formulario protegidoCRUD GUI + validacionesSEO y accesibilidad✅ Meta OG, alt, aria-label, enlaces "skip"Datos estructurados (JSON-LD)
🛠️ Stack tecnológico
CapaTecnologíaServidor webApache (XAMPP local / Apache/Nginx en Hostinger)BackendPHP ≥ 8.0 + PDOBase de datosMySQL / MariaDBFrontendHTML5, CSS3 (sin frameworks), JavaScript vanillaDependenciasCero dependencias externas
📁 Estructura del proyecto
maleja/
├── assets/
│   ├── css/           # base.css, layout.css, components.css, pages.css
│   ├── js/            # script.js (modal, filtros, light-box)
│   └── images/        # banners, logos, productos, nosotras
├── config/
│   ├── db.php         # Configuración PDO con variables de entorno
│   └── env.php        # Cargador de variables .env
├── includes/
│   ├── header.php     # Header global con meta OG
│   └── footer.php     # Footer global
├── auth.php           # Login (por implementar)
├── index.php          # Página principal
├── productos.php      # Catálogo con filtros y paginación
├── nosotras.php       # Sobre la empresa
├── contacto.php       # Página de contacto
└── .env               # Variables de entorno (NO versionar)
⚙️ Configuración e instalación
1. Variables de entorno
Crear archivo .env en la raíz:
bash# Local
APP_ENV=local
DB_HOST=localhost
DB_NAME=malejacalzado
DB_USER=root
DB_PASS=
DB_CHARSET=utf8mb4
bash# Producción (ejemplo)
APP_ENV=production
DB_HOST=127.0.0.1
DB_NAME=u889914626_malejacalzado
DB_USER=u889914626_williamppmm
DB_PASS=$2y$10$CIFRA_BCRYPT_HASH
APP_DEBUG=false
2. Base de datos
Estructura mínima:
sqlCREATE TABLE productos (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nombre VARCHAR(120) NOT NULL,
  referencia VARCHAR(60),
  slug VARCHAR(160) UNIQUE,
  precio DECIMAL(10,0) NOT NULL,
  descripcion_corta VARCHAR(255),
  destacado TINYINT(1) DEFAULT 0,
  orden_destacado INT,
  activo TINYINT(1) DEFAULT 1,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB CHARACTER SET utf8mb4;

CREATE TABLE producto_imagenes (
  id INT AUTO_INCREMENT PRIMARY KEY,
  producto_id INT NOT NULL,
  filename VARCHAR(255) NOT NULL,
  principal TINYINT(1) DEFAULT 0,
  FOREIGN KEY (producto_id) REFERENCES productos(id) ON DELETE CASCADE
);

CREATE TABLE admin_users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  username VARCHAR(60) UNIQUE,
  password_hash VARCHAR(255) NOT NULL,
  role ENUM('admin') DEFAULT 'admin',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
3. Instalación local
bash# 1. Clonar repositorio
git clone https://github.com/[usuario]/maleja.git
cd maleja

# 2. Configurar entorno
cp .env.example .env
# Editar .env con credenciales XAMPP

# 3. Importar base de datos
mysql -u root -p malejacalzado < database/seed.sql

# 4. Acceder al sitio
# http://localhost/maleja
⚡ Funcionalidades técnicas
Frontend (script.js)

Modal de detalle - Botón "Ver detalle" con focus trap
Filtros autosubmit - Selects/search con debounce
Light-box - Galería de imágenes con navegación por teclado
Lazy loading - IntersectionObserver para imágenes
Indicador de carga - Spinner durante búsquedas

Backend (PHP)

PDO preparado - Todas las consultas con parámetros seguros
Paginación optimizada - LIMIT/OFFSET eficiente
Filtros combinados - Búsqueda + categoría + ordenamiento
Gestión de imágenes - Fallback automático a placeholder

🚀 Despliegue
Local (XAMPP)

Copiar .env.example → .env
Importar dump SQL
Navegar a http://localhost/maleja

Producción (Hostinger)

Subir código vía Git/SFTP
Crear BD y usuario → actualizar .env
Importar tablas y datos
IMPORTANTE: Eliminar gen_hash.php

🗺️ Roadmap
✅ Fase 1 - MVP Catálogo (Actual)

 Diseño responsive con sistema modular CSS
 Catálogo con filtros, búsqueda y paginación
 Modal de productos con light-box
 Contacto WhatsApp integrado
 SEO básico y accesibilidad (WCAG AA)
 Zero dependencias externas

🔄 Fase 2 - Panel Administrativo (En desarrollo)
TareaDescripciónPrioridadLogin seguro/admin/login.php con CSRF, sesión y rate-limit🔴 AltaDashboard/admin/ listado productos (tabla, buscar, paginar)🔴 AltaCRUD productosCrear/editar/borrar con validaciones🔴 AltaUpload imágenesDrag & drop → guardar + thumbnail automático🔴 AltaToggle destacadoSwitch rápido sin recargar página🟡 MediaLog de accionesAuditoría simple de cambios🟡 Media
🚀 Fase 3 - E-commerce (Futuro)

 Carrito de compras con localStorage
 Variaciones de productos (tallas/colores)
 Sistema de pagos (PSE/Tarjetas)
 Gestión de pedidos y estados
 Inventario avanzado con alertas
 Reportes de ventas y analytics

🛡️ Buenas prácticas implementadas
Seguridad

✅ Consultas preparadas PDO
✅ password_hash + password_verify
✅ Variables de entorno para credenciales
✅ APP_DEBUG=false en producción

Performance

✅ Imágenes comprimidas (≤ 200 KB)
✅ JavaScript cargado con defer
✅ fetchpriority="high" solo para hero
✅ Lazy loading con IntersectionObserver

Accesibilidad

✅ Etiquetas aria-* en componentes interactivos
✅ Focus trap en modales
✅ Contraste verificado (WCAG AA)
✅ Navegación por teclado completa

🎨 Demo
Sitio en producción: https://calzadomaleja.co
📞 Contacto
MALEJA Calzado

📧 ventas@malejacalzado.co
📱 WhatsApp: +57 313 515 2530
📍 Cali, Colombia
📷 @malejacalzado


👨‍💻 Desarrollo
Desarrollado por: William
📱 WhatsApp: +57 315 272 8882
💼 Especializado en desarrollo web PHP y diseño responsive

📄 Licencia
Proyecto propietario de MALEJA Calzado. Código disponible para referencia educativa.

Última actualización: Julio 2025 - MVP Funcional