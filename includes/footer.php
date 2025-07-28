<?php
/** ------------------------------------------------------------------
 *  FOOTER GLOBAL
 *  ------------------------------------------------------------------
 *  Variables opcionales que puede definir la página antes de incluir:
 *    $includeProductModal (bool)  → mostrar / ocultar el modal global
 *    $extraScripts        (html)  → inyectar scripts extra al final
 *    $deferAppJs          (bool)  → cargar main.js con defer o no
 * ------------------------------------------------------------------*/
$includeProductModal = $includeProductModal ?? true;
$extraScripts        = $extraScripts        ?? '';
$deferAppJs          = $deferAppJs         ?? true;

$base        = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/');  // p.ej. /maleja
$adminLogin  = $base . '/admin/login.php';
?>
  </main>

  <!-- FOOTER -------------------------------------------------------->
  <footer class="footer" role="contentinfo">
    <div class="container footer__grid">

      <!-- Marca -->
      <div class="footer__col footer__brand">
        <img src="assets/images/logos/logo-white.png"
             alt="MALEJA Calzado - logo claro"
             id="logo-maleja-footer"
             width="140" height="auto" loading="lazy" decoding="async">
        <p class="footer__tagline">Estilo que camina contigo desde 2025.</p>
      </div>

      <!-- Contacto -->
      <div class="footer__col footer__contacto">
        <h3 class="footer__heading">Contacto</h3>
        <ul class="footer__list">
          <li><a href="mailto:ventas@malejacalzado.co">ventas@malejacalzado.co</a></li>
          <li><a href="https://wa.me/573135152530" target="_blank" rel="noopener">+57 313 515 2530</a></li>
        </ul>
      </div>

      <!-- Redes -->
      <div class="footer__col footer__social">
        <h3 class="footer__heading">Síguenos</h3>
        <div class="social social--footer">
          <a href="https://www.instagram.com/malejacalzado/" target="_blank" rel="noopener" aria-label="Instagram">
            <img src="assets/icons/instagram.png" width="26" height="26" loading="lazy" alt="">
          </a>
          <a href="https://www.facebook.com/share/178Cq8h6pT/" target="_blank" rel="noopener" aria-label="Facebook">
            <img src="assets/icons/facebook.png" width="26" height="26" loading="lazy" alt="">
          </a>
        </div>
      </div>

      <!-- Copyright + crédito -->
      <div class="copyright" style="position:relative;">
        <p>© <?= date('Y') ?> MALEJA Calzado · Todos los derechos reservados ·
          <span class="dev-credit" id="dev-credit-trigger"
                style="cursor:pointer;border-bottom:1px dotted rgba(255,255,255,.5);position:relative;"
                title="Clic para más info">Hecho con ❤️ en Cali</span>
        </p>

        <!-- Tooltip crédito -->
        <div class="dev-info" id="dev-info">
          <p style="margin:0;line-height:1.4;">
            ¿Te gusta este sitio? Creamos páginas web personalizadas<br>
            <a href="https://wa.me/573152728882?text=Hola%2C%20vi%20tu%20trabajo%20en%20Maleja%20Calzado%20y%20me%20interesa%20una%20página%20web"
               target="_blank" rel="noopener"
               style="color:#25D366;text-decoration:none;font-weight:bold;">
              WhatsApp: +57 315 272 8882
            </a>
          </p>
          <div class="tooltip-arrow"></div>
        </div>
      </div>

    </div>
  </footer>

  <!-- MODAL PRODUCTO ------------------------------------------------>
  <?php if ($includeProductModal): ?>
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
            <p class="modal__ref"    id="modal-ref"></p>
            <p class="modal__precio" id="modal-precio"></p>
            <p class="modal__descripcion" id="modal-descripcion"></p>
            <div class="modal__actions">
              <a id="modal-btn-whatsapp" class="btn btn--whatsapp" target="_blank" rel="noopener" href="#">Pedir por WhatsApp</a>
              <a id="modal-btn-mail"     class="btn btn--secondary" href="#">Escribir por correo</a>
              <a id="modal-btn-ver"      class="btn btn--outline"   href="productos.php">Ver más modelos</a>
            </div>
          </div>
        </div>
      </div>
    </div>
  <?php endif; ?>

  <!-- SCRIPTS ------------------------------------------------------->
  <?php if ($includeProductModal): ?>
    <!-- Modal de producto -->
    <script src="<?= $base ?>/assets/js/components/modal-producto.js?v=1" defer></script>
  <?php endif; ?>

  <!-- JS global (ahora incluye dev-credit integrado) -->
  <?php if ($deferAppJs): ?>
    <script src="<?= $base ?>/assets/js/main.js?v=2" defer></script>
  <?php else: ?>
    <script src="<?= $base ?>/assets/js/main.js?v=2"></script>
  <?php endif; ?>

  <!-- Triple‑click admin -->
  <script src="<?= $base ?>/assets/js/utils/hidden-admin.js?v=1" defer></script>

  <!-- Scripts adicionales que inyectes desde las páginas -->
  <?= $extraScripts ?>

  <!-- Config global para JS -->
  <script>window.__ADMIN_LOGIN__ = '<?= $adminLogin ?>';</script>
</body>
</html>