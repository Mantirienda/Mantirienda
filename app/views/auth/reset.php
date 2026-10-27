<main class="container-x py-16"><div class="max-w-md mx-auto card p-8 md:p-10">
  <div class="text-center"><span class="w-12 h-12 rounded-full bg-brand-green text-white grid place-items-center mx-auto"><?= icon('lock', 'w-6 h-6') ?></span>
    <h1 class="font-serif text-3xl font-bold text-brand-green mt-4">Nueva contraseña</h1></div>
  <form method="POST" action="<?= url('reset', ['t' => $token]) ?>" class="space-y-4 mt-7"><?= csrf_field() ?>
    <div><label class="label">Nueva contraseña</label><input class="input" type="password" name="password" minlength="8" required><p class="hint">Mínimo 8 caracteres, con letras y números.</p></div>
    <button class="btn btn-primary btn-block">Guardar contraseña</button>
  </form>
</div></main>
