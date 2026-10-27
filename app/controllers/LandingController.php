<?php
class LandingController {
  static function home() {
    render('landing', ['title' => 'Restaurante de flores, café y vivero', 'rangos' => Puntos::rangos(), 'platos' => Plato::conFlores(4), 'plantas' => array_slice(Planta::catalogo(), 0, 4)]);
  }
}
