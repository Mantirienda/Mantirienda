<?php
class StaffController {
  static function panel() {
    require_staff();
    $codigo = $_SESSION['codigo_nuevo'] ?? null; unset($_SESSION['codigo_nuevo']);
    render('staff/panel', ['title' => 'Panel del salón', 'mesas' => Mesa::todas(), 'plantas' => Planta::catalogo(), 'codigo' => $codigo]);
  }
  static function toggleMesa() { require_staff(); if (es_post()) Mesa::alternar((int)($_POST['id'] ?? 0)); redirect('staff'); }
  static function venta() {
    require_staff();
    if (es_post()) {
      $u = Usuario::porEmail($_POST['email'] ?? '');
      if ($u) { Planta::asignar($u['id'], (int)$_POST['planta_id']); flash('ok', "Planta asignada a {$u['nombre']}."); }
      else flash('error', 'No existe un usuario con ese email.');
    }
    redirect('staff');
  }
  static function cuenta() {
    require_staff();
    if (es_post() && (int)($_POST['puntos'] ?? 0) > 0) $_SESSION['codigo_nuevo'] = Puntos::generarCuenta((int)$_POST['puntos'], $_POST['total'] ?? null);
    redirect('staff');
  }
  // El mozo escanea el QR del cliente: ve el descuento y lo marca como usado
  static function canje() {
    require_staff(); $t = $_GET['t'] ?? '';
    if (es_post()) {
      Puntos::marcarUsado($t) ? flash('ok', 'Descuento aplicado') : flash('error', 'Este canje ya fue usado o venció.');
      redirect('staff_canje', ['t' => $t]);
    }
    $c = Puntos::canjePorToken($t);
    if (!$c) { http_response_code(404); render('error', ['title' => 'QR inválido', 'msg' => 'QR inválido.']); return; }
    render('staff/canje', ['title' => 'Validar canje', 'canje' => $c, 'vigente' => $c['estado'] === 'pendiente' && strtotime($c['expira_en']) > time()]);
  }
}
