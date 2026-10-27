<?php
class Puntos {
  static function rangos() { return all('SELECT * FROM rangos ORDER BY orden'); }
  static function movimientos($uid) { return all('SELECT * FROM movimientos_puntos WHERE usuario_id=? ORDER BY creado_en DESC, id DESC LIMIT 30', [$uid]); }

  // Ingresar el código que figura en la cuenta
  static function canjearCodigo($uid, string $codigo): array {
    $db = db(); $db->beginTransaction();
    try {
      $c = one('SELECT * FROM cuentas WHERE codigo=? FOR UPDATE', [strtoupper(trim($codigo))]);
      if (!$c) { $db->rollBack(); return ['error' => 'Ese código no existe.']; }
      if ($c['canjeado_por']) { $db->rollBack(); return ['error' => 'Ese código ya fue utilizado.']; }
      q('UPDATE cuentas SET canjeado_por=?, canjeado_en=NOW() WHERE id=?', [$uid, $c['id']]);
      q('UPDATE usuarios SET puntos_disponibles=puntos_disponibles+?, puntos_rango=puntos_rango+?, ultima_visita=NOW() WHERE id=?', [$c['puntos'], $c['puntos'], $uid]);
      q('UPDATE usuarios SET rango_id=(SELECT id FROM rangos WHERE puntos_min<=usuarios.puntos_rango ORDER BY puntos_min DESC LIMIT 1) WHERE id=?', [$uid]);
      q("INSERT INTO movimientos_puntos(usuario_id,tipo,puntos,detalle) VALUES(?,'ganado',?,?)", [$uid, $c['puntos'], 'Código ' . $c['codigo']]);
      $db->commit();
      return ['puntos' => $c['puntos']];
    } catch (Throwable $e) { $db->rollBack(); throw $e; }
  }

  // Elegir cuántos puntos gastar -> descuento + QR para el mozo
  static function crearCanje($uid, $puntos): array {
    $puntos = (int)$puntos;
    if (!$puntos || $puntos % PUNTOS_POR_5PCT || $puntos > TOPE_PUNTOS) return ['error' => 'Elegí múltiplos de ' . PUNTOS_POR_5PCT . ' (máx. ' . TOPE_PUNTOS . ').'];
    $pct = ($puntos / PUNTOS_POR_5PCT) * 5; $token = uuid();
    $db = db(); $db->beginTransaction();
    try {
      $n = q('UPDATE usuarios SET puntos_disponibles=puntos_disponibles-? WHERE id=? AND puntos_disponibles>=?', [$puntos, $uid, $puntos])->rowCount();
      if (!$n) { $db->rollBack(); return ['error' => 'No tenés puntos suficientes.']; }
      q('INSERT INTO canjes(usuario_id,puntos_usados,descuento_pct,token,expira_en) VALUES(?,?,?,?,DATE_ADD(NOW(), INTERVAL 24 HOUR))', [$uid, $puntos, $pct, $token]);
      q("INSERT INTO movimientos_puntos(usuario_id,tipo,puntos,detalle) VALUES(?,'canje',?,?)", [$uid, -$puntos, "Canje $pct% de descuento"]);
      $db->commit();
      return ['token' => $token];
    } catch (Throwable $e) { $db->rollBack(); throw $e; }
  }

  static function canjesActivos($uid) { return all("SELECT * FROM canjes WHERE usuario_id=? AND estado='pendiente' AND expira_en>NOW() ORDER BY creado_en DESC", [$uid]); }
  static function canjePorToken($t) {
    return one('SELECT c.*, u.nombre, u.email, r.nombre AS rango, r.descuento_pct AS rango_descuento
      FROM canjes c JOIN usuarios u ON u.id=c.usuario_id LEFT JOIN rangos r ON r.id=u.rango_id WHERE c.token=?', [$t]);
  }
  static function marcarUsado($t): bool { return q("UPDATE canjes SET estado='usado', usado_en=NOW() WHERE token=? AND estado='pendiente' AND expira_en>NOW()", [$t])->rowCount() > 0; }
  static function misiones($uid) {
    return all("SELECT m.*, COALESCE(um.estado,'disponible') AS progreso FROM misiones m
      LEFT JOIN usuario_misiones um ON um.mision_id=m.id AND um.usuario_id=?
      WHERE m.rango_min_id <= (SELECT rango_id FROM usuarios WHERE id=?) ORDER BY m.es_premium, m.id", [$uid, $uid]);
  }
  // Staff: genera el código que se imprime en la cuenta
  static function generarCuenta($puntos, $total): string {
    $codigo = strtoupper(substr(bin2hex(random_bytes(5)), 0, 8));
    q('INSERT INTO cuentas(codigo,puntos,total) VALUES(?,?,?)', [$codigo, $puntos, $total ?: null]);
    return $codigo;
  }
}
