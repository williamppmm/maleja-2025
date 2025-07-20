<?php
/**
 * Footer global.
 *
 * Variables opcionales que puedes definir ANTES de incluirlo:
 *   $includeProductModal (bool) -> incluir (true por defecto) o no el modal de producto.
 *   $extraScripts (string) -> inyectar scripts adicionales antes de cerrar </body>.
 *   $deferAppJs (bool) -> si necesitas desactivar el <script principal defer>.
 */

$includeProductModal = isset($includeProductModal) ? (bool)$includeProductModal : true;
$extraScripts        = $extraScripts ?? '';
$deferAppJs          = isset($deferAppJs) ? (bool)$deferAppJs : true;
?>
    </main>

    <footer class="footer" role="contentinfo">
      <div class="container footer__grid">
        <!-- Marca -->
        <div class="footer__col footer__brand">
          <img src="assets/images/logos/logo-white.png"
               alt="MALEJA Calzado - logo claro"
               width="140"
               height="auto"
               loading="lazy"
               decoding="async">
          <p class="footer__tagline">Estilo que camina contigo desde 2025.</p>
        </div>

        <!-- Contacto -->
        <div class="footer__col footer__contacto">
          <h3 class="footer__heading">Contacto</h3>
          <ul class="footer__list">
            <li><a href="mailto:ventas@malejacalzado.co">ventas@malejacalzado.co</a></li>
            <li><a href="https://wa.me/573172703742" target="_blank" rel="noopener">+57 317 270 3742</a></li>
          </ul>
        </div>

        <!-- Redes -->
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

        <!-- Copyright -->
        <div class="copyright">
          <p>© <?= date('Y') ?> MALEJA Calzado · Todos los derechos reservados · Hecho con ❤️ en Cali</p>
        </div>
      </div>
    </footer>

<?php if ($includeProductModal): ?>
    <!-- Modal producto (global) -->
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

    <!-- JS principal -->
    <?php if ($deferAppJs): ?>
      <script src="assets/js/script.js" defer></script>
    <?php else: ?>
      <script src="assets/js/script.js"></script>
    <?php endif; ?>

<?php if ($includeProductModal): ?>
    <!-- Lógica modal producto (auto‑contenida) -->
    <script>
    (function(){
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

      const formatCOP = n => '$' + Number(n).toLocaleString('es-CO',{maximumFractionDigits:0});

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

      function cerrar(){
        modal.classList.remove('is-open');
        modal.setAttribute('aria-hidden','true');
        document.removeEventListener('keydown', escClose);
        if(lastFocus) lastFocus.focus();
      }

      function escClose(e){
        if(e.key === 'Escape') cerrar();
      }

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

    <!-- Hook de scripts adicionales (por página) -->
    <?= $extraScripts ?>

  </body>
</html>