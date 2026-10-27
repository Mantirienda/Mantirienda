<main class="container-x py-16"><div class="max-w-md mx-auto card p-8 md:p-10">
  <div class="text-center"><span class="w-12 h-12 rounded-full bg-brand-green text-white grid place-items-center mx-auto"><?= icon('mail', 'w-6 h-6') ?></span>
    <h1 class="font-serif text-3xl font-bold text-brand-green mt-4">Recuperar contraseña</h1><p class="text-sm text-gray-500 mt-1">Ingresá tu email y te enviamos un enlace.</p></div>
  <form method="POST" action="<?= url('olvide') ?>" class="space-y-4 mt-7"><?= csrf_field() ?>
    <div><label class="label">Email de tu cuenta</label><input class="input" type="email" name="email" required></div>
    <button class="btn btn-primary btn-block">Enviar enlace</button>
  </form>
  <p class="text-sm text-center mt-6"><a class="text-brand-leaf font-semibold hover:underline" href="<?= url('login') ?>">Volver a iniciar sesión</a></p>
</div></main>
