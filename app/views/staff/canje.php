<main class="container-x py-14"><div class="max-w-md mx-auto card p-9 text-center">
  <p class="eyebrow">Validar descuento</p>
  <h1 class="font-serif text-2xl font-bold text-brand-green mt-1"><?= e($canje['nombre']) ?></h1>
  <p class="text-sm text-gray-500 capitalize">Rango <?= e($canje['rango']) ?> · <?= (int)$canje['rango_descuento'] ?>% por rango</p>
  <p class="font-serif text-6xl font-bold text-brand-accent mt-6">+<?= (int)$canje['descuento_pct'] ?>%</p>
  <p class="text-sm text-gray-500 mt-1">Descuento total a aplicar: <b class="text-brand-green"><?= (int)$canje['rango_descuento'] + (int)$canje['descuento_pct'] ?>%</b></p>
  <?php if ($vigente): ?>
    <form method="POST" action="<?= url('staff_canje', ['t' => $canje['token']]) ?>" class="mt-7"><?= csrf_field() ?><button class="btn btn-primary btn-block">Aplicar y marcar como usado</button></form>
  <?php else: ?><p class="mt-7 rounded-xl bg-red-50 text-red-800 border border-red-200 py-3 font-semibold text-sm">QR <?= $canje['estado'] === 'usado' ? 'ya utilizado' : 'vencido' ?></p><?php endif; ?>
</div></main>
