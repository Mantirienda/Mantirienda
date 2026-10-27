<?php
class Mesa {
  static function todas() { return all('SELECT * FROM mesas ORDER BY numero'); }
  static function alternar($id) { q("UPDATE mesas SET estado=IF(estado='libre','ocupada','libre') WHERE id=?", [$id]); }
}
