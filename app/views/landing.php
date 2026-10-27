<?php
$pasos = [
  ['utensils', 'Mesa a la vista', 'En recepción, una tablet muestra cada mesa: capullo verde si está libre, flor roja si está ocupada.'],
  ['flower', 'Pedí con flores', 'Tocá el dibujo de la flor junto al plato en el menú virtual o en el menú físico para confirmar tu pedido.'],
  ['chef', 'Cocina y mozos conectados', 'La cocina ve cada plato con el número de mesa; el mozo lo retira y lo entrega donde corresponde.'],
  ['receipt', 'Código en la cuenta', 'Si pediste platos con flores, tu cuenta incluye un código para sumar puntos en la app.'],
  ['star', 'Subí de rango', 'Tus puntos te llevan de Semilla a Flor. Si pasa mucho tiempo sin visitarnos, bajás de rango.'],
  ['qr', 'Tu QR de descuento', 'Elegí cuántos puntos usar, generá tu QR y mostralo al mozo para aplicar el descuento.'],
];
?>
<!-- HERO -->
<section class="hero">
  <div class="container-x relative py-20 md:py-28 grid lg:grid-cols-2 gap-14 items-center">
    <div class="space-y-6">
      <p class="eyebrow !text-brand-petal">Restaurante de flores · Café · Vivero</p>
      <h1 class="font-serif text-5xl md:text-6xl font-bold leading-[1.08]">Cocina con flores.<br>Plantas con historia.<br><span class="italic text-brand-petal">Puntos que florecen.</span></h1>
      <p class="text-brand-cream/80 text-lg max-w-xl font-light">Platos con flores comestibles, plantas con QR para aprender a cuidarlas y un programa de rangos con descuentos reales en cada visita.</p>
      <div class="flex flex-wrap gap-3 pt-2">
        <a href="<?= url('registro') ?>" class="btn btn-accent">Crear mi cuenta <?= icon('arrow', 'w-4 h-4') ?></a>
        <a href="<?= url('reserva_nueva') ?>" class="btn btn-ghost">Reservar una mesa</a>
      </div>
      <p class="text-sm text-brand-cream/60">¿Ya tenés cuenta? <a class="underline" href="<?= url('login') ?>">Iniciá sesión</a> · <a class="underline" href="<?= url('olvide') ?>">Olvidé mi contraseña</a></p>
    </div>
    <div class="grid grid-cols-2 gap-4">
      <div class="photo rounded-3xl h-72 translate-y-8 shadow-2xl" style="background-image:url('https://images.unsplash.com/photo-1416879595882-3373a0480b5b?auto=format&fit=crop&w=700&q=80')"></div>
      <div class="space-y-4">
        <div class="photo rounded-3xl h-36 shadow-2xl" style="background-image:url('https://images.unsplash.com/photo-1490750967868-88aa4486c946?auto=format&fit=crop&w=700&q=80')"></div>
        <div class="photo rounded-3xl h-48 shadow-2xl" style="background-image:url('https://images.unsplash.com/photo-1495474472287-4d71bcdd2085?auto=format&fit=crop&w=700&q=80')"></div>
      </div>
    </div>
  </div>
</section>

<!-- CIFRAS -->
<section class="bg-white border-b border-brand-sage/20">
  <div class="container-x py-8 grid grid-cols-2 md:grid-cols-4 gap-6 text-center">
    <?php foreach ([['4', 'rangos con descuentos'], ['QR', 'único en cada planta'], ['100%', 'flores comestibles'], ['24/7', 'reservas online']] as [$n, $t]): ?>
      <div><p class="font-serif text-3xl font-bold text-brand-green"><?= $n ?></p><p class="text-sm text-gray-500"><?= $t ?></p></div>
    <?php endforeach; ?>
  </div>
</section>

<!-- COMO FUNCIONA -->
<section id="como" class="container-x py-20">
  <div class="text-center max-w-2xl mx-auto">
    <p class="eyebrow">Tu visita</p>
    <h2 class="font-serif text-4xl font-bold text-brand-green mt-2">Así funciona Mantirienda</h2>
    <p class="text-gray-500 mt-3">Del momento en que llegás hasta tu próximo descuento.</p>
  </div>
  <div class="grid md:grid-cols-3 gap-6 mt-12">
    <?php foreach ($pasos as $i => [$ic, $t, $d]): ?>
      <div class="card card-hover p-7">
        <div class="flex items-center justify-between"><span class="step-num"><?= $i + 1 ?></span><span class="text-brand-sage"><?= icon($ic, 'w-7 h-7') ?></span></div>
        <h3 class="font-serif text-xl font-bold text-brand-green mt-5"><?= $t ?></h3>
        <p class="text-sm text-gray-600 mt-2 leading-relaxed"><?= $d ?></p>
      </div>
    <?php endforeach; ?>
  </div>
</section>

<!-- MENU -->
<?php if ($platos): ?>
<section id="menu" class="bg-white py-20">
  <div class="container-x">
    <div class="flex flex-wrap items-end justify-between gap-4">
      <div><p class="eyebrow">Menú floral</p><h2 class="font-serif text-4xl font-bold text-brand-green mt-2">Platos que suman puntos</h2></div>
      <p class="text-gray-500 max-w-md text-sm">Cada plato con flores acumula puntos. Al pedir la cuenta recibís un código para cargarlos en tu perfil.</p>
    </div>
    <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-5 mt-10">
      <?php foreach ($platos as $p): ?>
        <div class="card card-hover p-6 flex flex-col">
          <span class="w-11 h-11 rounded-full bg-brand-petal/60 text-brand-accent grid place-items-center"><?= icon('flower', 'w-6 h-6') ?></span>
          <h3 class="font-serif text-lg font-bold text-brand-green mt-4"><?= e($p['nombre']) ?></h3>
          <p class="text-sm text-gray-500 mt-1 flex-1"><?= e($p['descripcion']) ?></p>
          <div class="flex items-center justify-between mt-5 pt-4 border-t border-gray-100"><b class="text-brand-green"><?= money($p['precio']) ?></b><span class="badge badge-ok">+<?= (int)$p['puntos'] ?> pts</span></div>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>
<?php endif; ?>

<!-- RANGOS -->
<section id="rangos" class="container-x py-20">
  <div class="text-center max-w-2xl mx-auto"><p class="eyebrow">Programa de puntos</p><h2 class="font-serif text-4xl font-bold text-brand-green mt-2">Cuatro rangos para florecer</h2>
    <p class="text-gray-500 mt-3">Cada rango tiene su descuento en comidas y misiones propias.</p></div>
  <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-5 mt-12">
    <?php foreach ($rangos as $i => $r): ?>
      <div class="card card-hover p-7 text-center relative">
        <span class="w-14 h-14 rounded-full mx-auto grid place-items-center <?= $i === 3 ? 'bg-brand-accent text-white' : 'bg-brand-green/10 text-brand-green' ?>"><?= rank_icon($r['nombre'], 'w-7 h-7') ?></span>
        <h3 class="font-serif text-xl font-bold capitalize text-brand-green mt-4"><?= e($r['nombre']) ?></h3>
        <p class="text-xs text-gray-500">desde <?= (int)$r['puntos_min'] ?> puntos</p>
        <p class="mt-4 font-serif font-bold text-brand-accent text-4xl"><?= (int)$r['descuento_pct'] ?>%</p><p class="text-xs text-gray-500">de descuento en comidas</p>
      </div>
    <?php endforeach; ?>
  </div>
  <div class="grid md:grid-cols-2 gap-6 mt-8">
    <div class="card p-7 flex gap-4"><span class="text-brand-green shrink-0"><?= icon('target', 'w-8 h-8') ?></span><div><h3 class="font-serif text-xl font-bold text-brand-green">Misiones por rango</h3><p class="text-sm text-gray-600 mt-1">Completalas y ganá descuentos extra en tu comida.</p></div></div>
    <div class="card p-7 flex gap-4 bg-brand-petal/30"><span class="text-brand-accent shrink-0"><?= icon('flower', 'w-8 h-8') ?></span><div><h3 class="font-serif text-xl font-bold text-brand-green">Misión extra: pintá tu flor</h3><p class="text-sm text-gray-600 mt-1">Con un pago opcional te damos una flor de cerámica para pintar y, al terminar, una galletita de regalo.</p></div></div>
  </div>
</section>

<!-- PLANTAS -->
<section id="plantas" class="bg-white py-20">
  <div class="container-x">
    <?php require __DIR__ . '/partials/info_flores.php'; ?>
  </div>
</section>

<!-- CTA -->
<section class="hero">
  <div class="container-x relative py-16 text-center space-y-5">
    <h2 class="font-serif text-4xl font-bold">Empezá hoy como Semilla</h2>
    <p class="text-brand-cream/75 max-w-xl mx-auto">Creá tu cuenta gratis, reservá tu mesa y empezá a sumar puntos desde tu primera visita.</p>
    <a href="<?= url('registro') ?>" class="btn btn-accent">Registrarme gratis <?= icon('arrow', 'w-4 h-4') ?></a>
  </div>
</section>
