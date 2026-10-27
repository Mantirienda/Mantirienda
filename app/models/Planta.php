<?php
class Planta {
  private const BASE = 'SELECT p.*, c.nombre AS categoria, e.nombre AS estacion_ideal FROM plantas p
    LEFT JOIN categorias c ON c.id=p.categoria_id LEFT JOIN estaciones e ON e.id=p.estacion_ideal_id';
  static function catalogo() { return all(self::BASE . ' ORDER BY p.nombre_comun'); }
  static function deUsuario($uid) {
    return all('SELECT up.qr_token, up.comprada_en, p.id, p.nombre_comun, p.nombre_cientifico, p.imagen_url
      FROM usuario_plantas up JOIN plantas p ON p.id=up.planta_id WHERE up.usuario_id=? ORDER BY up.comprada_en DESC', [$uid]);
  }
  // La planta CORRESPONDIENTE al QR escaneado
  static function porToken($t) {
    return one('SELECT p.*, c.nombre AS categoria, e.nombre AS estacion_ideal, up.comprada_en
      FROM usuario_plantas up JOIN plantas p ON p.id=up.planta_id
      LEFT JOIN categorias c ON c.id=p.categoria_id LEFT JOIN estaciones e ON e.id=p.estacion_ideal_id
      WHERE up.qr_token=?', [$t]);
  }
  static function asignar($uid, $pid) { q('INSERT INTO usuario_plantas(usuario_id,planta_id,qr_token) VALUES(?,?,?)', [$uid, $pid, uuid()]); }
}
