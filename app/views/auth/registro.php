<main class="container-x py-16"><div class="max-w-md mx-auto card p-8 md:p-10">
  <div class="text-center"><span class="w-12 h-12 rounded-full bg-brand-green text-white grid place-items-center mx-auto"><?= icon('sprout', 'w-6 h-6') ?></span>
    <h1 class="font-serif text-3xl font-bold text-brand-green mt-4">Crear cuenta</h1><p class="text-sm text-gray-500 mt-1">Empezás en el rango Semilla.</p></div>
  <form method="POST" action="<?= url('registro') ?>" class="space-y-4 mt-7"><?= csrf_field() ?>
    <div><label class="label">Nombre y apellido</label><input class="input" name="nombre" autocomplete="name" required></div>
    <div><label class="label">Email</label><input class="input" type="email" name="email" autocomplete="email" required><p class="hint">Usá un email real: lo vamos a usar para recuperar tu cuenta.</p></div>
    <div><label class="label">Contraseña</label><input class="input" type="password" name="password" minlength="8" autocomplete="new-password" required><p class="hint">Mínimo 8 caracteres, con letras y números.</p></div>
    <button class="btn btn-primary btn-block">Crear cuenta</button>
  </form>
  <p class="text-sm text-center mt-6 text-gray-600">¿Ya tenés cuenta? <a class="text-brand-leaf font-semibold hover:underline" href="<?= url('login') ?>">Iniciá sesión</a></p>
</div></main>
