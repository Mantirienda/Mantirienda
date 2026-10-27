<?php
$max = min((int)$perfil['puntos_disponibles'], TOPE_PUNTOS);
$maxSlider = intdiv($max, PUNTOS_POR_5PCT) * PUNTOS_POR_5PCT;
page_header('Puntos y rangos', 'Sumá puntos, subí de rango y canjealos por descuentos.', 'puntos');
?>
<main class="container-x py-10 space-y-10">
  <div class="hero rounded-3xl">
    <div class="relative p-8 md:p-10 grid md:grid-cols-3 gap-8 items-center">
      <div class="flex items-center gap-4"><span class="w-16 h-16 rounded-full bg-white/10 grid place-items-center shrink-0"><?= rank_icon($perfil['rango'], 'w-8 h-8') ?></span>
        <div><p class="text-xs uppercase tracking-widest text-brand-petal font-bold">Tu rango</p><p class="font-serif text-3xl font-bold capitalize"><?= e($perfil['rango']) ?></p><p class="text-sm text-white/70"><?= (int)$perfil['rango_descuento'] ?>% de descuento en comidas</p></div></div>
      <div class="md:text-center"><p class="font-serif text-6xl font-bold leading-none"><?= (int)$perfil['puntos_disponibles'] ?></p><p class="text-sm text-white/70 mt-1">puntos disponibles</p></div>
      <div class="text-sm">
        <?php if ($siguiente): $falta = $siguiente['puntos_min'] - $perfil['puntos_rango']; ?>
          <p>Te faltan <b><?= (int)$falta ?> pts</b> para <b class="capitalize"><?= e($siguiente['nombre']) ?></b></p>
          <div class="h-2 bg-white/20 rounded-full mt-2"><div class="h-2 bg-brand-accent rounded-full" style="width: <?= min(100, round($perfil['puntos_rango'] / $siguiente['puntos_min'] * 100)) ?>%"></div></div>
        <?php else: ?><p>Llegaste al rango máximo.</p><?php endif; ?>
        <p class="text-xs text-white/55 mt-3">Si pasás <?= DIAS_INACTIVIDAD ?> días sin visitarnos, bajás un rango.</p>
      </div>
    </div>
  </div>

  <div class="grid md:grid-cols-2 gap-7">
    <section class="card p-7">
      <h2 class="font-serif text-xl font-bold text-brand-green flex items-center gap-2"><?= icon('receipt', 'w-5 h-5') ?>Ingresar código de la cuenta</h2>
      <p class="text-sm text-gray-500 mt-1">Lo encontrás al final de tu cuenta si pediste platos con flores.</p>
      <form method="POST" action="<?= url('puntos_codigo') ?>" class="flex gap-3 mt-5"><?= csrf_field() ?>
        <input class="input uppercase tracking-widest" name="codigo" placeholder="CÓDIGO" maxlength="12" required><button class="btn btn-primary">Sumar</button></form>
    </section>
    <section class="card p-7">
      <h2 class="font-serif text-xl font-bold text-brand-green flex items-center gap-2"><?= icon('ticket', 'w-5 h-5') ?>Canjear por descuento</h2>
      <?php if ($maxSlider < PUNTOS_POR_5PCT): ?>
        <p class="text-sm text-gray-500 mt-2">Necesitás al menos <?= PUNTOS_POR_5PCT ?> puntos para canjear.</p>
      <?php else: ?>
        <form method="POST" action="<?= url('puntos_canje') ?>" class="mt-4 space-y-3"><?= csrf_field() ?>
          <input id="puntosSlider" data-per="<?= PUNTOS_POR_5PCT ?>" type="range" name="puntos" min="<?= PUNTOS_POR_5PCT ?>" max="<?= $maxSlider ?>" step="<?= PUNTOS_POR_5PCT ?>" class="w-full accent-green-800">
          <p class="text-sm">Usar <b id="puntosOut"></b> pts = <b id="pctOut"></b>% de descuento <span class="text-gray-400">(se suma al de tu rango)</span></p>
          <button class="btn btn-accent">Generar QR</button>
        </form>
      <?php endif; ?>
      <?php if ($canjes): ?><div class="mt-5 pt-4 border-t border-gray-100 text-sm space-y-1"><p class="font-semibold text-brand-green">QR activos</p>
        <?php foreach ($canjes as $c): ?><a class="block text-brand-leaf hover:underline" href="<?= url('canje', ['t' => $c['token']]) ?>"><?= (int)$c['descuento_pct'] ?>% de descuento · vence <?= e(date('d/m H:i', strtotime($c['expira_en']))) ?></a><?php endforeach; ?></div><?php endif; ?>
    </section>
  </div>

  <section>
    <h2 class="font-serif text-2xl font-bold text-brand-green mb-5">Misiones de tu rango</h2>
    <div class="grid md:grid-cols-2 gap-5">
      <?php foreach ($misiones as $m): ?>
        <div class="card p-6 <?= $m['es_premium'] ? 'bg-brand-petal/30' : '' ?>">
          <div class="flex justify-between gap-3 items-start"><h3 class="font-semibold text-brand-green flex items-center gap-2"><span class="text-brand-accent"><?= icon($m['es_premium'] ? 'flower' : 'target', 'w-5 h-5') ?></span><?= e($m['titulo']) ?></h3>
            <span class="badge <?= $m['progreso'] === 'completada' ? 'badge-ok' : 'badge-off' ?> whitespace-nowrap"><?= $m['progreso'] === 'completada' ? 'Completada' : ($m['es_premium'] ? 'Extra · ' . money($m['precio']) : (int)$m['recompensa_pct'] . '% OFF') ?></span></div>
          <p class="text-sm text-gray-600 mt-2"><?= e($m['descripcion']) ?></p>
          <?php if ($m['es_premium']): ?><p class="text-xs text-gray-500 mt-2">Pedila en el local: te entregamos tu flor de cerámica para pintar y una galletita al terminar.</p><?php endif; ?>
        </div>
      <?php endforeach; ?>
    </div>
  </section>

  <section>
    <h2 class="font-serif text-2xl font-bold text-brand-green mb-5">Historial de puntos</h2>
    <div class="card divide-y divide-gray-100">
      <?php if (!$movimientos): ?><p class="p-6 text-sm text-gray-500">Sin movimientos todavía.</p><?php endif; ?>
      <?php foreach ($movimientos as $m): ?>
        <div class="px-6 py-3.5 flex justify-between text-sm"><span><?= e($m['detalle']) ?> <span class="text-gray-400">· <?= fecha_es($m['creado_en']) ?></span></span>
          <b class="<?= $m['puntos'] > 0 ? 'text-green-700' : 'text-red-600' ?>"><?= $m['puntos'] > 0 ? '+' : '' ?><?= (int)$m['puntos'] ?></b></div>
      <?php endforeach; ?>
    </div>
  </section>
</main>
