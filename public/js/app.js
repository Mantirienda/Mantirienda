// Menú desplegable del usuario
const btn = document.getElementById('menuBtn'), list = document.getElementById('menuList');
if (btn) {
  btn.addEventListener('click', e => { e.stopPropagation(); list.classList.toggle('hidden'); });
  document.addEventListener('click', () => list.classList.add('hidden'));
}
// Selector de puntos a canjear
const slider = document.getElementById('puntosSlider');
if (slider) {
  const out = document.getElementById('puntosOut'), pct = document.getElementById('pctOut');
  const upd = () => { out.textContent = slider.value; pct.textContent = (slider.value / slider.dataset.per) * 5; };
  slider.addEventListener('input', upd); upd();
}
// QR (se dibuja en el navegador)
if (window.QRCode) document.querySelectorAll('.qr').forEach(el => new QRCode(el, { text: el.dataset.url, width: +el.dataset.size || 200, height: +el.dataset.size || 200, colorDark: '#2D4A3E', colorLight: '#ffffff' }));
// Reserva: seña en vivo y detalle del método de pago
const form = document.getElementById('formReserva');
if (form) {
  const per = +form.dataset.sena, personas = form.elements['personas'], out = document.getElementById('senaOut'), outP = document.getElementById('personasOut');
  const fmt = n => '$' + n.toLocaleString('es-AR');
  const calc = () => { const n = Math.max(0, Math.min(8, +personas.value || 0)); out.textContent = fmt(n * per); outP.textContent = n || '-'; };
  personas.addEventListener('input', calc); calc();
  const show = () => document.querySelectorAll('.pay-info').forEach(el => el.classList.toggle('hidden', el.dataset.m !== (form.querySelector('input[name=metodo]:checked') || {}).value));
  form.querySelectorAll('input[name=metodo]').forEach(r => r.addEventListener('change', show)); show();
}
