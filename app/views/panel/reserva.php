<?php
$pagada = $r['pago_estado'] === 'pagado';
page_header('Reserva ' . $r['codigo'], 'Detalle de tu reserva y estado de la seña.', 'reservas');
?>
<main class="container-x py-10 grid lg:grid-cols-3 gap-7">
  <section class="card p-8 lg:col-span-2">
    <div class="flex flex-wrap justify-between items-start gap-3">
      <div><p class="eyebrow">Fecha y hora</p><h2 class="font-serif text-3xl font-bold text-brand-green mt-1"><?= fecha_es($r['fecha']) ?> · <?= substr($r['hora'], 0, 5) ?> hs</h2></div>
      <span class="badge <?= $r['estado'] === 'cancelada' ? 'badge-bad' : 'badge-ok' ?>"><?= $r['estado'] === 'cancelada' ? 'Cancelada' : ucfirst($r['estado']) ?></span>
    </div>
    <dl class="grid sm:grid-cols-2 gap-x-8 gap-y-5 mt-8 text-sm">
      <?php foreach ([['Código', $r['codigo']], ['A nombre de', $r['nombre_contacto']], ['Teléfono', $r['telefono']], ['Personas', $r['personas']], ['Mesa', 'Mesa ' . $r['mesa']], ['Zona', ZONAS[$r['zona']] ?? ''], ['Ocasión', $r['ocasion'] ?: 'Sin ocasión especial'], ['Comentarios', $r['notas'] ?: 'Ninguno']] as [$k, $val]): ?>
        <div><dt class="text-xs font-bold uppercase tracking-wider text-gray-400"><?= $k ?></dt><dd class="font-medium text-gray-800 mt-0.5"><?= e($val) ?></dd></div>
      <?php endforeach; ?>
    </dl>
    <div class="flex flex-wrap gap-3 mt-8 pt-6 border-t border-gray-100">
      <a href="<?= url('reservas') ?>" class="btn btn-outline">Volver a mis reservas</a>
      <?php if (puede_cancelar($r)): ?>
        <form method="POST" action="<?= url('reserva_cancelar') ?>" onsubmit="return confirm('¿Cancelar esta reserva?')"><?= csrf_field() ?><input type="hidden" name="id" value="<?= (int)$r['id'] ?>"><button class="btn !text-red-600 border-red-200 hover:bg-red-50">Cancelar reserva</button></form>
      <?php endif; ?>
    </div>
  </section>

  <aside class="card p-8 h-fit">
    <p class="eyebrow">Seña</p>
    <p class="font-serif text-4xl font-bold text-brand-green mt-1"><?= money($r['monto_sena']) ?></p>
    <span class="badge mt-2 <?= $pagada ? 'badge-ok' : 'badge-warn' ?>"><?= $pagada ? 'Pagada' : 'Pendiente de pago' ?></span>
    <div class="divider my-5"></div>
    <p class="text-sm font-semibold text-brand-green"><?= e(METODOS[$r['metodo_pago']] ?? '') ?></p>
    <?php if ($r['metodo_pago'] === 'transferencia'): ?>
      <dl class="text-sm text-gray-600 mt-3 space-y-1"><div>Titular: <b><?= e(TRANSF_TITULAR) ?></b></div><div>Alias: <b><?= e(TRANSF_ALIAS) ?></b></div><div class="break-all">CBU: <b><?= e(TRANSF_CBU) ?></b></div><div>Referencia: <b><?= e($r['codigo']) ?></b></div></dl>
      <p class="text-xs text-gray-500 mt-3">Enviá el comprobante al local indicando tu código de reserva.</p>
    <?php elseif ($r['metodo_pago'] === 'mercadopago'): ?>
      <p class="text-sm text-gray-600 mt-2">El cobro online todavía no está habilitado. El local te contacta para completar el pago de la seña.</p>
    <?php else: ?>
      <p class="text-sm text-gray-600 mt-2">Abonás la seña en efectivo al llegar al local.</p>
    <?php endif; ?>
  </aside>
</main>
