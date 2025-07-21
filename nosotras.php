<?php
require_once __DIR__ . '/config/db.php';

/**
 * Configuración de la página
 */
$currentPage      = 'nosotras';
$pageTitle        = 'Nosotras | MALEJA Calzado - María Alejandra Pérez';
$metaDescription  = 'Conoce la historia de María Alejandra Pérez, emprendedora con más de 10 años de experiencia en calzado femenino, ahora conquistando Cali con estilo y calidad.';
$canonicalUrl     = 'https://calzadomaleja.co/nosotras.php';

include __DIR__ . '/includes/header.php';
?>

<!-- Hero Nosotras -->
<section class="hero-nosotras" aria-label="Presentación de María Alejandra">
  <div class="container">
    <div class="hero-nosotras__content">
      <div class="hero-nosotras__text">
        <h1 class="hero-nosotras__title">Hola, soy María Alejandra</h1>
        <p class="hero-nosotras__subtitle">La persona detrás de MALEJA Calzado</p>
        <p class="hero-nosotras__intro">
          Emprendedora construyendo una relación auténtica con mujeres que buscan sentirse seguras, cómodas y con estilo.
        </p>
      </div>
      <div class="hero-nosotras__image">
        <img src="assets/images/nosotras/maleja-hero.jpg " 
             alt="María Alejandra Pérez - Fundadora de MALEJA Calzado"
             class="hero-nosotras__img"
             loading="eager"
             width="400" height="500">
      </div>
    </div>
  </div>
</section>

<!-- Mi Historia -->
<section class="section historia" aria-labelledby="historia-title">
  <div class="container">
    <h2 id="historia-title" class="section__title">Mi historia</h2>
    
    <div class="historia__content">
      <div class="historia__text">
        <p class="historia__lead">
          Mi pasión nació de una simple verdad: 
          cada mujer merece encontrar ese calzado perfecto que la haga sentir segura, cómoda y hermosa.
        </p>
        
        <p>
          A lo largo de mi trayectoria he trabajado de cerca con la moda y el calzado femenino, 
          aprendiendo sobre estilos que evolucionan constantemente y, sobre todo, sobre lo que realmente importa: 
          la calidad. Entiendo que cada inversión debe valer la pena, por eso me he especializado en seleccionar 
          únicamente lo mejor para mis clientas.
        </p>
        
        <p>
          Mi experiencia me ha llevado a desarrollar un ojo crítico para identificar calzado de excelente calidad, 
          trabajando con emprendedoras que mantienen los más altos estándares. 
          Cada pieza de nuestro catálogo es cuidadosamente seleccionada pensando en mujeres que valoran 
          tanto el estilo como la durabilidad.
        </p>
      </div>
    </div>
  </div>
</section>

<!-- Llegando a Cali -->
<section class="section llegada-cali section--alt" aria-labelledby="cali-title">
  <div class="container">
    <div class="llegada-cali__grid">
      <div class="llegada-cali__content">
        <h2 id="cali-title" class="section__title">Llegando a Cali con todo el flow</h2>
        <p class="llegada-cali__texto">
          Cali es nuestro nuevo hogar, y desde el 2025 MALEJA Calzado está aquí para conquistar 
          el corazón de las caleñas. Nos enamoramos de esta ciudad, de su gente, 
          de su energía y ese argot único que las caracteriza.
        </p>
        <p class="llegada-cali__texto">
          Sabemos que las mujeres de Cali tienen un estilo particular, esa actitud 
          y ese flow que las hace únicas. Por eso, cada pieza de nuestro catálogo 
          se selecciona pensando en ustedes: <strong>calzado hermoso, con la calidad, comodidad y durabilidad que merecen.</strong>
        </p>
      </div>
      <div class="llegada-cali__highlight">
        <div class="highlight-card">
          <h3 class="highlight-card__title">¡Cali-dad!</h3>
          <p class="highlight-card__text">
            Seleccionamos cada diseño bajo estrictos estándares de calidad, 
            comodidad y estilo. Trabajamos solo con marcas que garantizan 
            los mejores diseños para mujeres que saben lo que quieren.
          </p>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- Mi filosofía -->
<section class="section filosofia" aria-labelledby="filosofia-title">
  <div class="container">
    <h2 id="filosofia-title" class="section__title">Mi filosofía</h2>
    
    <div class="filosofia__grid">
      <div class="filosofia__item">
        <div class="filosofia__icon">👥</div>
        <h3 class="filosofia__subtitle">Conexión personal</h3>
        <p class="filosofia__text">
          Me gusta conversar y escuchar. 
          Cada mujer es única y merece una recomendación honesta que responda a su estilo, 
          su ritmo y lo que realmente necesita.
        </p>
      </div>
      
      <div class="filosofia__item">
        <div class="filosofia__icon">✨</div>
        <h3 class="filosofia__subtitle">Calidad ante todo</h3>
        <p class="filosofia__text">
          Solo incluyo pares que pasan mi filtro: horma cómoda, materiales confiables, buen acabado y presencia. 
          Trabajo con talleres y marcas que respetan ese estándar para que cada paso se sienta suave y seguro.
        </p>
      </div>
      
      <div class="filosofia__item">
        <div class="filosofia__icon">💝</div>
        <h3 class="filosofia__subtitle">Asesoría completa</h3>
        <p class="filosofia__text">
          No es solo vender: te acompaño a elegir el par adecuado, resuelvo dudas de tallas, uso y cuidado. 
          Busco que recibas justo lo que esperabas y quedes tranquila con tu compra.
        </p>
      </div>
    </div>
  </div>
</section>

<!-- El futuro -->
<section class="section futuro section--alt" aria-labelledby="futuro-title">
  <div class="container">
    <div class="futuro__content">
      <h2 id="futuro-title" class="section__title">Construyendo el futuro juntas</h2>
      <p class="futuro__text">
        MALEJA Calzado está creciendo, 
        y con el apoyo de las redes sociales y las recomendaciones de clientas satisfechas, 
        estamos expandiendo nuestra comunidad.
      </p>
      <p class="futuro__text">
        Mi sueño es llegar no solo a toda Cali, sino a toda Colombia, 
        llevando calidad, estilo y esa atención personalizada que me caracteriza. 
        Y quién sabe... ¡tal vez pronto incluyamos otros productos que sé que les van a encantar!
      </p>
      
      <div class="futuro__cta">
        <h3 class="futuro__cta-title">¿Lista para encontrar tu par perfecto?</h3>
        <p class="futuro__cta-text">
          Déjame asesorarte y juntas encontremos ese calzado que va perfectamente con tu estilo.
        </p>
        <div class="futuro__buttons">
          <a href="productos.php" class="btn btn--primary">Ver catálogo</a>
          <a href="https://wa.me/573172703742?text=Hola%20Maleja%2C%20quiero%20conocer%20m%C3%A1s%20sobre%20sus%20productos" 
             class="btn btn--whatsapp" target="_blank" rel="noopener">
            Conversemos por WhatsApp
          </a>
        </div>
      </div>
    </div>
  </div>
</section>

<?php
include __DIR__ . '/includes/footer.php';
?>