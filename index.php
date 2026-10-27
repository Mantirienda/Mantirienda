<?php
// Front controller: todo entra por acá  (index.php?r=ruta)
session_start();
require __DIR__ . '/config/config.php';
require __DIR__ . '/app/core/helpers.php';
spl_autoload_register(function ($c) {
  foreach (['models', 'controllers'] as $d) { $f = __DIR__ . "/app/$d/$c.php"; if (is_file($f)) { require $f; return; } }
});

$routes = [
  'home'          => ['Landing', 'home'],
  'login'         => ['Auth', 'login'],     'registro' => ['Auth', 'registro'],
  'olvide'        => ['Auth', 'olvide'],    'reset'    => ['Auth', 'reset'],     'logout' => ['Auth', 'logout'],
  'planta'        => ['Panel', 'planta'],   // QR de planta (público)
  'panel'         => ['Panel', 'plantas'],  'identificador' => ['Identificar', 'vista'], 'identificar_api' => ['Identificar', 'api'],  'perfil'   => ['Panel', 'perfil'], 'perfil_pass' => ['Panel', 'password'],
  'reservas'      => ['Reserva', 'lista'],  'reserva_nueva' => ['Reserva', 'nueva'], 'reserva' => ['Reserva', 'ver'], 'reserva_cancelar' => ['Reserva', 'cancelar'],
  'puntos'        => ['Puntos', 'ver'],     'puntos_codigo'    => ['Puntos', 'ingresarCodigo'],
  'puntos_canje'  => ['Puntos', 'crearCanje'], 'canje' => ['Puntos', 'verCanje'],
  'staff'         => ['Staff', 'panel'],    'staff_mesa'  => ['Staff', 'toggleMesa'],
  'staff_venta'   => ['Staff', 'venta'],    'staff_cuenta' => ['Staff', 'cuenta'],
  'staff_canje'   => ['Staff', 'canje'],
];

// Protección CSRF para todos los formularios
if (es_post() && !hash_equals(csrf_token(), $_POST['csrf'] ?? '')) {
  http_response_code(419);
  render('error', ['title' => 'Sesión vencida', 'msg' => 'La sesión del formulario venció. Volvé atrás, recargá la página y probá de nuevo.']); exit;
}

$r = $_GET['r'] ?? 'home';
if (!isset($routes[$r])) { http_response_code(404); render('error', ['title' => 'No encontrada', 'msg' => 'Esta página no existe.']); exit; }
[$ctrl, $metodo] = $routes[$r];
try { $clase = $ctrl . 'Controller'; $clase::$metodo(); }
catch (Throwable $e) {
  error_log($e);
  http_response_code(500);
  render('error', ['title' => 'Error', 'msg' => DEV_MODE ? $e->getMessage() : 'Algo salió mal. Probá de nuevo.']);
}
