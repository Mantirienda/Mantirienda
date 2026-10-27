<?php
class Usuario {
  static function porEmail(string $e) { return one('SELECT * FROM usuarios WHERE email=?', [strtolower(trim($e))]); }
  static function porId(int $id) {
    return one('SELECT u.*, r.nombre AS rango, r.descuento_pct AS rango_descuento, r.orden AS rango_orden
                FROM usuarios u LEFT JOIN rangos r ON r.id=u.rango_id WHERE u.id=?', [$id]);
  }
  static function crear(string $nombre, string $email, string $pass): array {
    q('INSERT INTO usuarios(nombre,email,password_hash,rango_id) VALUES(?,?,?,(SELECT id FROM rangos ORDER BY puntos_min LIMIT 1))',
      [trim($nombre), strtolower(trim($email)), password_hash($pass, PASSWORD_BCRYPT)]);
    return one('SELECT * FROM usuarios WHERE id=?', [db()->lastInsertId()]);
  }
  static function verificar(string $email, string $pass) {
    $u = self::porEmail($email);
    return $u && password_verify($pass, $u['password_hash']) ? $u : null;
  }
  static function actualizarPerfil($id, $nombre, $email, $tel) {
    q('UPDATE usuarios SET nombre=?, email=?, telefono=? WHERE id=?', [trim($nombre), strtolower(trim($email)), $tel ?: null, $id]);
  }
  static function cambiarPass($id, $pass) {
    q('UPDATE usuarios SET password_hash=?, reset_token=NULL, reset_expira=NULL WHERE id=?', [password_hash($pass, PASSWORD_BCRYPT), $id]);
  }
  static function guardarToken($id, $t) { q('UPDATE usuarios SET reset_token=?, reset_expira=DATE_ADD(NOW(), INTERVAL 1 HOUR) WHERE id=?', [$t, $id]); }
  static function porToken($t) { return one('SELECT * FROM usuarios WHERE reset_token=? AND reset_expira>NOW()', [$t]); }

  // Si pasaron X días sin visitar, baja un rango (y reinicia el reloj)
  static function aplicarInactividad($id) {
    q('UPDATE usuarios u JOIN rangos act ON act.id=u.rango_id JOIN rangos r ON r.orden=act.orden-1
       SET u.puntos_rango=r.puntos_min, u.rango_id=r.id, u.ultima_visita=NOW()
       WHERE u.id=? AND u.ultima_visita < DATE_SUB(NOW(), INTERVAL ' . (int)DIAS_INACTIVIDAD . ' DAY)', [$id]);
  }
}
