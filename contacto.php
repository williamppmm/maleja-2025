<?php
require_once __DIR__ . '/config/db.php';

/**
 * Configuración de la página
 */
$currentPage      = 'contacto';
$pageTitle        = 'Contacto | MALEJA Calzado - WhatsApp y Asesoría Personal';
$metaDescription  = 'Contáctanos por WhatsApp para asesoría personalizada en calzado femenino. María Alejandra te ayuda a encontrar tu par perfecto.';
$canonicalUrl     = 'https://calzadomaleja.co/contacto.php';

include __DIR__ . '/includes/header.php';
?>

<!-- Hero Contacto -->
<section class="hero-contacto" aria-label="Información de contacto">
  <div class="container">
    <div class="hero-contacto__content">
      <h1 class="hero-contacto__title">¡Conversemos sobre tu estilo!</h1>
      <p class="hero-contacto__subtitle">
        María Alejandra está lista para asesorarte y ayudarte a encontrar 
        ese calzado perfecto que va con tu personalidad.
      </p>
      <p class="hero-contacto__texto">
        Nos encanta hablar de moda, estilos y, por supuesto, del calzado que te hará sentir increíble. 
        ¡Escríbenos y platiquemos!
      </p>
    </div>
  </div>
</section>

<!-- Formas de Contacto -->
<section class="section contacto-metodos" aria-labelledby="metodos-title">
  <div class="container">
    <h2 id="metodos-title" class="section__title">¿Cómo prefieres que hablemos?</h2>
    
    <div class="metodos-grid">
      <!-- WhatsApp -->
      <div class="metodo-card metodo-card--principal">
        <div class="metodo-card__icon">
          <img src="assets/icons/whatsapp.png" alt="" width="48" height="48">
        </div>
        <h3 class="metodo-card__title">WhatsApp</h3>
        <p class="metodo-card__subtitle">¡La forma más rápida de conectar!</p>
        <p class="metodo-card__texto">
          Chateamos en tiempo real, te mando fotos de los modelos, 
          resolvemos dudas al instante y coordinamos tu pedido.
        </p>
        <div class="metodo-card__info">
          <p class="metodo-card__dato">+57 317 270 3742</p>
          <p class="metodo-card__horario">8:00 AM - 6:00 PM</p>
          <p class="metodo-card__nota">*Respondo rápido, incluso fuera de horario</p>
        </div>
        <a href="https://wa.me/573172703742?text=Hola%20Maleja%2C%20quiero%20conocer%20m%C3%A1s%20sobre%20sus%20productos" 
           class="btn btn--whatsapp btn--large" target="_blank" rel="noopener">
          Chatear por WhatsApp
        </a>
      </div>
      
      <!-- Email -->
      <div class="metodo-card">
        <div class="metodo-card__icon">
          <img src="assets/icons/mail.png" alt="" width="48" height="48">
        </div>
        <h3 class="metodo-card__title">Correo Electrónico</h3>
        <p class="metodo-card__subtitle">Para consultas más detalladas</p>
        <p class="metodo-card__texto">
          Ideal para enviar referencias específicas, hacer consultas 
          sobre múltiples productos o cualquier duda especial.
        </p>
        <div class="metodo-card__info">
          <p class="metodo-card__dato">ventas@malejacalzado.co</p>
          <p class="metodo-card__horario">Respuesta en 24 horas</p>
        </div>
        <a href="mailto:ventas@malejacalzado.co?subject=Consulta%20sobre%20productos&body=Hola%20Maleja%2C%0A%0AMe%20interesa%20conocer%20m%C3%A1s%20sobre%20sus%20productos.%0A%0AGracias" 
           class="btn btn--secondary">
          Enviar correo
        </a>
      </div>
      
      <!-- Redes Sociales -->
      <div class="metodo-card">
        <div class="metodo-card__icon">
          <img src="assets/icons/instagram.png" alt="" width="48" height="48">
        </div>
        <h3 class="metodo-card__title">Redes Sociales</h3>
        <p class="metodo-card__subtitle">Síguenos y mantente al día</p>
        <p class="metodo-card__texto">
          Ve las últimas novedades, outfits de temporada 
          y entérate de las nuevas colecciones que llegan.
        </p>
        <div class="metodo-card__social">
          <a href="https://www.instagram.com/malejacalzado/" 
             target="_blank" rel="noopener" class="social-link">
            <img src="assets/icons/instagram.png" alt="" width="24" height="24">
            @malejacalzado
          </a>
          <a href="https://www.facebook.com/malejacalzado/" 
             target="_blank" rel="noopener" class="social-link social-link--proximamente">
            <img src="assets/icons/facebook.png" alt="" width="24" height="24">
            @malejacalzado
            <span class="proximamente-badge">Próximamente</span>
          </a>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- Proceso de Compra -->
<section class="section proceso section--alt" aria-labelledby="proceso-title">
  <div class="container">
    <h2 id="proceso-title" class="section__title">¿Cómo funciona nuestro proceso?</h2>
    <p class="section__subtitle">Simple, seguro y personalizado</p>
    
    <div class="proceso-steps">
      <div class="step">
        <div class="step__number">1</div>
        <h3 class="step__title">Conversamos</h3>
        <p class="step__texto">
          Me cuentas qué estás buscando, tu talla, ocasión de uso 
          y preferencias de estilo. ¡Me encanta conocer a mis clientas!
        </p>
      </div>
      
      <div class="step">
        <div class="step__number">2</div>
        <h3 class="step__title">Te asesoro</h3>
        <p class="step__texto">
          Te muestro opciones perfectas para ti, con fotos reales 
          y toda la información: precios, materiales y disponibilidad.
        </p>
      </div>
      
      <div class="step">
        <div class="step__number">3</div>
        <h3 class="step__title">Coordinas tu pedido</h3>
        <p class="step__texto">
          Apartamos tu par favorito y coordinamos la entrega. 
          Para clientas nuevas: contraentrega segura.
        </p>
      </div>
      
      <div class="step">
        <div class="step__number">4</div>
        <h3 class="step__title">¡Disfrutas tu estilo!</h3>
        <p class="step__texto">
          Recibes tu calzado perfecto y te sientes increíble. 
          Siempre estoy aquí para cualquier duda posterior.
        </p>
      </div>
    </div>
  </div>
</section>

<!-- FAQ -->
<section class="section faq" aria-labelledby="faq-title">
  <div class="container">
    <h2 id="faq-title" class="section__title">Preguntas frecuentes</h2>
    
    <div class="faq-grid">
      <div class="faq-item">
        <h3 class="faq-question">¿Manejan todas las tallas?</h3>
        <p class="faq-answer">
          Sí, manejamos desde la talla 35 hasta la 40, y en algunos modelos 
          hasta 41. Si tienes una talla especial, consúltanos y buscamos opciones.
        </p>
      </div>
      
      <div class="faq-item">
        <h3 class="faq-question">¿Cómo sé si la talla me queda?</h3>
        <p class="faq-answer">
          Te ayudo con una guía de tallas personalizada y, si es tu primera compra, 
          podemos coordinar una entrega personal para asegurar el ajuste perfecto.
        </p>
      </div>
      
      <div class="faq-item">
        <h3 class="faq-question">¿Tienen garantía?</h3>
        <p class="faq-answer">
          Todos nuestros productos tienen garantía de calidad. Si hay algún 
          defecto de fabricación, lo solucionamos inmediatamente.
        </p>
      </div>
      
      <div class="faq-item">
        <h3 class="faq-question">¿Qué métodos de pago aceptan?</h3>
        <p class="faq-answer">
          Aceptamos efectivo, transferencias bancarias y pagos por apps móviles. 
          Para clientas nuevas, preferimos contraentrega para mayor confianza.
        </p>
      </div>
      
      <div class="faq-item">
        <h3 class="faq-question">¿Hacen entregas a domicilio?</h3>
        <p class="faq-answer">
          Sí, coordinamos entregas en Cali. Para mis clientas conocidas, 
          a veces hago entregas personales. ¡Me encanta conocer a quienes confían en MALEJA!
        </p>
      </div>
      
      <div class="faq-item">
        <h3 class="faq-question">¿Tienen productos nuevos frecuentemente?</h3>
        <p class="faq-answer">
          ¡Constantemente! Como selecciono de las últimas colecciones de Bucaramanga 
          y otras ciudades, siempre hay novedades. Síguenos en redes para no perderte nada.
        </p>
      </div>
    </div>
  </div>
</section>

<!-- CTA Final -->
<section class="section cta-final section--alt">
  <div class="container">
    <div class="cta-final__content">
      <h2 class="cta-final__title">¿Lista para encontrar tu calzado perfecto?</h2>
      <p class="cta-final__texto">
        No esperes más. Escríbeme y empecemos a buscar juntas ese par 
        que va a enamorarte y que hará que te sientas espectacular.
      </p>
      <div class="cta-final__buttons">
        <a href="https://wa.me/573172703742?text=Hola%20Maleja%2C%20quiero%20que%20me%20asesores%20para%20encontrar%20mi%20calzado%20perfecto" 
           class="btn btn--primary btn--large" target="_blank" rel="noopener">
          ¡Hablemos por WhatsApp!
        </a>
        <a href="productos.php" class="btn btn--outline">
          Ver catálogo
        </a>
      </div>
    </div>
  </div>
</section>

<?php
include __DIR__ . '/includes/footer.php';
?>