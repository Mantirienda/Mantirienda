<?php
// Crea un mozo/admin. Abrí:  http://localhost/mantirienda/crear-staff.php?nombre=Ana&email=ana@mail.com&clave=123456&rol=mozo
// BORRÁ este archivo cuando termines de crear tus usuarios.
require __DIR__ . '/config/config.php'; require __DIR__ . '/app/core/helpers.php';
if (!in_array($_SERVER['REMOTE_ADDR'], ['127.0.0.1', '::1'])) die('Solo desde localhost.');
$n = $_GET['nombre'] ?? ''; $e = $_GET['email'] ?? ''; $c = $_GET['clave'] ?? ''; $r = in_array($_GET['rol'] ?? '', ['mozo', 'admin']) ? $_GET['rol'] : 'admin';
if (!$n || !$e || strlen($c) < 6) die('Faltan datos. Ej: ?nombre=Ana&email=ana@mail.com&clave=123456&rol=mozo');
q('INSERT INTO usuarios(nombre,email,password_hash,rol,rango_id) VALUES(?,?,?,?,(SELECT id FROM rangos ORDER BY puntos_min LIMIT 1))
   ON DUPLICATE KEY UPDATE rol=VALUES(rol), password_hash=VALUES(password_hash)', [$n, strtolower($e), password_hash($c, PASSWORD_BCRYPT), $r]);
echo "Listo, $r creado: " . htmlspecialchars($e) . ". Ahora borrá crear-staff.php";
