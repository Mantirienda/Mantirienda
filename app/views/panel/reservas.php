<?php page_header('Reservas', 'Tus reservas actuales y el historial.', 'reservas'); ?>
<main class="container-x py-10 space-y-12">
  <div class="card p-6 flex flex-wrap items-center justify-between gap-4 bg-brand-green text-white border-0">
    <div><h2 class="font-serif text-2xl font-bold">¿Querés reservar una mesa?</h2><p class="text-white/75 text-sm mt-1">Reservá con al menos <?= DIAS_ANTICIPACION ?> días de anticipación. Seña de <?= money(SENA_POR_PERSONA) ?> por persona, que se descuenta de tu cuenta.</p></div>
    <a href="<?= url('reserva_nueva') ?>" class="btn btn-accent">Nueva reserva <?= icon('arrow', 'w-4 h-4') ?></a>
  </div>

  <section>
    <h2 class="font-serif text-2xl font-bold text-brand-green mb-5">Reservas actuales</h2>
    <?php if (!$actuales): ?><div class="card p-8 text-center text-gray-500 text-sm">No tenés reservas próximas.</div><?php endif; ?>
    <div class="grid md:grid-cols-2 gap-5">
      <?php foreach ($actuales as $r): ?>
        <div class="card p-6">
          <div class="flex justify-between items-start gap-3">
            <div><p class="text-xs font-bold tracking-wider text-gray-400">RESERVA <?= e($r['codigo'] ?: '#' . $r['id']) ?></p>
              <p class="font-serif text-xl font-bold text-brand-green mt-1"><?= fecha_es($r['fecha']) ?> · <?= substr($r['hora'], 0, 5) ?> hs</p></div>
            <span class="badge <?= $r['pago_estado'] === 'pagado' ? 'badge-ok' : 'badge-warn' ?>"><?= $r['pago_estado'] === 'pagado' ? 'Seña pagada' : 'Seña pendiente' ?></span>
          </div>
          <p class="text-sm text-gray-500 mt-3 flex flex-wrap gap-x-4 gap-y-1">
            <span class="inline-flex items-center gap-1.5"><?= icon('users', 'w-4 h-4') ?><?= (int)$r['personas'] ?> persona<?= $r['personas'] > 1 ? 's' : '' ?></span>
            <span class="inline-flex items-center gap-1.5"><?= icon('pin', 'w-4 h-4') ?>Mesa <?= e($r['mesa']) ?> · <?= e(ZONAS[$r['zona']] ?? '') ?></span></p>
          <div class="flex items-center justify-between mt-5 pt-4 border-t border-gray-100">
            <a href="<?= url('reserva', ['id' => $r['id']]) ?>" class="text-sm font-semibold text-brand-leaf hover:underline">Ver detalle y pago</a>
            <?php if (puede_cancelar($r)): ?>
              <form method="POST" action="<?= url('reserva_cancelar') ?>" onsubmit="return confirm('¿Cancelar esta reserva?')"><?= csrf_field() ?><input type="hidden" name="id" value="<?= (int)$r['id'] ?>"><button class="text-sm text-red-600 hover:underline">Cancelar</button></form>
            <?php else: ?><span class="text-xs text-gray-400">Cancelación cerrada</span><?php endif; ?>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  </section>

  <section>
    <h2 class="font-serif text-2xl font-bold text-brand-green mb-5">Reservas anteriores</h2>
    <?php if (!$anteriores): ?><div class="card p-8 text-center text-gray-500 text-sm">Todavía no hay historial.</div><?php else: ?>
    <div class="card divide-y divide-gray-100">
      <?php foreach ($anteriores as $r): ?>
        <div class="px-6 py-4 flex flex-wrap items-center justify-between gap-3">
          <div><b class="text-brand-green"><?= fecha_es($r['fecha']) ?> · <?= substr($r['hora'], 0, 5) ?> hs</b>
            <p class="text-sm text-gray-500"><?= (int)$r['personas'] ?> pers. · Mesa <?= e($r['mesa'] ?: '-') ?><?= $r['codigo'] ? ' · ' . e($r['codigo']) : '' ?></p></div>
          <span class="badge <?= $r['estado'] === 'cancelada' ? 'badge-bad' : 'badge-off' ?>"><?= $r['estado'] === 'confirmada' ? 'Realizada' : ($r['estado'] === 'cancelada' ? 'Cancelada' : 'Completada') ?></span>
        </div>
      <?php endforeach; ?>
    </div><?php endif; ?>
  </section>
</main>
