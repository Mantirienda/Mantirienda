<?php
class PanelController {
  static function plantas() {
    require_login();
    render('panel/plantas', ['title' => 'Mis plantas', 'plantas' => Planta::deUsuario(user()['id'])]);
  }
  static function perfil() {
    require_login(); $id = user()['id'];
    if (es_post()) {
      $n = trim($_POST['nombre'] ?? ''); $e = trim($_POST['email'] ?? '');
      $err = strlen($n) < 2 ? 'Ingresá tu nombre.' : email_error($e);
      $otro = $err ? null : Usuario::porEmail($e);
      if (!$err && $otro && $otro['id'] != $id) $err = 'Ese email ya está en uso por otra cuenta.';
      $tel = trim($_POST['telefono'] ?? '');
      if (!$err && $tel !== '' && !preg_match('/^\d{8,15}$/', preg_replace('/[\s\-\+\(\)]/', '', $tel))) $err = 'El teléfono no es válido.';
      if ($err) { flash('error', $err); redirect('perfil'); }
      Usuario::actualizarPerfil($id, $n, $e, $tel);
      $_SESSION['user']['nombre'] = $n; $_SESSION['user']['email'] = strtolower($e);
      flash('ok', 'Perfil actualizado.'); redirect('perfil');
    }
    Usuario::aplicarInactividad($id);
    render('panel/perfil', ['title' => 'Mi perfil', 'perfil' => Usuario::porId($id)]);
  }
  static function password() {
    require_login(); $u = Usuario::porId(user()['id']);
    if (es_post()) {
      $err = !password_verify($_POST['actual'] ?? '', $u['password_hash']) ? 'La contraseña actual no es correcta.' : pass_error($_POST['nueva'] ?? '');
      if ($err) flash('error', $err); else { Usuario::cambiarPass($u['id'], $_POST['nueva']); flash('ok', 'Contraseña actualizada.'); }
    }
    redirect('perfil');
  }
  // Destino del QR de cada planta (público): muestra la planta CORRESPONDIENTE
  static function planta() {
    $p = Planta::porToken($_GET['t'] ?? '');
    if (!$p) { http_response_code(404); render('error', ['title' => 'QR inválido', 'msg' => 'Este QR no corresponde a ninguna planta.']); return; }
    render('planta', ['title' => $p['nombre_comun'], 'planta' => $p]);
  }
}
