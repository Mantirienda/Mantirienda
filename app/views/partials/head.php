<?php $user = user(); $flash = take_flash(); ?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= e($title ?? 'Mantirienda') ?> · Mantirienda</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,500;0,700;1,400&family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  <script>
    tailwind.config = { theme: { extend: {
      colors: { brand: { green: '#2D4A3E', dark: '#1f3a2e', sage: '#8A9A86', cream: '#FDFBF7', coffee: '#6F4E37', accent: '#D9795B', petal: '#F7D6D0', leaf: '#4F7F63' } },
      fontFamily: { serif: ['Playfair Display', 'serif'], sans: ['Plus Jakarta Sans', 'sans-serif'] } } } }
  </script>
  <link rel="stylesheet" href="public/css/styles.css">
</head>
<body class="text-gray-800 font-sans antialiased min-h-screen flex flex-col">
<header class="bg-white/90 backdrop-blur-md sticky top-0 z-50 border-b border-brand-sage/20">
  <div class="container-x py-3.5 flex justify-between items-center">
    <a href="index.php" class="flex items-center gap-2.5">
      <span class="w-9 h-9 rounded-full bg-brand-green text-white grid place-items-center"><?= icon('flower', 'w-5 h-5') ?></span>
      <span class="font-serif text-2xl font-bold text-brand-green">Mantirienda</span>
    </a>
    <nav class="hidden md:flex items-center gap-7 text-sm font-medium text-gray-600">
      <a href="index.php#como" class="hover:text-brand-green">Cómo funciona</a>
      <a href="index.php#menu" class="hover:text-brand-green">Menú floral</a>
      <a href="index.php#rangos" class="hover:text-brand-green">Rangos</a>
      <a href="index.php#plantas" class="hover:text-brand-green">Plantas</a>
    </nav>
    <div class="flex items-center gap-3 text-sm font-semibold">
      <?php if (!$user): ?>
        <a href="<?= url('login') ?>" class="text-brand-green hover:text-brand-leaf">Iniciar sesión</a>
        <a href="<?= url('registro') ?>" class="btn btn-primary !py-2">Registrarse</a>
      <?php else: ?>
        <div class="relative">
          <button id="menuBtn" class="flex items-center gap-2 pl-1.5 pr-3 py-1.5 rounded-full border border-brand-sage/40 hover:bg-brand-cream transition">
            <span class="w-8 h-8 rounded-full bg-brand-green text-white grid place-items-center text-sm font-bold"><?= e(strtoupper(mb_substr($user['nombre'], 0, 1))) ?></span>
            <span class="hidden sm:inline text-brand-green"><?= e(explode(' ', $user['nombre'])[0]) ?></span><?= icon('chevron', 'w-4 h-4 text-gray-500') ?>
          </button>
          <div id="menuList" class="hidden absolute right-0 mt-2 w-60 bg-white rounded-2xl shadow-xl border border-brand-sage/20 overflow-hidden py-1">
            <?php if (in_array($user['rol'], ['mozo', 'admin'])): ?><a class="menu-i" href="<?= url('staff') ?>"><?= icon('chef', 'w-4 h-4') ?>Panel del salón</a><?php endif; ?>
            <a class="menu-i" href="<?= url('panel') ?>"><?= icon('sprout', 'w-4 h-4') ?>Mis plantas</a>
            <a class="menu-i" href="<?= url('identificador') ?>"><?= icon('leaf', 'w-4 h-4') ?>Identificador de plantas</a>
            <a class="menu-i" href="<?= url('reservas') ?>"><?= icon('calendar', 'w-4 h-4') ?>Reservas</a>
            <a class="menu-i" href="<?= url('puntos') ?>"><?= icon('star', 'w-4 h-4') ?>Puntos y rangos</a>
            <a class="menu-i" href="<?= url('perfil') ?>"><?= icon('user', 'w-4 h-4') ?>Mi perfil</a>
            <div class="divider my-1"></div>
            <a class="menu-i text-red-600" href="<?= url('logout') ?>"><?= icon('logout', 'w-4 h-4') ?>Cerrar sesión</a>
          </div>
        </div>
      <?php endif; ?>
    </div>
  </div>
</header>
<style>.menu-i{display:flex;align-items:center;gap:.7rem;padding:.7rem 1.1rem;font-size:.875rem;font-weight:500}.menu-i:hover{background:#FDFBF7}</style>
<div class="flex-1">
<?php if ($flash): ?>
  <div class="container-x pt-6"><div class="max-w-3xl mx-auto flex gap-3 items-start rounded-xl px-5 py-3.5 text-sm font-medium border <?= $flash['tipo'] === 'ok' ? 'bg-green-50 text-green-800 border-green-200' : 'bg-red-50 text-red-800 border-red-200' ?>">
    <?= icon($flash['tipo'] === 'ok' ? 'check' : 'alert', 'w-5 h-5 shrink-0 mt-0.5') ?>
    <div><?= e($flash['msg']) ?><?php if ($flash['link']): ?><br><a class="underline break-all" href="<?= e($flash['link']) ?>"><?= e($flash['link']) ?></a><?php endif; ?></div></div></div>
<?php endif; ?>
