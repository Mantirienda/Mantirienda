-- Mantirienda · MySQL / MariaDB (XAMPP + phpMyAdmin)
-- phpMyAdmin > Importar > elegir este archivo > Continuar (crea la base y carga los datos iniciales)
CREATE DATABASE IF NOT EXISTS mantirienda_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE mantirienda_db;

SET FOREIGN_KEY_CHECKS = 0;
DROP TABLE IF EXISTS usuario_misiones, misiones, canjes, movimientos_puntos, cuentas, pedido_items, pedidos,
  platos, reservas, mesas, usuario_plantas, plantas, categorias, estaciones, usuarios, rangos;
SET FOREIGN_KEY_CHECKS = 1;

-- ===== Rangos y usuarios =====
CREATE TABLE rangos (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nombre VARCHAR(30) NOT NULL UNIQUE,          -- semilla, capullo, floreciendo, flor
  orden INT NOT NULL UNIQUE,
  puntos_min INT NOT NULL,
  descuento_pct INT NOT NULL DEFAULT 0         -- descuento en comidas por rango
) ENGINE=InnoDB;

CREATE TABLE usuarios (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nombre VARCHAR(100) NOT NULL,
  email VARCHAR(150) NOT NULL UNIQUE,
  telefono VARCHAR(30),
  password_hash VARCHAR(100) NOT NULL,
  rol ENUM('cliente','mozo','admin') NOT NULL DEFAULT 'cliente',
  puntos_disponibles INT NOT NULL DEFAULT 0,   -- se gastan al canjear
  puntos_rango INT NOT NULL DEFAULT 0,         -- definen el rango, no se gastan
  rango_id INT,
  ultima_visita DATETIME DEFAULT CURRENT_TIMESTAMP,  -- si pasa mucho tiempo, baja de rango
  reset_token VARCHAR(64),
  reset_expira DATETIME NULL,
  creado_en DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (rango_id) REFERENCES rangos(id)
) ENGINE=InnoDB;

-- ===== Plantas (catálogo + compradas con QR único) =====
CREATE TABLE categorias (id INT AUTO_INCREMENT PRIMARY KEY, nombre VARCHAR(50) NOT NULL UNIQUE, descripcion TEXT) ENGINE=InnoDB;
CREATE TABLE estaciones (id INT AUTO_INCREMENT PRIMARY KEY, nombre VARCHAR(20) NOT NULL UNIQUE) ENGINE=InnoDB;
CREATE TABLE plantas (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nombre_comun VARCHAR(100) NOT NULL,
  nombre_cientifico VARCHAR(100),
  categoria_id INT NULL, estacion_ideal_id INT NULL,
  riego VARCHAR(100), luz VARCHAR(100), temperatura_ideal VARCHAR(50),
  descripcion TEXT, imagen_url VARCHAR(255),
  creado_en DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (categoria_id) REFERENCES categorias(id) ON DELETE SET NULL,
  FOREIGN KEY (estacion_ideal_id) REFERENCES estaciones(id) ON DELETE SET NULL
) ENGINE=InnoDB;
CREATE TABLE usuario_plantas (                 -- cada compra tiene su QR único (qr_token)
  id INT AUTO_INCREMENT PRIMARY KEY,
  usuario_id INT NOT NULL, planta_id INT NOT NULL,
  qr_token CHAR(36) NOT NULL UNIQUE,
  comprada_en DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE,
  FOREIGN KEY (planta_id) REFERENCES plantas(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ===== Salón y reservas =====
CREATE TABLE mesas (
  id INT AUTO_INCREMENT PRIMARY KEY,
  numero INT NOT NULL UNIQUE,
  capacidad INT NOT NULL DEFAULT 4,
  estado ENUM('libre','ocupada') NOT NULL DEFAULT 'libre'   -- libre = capullo verde, ocupada = flor roja
) ENGINE=InnoDB;
CREATE TABLE reservas (
  id INT AUTO_INCREMENT PRIMARY KEY,
  codigo VARCHAR(10) NULL UNIQUE,
  usuario_id INT NOT NULL, mesa_id INT NULL,
  nombre_contacto VARCHAR(100), telefono VARCHAR(30),
  zona ENUM('salon','jardin','terraza') NOT NULL DEFAULT 'salon',
  ocasion VARCHAR(40) NULL,
  metodo_pago ENUM('transferencia','mercadopago','efectivo') NOT NULL DEFAULT 'transferencia',
  monto_sena DECIMAL(10,2) NOT NULL DEFAULT 0,
  pago_estado ENUM('pendiente','pagado') NOT NULL DEFAULT 'pendiente',
  fecha DATE NOT NULL, hora TIME NOT NULL,
  personas INT NOT NULL,
  notas TEXT,
  estado ENUM('confirmada','cancelada','completada') NOT NULL DEFAULT 'confirmada',
  creado_en DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE,
  FOREIGN KEY (mesa_id) REFERENCES mesas(id) ON DELETE SET NULL,
  INDEX idx_reservas_usuario (usuario_id, fecha)
) ENGINE=InnoDB;

-- ===== Menú, pedidos y cuentas =====
CREATE TABLE platos (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nombre VARCHAR(100) NOT NULL, descripcion TEXT,
  precio DECIMAL(10,2) NOT NULL,
  lleva_flores TINYINT(1) NOT NULL DEFAULT 0,
  puntos INT NOT NULL DEFAULT 0,               -- puntos que suma si lleva flores
  imagen_url VARCHAR(255)
) ENGINE=InnoDB;
CREATE TABLE pedidos (                         -- lo carga el mozo (rol mozo/admin); la cocina lo ve en pantalla con la mesa
  id INT AUTO_INCREMENT PRIMARY KEY,
  mesa_id INT NOT NULL, usuario_id INT NULL, mozo_id INT NULL,
  estado ENUM('pendiente','listo','entregado') NOT NULL DEFAULT 'pendiente',
  creado_en DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (mesa_id) REFERENCES mesas(id),
  FOREIGN KEY (usuario_id) REFERENCES usuarios(id),
  FOREIGN KEY (mozo_id) REFERENCES usuarios(id)
) ENGINE=InnoDB;
CREATE TABLE pedido_items (
  id INT AUTO_INCREMENT PRIMARY KEY,
  pedido_id INT NOT NULL, plato_id INT NOT NULL,
  cantidad INT NOT NULL DEFAULT 1,
  FOREIGN KEY (pedido_id) REFERENCES pedidos(id) ON DELETE CASCADE,
  FOREIGN KEY (plato_id) REFERENCES platos(id)
) ENGINE=InnoDB;
CREATE TABLE cuentas (                         -- al pedir la cuenta se genera el código de puntos
  id INT AUTO_INCREMENT PRIMARY KEY,
  pedido_id INT NULL,
  total DECIMAL(10,2),
  puntos INT NOT NULL DEFAULT 0,
  codigo VARCHAR(12) NOT NULL UNIQUE,
  canjeado_por INT NULL,
  canjeado_en DATETIME NULL,
  creado_en DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (pedido_id) REFERENCES pedidos(id),
  FOREIGN KEY (canjeado_por) REFERENCES usuarios(id)
) ENGINE=InnoDB;

-- ===== Puntos =====
CREATE TABLE movimientos_puntos (
  id INT AUTO_INCREMENT PRIMARY KEY,
  usuario_id INT NOT NULL,
  tipo ENUM('ganado','canje','ajuste') NOT NULL,
  puntos INT NOT NULL,                         -- positivo suma, negativo resta
  detalle VARCHAR(200),
  creado_en DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE,
  INDEX idx_mov_usuario (usuario_id, creado_en)
) ENGINE=InnoDB;
CREATE TABLE canjes (                          -- puntos elegidos -> descuento + QR para el mozo
  id INT AUTO_INCREMENT PRIMARY KEY,
  usuario_id INT NOT NULL,
  puntos_usados INT NOT NULL,
  descuento_pct INT NOT NULL,
  token CHAR(36) NOT NULL UNIQUE,
  estado ENUM('pendiente','usado','vencido') NOT NULL DEFAULT 'pendiente',
  creado_en DATETIME DEFAULT CURRENT_TIMESTAMP,
  expira_en DATETIME NOT NULL,
  usado_en DATETIME NULL,
  FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ===== Misiones =====
CREATE TABLE misiones (
  id INT AUTO_INCREMENT PRIMARY KEY,
  titulo VARCHAR(100) NOT NULL, descripcion TEXT,
  rango_min_id INT NULL,                       -- visible desde este rango
  recompensa_pct INT NOT NULL DEFAULT 0,       -- descuento en comida al completarla
  es_premium TINYINT(1) NOT NULL DEFAULT 0,    -- misión extra paga (flor de cerámica + galletita)
  precio DECIMAL(10,2) DEFAULT 0,
  FOREIGN KEY (rango_min_id) REFERENCES rangos(id)
) ENGINE=InnoDB;
CREATE TABLE usuario_misiones (
  id INT AUTO_INCREMENT PRIMARY KEY,
  usuario_id INT NOT NULL, mision_id INT NOT NULL,
  estado ENUM('en_curso','completada') NOT NULL DEFAULT 'en_curso',
  completada_en DATETIME NULL,
  UNIQUE (usuario_id, mision_id),
  FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE,
  FOREIGN KEY (mision_id) REFERENCES misiones(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ===== Datos iniciales =====
INSERT INTO rangos (nombre, orden, puntos_min, descuento_pct) VALUES
('semilla',1,0,0),('capullo',2,100,5),('floreciendo',3,300,10),('flor',4,700,15);

INSERT INTO estaciones (nombre) VALUES ('Primavera'),('Verano'),('Otoño'),('Invierno');
INSERT INTO categorias (nombre, descripcion) VALUES
('Interior','Plantas ideales para decorar ambientes cerrados.'),
('Exterior','Plantas resistentes para jardines, balcones o terrazas.'),
('Suculentas y Cactos','Requieren poco riego y buena iluminación.');

INSERT INTO plantas (nombre_comun,nombre_cientifico,categoria_id,estacion_ideal_id,riego,luz,temperatura_ideal,descripcion,imagen_url) VALUES
('Monstera Deliciosa','Monstera deliciosa',1,1,'Cada 7-10 días','Luz indirecta brillante','18°C - 27°C','Famosa por sus hojas perforadas. Aporta un toque tropical único.','https://images.unsplash.com/photo-1614594975525-e45190c55d0b?auto=format&fit=crop&w=600&q=80'),
('Ficus Lyrata','Ficus lyrata',1,2,'Moderado, sin encharcar','Mucha luz indirecta','20°C - 28°C','Hojas gigantes en forma de lira, elegante y de gran tamaño.','https://images.unsplash.com/photo-1545241047-6083a3684587?auto=format&fit=crop&w=600&q=80'),
('Sansevieria','Sansevieria trifasciata',1,4,'Cada 15-20 días','Sombra a luz directa','15°C - 30°C','Lengua de suegra: purificadora y de bajísimo mantenimiento.','https://images.unsplash.com/photo-1509423350716-97f9360b4e09?auto=format&fit=crop&w=600&q=80'),
('Lavanda','Lavandula angustifolia',2,3,'Escaso','Sol directo','10°C - 25°C','Aroma relajante; sus flores atraen polinizadores y alejan plagas.','https://images.unsplash.com/photo-1528183429752-a97d0bf99b5a?auto=format&fit=crop&w=600&q=80');

INSERT INTO mesas (numero, capacidad) VALUES (1,2),(2,2),(3,2),(4,2),(5,4),(6,4),(7,4),(8,4),(9,6),(10,6);

INSERT INTO platos (nombre,descripcion,precio,lleva_flores,puntos) VALUES
('Ensalada de pétalos','Hojas verdes, pétalos de rosa y flores de borraja',9500,1,30),
('Tostada de ricota y caléndula','Pan de masa madre, ricota y flores de caléndula',8200,1,25),
('Risotto de flor de zapallo','Arroz, parmesano y flor de zapallo',13500,1,50),
('Té de lavanda y hibisco','Infusión floral de la casa',4500,1,15),
('Café de especialidad','Grano de origen, tostado local',3800,0,0),
('Tarta de zanahoria','Con frosting de queso',6200,0,0);

INSERT INTO misiones (titulo,descripcion,rango_min_id,recompensa_pct,es_premium,precio) VALUES
('Primer brote','Probá tu primer plato con flores',1,5,0,0),
('Jardinero curioso','Escaneá el QR de una planta y leé sus cuidados',1,5,0,0),
('Tres visitas floridas','Visitanos 3 veces en un mes',2,10,0,0),
('Reseña del jardín','Contanos cómo te fue en tu última visita',3,10,0,0),
('Pintá tu flor 🎨','Misión extra: te damos una flor de cerámica para pintar y, al terminar, una galletita de regalo',1,0,1,6500);
