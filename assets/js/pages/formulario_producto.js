// ========================================
// CONSTANTES Y CONFIGURACIÓN
// ========================================
const CONFIG = {
  INACTIVITY_TIMEOUT: 15 * 60 * 1000, // 15 minutos
  INACTIVITY_WARNING: 30 * 1000,      // 30 segundos de aviso
  MAX_IMAGE_SIZE: 5 * 1024 * 1024,    // 5MB
  ALLOWED_IMAGE_TYPES: ['image/jpeg', 'image/png', 'image/webp'],
  SLUG_MAX_LENGTH: 140,
  NOMBRE_MAX_LENGTH: 120,
  DESCRIPCION_CORTA_MAX_LENGTH: 255,
  REFERENCIA_MIN_LENGTH: 2,
  REFERENCIA_MAX_LENGTH: 50
};

// ========================================
// GESTIÓN DE INACTIVIDAD
// ========================================
class InactivityManager {
  constructor() {
    this.timer = null;
    this.warningTimer = null;
    this.init();
  }

  init() {
    this.resetTimer();
    ['mousemove', 'keydown', 'click', 'scroll', 'touchstart'].forEach(event => {
      document.addEventListener(event, () => this.resetTimer(), { passive: true });
    });
  }

  resetTimer() {
    this.clearTimers();
    this.timer = setTimeout(() => this.showWarning(), CONFIG.INACTIVITY_TIMEOUT);
  }

  showWarning() {
    alert('Tu sesión se cerrará por inactividad en 30 segundos.');
    this.warningTimer = setTimeout(() => {
      window.location.href = 'logout.php';
    }, CONFIG.INACTIVITY_WARNING);
  }

  clearTimers() {
    if (this.timer) clearTimeout(this.timer);
    if (this.warningTimer) clearTimeout(this.warningTimer);
  }
}

// ========================================
// UTILIDADES UI
// ========================================
class UIUtils {
  static mostrarError(elemento, mensaje) {
    if (!elemento) return;
    
    elemento.classList.add('error');
    
    let errorDiv = elemento.nextElementSibling;
    if (!errorDiv?.classList.contains('field-error')) {
      errorDiv = document.createElement('div');
      errorDiv.className = 'field-error';
      elemento.parentNode.insertBefore(errorDiv, elemento.nextSibling);
    }
    
    errorDiv.innerHTML = `<i class="fas fa-exclamation-circle"></i> ${mensaje}`;
  }

  static limpiarError(elemento) {
    if (!elemento) return;
    
    elemento.classList.remove('error');
    
    const errorDiv = elemento.nextElementSibling;
    if (errorDiv?.classList.contains('field-error')) {
      errorDiv.remove();
    }
  }

  static toggleLoading(show) {
    const btnSubmit = document.getElementById('btn-submit');
    if (!btnSubmit) return;
    
    const btnText = btnSubmit.querySelector('.btn-text');
    const loading = btnSubmit.querySelector('.loading');
    
    if (btnText && loading) {
      btnText.style.display = show ? 'none' : 'inline-block';
      loading.style.display = show ? 'inline-block' : 'none';
      btnSubmit.disabled = show;
    }
  }

  static actualizarContadorCaracteres(input, contadorId, maxLength) {
    const contador = document.getElementById(contadorId);
    if (!contador || !input) return;

    const actual = input.value.length;
    contador.textContent = `${actual}/${maxLength}`;
    contador.style.color = actual > maxLength ? '#e74c3c' : '#666';
  }
}

// ========================================
// GENERACIÓN Y VALIDACIÓN DE SLUG
// ========================================
class SlugManager {
  static generar(texto) {
    if (!texto) return '';

    return texto.toLowerCase()
      .normalize('NFD').replace(/[\u0300-\u036f]/g, '') // Quita acentos
      .replace(/[^a-z0-9\s-]/g, '') // Elimina símbolos
      .replace(/\s+/g, '-') // Espacios → guiones
      .replace(/-+/g, '-') // Guiones repetidos → uno solo
      .replace(/^-+|-+$/g, '') // Quita guiones extremos
      .substring(0, CONFIG.SLUG_MAX_LENGTH);
  }

  static validar(slug) {
    return /^[a-z0-9-]+$/.test(slug) && slug.length <= CONFIG.SLUG_MAX_LENGTH;
  }

  static configurarAutogeneracion(nombreInput, slugInput) {
    if (!nombreInput || !slugInput) return;

    // Inicializar el data attribute si no existe
    if (!slugInput.dataset.generated) {
      slugInput.dataset.generated = slugInput.value ? 'false' : 'true';
    }

    // Cuando el usuario escribe en el nombre...
    nombreInput.addEventListener('input', function() {
      // Solo autogenerar si el slug está vacío o fue generado automáticamente
      if (!slugInput.value || slugInput.dataset.generated === 'true') {
        const slug = SlugManager.generar(this.value);
        slugInput.value = slug;
        slugInput.dataset.generated = 'true';
      }
    });

    // Si el usuario edita manualmente el slug...
    slugInput.addEventListener('input', function() {
      this.dataset.generated = 'false';
    });

    // Validación del slug solo al perder el foco
    slugInput.addEventListener('blur', function() {
      if (this.value && !SlugManager.validar(this.value)) {
        UIUtils.mostrarError(this, 'El slug solo puede contener letras minúsculas, números y guiones');
      } else {
        UIUtils.limpiarError(this);
      }
    });
  }
}

// ========================================
// GENERACIÓN DE REFERENCIAS CON BD REAL
// ========================================
class ReferenceManager {
  constructor() {
    this.isGenerating = false;
    this.lastGeneratedPrefix = '';
    this.debounceTimeout = null;
  }

  static generarPrefijo(nombre) {
    if (!nombre || typeof nombre !== 'string') return '';
    
    // Normalizar el texto: quitar acentos, símbolos y espacios extra
    const textoLimpio = nombre
      .normalize("NFD")
      .replace(/[\u0300-\u036f]/g, '') // Quitar acentos
      .replace(/[^a-zA-Z\s]/g, '') // Solo letras y espacios
      .trim()
      .replace(/\s+/g, ' '); // Espacios múltiples → uno solo

    if (!textoLimpio) return '';

    // Dividir en palabras y filtrar palabras significativas (>2 caracteres)
    const palabras = textoLimpio
      .split(' ')
      .filter(palabra => palabra.length > 2) // Filtrar palabras muy cortas
      .slice(0, 3); // Máximo 3 palabras

    // Si no hay palabras significativas, tomar las primeras 3 palabras
    if (palabras.length === 0) {
      const todasPalabras = textoLimpio.split(' ').slice(0, 3);
      return todasPalabras.map(p => p.charAt(0).toUpperCase()).join('');
    }

    // Generar prefijo con las iniciales
    return palabras.map(palabra => palabra.charAt(0).toUpperCase()).join('');
  }

  async obtenerSiguienteReferencia(prefijo) {
    if (!prefijo || this.isGenerating) return null;

    this.isGenerating = true;
    console.log('🔍 Consultando referencia para prefijo:', prefijo);

    try {
      // URL correcta según tu estructura
      const url = `generar_referencia.php?prefijo=${encodeURIComponent(prefijo)}`;
      console.log('📡 URL de consulta:', url);
      
      const response = await fetch(url, {
        method: 'GET',
        headers: {
          'Accept': 'application/json',
          'X-Requested-With': 'XMLHttpRequest'
        },
        credentials: 'same-origin'
      });

      console.log('📊 Status de respuesta:', response.status);
      console.log('📋 Content-Type:', response.headers.get('content-type'));

      // Obtener el texto crudo de la respuesta para debugging
      const responseText = await response.text();
      console.log('📄 Respuesta cruda (primeros 200 chars):', responseText.substring(0, 200));

      // Verificar si es HTML (indica error/redirección)
      if (responseText.trim().startsWith('<') || responseText.includes('<!DOCTYPE')) {
        console.error('❌ La respuesta es HTML, no JSON. Posibles causas:');
        console.error('   - Sesión expirada (redirección a login)');
        console.error('   - Error de PHP (página de error)');
        console.error('   - Ruta incorrecta del archivo');
        throw new Error('El servidor devolvió HTML en lugar de JSON');
      }

      // Intentar parsear como JSON
      let data;
      try {
        data = JSON.parse(responseText);
      } catch (parseError) {
        console.error('❌ Error al parsear JSON:', parseError);
        throw new Error('Respuesta del servidor no es JSON válido');
      }

      if (!response.ok) {
        throw new Error(data.error || `HTTP ${response.status}`);
      }

      if (data.error) {
        console.error('❌ Error del servidor:', data.error);
        throw new Error(data.error);
      }

      console.log('✅ Referencia generada exitosamente:', data.referencia);
      return data.referencia;
      
    } catch (error) {
      console.error('💥 Error completo:', error);
      
      // En caso de error, generar referencia temporal local más inteligente
      console.warn('⚠️ Generando referencia temporal local como fallback');
      const numeroTemporal = Date.now() % 10000; // Usar timestamp para reducir duplicados
      const referenciaFallback = prefijo + '-' + numeroTemporal.toString().padStart(4, '0');
      console.log('🔄 Referencia temporal:', referenciaFallback);
      
      // Mostrar advertencia al usuario
      const referenciaInput = document.getElementById('referencia');
      if (referenciaInput) {
        UIUtils.mostrarError(referenciaInput, 'Error BD: usando referencia temporal');
      }
      
      return referenciaFallback;
      
    } finally {
      this.isGenerating = false;
    }
  }

  async generarReferenciaAutomatica(nombre, referenciaInput) {
    if (!nombre || !referenciaInput) return;

    const prefijo = ReferenceManager.generarPrefijo(nombre);
    
    if (!prefijo) {
      console.warn('⚠️ No se pudo generar prefijo desde el nombre:', nombre);
      return;
    }

    // Si es el mismo prefijo que la última vez, no regenerar
    if (prefijo === this.lastGeneratedPrefix && referenciaInput.value) {
      return;
    }

    this.lastGeneratedPrefix = prefijo;

    // Indicadores visuales
    const originalPlaceholder = referenciaInput.placeholder;
    referenciaInput.placeholder = 'Consultando BD...';
    referenciaInput.style.background = '#f8f9fa';

    // Limpiar errores previos
    UIUtils.limpiarError(referenciaInput);

    try {
      console.log('🚀 Iniciando generación de referencia para:', nombre);
      
      const nuevaReferencia = await this.obtenerSiguienteReferencia(prefijo);
      
      if (nuevaReferencia) {
        referenciaInput.value = nuevaReferencia;
        referenciaInput.dataset.generated = 'true';
        
        // Efecto visual de éxito
        referenciaInput.style.background = '#d4edda';
        setTimeout(() => {
          referenciaInput.style.background = '';
        }, 1500);
        
        console.log('✅ Referencia asignada al campo:', nuevaReferencia);
      }
      
    } catch (error) {
      console.error('💥 Error final en generarReferenciaAutomatica:', error);
    } finally {
      // Restaurar estado
      referenciaInput.placeholder = originalPlaceholder;
    }
  }

  configurarAutogeneracion(nombreInput, referenciaInput) {
    if (!nombreInput || !referenciaInput) return;

    // Inicializar el data attribute si no existe
    if (!referenciaInput.dataset.generated) {
      referenciaInput.dataset.generated = referenciaInput.value ? 'false' : 'true';
    }

    // Cuando el usuario escribe en el nombre...
    nombreInput.addEventListener('input', () => {
      // Limpiar timeout anterior para debounce
      if (this.debounceTimeout) {
        clearTimeout(this.debounceTimeout);
      }

      const nombre = nombreInput.value.trim();
      
      // Solo generar si:
      // 1. El campo referencia está vacío O fue generado automáticamente
      // 2. El nombre tiene al menos 3 caracteres
      const debeGenerar = (!referenciaInput.value || referenciaInput.dataset.generated === 'true') 
                         && nombre.length >= 3;

      if (debeGenerar) {
        // Debounce de 1000ms para evitar muchas consultas
        this.debounceTimeout = setTimeout(() => {
          this.generarReferenciaAutomatica(nombre, referenciaInput);
        }, 1000);
      }
    });

    // Si el usuario edita manualmente la referencia...
    referenciaInput.addEventListener('input', function() {
      this.dataset.generated = 'false';
      UIUtils.limpiarError(this);
    });

    // Botón para regenerar manualmente
    this.agregarBotonRegenerar(nombreInput, referenciaInput);
  }

  agregarBotonRegenerar(nombreInput, referenciaInput) {
    const formGroup = referenciaInput.closest('.form-group');
    if (!formGroup) return;

    const btnRegenerar = document.createElement('button');
    btnRegenerar.type = 'button';
    btnRegenerar.className = 'btn-regenerar-ref';
    btnRegenerar.innerHTML = '<i class="fas fa-sync-alt"></i>';
    btnRegenerar.title = 'Regenerar referencia desde base de datos';
    
    // Estilos inline
    Object.assign(btnRegenerar.style, {
      position: 'absolute',
      right: '8px',
      top: '50%',
      transform: 'translateY(-50%)',
      background: '#007bff',
      color: 'white',
      border: 'none',
      borderRadius: '4px',
      padding: '6px 8px',
      cursor: 'pointer',
      fontSize: '12px',
      zIndex: '10'
    });

    // Hacer el contenedor relativo
    const referenciaContainer = referenciaInput.parentNode;
    referenciaContainer.style.position = 'relative';
    referenciaInput.style.paddingRight = '40px';

    referenciaContainer.appendChild(btnRegenerar);

    // Funcionalidad del botón
    btnRegenerar.addEventListener('click', async () => {
      const nombre = nombreInput.value.trim();
      
      if (!nombre) {
        alert('Ingresa un nombre para el producto primero');
        nombreInput.focus();
        return;
      }

      console.log('🔄 Regeneración manual solicitada');
      
      // Forzar regeneración
      referenciaInput.value = '';
      referenciaInput.dataset.generated = 'true';
      
      await this.generarReferenciaAutomatica(nombre, referenciaInput);
    });

    // Efectos hover
    btnRegenerar.addEventListener('mouseenter', () => {
      btnRegenerar.style.background = '#0056b3';
    });

    btnRegenerar.addEventListener('mouseleave', () => {
      btnRegenerar.style.background = '#007bff';
    });
  }
}

// ========================================
// MANEJO DE IMÁGENES
// ========================================
class ImageManager {
  static crearPreview(file, containerId) {
    const container = document.getElementById(containerId);
    if (!container) return false;

    // Validar tipo de archivo
    if (!CONFIG.ALLOWED_IMAGE_TYPES.includes(file.type)) {
      container.innerHTML = `
        <div class="error-preview">
          <i class="fas fa-exclamation-triangle"></i>
          <div>Tipo de archivo no permitido. Formatos válidos: JPG, PNG, WebP</div>
        </div>`;
      return false;
    }

    // Validar tamaño
    if (file.size > CONFIG.MAX_IMAGE_SIZE) {
      const maxSizeMB = (CONFIG.MAX_IMAGE_SIZE / 1024 / 1024).toFixed(1);
      container.innerHTML = `
        <div class="error-preview">
          <i class="fas fa-exclamation-triangle"></i>
          <div>Imagen demasiado grande (máx. ${maxSizeMB}MB)</div>
        </div>`;
      return false;
    }

    const reader = new FileReader();
    reader.onload = (e) => {
      container.innerHTML = `
        <div class="image-preview">
          <img src="${e.target.result}" alt="Vista previa" loading="lazy">
          <div class="image-info">
            <span>${file.name}</span>
            <span>${(file.size / 1024 / 1024).toFixed(2)} MB</span>
          </div>
          <button type="button" class="remove-image" onclick="ImageManager.removerPreview('${containerId}')">
            <i class="fas fa-times"></i>
          </button>
        </div>`;
    };
    
    reader.onerror = () => {
      container.innerHTML = `
        <div class="error-preview">
          <i class="fas fa-exclamation-triangle"></i>
          <div>Error al cargar la imagen</div>
        </div>`;
    };
    
    reader.readAsDataURL(file);
    return true;
  }

  static removerPreview(containerId) {
    const container = document.getElementById(containerId);
    const fileInput = container?.closest('.image-upload-item')?.querySelector('input[type="file"]');
    
    if (container) container.innerHTML = '';
    if (fileInput) fileInput.value = '';
  }
}

// ========================================
// VALIDADORES
// ========================================
class Validators {
  static nombre(valor) {
    const trimmed = valor?.trim() || '';
    return {
      valido: trimmed.length > 0 && trimmed.length <= CONFIG.NOMBRE_MAX_LENGTH,
      mensaje: `El nombre debe tener entre 1 y ${CONFIG.NOMBRE_MAX_LENGTH} caracteres`
    };
  }

  static referencia(valor) {
    const trimmed = valor?.trim() || '';
    const regex = /^[A-Z0-9-]+$/;
    const valido = regex.test(trimmed) && 
                   trimmed.length >= CONFIG.REFERENCIA_MIN_LENGTH && 
                   trimmed.length <= CONFIG.REFERENCIA_MAX_LENGTH;
    
    return {
      valido,
      mensaje: `Solo letras mayúsculas, números y guiones (${CONFIG.REFERENCIA_MIN_LENGTH}-${CONFIG.REFERENCIA_MAX_LENGTH} caracteres)`
    };
  }

  static precio(valor) {
    const precio = parseFloat(valor) || 0;
    return {
      valido: precio > 0 && precio <= 999999999,
      mensaje: 'El precio debe ser un número mayor a 0'
    };
  }

  static stock(valor) {
    if (valor === '' || valor === null || valor === undefined) return { valido: true };
    const stock = parseInt(valor);
    return {
      valido: !isNaN(stock) && stock >= 0,
      mensaje: 'El stock debe ser un número positivo o estar vacío'
    };
  }

  static categorias() {
    const seleccionadas = document.querySelectorAll('input[name="categorias[]"]:checked').length;
    return {
      valido: seleccionadas > 0,
      mensaje: 'Debes seleccionar al menos una categoría'
    };
  }

  static imagenPrincipal() {
    const input = document.getElementById('imagen_principal');
    return {
      valido: input?.files && input.files[0],
      mensaje: 'La imagen principal es obligatoria'
    };
  }
}

// ========================================
// FORMULARIO PRINCIPAL
// ========================================
class ProductForm {
  constructor() {
    this.form = document.getElementById('formulario-producto');
    this.formModified = false;
    this.elementos = {};
    this.isSubmitting = false;
    this.referenceManager = new ReferenceManager(); // ✅ Instanciar el manager
    this.init();
  }

  init() {
    if (!this.form) return;
    
    this.obtenerElementos();
    this.configurarEventos();
    this.configurarPrevencionPerdidaDatos();
  }

  obtenerElementos() {
    this.elementos = {
      nombre: document.getElementById('nombre'),
      slug: document.getElementById('slug'),
      referencia: document.getElementById('referencia'),
      precio: document.getElementById('precio'),
      stock: document.getElementById('stock'),
      destacado: document.getElementById('destacado'),
      ordenDestacado: document.getElementById('orden_destacado'),
      descripcionCorta: document.getElementById('descripcion_corta'),
      imagenPrincipal: document.getElementById('imagen_principal')
    };
  }

  configurarEventos() {
    this.configurarSlug();              // ✅ Funcionalidad del slug
    this.configurarReferencia();        // ✅ Funcionalidad de referencia
    this.configurarReferenciaValidacion();
    this.configurarPrecioYStock();
    this.configurarDestacado();
    this.configurarDescripcionCorta();
    this.configurarImagenes();
    this.configurarArchivos();
    this.configurarCategorias();
    this.configurarEnvio();
  }

  configurarSlug() {
    const { nombre, slug } = this.elementos;
    if (nombre && slug) {
      // ✅ Mantener la funcionalidad original del slug
      SlugManager.configurarAutogeneracion(nombre, slug);
    }
  }

  configurarReferencia() {
    const { nombre, referencia } = this.elementos;
    if (nombre && referencia) {
      // ✅ Nueva funcionalidad de referencia con BD
      this.referenceManager.configurarAutogeneracion(nombre, referencia);
    }
  }

  configurarReferenciaValidacion() {
    const { referencia } = this.elementos;
    if (!referencia) return;

    // Convertir a mayúsculas solo si no fue generada automáticamente
    referencia.addEventListener('input', function() {
      if (this.dataset.generated !== 'true') {
        this.value = this.value.toUpperCase();
      }
    });

    // Validación al perder el foco
    referencia.addEventListener('blur', function() {
      const validacion = Validators.referencia(this.value);
      if (!validacion.valido) {
        UIUtils.mostrarError(this, validacion.mensaje);
      } else {
        UIUtils.limpiarError(this);
      }
    });
  }

  configurarPrecioYStock() {
    const { precio, stock } = this.elementos;

    if (precio) {
      precio.addEventListener('blur', function() {
        const validacion = Validators.precio(this.value);
        if (!validacion.valido) {
          UIUtils.mostrarError(this, validacion.mensaje);
        } else {
          UIUtils.limpiarError(this);
        }
      });
    }

    if (stock) {
      stock.addEventListener('blur', function() {
        const validacion = Validators.stock(this.value);
        if (!validacion.valido) {
          UIUtils.mostrarError(this, validacion.mensaje);
        } else {
          UIUtils.limpiarError(this);
        }
      });
    }
  }

  configurarDestacado() {
    const { destacado, ordenDestacado } = this.elementos;
    if (!destacado || !ordenDestacado) return;

    destacado.addEventListener('change', function() {
      ordenDestacado.disabled = !this.checked;
      ordenDestacado.required = this.checked;
      
      if (this.checked && (!ordenDestacado.value || ordenDestacado.value === '0')) {
        ordenDestacado.value = '1';
      }
    });
  }

  configurarDescripcionCorta() {
    const { descripcionCorta } = this.elementos;
    if (!descripcionCorta) return;

    const actualizarContador = () => {
      UIUtils.actualizarContadorCaracteres(
        descripcionCorta, 
        'contador-corta', 
        CONFIG.DESCRIPCION_CORTA_MAX_LENGTH
      );
    };

    descripcionCorta.addEventListener('input', actualizarContador);
    actualizarContador(); // Inicializar
  }

  configurarImagenes() {
    const { imagenPrincipal } = this.elementos;
    if (!imagenPrincipal) return;

    imagenPrincipal.addEventListener('change', function() {
      if (this.files && this.files[0]) {
        if (!ImageManager.crearPreview(this.files[0], 'preview-0')) {
          this.value = '';
        }
      }
    });
  }

  configurarArchivos() {
    document.querySelectorAll('.file-upload-wrapper input[type="file"]').forEach(input => {
      input.addEventListener('change', function() {
        const label = this.parentNode.querySelector('.file-upload-label');
        if (label) {
          if (this.files && this.files.length > 0) {
            label.textContent = this.files[0].name;
          } else {
            label.textContent = 'Ningún archivo seleccionado';
          }
        }
      });
    });
  }

  configurarCategorias() {
    document.querySelectorAll('input[name="categorias[]"]').forEach(checkbox => {
      checkbox.addEventListener('change', function() {
        const errorDiv = document.getElementById('error-categorias');
        if (errorDiv) errorDiv.style.display = 'none';
      });
    });
  }

  configurarEnvio() {
    this.form.addEventListener('submit', (e) => this.manejarEnvio(e));
  }

  manejarEnvio(e) {
    e.preventDefault();
    
    if (this.isSubmitting) return false;
    
    const validaciones = this.validarFormulario();
    
    if (validaciones.length > 0) {
      alert('Por favor corrige los siguientes errores:\n\n• ' + validaciones.join('\n• '));
      return false;
    }

    this.isSubmitting = true;
    UIUtils.toggleLoading(true);
    this.formModified = false;
    
    setTimeout(() => {
      this.form.submit();
    }, 100);
  }

  validarFormulario() {
    const errores = [];
    const { nombre, referencia, precio, stock } = this.elementos;

    if (nombre) {
      const validacionNombre = Validators.nombre(nombre.value);
      if (!validacionNombre.valido) {
        errores.push(validacionNombre.mensaje);
        if (errores.length === 1) nombre.focus();
      }
    }

    if (referencia) {
      const validacionReferencia = Validators.referencia(referencia.value);
      if (!validacionReferencia.valido) {
        errores.push(validacionReferencia.mensaje);
      }
    }

    if (precio) {
      const validacionPrecio = Validators.precio(precio.value);
      if (!validacionPrecio.valido) {
        errores.push(validacionPrecio.mensaje);
      }
    }

    if (stock) {
      const validacionStock = Validators.stock(stock.value);
      if (!validacionStock.valido) {
        errores.push(validacionStock.mensaje);
      }
    }

    const validacionCategorias = Validators.categorias();
    if (!validacionCategorias.valido) {
      errores.push(validacionCategorias.mensaje);
      const errorDiv = document.getElementById('error-categorias');
      if (errorDiv) errorDiv.style.display = 'block';
    }

    const validacionImagen = Validators.imagenPrincipal();
    if (!validacionImagen.valido) {
      errores.push(validacionImagen.mensaje);
    }

    return errores;
  }

  configurarPrevencionPerdidaDatos() {
    this.form.querySelectorAll('input, textarea, select').forEach(input => {
      input.addEventListener('input', () => {
        this.formModified = true;
      });
    });

    window.addEventListener('beforeunload', (e) => {
      if (this.formModified && !this.isSubmitting) {
        e.preventDefault();
        e.returnValue = '¿Estás seguro de que quieres salir? Los cambios no guardados se perderán.';
        return e.returnValue;
      }
    });
  }
}

// ========================================
// INICIALIZACIÓN
// ========================================
document.addEventListener('DOMContentLoaded', function() {
  // Inicializar gestión de inactividad
  new InactivityManager();
  
  // Inicializar formulario
  new ProductForm();
});

// ========================================
// FUNCIONES GLOBALES (para compatibilidad)
// ========================================
window.removerPreview = ImageManager.removerPreview.bind(ImageManager);