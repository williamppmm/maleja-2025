// assets/js/components/dev-credit.js
// Tooltip / crédito del desarrollador

document.addEventListener('DOMContentLoaded', () => {
  const trigger = document.getElementById('dev-credit-trigger');
  const info    = document.getElementById('dev-info');
  if (!trigger || !info) return;

  let visible  = false;
  let autoHide = null;

  const show = () => {
    clearTimeout(autoHide);
    info.style.display   = 'block';
    // pequeño “fade‑in”
    requestAnimationFrame(() => {
      info.style.opacity   = '1';
      info.style.transform = 'translateX(-50%) translateY(0)';
    });
    visible = true;
    autoHide = setTimeout(hide, 6000);            // se cierra solo
  };

  const hide = () => {
    clearTimeout(autoHide);
    info.style.opacity = '0';
    setTimeout(() => { info.style.display = 'none'; visible = false; }, 300);
  };

  trigger.addEventListener('click',  e => { e.preventDefault(); visible ? hide() : show(); });
  document.addEventListener('click', e => {
    if (visible && !trigger.contains(e.target) && !info.contains(e.target)) hide();
  });
});