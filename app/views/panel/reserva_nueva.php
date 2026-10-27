<?php
$min = date('Y-m-d', strtotime('+' . DIAS_ANTICIPACION . ' days')); $max = date('Y-m-d', strtotime('+' . DIAS_MAX_RESERVA . ' days'));
$v = fn($k, $def = '') => e($old[$k] ?? $def);
$metodoSel = $old['metodo'] ?? 'transferencia';
$iconos = ['transferencia' => 'bank', 'mercadopago' => 'card', 'efectivo' => 'cash'];
$descr = ['transferencia' => 'Transferí la seña y enviá el comprobante.', 'mercadopago' => 'Tarjeta o dinero en cuenta.', 'efectivo' => 'Abonás la seña al llegar al local.'];
page_header('Nueva reserva', 'Completá los datos de tu visita. Reservamos con ' . DIAS_ANTICIPACION . ' días de anticipación como mínimo.', 'reservas');
?>
<main class="container-x py-10">
  <form id="formReserva" method="POST" action="<?= url('reserva_nueva') ?>" data-sena="<?= SENA_POR_PERSONA ?>" class="grid lg:grid-cols-3 gap-7"><?= csrf_field() ?>
    <div class="lg:col-span-2 space-y-7">
      <section class="card p-8">
        <h2 class="font-serif text-xl font-bold text-brand-green flex items-center gap-2"><span class="step-num !w-8 !h-8 text-sm">1</span>Datos de contacto</h2>
        <div class="grid sm:grid-cols-2 gap-4 mt-5">
          <div><label class="label">Nombre de quien reserva</label><input class="input" name="nombre" value="<?= $v('nombre', $perfil['nombre']) ?>" required></div>
          <div><label class="label">Teléfono de contacto</label><input class="input" name="telefono" value="<?= $v('telefono', $perfil['telefono']) ?>" placeholder="Ej: 11 5555 1234" required><p class="hint">Te escribimos por cualquier cambio.</p></div>
        </div>
      </section>

      <section class="card p-8">
        <h2 class="font-serif text-xl font-bold text-brand-green flex items-center gap-2"><span class="step-num !w-8 !h-8 text-sm">2</span>Tu visita</h2>
        <div class="grid sm:grid-cols-2 gap-4 mt-5">
          <div><label class="label">Fecha</label><input class="input" type="date" name="fecha" min="<?= $min ?>" max="<?= $max ?>" value="<?= $v('fecha') ?>" required><p class="hint">Desde el <?= fecha_es($min) ?> (<?= DIAS_ANTICIPACION ?> días de anticipación).</p></div>
          <div><label class="label">Horario</label><select class="input" name="hora" required><option value="">Elegí un horario</option>
            <?php foreach (horarios() as $h): ?><option value="<?= $h ?>" <?= ($old['hora'] ?? '') === $h ? 'selected' : '' ?>><?= $h ?> hs</option><?php endforeach; ?></select></div>
          <div><label class="label">Cantidad de personas</label><input class="input" type="number" name="personas" min="1" max="8" value="<?= $v('personas', 2) ?>" required><p class="hint">De 1 a 8 personas.</p></div>
          <div><label class="label">Zona preferida</label><select class="input" name="zona"><?php foreach (ZONAS as $k => $n): ?><option value="<?= $k ?>" <?= ($old['zona'] ?? 'salon') === $k ? 'selected' : '' ?>><?= $n ?></option><?php endforeach; ?></select><p class="hint">Sujeta a disponibilidad.</p></div>
          <div class="sm:col-span-2"><label class="label">Ocasión (opcional)</label><select class="input" name="ocasion"><option value="">Sin ocasión especial</option>
            <?php foreach (OCASIONES as $o): ?><option <?= ($old['ocasion'] ?? '') === $o ? 'selected' : '' ?>><?= $o ?></option><?php endforeach; ?></select></div>
          <div class="sm:col-span-2"><label class="label">Comentarios (opcional)</label><textarea class="input" name="notas" rows="3" placeholder="Alergias, restricciones alimentarias, silla para bebé, accesibilidad..."><?= $v('notas') ?></textarea></div>
        </div>
      </section>

      <section class="card p-8">
        <h2 class="font-serif text-xl font-bold text-brand-green flex items-center gap-2"><span class="step-num !w-8 !h-8 text-sm">3</span>Seña y método de pago</h2>
        <p class="text-sm text-gray-500 mt-2">La seña confirma tu mesa y se descuenta del total de tu cuenta.</p>
        <div class="grid sm:grid-cols-3 gap-3 mt-5">
          <?php foreach (METODOS as $k => $n): ?>
            <label class="pay-opt relative"><input type="radio" name="metodo" value="<?= $k ?>" <?= $metodoSel === $k ? 'checked' : '' ?>>
              <span class="box block"><span class="text-brand-green"><?= icon($iconos[$k], 'w-6 h-6') ?></span><span class="block font-semibold text-sm text-brand-green mt-2"><?= $n ?></span><span class="block text-xs text-gray-500 mt-0.5"><?= $descr[$k] ?></span></span></label>
          <?php endforeach; ?>
        </div>
        <div class="mt-5 rounded-xl bg-brand-cream border border-brand-sage/25 p-5 text-sm">
          <div class="pay-info" data-m="transferencia">
            <p class="font-semibold text-brand-green">Datos para la transferencia</p>
            <dl class="grid grid-cols-[auto_1fr] gap-x-5 gap-y-1 mt-2 text-gray-600"><dt>Titular</dt><dd class="font-medium"><?= e(TRANSF_TITULAR) ?></dd><dt>Alias</dt><dd class="font-medium"><?= e(TRANSF_ALIAS) ?></dd><dt>CBU</dt><dd class="font-medium break-all"><?= e(TRANSF_CBU) ?></dd></dl>
            <p class="text-xs text-gray-500 mt-3">Al terminar la reserva vas a ver tu código. Usalo como referencia y enviá el comprobante al local.</p>
          </div>
          <div class="pay-info hidden" data-m="mercadopago"><p class="font-semibold text-brand-green">Mercado Pago</p><p class="text-gray-600 mt-1">Pagás con tarjeta o dinero en cuenta. Por ahora el cobro online no está habilitado: tu reserva queda registrada con la seña pendiente y el local te contacta para completar el pago.</p></div>
          <div class="pay-info hidden" data-m="efectivo"><p class="font-semibold text-brand-green">Efectivo en el local</p><p class="text-gray-600 mt-1">Abonás la seña al llegar. Tu mesa se mantiene 15 minutos después del horario reservado.</p></div>
        </div>
      </section>

      <label class="flex items-start gap-3 text-sm text-gray-600 px-1"><input type="checkbox" name="acepto" class="mt-1 accent-green-800" <?= isset($old['nombre']) ? 'checked' : '' ?>>
        <span>Acepto las condiciones: la reserva se puede cancelar hasta <?= HORAS_CANCELACION ?> horas antes y la mesa se mantiene 15 minutos.</span></label>
    </div>

    <aside class="lg:sticky lg:top-24 h-fit space-y-5">
      <div class="card p-7">
        <h3 class="font-serif text-xl font-bold text-brand-green">Resumen</h3>
        <dl class="mt-4 space-y-2 text-sm"><div class="flex justify-between"><dt class="text-gray-500">Personas</dt><dd id="personasOut" class="font-semibold">-</dd></div>
          <div class="flex justify-between"><dt class="text-gray-500">Seña por persona</dt><dd class="font-semibold"><?= money(SENA_POR_PERSONA) ?></dd></div></dl>
        <div class="divider my-4"></div>
        <div class="flex justify-between items-baseline"><span class="text-sm text-gray-500">Seña a abonar</span><span id="senaOut" class="font-serif text-3xl font-bold text-brand-green">$0</span></div>
        <button class="btn btn-primary btn-block mt-6">Confirmar reserva</button>
      </div>
      <div class="card p-6 text-sm text-gray-600 space-y-3">
        <p class="flex gap-3"><span class="text-brand-leaf shrink-0"><?= icon('calendar', 'w-5 h-5') ?></span>Anticipación mínima de <?= DIAS_ANTICIPACION ?> días.</p>
        <p class="flex gap-3"><span class="text-brand-leaf shrink-0"><?= icon('clock', 'w-5 h-5') ?></span>Atendemos de <?= HORA_APERTURA ?> a <?= HORA_CIERRE ?> hs.</p>
        <p class="flex gap-3"><span class="text-brand-leaf shrink-0"><?= icon('receipt', 'w-5 h-5') ?></span>La seña se descuenta de tu cuenta.</p>
      </div>
    </aside>
  </form>
</main>
