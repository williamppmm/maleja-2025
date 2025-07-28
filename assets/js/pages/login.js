// Mostrar/Ocultar contraseña
document.getElementById('togglePassword')?.addEventListener('click', function () {
  const passwordInput = document.getElementById('password');
  const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
  passwordInput.setAttribute('type', type);
  this.textContent = type === 'password' ? '👁️' : '🙈';
  this.setAttribute('aria-label', type === 'password' ? 'Mostrar contraseña' : 'Ocultar contraseña');
});

// Animación de carga al enviar
const loginForm = document.getElementById('loginForm');
const submitBtn = document.getElementById('submitBtn');
const btnText = document.querySelector('.btn-text');
const loading = document.querySelector('.loading');

loginForm?.addEventListener('submit', function () {
  submitBtn.disabled = true;
  btnText.style.opacity = '0';
  loading.style.display = 'block';
});

// Enfocar el input vacío automáticamente
document.addEventListener('DOMContentLoaded', () => {
  const usernameInput = document.getElementById('username');
  const passwordInput = document.getElementById('password');
  if (!usernameInput.value) usernameInput.focus();
  else passwordInput.focus();
});

// Limpiar parámetro ?error de la URL
if (window.location.search.includes('error=')) {
  const url = new URL(window.location);
  url.searchParams.delete('error');
  window.history.replaceState({}, document.title, url.pathname);
}