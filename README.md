# MALEJA Calzado

**Sitio web de catálogo para tienda de calzado femenino**

Página web minimalista y responsive para mostrar el catálogo de sandalias y calzado femenino de MALEJA, micro-marca caleña de calidad.

## Estado del Proyecto

**Estado actual:** Sistema completo con panel administrativo funcional  
**Próximo hito:** Funcionalidades de e-commerce (carrito, pagos, gestión de pedidos)

## Propósito y Visión

MALEJA Calzado es una tienda online minimalista enfocada en:

| Objetivo | Estado actual | Próximos pasos |
|----------|---------------|----------------|
| Mostrar catálogo con buena UX | ✅ productos.php con filtros, orden, paginación, modal y light-box | Permitir variaciones (tallas/colores) |
| Mantener sitio ligero | ✅ Imágenes lazy-loaded y optimizadas (< 200 imágenes) | Compresión automática en panel |
| Diseño coherente | ✅ Sistema de diseño modular (oscuro + dorado) | Tokens CSS en variables :root |
| Gestión sencilla | 🔄 Pendiente formulario protegido | CRUD GUI + validaciones |
| SEO y accesibilidad | ✅ Meta OG, alt, aria-label, enlaces "skip" | Datos estructurados (JSON-LD) |

## Stack Tecnológico

| Capa | Tecnología |
|------|------------|
| Servidor web | Apache (XAMPP local / Apache/Nginx en Hostinger) |
| Backend | PHP ≥ 8.0 + PDO |
| Base de datos | MySQL / MariaDB |
| Frontend | HTML5, CSS3 (sin frameworks), JavaScript vanilla |
| Dependencias | Cero dependencias externas |

## Estructura del Proyecto

```
maleja/
├── admin/
│   ├── dashboard.php               # Panel principal administrativo
│   ├── formulario_producto.php     # Crear nuevos productos
│   ├── editar_producto.php         # Editar productos existentes
│   ├── generar_referencia.php      # Generador automático de referencias
│   ├── listar_categorias.php       # Gestión de categorías
│   ├── listar_productos.php        # Listado y administración de productos
│   ├── login.php                   # Autenticación de administradores
│   ├── logout.php                  # Cerrar sesión
│   ├── procesar_producto.php       # Procesamiento de creación de productos
│   └── procesar_edicion_producto.php # Procesamiento de edición de productos
├── assets/
│   ├── css/
│   │   ├── base.css           # Estilos base y variables CSS
│   │   ├── components.css     # Componentes reutilizables
│   │   ├── layout.css         # Layout y estructura
│   │   ├── utilities.css      # Clases utilitarias
│   │   ├── components/
│   │   │   ├── dev-credit.css # Créditos del desarrollador
│   │   │   ├── lightbox.css   # Estilos para lightbox de imágenes
│   │   │   └── modal-producto.css # Modal de detalle de productos
│   │   └── pages/
│   │       ├── contacto.css   # Página de contacto
│   │       ├── dashboard.css  # Panel administrativo
│   │       ├── home.css       # Página principal
│   │       ├── login.css      # Página de login
│   │       ├── nosotras.css   # Página nosotras
│   │       ├── productos.css  # Catálogo de productos
│   │       └── registros.css  # Páginas de registro
│   ├── js/
│   │   ├── main.js            # JavaScript principal
│   │   ├── components/
│   │   │   ├── lightbox.js    # Lightbox para galería de imágenes
│   │   │   ├── modal-producto.js # Modal de productos
│   │   │   └── product-image-zoom.js # Zoom de imágenes de productos
│   │   ├── pages/
│   │   │   ├── formulario_producto.js # Formulario de productos
│   │   │   └── login.js       # Funcionalidades de login
│   │   └── utils/
│   │       ├── auto-logout.js # Auto-logout por inactividad
│   │       ├── dev-credit.js  # Créditos del desarrollador
│   │       └── hidden-admin.js # Acceso oculto al admin
│   ├── icons/             # Iconos de redes sociales
│   └── images/
│       ├── banners/       # Imágenes de banner
│       ├── logos/         # Logotipos de la marca
│       ├── nosotras/      # Imágenes de la página nosotras
│       └── productos/     # Catálogo de imágenes de productos
├── config/
│   ├── db.php             # Configuración PDO con variables de entorno
│   └── env.php            # Cargador de variables .env
├── includes/
│   ├── admin_auth.php     # Middleware de autenticación admin
│   ├── header.php         # Header global con meta OG
│   └── footer.php         # Footer global
├── scripts/
│   └── generate_sitemap.php # Generador dinámico de sitemap XML
├── .github/
│   └── workflows/
│       └── deploy.yml     # GitHub Actions para deploy automático FTP
├── index.php              # Página principal
├── productos.php          # Catálogo con filtros y paginación
├── nosotras.php           # Sobre la empresa
├── contacto.php           # Página de contacto
├── gen_hash.php           # Generador de hash para contraseñas (desarrollo)
└── .env                   # Variables de entorno (NO versionar)
```

## Configuración e Instalación

### 1. Variables de entorno

Crear archivo `.env` en la raíz:

**Local:**
```bash
APP_ENV=local
DB_HOST=localhost
DB_NAME=malejacalzado
DB_USER=root
DB_PASS=
DB_CHARSET=utf8mb4
```

**Producción:**
```bash
APP_ENV=production
DB_HOST=127.0.0.1
DB_NAME=u889914626_malejacalzado
DB_USER=u889914626_williamppmm
DB_PASS=$2y$10$CIFRA_BCRYPT_HASH
APP_DEBUG=false
```

### 2. Base de datos

Estructura mínima:

```sql
CREATE TABLE productos (
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
```

### 3. Instalación local

```bash
# 1. Clonar repositorio
git clone https://github.com/[usuario]/maleja.git
cd maleja

# 2. Configurar entorno
cp .env.example .env
# Editar .env con credenciales XAMPP

# 3. Importar base de datos
mysql -u root -p malejacalzado < database/seed.sql

# 4. Crear usuario administrador (opcional)
# Usar gen_hash.php para generar hash de contraseña
# Insertar manualmente en tabla admin_users

# 5. Acceder al sitio
# Público: http://localhost/maleja
# Admin: http://localhost/maleja/admin/
```

## Estadísticas del Proyecto

- **Archivos PHP:** 20+ (frontend, admin y utilidades)
- **Archivos CSS:** 14 (modular y organizado por componentes/páginas)
- **Archivos JavaScript:** 9 (vanilla JS con separación de responsabilidades)
- **Commits:** 22+ (control de versiones con Git)
- **Arquitectura:** Modular con separación de responsabilidades
- **Deploy:** Automatizado con GitHub Actions (FTP)

## Funcionalidades Técnicas

### Frontend (JavaScript)

**Público:**
- **Modal de detalle** - Botón "Ver detalle" con focus trap
- **Filtros autosubmit** - Selects/search con debounce
- **Light-box** - Galería de imágenes con navegación por teclado
- **Lazy loading** - IntersectionObserver para imágenes
- **Indicador de carga** - Spinner durante búsquedas

**Administrativo:**
- **Auto-logout** - Cierre automático de sesión por inactividad
- **Formulario dinámico** - Validaciones en tiempo real para productos
- **Acceso oculto** - Método discreto para acceder al panel admin
- **Créditos del desarrollador** - Información del creador

### Backend (PHP)

**Público:**
- **PDO preparado** - Todas las consultas con parámetros seguros
- **Paginación optimizada** - LIMIT/OFFSET eficiente
- **Filtros combinados** - Búsqueda + categoría + ordenamiento
- **Gestión de imágenes** - Fallback automático a placeholder

**Administrativo:**
- **Autenticación segura** - Sistema de login con middleware de protección y CSRF
- **CRUD completo** - Crear, leer, actualizar y eliminar productos con validaciones
- **Gestión de imágenes** - Upload múltiple, procesamiento, eliminación y actualización de alt text
- **Edición avanzada** - Formulario de edición independiente con vista previa de imágenes
- **Generador de referencias** - Creación automática de códigos únicos para productos
- **Dashboard funcional** - Panel de control con estadísticas y gestión centralizada

## Despliegue

### Local (XAMPP)
1. Copiar `.env.example` → `.env`
2. Importar dump SQL
3. Crear usuario admin (usar `gen_hash.php`)
4. Navegar a `http://localhost/maleja`
5. Panel admin: `http://localhost/maleja/admin/`

### Producción (Hostinger con GitHub Actions)

**Deploy automático:**
El proyecto utiliza GitHub Actions para deploy automático vía FTP cuando se hace push a `main`.

Configuración de secretos en GitHub:
- `FTP_SERVER` - Servidor FTP de Hostinger
- `FTP_USERNAME` - Usuario FTP
- `FTP_PASSWORD` - Contraseña FTP

**Pasos manuales iniciales:**
1. Crear BD y usuario en Hostinger → actualizar `.env` en servidor
2. Importar tablas y datos iniciales
3. Crear usuario administrador en BD
4. **IMPORTANTE:** Eliminar `gen_hash.php` después del despliegue
5. Verificar acceso: `https://calzadomaleja.com`

**Archivos excluidos del deploy automático:**
- `.env` y configuraciones locales
- `assets/images/productos/` (imágenes se suben manualmente)
- Scripts de desarrollo y base de datos

## Roadmap

### ✅ Fase 1 - MVP Catálogo (Completado)

- [x] Diseño responsive con sistema modular CSS
- [x] Catálogo con filtros, búsqueda y paginación
- [x] Modal de productos con light-box
- [x] Contacto WhatsApp integrado
- [x] SEO básico y accesibilidad (WCAG AA)
- [x] Zero dependencias externas

### ✅ Fase 2 - Panel Administrativo (Completado)

- [x] Login seguro con autenticación, CSRF y middleware de protección
- [x] Dashboard administrativo con estadísticas y gestión
- [x] CRUD completo de productos con validaciones robustas
- [x] Formulario independiente para editar productos con vista previa
- [x] Upload múltiple de imágenes con procesamiento y validación
- [x] Gestión de imágenes: eliminar, reordenar y editar alt text
- [x] Generador automático de referencias de productos
- [x] Gestión de categorías y listados administrativos
- [x] Auto-logout por inactividad y acceso discreto al admin
- [x] Interfaz administrativa responsive y funcional
- [x] Deploy automático con GitHub Actions (FTP)
- [x] Generador dinámico de sitemap XML para SEO

### 🔄 Fase 3 - Optimizaciones y Mejoras (En evaluación)

| Tarea | Descripción | Prioridad |
|-------|-------------|-----------|
| Compresión automática | Optimización automática de imágenes en upload | 🟡 Media |
| Datos estructurados | Implementar JSON-LD para SEO avanzado (Schema.org) | 🟡 Media |
| Log de auditoría | Sistema de logs para cambios administrativos | 🟡 Media |
| Backup automatizado | Sistema de respaldos automáticos de BD | 🟡 Media |
| Variaciones de producto | Tallas y colores como opciones de producto | 🟢 Baja |
| PWA | Convertir en Progressive Web App | 🟢 Baja |

### 🚀 Fase 4 - E-commerce (Futuro)

- [ ] Carrito de compras con localStorage
- [ ] Sistema de pagos (PSE/Tarjetas/Nequi)
- [ ] Gestión de pedidos y estados
- [ ] Inventario avanzado con alertas de stock
- [ ] Sistema de cupones y descuentos
- [ ] Reportes de ventas y analytics
- [ ] Notificaciones por email/WhatsApp
- [ ] Integración con redes sociales para marketing

## Buenas Prácticas Implementadas

### Seguridad
- ✅ Consultas preparadas PDO
- ✅ `password_hash` + `password_verify`
- ✅ Variables de entorno para credenciales
- ✅ `APP_DEBUG=false` en producción

### Performance
- ✅ Imágenes comprimidas (≤ 200 KB)
- ✅ JavaScript cargado con defer
- ✅ `fetchpriority="high"` solo para hero
- ✅ Lazy loading con IntersectionObserver

### Accesibilidad
- ✅ Etiquetas `aria-*` en componentes interactivos
- ✅ Focus trap en modales
- ✅ Contraste verificado (WCAG AA)
- ✅ Navegación por teclado completa

## Demo

**Sitio en producción:** https://calzadomaleja.com

## Contacto

### MALEJA Calzado
- **Email:** ventas@malejacalzado.com
- **WhatsApp:** +57 317 270 3742
- **Ubicación:** Cali, Colombia
- **Instagram:** @malejacalzado

### Desarrollo
- **Desarrollador:** William
- **WhatsApp:** +57 315 272 8882
- **Especialización:** Desarrollo web PHP y diseño responsive

## Licencia

Proyecto propietario de MALEJA Calzado. Código disponible para referencia educativa.

---

**Última actualización:** Octubre 2025 - Sistema completo con panel administrativo, edición avanzada y deploy automático
