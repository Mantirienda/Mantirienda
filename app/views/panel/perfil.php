<?php page_header('Mi perfil', 'Tus datos personales y seguridad.', 'perfil'); ?>
<main class="container-x py-10 grid lg:grid-cols-3 gap-7">
  <div class="space-y-7 lg:col-span-2">
    <section class="card p-8">
      <h2 class="font-serif text-xl font-bold text-brand-green">Datos personales</h2>
      <form method="POST" action="<?= url('perfil') ?>" class="grid sm:grid-cols-2 gap-4 mt-5"><?= csrf_field() ?>
        <div class="sm:col-span-2"><label class="label">Nombre y apellido</label><input class="input" name="nombre" value="<?= e($perfil['nombre']) ?>" required></div>
        <div><label class="label">Email</label><input class="input" type="email" name="email" value="<?= e($perfil['email']) ?>" required><p class="hint">Tiene que ser un email real (ej. nombre@gmail.com).</p></div>
        <div><label class="label">Teléfono</label><input class="input" name="telefono" value="<?= e($perfil['telefono']) ?>" placeholder="Ej: 11 5555 1234"></div>
        <div class="sm:col-span-2"><button class="btn btn-primary">Guardar cambios</button></div>
      </form>
    </section>
    <section class="card p-8">
      <h2 class="font-serif text-xl font-bold text-brand-green">Cambiar contraseña</h2>
      <form method="POST" action="<?= url('perfil_pass') ?>" class="grid sm:grid-cols-2 gap-4 mt-5"><?= csrf_field() ?>
        <div><label class="label">Contraseña actual</label><input class="input" type="password" name="actual" required></div>
        <div><label class="label">Nueva contraseña</label><input class="input" type="password" name="nueva" minlength="8" required><p class="hint">Mínimo 8 caracteres, con letras y números.</p></div>
        <div class="sm:col-span-2"><button class="btn btn-outline">Actualizar contraseña</button></div>
      </form>
    </section>
  </div>
  <aside class="card p-8 h-fit text-center">
    <span class="w-16 h-16 rounded-full bg-brand-green text-white grid place-items-center mx-auto"><?= rank_icon($perfil['rango'], 'w-8 h-8') ?></span>
    <p class="eyebrow mt-4">Tu rango</p><p class="font-serif text-2xl font-bold capitalize text-brand-green"><?= e($perfil['rango']) ?></p>
    <p class="text-sm text-gray-500"><?= (int)$perfil['rango_descuento'] ?>% de descuento en comidas</p>
    <div class="divider my-5"></div>
    <p class="font-serif text-4xl font-bold text-brand-green"><?= (int)$perfil['puntos_disponibles'] ?></p><p class="text-xs text-gray-500">puntos disponibles</p>
    <a href="<?= url('puntos') ?>" class="btn btn-outline mt-5 !py-2">Ver mis puntos</a>
  </aside>
</main>
