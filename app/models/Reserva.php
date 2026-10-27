<?php
class Reserva {
  static function deUsuario($uid): array {
    $sql = 'SELECT r.*, m.numero AS mesa FROM reservas r LEFT JOIN mesas m ON m.id=r.mesa_id WHERE r.usuario_id=? AND ';
    return [
      'actuales'   => all($sql . "r.estado='confirmada' AND r.fecha>=CURDATE() ORDER BY r.fecha, r.hora", [$uid]),
      'anteriores' => all($sql . "(r.estado<>'confirmada' OR r.fecha<CURDATE()) ORDER BY r.fecha DESC, r.hora", [$uid]),
    ];
  }
  static function porId($id, $uid) {
    return one('SELECT r.*, m.numero AS mesa FROM reservas r LEFT JOIN mesas m ON m.id=r.mesa_id WHERE r.id=? AND r.usuario_id=?', [$id, $uid]);
  }
  static function sena(int $personas): float { return $personas * SENA_POR_PERSONA; }

  // Asigna la mesa más chica que alcance y esté libre en ese día/hora (margen de 90 min). Devuelve el id o null.
  static function crear($uid, array $d): ?int {
    $m = one("SELECT id FROM mesas m WHERE capacidad>=? AND NOT EXISTS (
        SELECT 1 FROM reservas r WHERE r.mesa_id=m.id AND r.fecha=? AND r.estado='confirmada'
        AND r.hora BETWEEN SUBTIME(?, '01:30:00') AND ADDTIME(?, '01:30:00'))
      ORDER BY capacidad, numero LIMIT 1", [$d['personas'], $d['fecha'], $d['hora'], $d['hora']]);
    if (!$m) return null;
    $codigo = 'R' . strtoupper(substr(bin2hex(random_bytes(4)), 0, 5));
    q('INSERT INTO reservas(codigo,usuario_id,mesa_id,nombre_contacto,telefono,fecha,hora,personas,zona,ocasion,notas,metodo_pago,monto_sena)
       VALUES(?,?,?,?,?,?,?,?,?,?,?,?,?)',
      [$codigo, $uid, $m['id'], $d['nombre'], $d['telefono'], $d['fecha'], $d['hora'], $d['personas'], $d['zona'], $d['ocasion'] ?: null, $d['notas'] ?: null, $d['metodo'], self::sena($d['personas'])]);
    return (int)db()->lastInsertId();
  }
  // Solo si faltan HORAS_CANCELACION horas o más
  static function cancelar($id, $uid): bool {
    return q("UPDATE reservas SET estado='cancelada' WHERE id=? AND usuario_id=? AND estado='confirmada'
              AND TIMESTAMP(fecha,hora) >= DATE_ADD(NOW(), INTERVAL " . (int)HORAS_CANCELACION . " HOUR)", [$id, $uid])->rowCount() > 0;
  }
}
