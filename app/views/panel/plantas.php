<?php page_header('Mis plantas', 'Cada QR es único y lleva a la guía de esa planta.', 'panel'); ?>
<main class="container-x py-10">
  <?php if (!$plantas): ?>
    <div class="card p-14 text-center max-w-xl mx-auto">
      <span class="w-14 h-14 rounded-full bg-brand-green/10 text-brand-green grid place-items-center mx-auto"><?= icon('sprout', 'w-7 h-7') ?></span>
      <h2 class="font-serif text-2xl font-bold text-brand-green mt-4">Todavía no tenés plantas</h2>
      <p class="text-gray-500 mt-2 text-sm">Cuando compres una en el local, va a aparecer acá con su QR y su guía de cuidados.</p>
    </div>
  <?php else: ?>
  <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-7">
    <?php foreach ($plantas as $p): ?>
      <div class="card card-hover overflow-hidden">
        <div class="photo h-44" style="background-image:url('<?= e($p['imagen_url']) ?>')"></div>
        <div class="p-6 text-center">
          <h3 class="font-serif text-2xl font-bold text-brand-green"><?= e($p['nombre_comun']) ?></h3>
          <p class="text-xs italic text-gray-400"><?= e($p['nombre_cientifico']) ?></p>
          <div class="qr flex justify-center my-5" data-url="<?= e(abs_url('planta', ['t' => $p['qr_token']])) ?>" data-size="170"></div>
          <a href="<?= url('planta', ['t' => $p['qr_token']]) ?>" class="btn btn-outline !py-2">Ver guía de cuidados</a>
        </div>
      </div>
    <?php endforeach; ?>
  </div>
  <?php endif; ?>
</main>
