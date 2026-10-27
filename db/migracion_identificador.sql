-- =====================================================
-- MANTIRIENDA - Migración: identificador de plantas
-- Ejecutar UNA sola vez sobre la base existente.
-- =====================================================

SET NAMES utf8mb4;

-- Índice para cruzar el resultado de la IA con el catálogo del vivero
-- (búsquedas por nombre científico exacto y por género: 'Monstera %').
ALTER TABLE plantas
  ADD KEY idx_nombre_cientifico (nombre_cientifico);

-- Historial de identificaciones (sirve también para limitar abusos).
CREATE TABLE identificaciones (
  id              INT AUTO_INCREMENT PRIMARY KEY,
  usuario_id      INT NULL,                -- NULL si no inició sesión
  ip_hash         CHAR(64) NOT NULL,                -- HMAC de la IP, nunca la IP en claro
  cantidad_fotos  TINYINT NOT NULL,
  especie_top     VARCHAR(120) NULL,                -- mejor coincidencia devuelta por la IA
  puntaje_top     DECIMAL(5,4) NULL,                -- 0.0000 a 1.0000
  planta_id       INT NULL,                -- planta del catálogo que coincidió
  creado_en       DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  KEY idx_ip_fecha (ip_hash, creado_en),
  KEY idx_usuario_fecha (usuario_id, creado_en),
  KEY idx_planta (planta_id),
  CONSTRAINT fk_ident_usuario FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE SET NULL,
  CONSTRAINT fk_ident_planta  FOREIGN KEY (planta_id)  REFERENCES plantas(id)  ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
