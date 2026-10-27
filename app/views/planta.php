<main class="container-x py-12 max-w-3xl">
  <div class="card overflow-hidden">
    <div class="photo h-72 relative" style="background-image:url('<?= e($planta['imagen_url']) ?>')">
      <span class="absolute top-4 right-4 badge bg-white/95 text-brand-green shadow"><?= icon('calendar', 'w-3.5 h-3.5') ?>Ideal en <?= e($planta['estacion_ideal']) ?></span>
    </div>
    <div class="p-8 md:p-10">
      <p class="eyebrow !text-brand-sage"><?= e($planta['categoria']) ?></p>
      <h1 class="font-serif text-4xl font-bold text-brand-green mt-1"><?= e($planta['nombre_comun']) ?></h1>
      <p class="italic text-gray-400"><?= e($planta['nombre_cientifico']) ?></p>
      <p class="text-gray-600 mt-5 leading-relaxed"><?= e($planta['descripcion']) ?></p>
      <div class="grid sm:grid-cols-3 gap-3 mt-8">
        <?php foreach ([['droplet', 'Riego', $planta['riego']], ['sun', 'Luz', $planta['luz']], ['thermo', 'Temperatura', $planta['temperatura_ideal']]] as [$i, $t, $v]): ?>
          <div class="rounded-2xl bg-brand-cream border border-brand-sage/20 p-4"><span class="text-brand-leaf"><?= icon($i, 'w-6 h-6') ?></span><p class="text-xs font-bold uppercase tracking-wider text-gray-400 mt-2"><?= $t ?></p><p class="text-sm font-medium text-brand-green mt-0.5"><?= e($v) ?></p></div>
        <?php endforeach; ?>
      </div>
    </div>
  </div>
</main>
