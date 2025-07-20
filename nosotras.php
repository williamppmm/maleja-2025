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
          Emprendedora, madre de 5 hijos y apasionada por el calzado femenino. 
          Con más de 10 años conectando con mujeres a través del estilo y la calidad.
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
          Hace más de 10 años descubrí mi pasión: ayudar a las mujeres a encontrar 
          ese calzado perfecto que las haga sentir seguras, cómodas y hermosas.
        </p>
        
        <p>
          Durante todos estos años he trabajado de cerca con la moda y el calzado femenino, 
          aprendiendo sobre estilos que se renuevan constantemente y, sobre todo, 
          sobre la importancia de la calidad. Como madre de 5 hijos, entiendo perfectamente 
          que cada peso cuenta, y por eso me he forjado a pulso para ofrecer siempre 
          lo mejor a mis clientas.
        </p>
        
        <p>
          Mi experiencia me ha llevado a conocer la excepcional calidad del calzado de 
          Bucaramanga, reconocido en todo el país por su tradición y excelencia. 
          Sin embargo, también valoro y distribuyo diseños de Cali y otras ciudades 
          que mantienen esos altos estándares que busco.
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
          se selecciona pensando en ustedes: <strong>calzado hermoso que se ajuste al estilo 
          caleño, pero que conserve la calidad que caracteriza el calzado de Bucaramanga.</strong>
        </p>
      </div>
      <div class="llegada-cali__highlight">
        <div class="highlight-card">
          <h3 class="highlight-card__title">¿Por qué Bucaramanga?</h3>
          <p class="highlight-card__text">
            Bucaramanga es la capital del calzado en Colombia. Su tradición, 
            técnicas artesanales y control de calidad son incomparables. 
            Esa es la base de nuestro catálogo.
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
          Soy muy conversadora y me encanta conectar con mis clientas. 
          Cada mujer es única y merece una atención personalizada para 
          encontrar exactamente lo que busca.
        </p>
      </div>
      
      <div class="filosofia__item">
        <div class="filosofia__icon">✨</div>
        <h3 class="filosofia__subtitle">Calidad ante todo</h3>
        <p class="filosofia__text">
          Cada par que selecciono pasa por mi filtro personal. 
          Después de 10 años en esto, reconozco la calidad al primer vistazo 
          y solo ofrezco lo que yo misma usaría.
        </p>
      </div>
      
      <div class="filosofia__item">
        <div class="filosofia__icon">💝</div>
        <h3 class="filosofia__subtitle">Asesoría completa</h3>
        <p class="filosofia__text">
          No solo vendo calzado, te asesoro para que elijas el producto perfecto. 
          Mi objetivo es que te sientas completamente satisfecha con tu compra.
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
        MALEJA Calzado está creciendo, y con el apoyo de las redes sociales 
        y el boca a boca que tanto me gusta, estamos expandiendo nuestra familia 
        de clientas satisfechas. 
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