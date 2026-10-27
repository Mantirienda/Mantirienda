<?php
// XAMPP por defecto: usuario root, sin clave
const DB_HOST = 'localhost';
const DB_PORT = '3306';
const DB_NAME = 'mantirienda_db';
const DB_USER = 'root';
const DB_PASS = '';

// Para que los QR funcionen desde el celu, poné la IP de tu PC. Ej: 'http://192.168.1.15/mantirienda/'
// Vacío = se detecta solo (sirve para probar desde la misma PC).
const BASE_URL = '';

// true = el mail de "olvidé mi contraseña" no se envía: el enlace se muestra en pantalla (ideal para local)
const DEV_MODE = true;

// Puntos
const PUNTOS_POR_5PCT = 50;   // 50 pts = 5% de descuento
const TOPE_PUNTOS = 250;      // máx. 25%
const DIAS_INACTIVIDAD = 60;  // sin visitar = baja un rango

// Reservas
const DIAS_ANTICIPACION = 7;      // mínimo de días de anticipación
const DIAS_MAX_RESERVA  = 120;    // máximo hacia adelante
const HORA_APERTURA = '12:00';
const HORA_CIERRE   = '23:00';    // último turno: 1 hora antes
const SENA_POR_PERSONA = 2000;    // seña (se descuenta de la cuenta)
const HORAS_CANCELACION = 48;     // se puede cancelar hasta 48 hs antes
const ZONAS = ['salon' => 'Salón principal', 'jardin' => 'Jardín interior', 'terraza' => 'Terraza'];
const OCASIONES = ['Cumpleaños', 'Aniversario', 'Reunión de amigos', 'Encuentro de trabajo', 'Otra ocasión'];
const METODOS = ['transferencia' => 'Transferencia bancaria', 'mercadopago' => 'Mercado Pago', 'efectivo' => 'Efectivo en el local'];

// Datos para transferencias (completá con los reales)
const TRANSF_TITULAR = 'Mantirienda';
const TRANSF_ALIAS = 'mantirienda.flores';
const TRANSF_CBU = '0000000000000000000000';

// Identificador de plantas (PlantNet: https://my.plantnet.org)
define('PLANTNET_API_KEY', getenv('PLANTNET_API_KEY') ?: '');   // pegá tu clave acá o usá la variable de entorno
define('APP_SECRET', getenv('APP_SECRET') ?: 'cambiá-esto-por-una-cadena-larga-y-aleatoria');
