</div>
<footer class="bg-brand-dark text-brand-cream mt-20">
  <div class="container-x py-14 grid md:grid-cols-4 gap-10 text-sm">
    <div class="md:col-span-2 space-y-3">
      <div class="flex items-center gap-2.5"><span class="w-9 h-9 rounded-full bg-white/10 grid place-items-center"><?= icon('flower', 'w-5 h-5') ?></span><span class="font-serif text-2xl font-bold">Mantirienda</span></div>
      <p class="text-brand-cream/65 max-w-sm">Restaurante de flores, café de especialidad y vivero. Cocina con flores comestibles, plantas con guía de cuidados y un programa de puntos que premia cada visita.</p>
    </div>
    <div class="space-y-2">
      <p class="font-semibold text-white">Explorar</p>
      <a class="block text-brand-cream/65 hover:text-white" href="index.php#como">Cómo funciona</a>
      <a class="block text-brand-cream/65 hover:text-white" href="index.php#menu">Menú floral</a>
      <a class="block text-brand-cream/65 hover:text-white" href="index.php#rangos">Rangos y descuentos</a>
      <a class="block text-brand-cream/65 hover:text-white" href="<?= url('reservas') ?>">Reservas</a>
    </div>
    <div class="space-y-2">
      <p class="font-semibold text-white">Horario</p>
      <p class="text-brand-cream/65">Todos los días<br><?= HORA_APERTURA ?> a <?= HORA_CIERRE ?> hs</p>
      <p class="text-brand-cream/65 pt-2">Reservas con <?= DIAS_ANTICIPACION ?> días de anticipación.</p>
    </div>
  </div>
  <div class="border-t border-white/10"><div class="container-x py-5 text-xs text-brand-cream/45 flex flex-wrap justify-between gap-2"><span>© 2026 Mantirienda. Todos los derechos reservados.</span><span>Cuidamos el planeta, un pétalo a la vez.</span></div></div>
</footer>
<script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
<script src="public/js/app.js"></script>
</body></html>
