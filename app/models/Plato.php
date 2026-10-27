<?php
class Plato {
  static function conFlores($n = 4) { return all('SELECT * FROM platos WHERE lleva_flores=1 ORDER BY puntos DESC LIMIT ' . (int)$n); }
}
