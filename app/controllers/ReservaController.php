<?php
class ReservaController {
  static function lista() {
    require_login();
    render('panel/reservas', ['title' => 'Mis reservas'] + Reserva::deUsuario(user()['id']));
  }

  private static function validar(array $d, bool $acepta): ?string {
    if (strlen($d['nombre']) < 2) return 'Ingresá el nombre de quien reserva.';
    if (!preg_match('/^\d{8,15}$/', preg_replace('/[\s\-\+\(\)]/', '', $d['telefono']))) return 'Ingresá un teléfono de contacto válido.';
    $dt = DateTime::createFromFormat('Y-m-d', $d['fecha']);
    if (!$dt || $dt->format('Y-m-d') !== $d['fecha']) return 'Elegí una fecha válida.';
    $min = date('Y-m-d', strtotime('+' . DIAS_ANTICIPACION . ' days')); $max = date('Y-m-d', strtotime('+' . DIAS_MAX_RESERVA . ' days'));
    if ($d['fecha'] < $min) return 'Las reservas se hacen con al menos ' . DIAS_ANTICIPACION . ' días de anticipación (desde el ' . fecha_es($min) . ').';
    if ($d['fecha'] > $max) return 'Solo podés reservar hasta el ' . fecha_es($max) . '.';
    if (!in_array($d['hora'], horarios(), true)) return 'Elegí un horario de la lista.';
    if ($d['personas'] < 1 || $d['personas'] > 8) return 'Las reservas son de 1 a 8 personas. Para grupos más grandes, contactanos.';
    if (!isset(ZONAS[$d['zona']])) return 'Elegí una zona del local.';
    if ($d['ocasion'] !== '' && !in_array($d['ocasion'], OCASIONES, true)) return 'Ocasión no válida.';
    if (!isset(METODOS[$d['metodo']])) return 'Elegí un método de pago para la seña.';
    if (!$acepta) return 'Tenés que aceptar las condiciones de la reserva.';
    return null;
  }

  static function nueva() {
    require_login(); $u = user();
    if (es_post()) {
      $d = ['nombre' => trim($_POST['nombre'] ?? ''), 'telefono' => trim($_POST['telefono'] ?? ''), 'fecha' => $_POST['fecha'] ?? '', 'hora' => $_POST['hora'] ?? '',
            'personas' => (int)($_POST['personas'] ?? 0), 'zona' => $_POST['zona'] ?? '', 'ocasion' => $_POST['ocasion'] ?? '', 'notas' => trim($_POST['notas'] ?? ''), 'metodo' => $_POST['metodo'] ?? ''];
      $err = self::validar($d, isset($_POST['acepto']));
      $id = $err ? null : Reserva::crear($u['id'], $d);
      if (!$err && !$id) $err = 'No hay mesas disponibles en ese día y horario. Probá con otro.';
      if ($err) { flash('error', $err); $_SESSION['old'] = $d; redirect('reserva_nueva'); }
      flash('ok', 'Reserva registrada. Seguí las instrucciones para abonar la seña.');
      redirect('reserva', ['id' => $id]);
    }
    $old = $_SESSION['old'] ?? []; unset($_SESSION['old']);
    render('panel/reserva_nueva', ['title' => 'Nueva reserva', 'old' => $old, 'perfil' => Usuario::porId($u['id'])]);
  }

  static function ver() {
    require_login();
    $r = Reserva::porId((int)($_GET['id'] ?? 0), user()['id']);
    if (!$r) { http_response_code(404); render('error', ['title' => 'No encontrada', 'msg' => 'Reserva no encontrada.']); return; }
    render('panel/reserva', ['title' => 'Reserva ' . $r['codigo'], 'r' => $r]);
  }

  static function cancelar() {
    require_login();
    if (es_post()) {
      Reserva::cancelar((int)($_POST['id'] ?? 0), user()['id'])
        ? flash('ok', 'Reserva cancelada.')
        : flash('error', 'Solo se puede cancelar hasta ' . HORAS_CANCELACION . ' horas antes de la reserva.');
    }
    redirect('reservas');
  }
}
