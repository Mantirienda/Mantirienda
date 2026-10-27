<?php
class PuntosController {
  static function ver() {
    require_login(); $uid = user()['id'];
    Usuario::aplicarInactividad($uid);
    $perfil = Usuario::porId($uid); $rangos = Puntos::rangos();
    $sig = null; foreach ($rangos as $r) if ($r['orden'] == $perfil['rango_orden'] + 1) $sig = $r;
    render('panel/puntos', ['title' => 'Mis puntos', 'perfil' => $perfil, 'siguiente' => $sig,
      'movimientos' => Puntos::movimientos($uid), 'misiones' => Puntos::misiones($uid), 'canjes' => Puntos::canjesActivos($uid)]);
  }
  static function ingresarCodigo() {
    require_login();
    if (es_post()) {
      $r = Puntos::canjearCodigo(user()['id'], $_POST['codigo'] ?? '');
      isset($r['error']) ? flash('error', $r['error']) : flash('ok', "Sumaste {$r['puntos']} puntos.");
    }
    redirect('puntos');
  }
  static function crearCanje() {
    require_login();
    if (!es_post()) redirect('puntos');
    $r = Puntos::crearCanje(user()['id'], $_POST['puntos'] ?? 0);
    if (isset($r['error'])) { flash('error', $r['error']); redirect('puntos'); }
    redirect('canje', ['t' => $r['token']]);
  }
  // QR que escanea el mozo
  static function verCanje() {
    require_login();
    $c = Puntos::canjePorToken($_GET['t'] ?? '');
    if (!$c || $c['usuario_id'] != user()['id']) { http_response_code(404); render('error', ['title' => 'No encontrado', 'msg' => 'Canje no encontrado.']); return; }
    render('panel/canje', ['title' => 'Tu QR de descuento', 'canje' => $c, 'qrUrl' => abs_url('staff_canje', ['t' => $c['token']])]);
  }
}
