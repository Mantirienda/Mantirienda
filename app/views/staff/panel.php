<main class="container-x py-12 space-y-12">
  <section>
    <p class="eyebrow">Personal</p><h1 class="font-serif text-4xl font-bold text-brand-green mt-1">Salón</h1>
    <p class="text-sm text-gray-500 mt-1 flex flex-wrap items-center gap-x-5 gap-y-1">Tocá una mesa para cambiar su estado.
      <span class="capullo inline-flex items-center gap-1.5 font-medium"><?= icon('sprout', 'w-4 h-4') ?>Capullo verde: libre</span>
      <span class="flor-roja inline-flex items-center gap-1.5 font-medium"><?= icon('flower', 'w-4 h-4') ?>Flor roja: ocupada</span></p>
    <div class="grid grid-cols-2 sm:grid-cols-4 lg:grid-cols-5 gap-4 mt-6">
      <?php foreach ($mesas as $m): $libre = $m['estado'] === 'libre'; ?>
        <form method="POST" action="<?= url('staff_mesa') ?>"><?= csrf_field() ?><input type="hidden" name="id" value="<?= (int)$m['id'] ?>">
          <button class="mesa card w-full p-6 text-center border-2 <?= $libre ? '!border-green-300 bg-green-50/50' : '!border-red-300 bg-red-50/50' ?>">
            <span class="<?= $libre ? 'capullo' : 'flor-roja' ?> block"><?= icon($libre ? 'sprout' : 'flower', 'w-12 h-12 mx-auto') ?></span>
            <b class="block mt-2 text-brand-green">Mesa <?= (int)$m['numero'] ?></b><span class="text-xs text-gray-500"><?= (int)$m['capacidad'] ?> personas · <?= $libre ? 'Libre' : 'Ocupada' ?></span>
          </button>
        </form>
      <?php endforeach; ?>
    </div>
  </section>
  <div class="grid md:grid-cols-2 gap-7">
    <section class="card p-7">
      <h2 class="font-serif text-xl font-bold text-brand-green flex items-center gap-2"><?= icon('receipt', 'w-5 h-5') ?>Código de puntos para la cuenta</h2>
      <form method="POST" action="<?= url('staff_cuenta') ?>" class="space-y-3 mt-5"><?= csrf_field() ?>
        <input class="input" type="number" name="puntos" placeholder="Puntos a otorgar" min="1" required>
        <input class="input" type="number" step="0.01" name="total" placeholder="Total de la cuenta (opcional)">
        <button class="btn btn-primary">Generar código</button>
      </form>
      <?php if ($codigo): ?><p class="mt-5 text-center text-3xl font-mono font-bold tracking-widest text-brand-green bg-brand-cream border border-brand-sage/30 rounded-xl py-4"><?= e($codigo) ?></p><?php endif; ?>
    </section>
    <section class="card p-7">
      <h2 class="font-serif text-xl font-bold text-brand-green flex items-center gap-2"><?= icon('sprout', 'w-5 h-5') ?>Asignar planta comprada</h2>
      <form method="POST" action="<?= url('staff_venta') ?>" class="space-y-3 mt-5"><?= csrf_field() ?>
        <input class="input" type="email" name="email" placeholder="Email del cliente" required>
        <select class="input" name="planta_id"><?php foreach ($plantas as $p): ?><option value="<?= (int)$p['id'] ?>"><?= e($p['nombre_comun']) ?></option><?php endforeach; ?></select>
        <button class="btn btn-primary">Asignar planta</button>
      </form>
    </section>
  </div>
</main>
