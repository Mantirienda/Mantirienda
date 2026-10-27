<main class="container-x py-16"><div class="max-w-md mx-auto card p-8 md:p-10">
  <div class="text-center"><span class="w-12 h-12 rounded-full bg-brand-green text-white grid place-items-center mx-auto"><?= icon('lock', 'w-6 h-6') ?></span>
    <h1 class="font-serif text-3xl font-bold text-brand-green mt-4">Iniciar sesión</h1><p class="text-sm text-gray-500 mt-1">Accedé a tus plantas, reservas y puntos.</p></div>
  <form method="POST" action="<?= url('login') ?>" class="space-y-4 mt-7"><?= csrf_field() ?>
    <div><label class="label">Email</label><input class="input" type="email" name="email" autocomplete="email" required></div>
    <div><label class="label">Contraseña</label><input class="input" type="password" name="password" autocomplete="current-password" required></div>
    <button class="btn btn-primary btn-block">Entrar</button>
  </form>
  <div class="text-sm text-center mt-6 space-y-1 text-gray-600"><a class="text-brand-leaf font-semibold hover:underline" href="<?= url('olvide') ?>">Olvidé mi contraseña</a><p>¿No tenés cuenta? <a class="text-brand-leaf font-semibold hover:underline" href="<?= url('registro') ?>">Registrate</a></p></div>
</div></main>
