<?php
function db(): PDO {
  static $pdo;
  if (!$pdo) $pdo = new PDO('mysql:host=' . DB_HOST . ';port=' . DB_PORT . ';dbname=' . DB_NAME . ';charset=utf8mb4', DB_USER, DB_PASS, [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC]);
  return $pdo;
}
function q(string $sql, array $p = []): PDOStatement { $s = db()->prepare($sql); $s->execute($p); return $s; }
function one(string $sql, array $p = []) { return q($sql, $p)->fetch() ?: null; }
function all(string $sql, array $p = []): array { return q($sql, $p)->fetchAll(); }

function e($v): string { return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8'); }
function url(string $r, array $p = []): string { return 'index.php?r=' . $r . ($p ? '&' . http_build_query($p) : ''); }
function abs_url(string $r, array $p = []): string {
  $base = BASE_URL ?: ((!empty($_SERVER['HTTPS']) ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'] . rtrim(dirname($_SERVER['SCRIPT_NAME']), '/\\') . '/');
  return rtrim($base, '/') . '/' . url($r, $p);
}
function redirect(string $r, array $p = []): void { header('Location: ' . url($r, $p)); exit; }
function es_post(): bool { return $_SERVER['REQUEST_METHOD'] === 'POST'; }

function flash(string $tipo, string $msg, ?string $link = null): void { $_SESSION['flash'] = compact('tipo', 'msg', 'link'); }
function take_flash(): ?array { $f = $_SESSION['flash'] ?? null; unset($_SESSION['flash']); return $f; }

function user(): ?array { return $_SESSION['user'] ?? null; }
function require_login(): void {
  if (user()) return;
  $_SESSION['volver'] = $_SERVER['REQUEST_URI'];
  flash('error', 'Iniciá sesión para continuar.'); redirect('login');
}
function require_staff(): void {
  require_login();
  if (!in_array(user()['rol'], ['mozo', 'admin'])) { flash('error', 'Solo para personal del restaurante.'); redirect('panel'); }
}
function uuid(): string {
  $d = random_bytes(16); $d[6] = chr(ord($d[6]) & 0x0f | 0x40); $d[8] = chr(ord($d[8]) & 0x3f | 0x80);
  return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($d), 4));
}
function render(string $vista, array $data = []): void {
  extract($data);
  require __DIR__ . '/../views/partials/head.php';
  require __DIR__ . "/../views/$vista.php";
  require __DIR__ . '/../views/partials/foot.php';
}

// ---------- Seguridad / validaciones ----------
function csrf_token(): string { if (empty($_SESSION['csrf'])) $_SESSION['csrf'] = bin2hex(random_bytes(16)); return $_SESSION['csrf']; }
function csrf_field(): string { return '<input type="hidden" name="csrf" value="' . csrf_token() . '">'; }

// Valida formato, errores de tipeo típicos (gmal.com, gmial.com...) y que el dominio exista
function email_error(string $e): ?string {
  $e = strtolower(trim($e));
  if (!filter_var($e, FILTER_VALIDATE_EMAIL)) return 'Ingresá un email válido.';
  $dom = substr(strrchr($e, '@'), 1);
  $conocidos = ['gmail.com', 'hotmail.com', 'hotmail.es', 'hotmail.com.ar', 'outlook.com', 'outlook.es', 'outlook.com.ar', 'live.com', 'live.com.ar',
    'yahoo.com', 'yahoo.es', 'yahoo.com.ar', 'yahoo.com.mx', 'icloud.com', 'me.com', 'msn.com', 'mail.com', 'proton.me', 'protonmail.com', 'fibertel.com.ar', 'speedy.com.ar'];
  if (in_array($dom, $conocidos, true)) return null;
  foreach (['gmail.com', 'hotmail.com', 'outlook.com', 'yahoo.com', 'icloud.com'] as $real)
    if (levenshtein($dom, $real) <= 2) return "El dominio \"$dom\" parece mal escrito. ¿Quisiste decir $real?";
  if (!preg_match('/\.[a-z]{2,}$/', $dom)) return 'El dominio del email no es válido.';
  if (function_exists('checkdnsrr') && !(checkdnsrr($dom, 'MX') || checkdnsrr($dom, 'A'))) return 'El dominio del email no existe. Revisalo.';
  return null;
}
function pass_error(string $p): ?string {
  return (strlen($p) < 8 || !preg_match('/[A-Za-z]/', $p) || !preg_match('/\d/', $p)) ? 'La contraseña debe tener al menos 8 caracteres, con letras y números.' : null;
}
function money($n): string { return '$' . number_format((float)$n, 0, ',', '.'); }
function fecha_es($f): string { return date('d/m/Y', strtotime($f)); }
function horarios(): array {
  $out = []; $t = strtotime(HORA_APERTURA); $fin = strtotime(HORA_CIERRE) - 3600;
  for (; $t <= $fin; $t += 1800) $out[] = date('H:i', $t);
  return $out;
}
function puede_cancelar(array $r): bool {
  return $r['estado'] === 'confirmada' && strtotime($r['fecha'] . ' ' . $r['hora']) - time() >= HORAS_CANCELACION * 3600;
}

// ---------- Iconos (SVG en línea, sin emojis) ----------
function icon(string $n, string $c = 'w-5 h-5'): string {
  static $p = [
    'leaf' => '<path d="M11 20A7 7 0 0 1 9.8 6.1C15.5 5 17 4.48 19 2c1 2 2 4.18 2 8 0 5.5-4.78 10-10 10Z"/><path d="M2 21c0-3 1.85-5.36 5.08-6C9.5 14.52 12 13 13 12"/>',
    'sprout' => '<path d="M7 20h10"/><path d="M10 20c5.5-2.5.8-6.4 3-10"/><path d="M9.5 9.4c1.1.8 1.8 2.2 2.3 3.7-2 .4-3.5.4-4.8-.3-1.2-.6-2.3-1.9-3-4.2 2.8-.5 4.4 0 5.5.8z"/><path d="M14.1 6a7 7 0 0 0-1.1 4c1.9-.1 3.3-.6 4.3-1.4 1-1 1.6-2.3 1.7-4.6-2.7.1-4 1-4.9 2z"/>',
    'seed' => '<ellipse cx="12" cy="12" rx="5" ry="8" transform="rotate(35 12 12)"/><path d="M9 9c2 1 4 3 6 6"/>',
    'flower' => '<circle cx="12" cy="8" r="2"/><path d="M12 10v12"/><path d="M12 22c4.2 0 7-1.667 7-5-4.2 0-7 1.667-7 5Z"/><path d="M12 22c-4.2 0-7-1.667-7-5 4.2 0 7 1.667 7 5Z"/><path d="M12 6a3 3 0 1 0-3 3"/><path d="M12 6a3 3 0 1 1 3 3"/>',
    'calendar' => '<rect x="3" y="4" width="18" height="18" rx="2"/><path d="M16 2v4M8 2v4M3 10h18"/>',
    'qr' => '<rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/><path d="M14 14h3v3h-3zM20 14v.01M14 20h3M20 17v4"/>',
    'user' => '<path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/>',
    'users' => '<path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75"/>',
    'logout' => '<path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4M16 17l5-5-5-5M21 12H9"/>',
    'star' => '<path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>',
    'coffee' => '<path d="M17 8h1a4 4 0 1 1 0 8h-1"/><path d="M3 8h14v9a4 4 0 0 1-4 4H7a4 4 0 0 1-4-4Z"/><path d="M6 2v2M10 2v2M14 2v2"/>',
    'receipt' => '<path d="M4 2v20l3-2 2 2 3-2 3 2 2-2 3 2V2z"/><path d="M8 8h8M8 12h8"/>',
    'ticket' => '<path d="M2 9a3 3 0 0 1 0 6v2a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2v-2a3 3 0 0 1 0-6V7a2 2 0 0 0-2-2H4a2 2 0 0 0-2 2Z"/><path d="M13 5v2M13 17v2M13 11v2"/>',
    'droplet' => '<path d="M12 22a7 7 0 0 0 7-7c0-2-1-3.9-3-5.5s-3.5-4-4-6.5c-.5 2.5-2 4.9-4 6.5C6 11.1 5 13 5 15a7 7 0 0 0 7 7z"/>',
    'sun' => '<circle cx="12" cy="12" r="4"/><path d="M12 2v2M12 20v2M4.93 4.93l1.41 1.41M17.66 17.66l1.41 1.41M2 12h2M20 12h2M6.34 17.66l-1.41 1.41M19.07 4.93l-1.41 1.41"/>',
    'thermo' => '<path d="M14 4v10.54a4 4 0 1 1-4 0V4a2 2 0 0 1 4 0Z"/>',
    'clock' => '<circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/>',
    'check' => '<path d="M20 6 9 17l-5-5"/>',
    'alert' => '<circle cx="12" cy="12" r="10"/><path d="M12 8v4M12 16h.01"/>',
    'arrow' => '<path d="M5 12h14M12 5l7 7-7 7"/>',
    'mail' => '<rect x="2" y="4" width="20" height="16" rx="2"/><path d="m22 7-10 7L2 7"/>',
    'lock' => '<rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/>',
    'target' => '<circle cx="12" cy="12" r="10"/><circle cx="12" cy="12" r="6"/><circle cx="12" cy="12" r="2"/>',
    'chef' => '<path d="M6 13.87A4 4 0 0 1 7.41 6a5.11 5.11 0 0 1 1.05-1.54 5 5 0 0 1 7.08 0A5.11 5.11 0 0 1 16.59 6 4 4 0 0 1 18 13.87V21H6Z"/><path d="M6 17h12"/>',
    'utensils' => '<path d="M3 2v7c0 1.1.9 2 2 2h4a2 2 0 0 0 2-2V2M7 2v20M21 15V2a5 5 0 0 0-5 5v6c0 1.1.9 2 2 2h3Zm0 0v7"/>',
    'card' => '<rect x="2" y="5" width="20" height="14" rx="2"/><path d="M2 10h20"/>',
    'bank' => '<path d="M3 21h18M5 21V10M19 21V10M9 21V10M15 21V10M2 10l10-7 10 7"/>',
    'cash' => '<rect x="2" y="6" width="20" height="12" rx="2"/><circle cx="12" cy="12" r="2"/><path d="M6 12h.01M18 12h.01"/>',
    'chevron' => '<path d="m6 9 6 6 6-6"/>',
    'pin' => '<path d="M20 10c0 6-8 12-8 12S4 16 4 10a8 8 0 0 1 16 0Z"/><circle cx="12" cy="10" r="3"/>',
    'info' => '<circle cx="12" cy="12" r="10"/><path d="M12 16v-4M12 8h.01"/>',
    'edit' => '<path d="M12 20h9"/><path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4Z"/>',
  ];
  return '<svg class="' . $c . '" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">' . ($p[$n] ?? $p['leaf']) . '</svg>';
}
function rank_icon(string $nombre, string $c = 'w-6 h-6'): string {
  return icon(['semilla' => 'seed', 'capullo' => 'sprout', 'floreciendo' => 'leaf', 'flor' => 'flower'][$nombre] ?? 'leaf', $c);
}

// Encabezado de las páginas del usuario, con pestañas
function page_header(string $titulo, string $sub = '', string $activa = ''): void {
  $tabs = ['panel' => ['Mis plantas', 'sprout'], 'identificador' => ['Identificador de plantas', 'leaf'], 'reservas' => ['Reservas', 'calendar'], 'puntos' => ['Puntos y rangos', 'star'], 'perfil' => ['Mi perfil', 'user']];
  echo '<div class="bg-white border-b border-brand-sage/20"><div class="container-x pt-10">';
  echo '<p class="eyebrow">Mi cuenta</p><h1 class="font-serif text-3xl md:text-4xl font-bold text-brand-green mt-1">' . e($titulo) . '</h1>';
  if ($sub) echo '<p class="text-gray-500 mt-1">' . e($sub) . '</p>';
  echo '<nav class="flex gap-1 mt-6 overflow-x-auto">';
  foreach ($tabs as $r => [$t, $i]) echo '<a href="' . url($r) . '" class="tab ' . ($r === $activa ? 'tab-on' : '') . '">' . icon($i, 'w-4 h-4') . $t . '</a>';
  echo '</nav></div></div>';
}
