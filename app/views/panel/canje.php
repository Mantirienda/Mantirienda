<main class="container-x py-14"><div class="max-w-md mx-auto card p-9 text-center">
  <p class="eyebrow">Descuento canjeado</p>
  <p class="font-serif text-6xl font-bold text-brand-accent mt-2"><?= (int)$canje['descuento_pct'] ?>%</p>
  <div class="qr flex justify-center my-6" data-url="<?= e($qrUrl) ?>" data-size="240"></div>
  <p class="text-sm text-gray-500">Mostrale este QR al mozo al pagar.<br><?= $canje['estado'] === 'usado' ? 'Este QR ya fue usado.' : 'Vence el ' . e(date('d/m/Y H:i', strtotime($canje['expira_en']))) . ' hs.' ?></p>
  <a href="<?= url('puntos') ?>" class="btn btn-outline mt-6">Volver a mis puntos</a>
</div></main>
