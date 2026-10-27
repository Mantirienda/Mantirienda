-- Si ya importaste mantirienda.sql ANTES de esta versión, ejecutá esto en phpMyAdmin (pestaña SQL) sobre mantirienda_db.
USE mantirienda_db;
ALTER TABLE reservas
  ADD COLUMN codigo VARCHAR(10) NULL UNIQUE AFTER id,
  ADD COLUMN nombre_contacto VARCHAR(100) NULL,
  ADD COLUMN telefono VARCHAR(30) NULL,
  ADD COLUMN zona ENUM('salon','jardin','terraza') NOT NULL DEFAULT 'salon',
  ADD COLUMN ocasion VARCHAR(40) NULL,
  ADD COLUMN metodo_pago ENUM('transferencia','mercadopago','efectivo') NOT NULL DEFAULT 'transferencia',
  ADD COLUMN monto_sena DECIMAL(10,2) NOT NULL DEFAULT 0,
  ADD COLUMN pago_estado ENUM('pendiente','pagado') NOT NULL DEFAULT 'pendiente';
