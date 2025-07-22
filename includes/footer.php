<?php
/**
 * Footer global.
 */

// Configuración de variables para el footer
$includeProductModal = isset($includeProductModal) ? (bool)$includeProductModal : true;
$extraScripts        = $extraScripts ?? '';
$deferAppJs          = isset($deferAppJs) ? (bool)$deferAppJs : true;
?>
  </main>

  <!-- Inicio del footer -->
  <footer class="footer" role="contentinfo">
    <div class="container footer__grid">
    <!-- Columna: Marca -->
    <div class="footer__col footer__brand">
      <img src="assets/images/logos/logo-white.png"
         alt="MALEJA Calzado - logo claro"
         width="140"
         height="auto"
         loading="lazy"
         decoding="async">
      <p class="footer__tagline">Estilo que camina contigo desde 2025.</p>
    </div>

    <!-- Columna: Contacto -->
    <div class="footer__col footer__contacto">
      <h3 class="footer__heading">Contacto</h3>
      <ul class="footer__list">
      <li><a href="mailto:ventas@malejacalzado.co">ventas@malejacalzado.co</a></li>
      <li><a href="https://wa.me/573172703742" target="_blank" rel="noopener">+57 317 270 3742</a></li>
      </ul>
    </div>

    <!-- Columna: Redes sociales -->
    <div class="footer__col footer__social">
      <h3 class="footer__heading">Síguenos</h3>
      <div class="social social--footer">
        <a href="https://www.instagram.com/malejacalzado/"
         target="_blank" rel="noopener"
         aria-label="Instagram">
        <img src="assets/icons/instagram.png" alt="" width="26" height="26" loading="lazy">
        </a>
        <a href="#"
         aria-label="Facebook (próximamente)">
        <img src="assets/icons/facebook.png" alt="" width="26" height="26" loading="lazy">
        </a>
      </div>
    </div>

    <!-- Copyright y crédito desarrollador -->
    <div class="copyright" style="position: relative;">
      <p>© <?= date('Y') ?> MALEJA Calzado · Todos los derechos reservados · 
      <span class="dev-credit" id="dev-credit-trigger" 
          style="cursor: pointer; border-bottom: 1px dotted rgba(255,255,255,0.5); position: relative;"
          title="Clic para más info">Hecho con ❤️ en Cali</span>
      </p>
      
      <!-- Mensaje oculto con información del desarrollador -->
      <div class="dev-info" id="dev-info" 
         style="display: none; position: absolute; bottom: 100%; left: 50%; 
            transform: translateX(-50%); background: #2c3e50; color: white; 
            padding: 15px 20px; border-radius: 10px; font-size: 14px; 
            white-space: nowrap; box-shadow: 0 4px 15px rgba(0,0,0,0.3); 
            z-index: 9999; margin-bottom: 10px; border: 1px solid #34495e;">
      <p style="margin: 0; line-height: 1.4;">
        ¿Te gusta este sitio? Creamos páginas web personalizadas<br>
        <a href="https://wa.me/573152728882?text=Hola%2C%20vi%20tu%20trabajo%20en%20Maleja%20Calzado%20y%20me%20interesa%20una%20p%C3%A1gina%20web"
           target="_blank" rel="noopener"
           style="color: #25D366; text-decoration: none; font-weight: bold;">
          WhatsApp: +57 315 272 8882
        </a>
      </p>
      <!-- Flecha decorativa -->
      <div style="position: absolute; top: 100%; left: 50%; transform: translateX(-50%); 
            width: 0; height: 0; border-left: 8px solid transparent; 
            border-right: 8px solid transparent; border-top: 8px solid #2c3e50;"></div>
      </div>
    </div>
    </div>
  </footer>

<?php if ($includeProductModal): ?>
  <!-- Modal de producto global -->
  <div class="modal" id="modal-producto" aria-hidden="true" aria-labelledby="modal-titulo" role="dialog">
    <div class="modal__backdrop" data-modal-cerrar></div>
    <div class="modal__dialog" role="document">
    <button class="modal__close" type="button" aria-label="Cerrar" data-modal-cerrar>&times;</button>
    <div class="modal__content">
      <div class="modal__img-wrap">
      <img src="" alt="" id="modal-imagen" decoding="async">
      </div>
      <div class="modal__info">
      <h3 id="modal-titulo" class="modal__title"></h3>
      <p class="modal__ref" id="modal-ref"></p>
      <p class="modal__precio" id="modal-precio"></p>
      <p class="modal__descripcion" id="modal-descripcion"></p>
      <div class="modal__actions">
        <a id="modal-btn-whatsapp" class="btn btn--whatsapp" target="_blank" rel="noopener" href="#">
        Pedir por WhatsApp
        </a>
        <a id="modal-btn-mail" class="btn btn--secondary" href="#">
        Escribir por correo
        </a>
        <a id="modal-btn-ver" class="btn btn--outline" href="productos.php">
        Ver más modelos
        </a>
      </div>
      </div>
    </div>
    </div>
  </div>
<?php endif; ?>

  <!-- JS principal del sitio -->
  <?php if ($deferAppJs): ?>
    <script src="assets/js/script.js" defer></script>
  <?php else: ?>
    <script src="assets/js/script.js"></script>
  <?php endif; ?>

<?php if ($includeProductModal): ?>
  <!-- Lógica para el modal de producto -->
  <script>
  (function(){
    // Elementos del modal
    const modal = document.getElementById('modal-producto');
    if(!modal) return;

    const imgEl    = document.getElementById('modal-imagen');
    const tituloEl = document.getElementById('modal-titulo');
    const refEl    = document.getElementById('modal-ref');
    const precioEl = document.getElementById('modal-precio');
    const descEl   = document.getElementById('modal-descripcion');
    const btnWhats = document.getElementById('modal-btn-whatsapp');
    const btnMail  = document.getElementById('modal-btn-mail');
    let   lastFocus = null;

    // Formatear precio en COP
    const formatCOP = n => '$' + Number(n).toLocaleString('es-CO',{maximumFractionDigits:0});

    // Abrir modal con datos del producto
    function abrir(card){
    lastFocus = document.activeElement;
    const nombre = card.dataset.nombre || 'Producto';
    const ref    = card.dataset.referencia || '';
    const precio = card.dataset.precio || '';
    const descr  = card.dataset.descripcion || '';
    const img    = card.dataset.img || '';

    tituloEl.textContent = nombre;
    refEl.textContent    = ref ? 'Ref: ' + ref : '';
    precioEl.textContent = precio ? formatCOP(precio) : '';
    descEl.textContent   = descr;
    imgEl.src            = img;
    imgEl.alt            = nombre;

    // Enlaces dinámicos para WhatsApp y correo
    const msg = encodeURIComponent(`Hola Maleja, me interesa el producto: ${nombre}${ref ? ' (ref '+ref+')' : ''}`);
    btnWhats.href = `https://wa.me/573172703742?text=${msg}`;

    const subject = encodeURIComponent(`Consulta: ${nombre}${ref ? ' - Ref '+ref : ''}`);
    const body    = encodeURIComponent('Hola, me interesa este modelo. ¿Disponibilidad de tallas?');
    btnMail.href  = `mailto:ventas@malejacalzado.co?subject=${subject}&body=${body}`;

    modal.classList.add('is-open');
    modal.setAttribute('aria-hidden','false');
    setTimeout(()=>{
      const closeBtn = modal.querySelector('.modal__close');
      closeBtn && closeBtn.focus();
    },25);
    document.addEventListener('keydown', escClose);
    }

    // Cerrar modal
    function cerrar(){
    modal.classList.remove('is-open');
    modal.setAttribute('aria-hidden','true');
    document.removeEventListener('keydown', escClose);
    if(lastFocus) lastFocus.focus();
    }

    // Cerrar con tecla Escape
    function escClose(e){
    if(e.key === 'Escape') cerrar();
    }

    // Eventos para abrir/cerrar modal
    document.addEventListener('click', e=>{
    const trigger = e.target.closest('[data-accion="info"]');
    if(trigger){
      const card = trigger.closest('.producto-card');
      if(card) abrir(card);
    }
    if(e.target.hasAttribute('data-modal-cerrar') ||
       e.target === modal.querySelector('.modal__backdrop')){
      cerrar();
    }
    });
  })();
  </script>
<?php endif; ?>

  <!-- Lógica para mostrar crédito del desarrollador -->
  <script>
  console.log('🔧 Iniciando script de crédito desarrollador...');
  
  // Esperar a que el DOM esté cargado
  document.addEventListener('DOMContentLoaded', function() {
    console.log('📄 DOM cargado, buscando elementos...');
    
    const trigger = document.getElementById('dev-credit-trigger');
    const info = document.getElementById('dev-info');
    
    console.log('🎯 Trigger encontrado:', trigger);
    console.log('📋 Info encontrado:', info);
    
    if (!trigger || !info) {
      console.error('❌ No se encontraron los elementos necesarios');
      return;
    }
    
    let isVisible = false;
    let timeout;
    
    // Mostrar el mensaje de crédito
    function showCredit() {
      console.log('✅ Mostrando crédito...');
      clearTimeout(timeout);
      
      info.style.display = 'block';
      info.style.opacity = '1';
      info.style.transform = 'translateX(-50%) translateY(0)';
      info.style.transition = 'all 0.3s ease';
      
      isVisible = true;
      
      // Ocultar automáticamente después de 6 segundos
      timeout = setTimeout(function() {
        hideCredit();
      }, 6000);
    }
    
    // Ocultar el mensaje de crédito
    function hideCredit() {
      console.log('❌ Ocultando crédito...');
      clearTimeout(timeout);
      
      info.style.opacity = '0';
      setTimeout(function() {
        info.style.display = 'none';
        isVisible = false;
      }, 300);
    }
    
    // Evento para mostrar/ocultar al hacer clic
    trigger.addEventListener('click', function(e) {
      console.log('🖱️ Clic en trigger detectado');
      e.preventDefault();
      e.stopPropagation();
      
      if (isVisible) {
        hideCredit();
      } else {
        showCredit();
      }
    });
    
    // Ocultar al hacer clic fuera del mensaje
    document.addEventListener('click', function(e) {
      if (isVisible && !trigger.contains(e.target) && !info.contains(e.target)) {
        console.log('🔄 Clic fuera detectado, ocultando...');
        hideCredit();
      }
    });
    
    console.log('🎉 Script de crédito inicializado correctamente');
  });
  </script>

  <!-- CSS adicional para móviles -->
  <style>
  @media (max-width: 768px) {
    #dev-info {
      position: fixed !important;
      bottom: 20px !important;
      left: 20px !important;
      right: 20px !important;
      transform: none !important;
      white-space: normal !important;
      text-align: center !important;
    }
    #dev-info div:last-child {
      display: none !important;
    }
  }
  
  .dev-credit:hover {
    border-bottom-color: rgba(255,255,255,0.8) !important;
  }
  </style>

  <!-- Scripts adicionales por página -->
  <?= $extraScripts ?>

  </body>
</html>