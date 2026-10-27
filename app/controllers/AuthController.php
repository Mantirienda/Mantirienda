<?php
class AuthController {
  private static function sesion(array $u): array { return ['id' => $u['id'], 'nombre' => $u['nombre'], 'email' => $u['email'], 'rol' => $u['rol']]; }

  static function login() {
    if (es_post()) {
      $u = Usuario::verificar($_POST['email'] ?? '', $_POST['password'] ?? '');
      if (!$u) { flash('error', 'Email o contraseña incorrectos.'); redirect('login'); }
      Usuario::aplicarInactividad($u['id']);
      session_regenerate_id(true);
      $_SESSION['user'] = self::sesion($u);
      $volver = $_SESSION['volver'] ?? null; unset($_SESSION['volver']);
      if ($volver) { header('Location: ' . $volver); exit; }
      redirect(in_array($u['rol'], ['mozo', 'admin']) ? 'staff' : 'panel');
    }
    render('auth/login', ['title' => 'Iniciar sesión']);
  }

  static function registro() {
    if (es_post()) {
      $n = trim($_POST['nombre'] ?? ''); $e = trim($_POST['email'] ?? ''); $p = $_POST['password'] ?? '';
      $err = strlen($n) < 2 ? 'Ingresá tu nombre.' : (email_error($e) ?? pass_error($p));
      if (!$err && Usuario::porEmail($e)) $err = 'Ya existe una cuenta con ese email.';
      if ($err) { flash('error', $err); redirect('registro'); }
      session_regenerate_id(true);
      $_SESSION['user'] = self::sesion(Usuario::crear($n, $e, $p));
      flash('ok', 'Bienvenida/o a Mantirienda. Empezás en el rango Semilla.');
      redirect('panel');
    }
    render('auth/registro', ['title' => 'Crear cuenta']);
  }

  static function olvide() {
    if (es_post()) {
      $u = Usuario::porEmail($_POST['email'] ?? '');
      if (!$u) { flash('error', 'No existe ninguna cuenta con ese email.'); redirect('olvide'); }
      $token = bin2hex(random_bytes(24));
      Usuario::guardarToken($u['id'], $token);
      $link = abs_url('reset', ['t' => $token]);
      if (DEV_MODE) {   // local: no se envía mail, se muestra el enlace
        file_put_contents(__DIR__ . '/../../storage/mails.log', date('c') . " {$u['email']} $link\n", FILE_APPEND);
        flash('ok', 'Modo local: el mail no se envía. Usá este enlace para elegir una nueva contraseña:', $link);
      } else {
        mail($u['email'], 'Recuperá tu contraseña - Mantirienda', "Hola {$u['nombre']}, entrá acá para elegir una nueva contraseña (vale 1 hora):\n$link", "Content-Type: text/plain; charset=UTF-8");
        flash('ok', 'Te enviamos un mail con el enlace para restablecer tu contraseña.');
      }
      redirect('login');
    }
    render('auth/olvide', ['title' => 'Recuperar contraseña']);
  }

  static function reset() {
    $t = $_GET['t'] ?? '';
    $u = Usuario::porToken($t);
    if (!$u) { flash('error', 'El enlace venció o no es válido.'); redirect('olvide'); }
    if (es_post()) {
      $err = pass_error($_POST['password'] ?? '');
      if ($err) { flash('error', $err); redirect('reset', ['t' => $t]); }
      Usuario::cambiarPass($u['id'], $_POST['password']);
      flash('ok', 'Contraseña actualizada. Ya podés iniciar sesión.'); redirect('login');
    }
    render('auth/reset', ['title' => 'Nueva contraseña', 'token' => $t]);
  }

  static function logout() { session_destroy(); header('Location: index.php'); exit; }
}
